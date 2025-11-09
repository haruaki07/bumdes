<?php

namespace App\Http\Controllers;

use App\Enums\FundingRequestStatus;
use App\Enums\FundingRequestTimelineAction;
use App\Http\Requests\ApproveFundingRequestRequest;
use App\Http\Requests\DisburseFundingRequestRequest;
use App\Http\Requests\RejectFundingRequestRequest;
use App\Http\Requests\StoreFundingRequestRequest;
use App\Http\Requests\StoreRepaymentRequest;
use App\Models\Business;
use App\Models\FundingDisbursement;
use App\Models\FundingRepayment;
use App\Models\FundingRequest;
use App\Models\FundingRequestTimeline;
use App\Models\User;
use App\Notifications\DisbursementCompleted;
use App\Notifications\FundingRequestApproved;
use App\Notifications\FundingRequestRejected;
use App\Notifications\FundingRequestSubmitted;
use App\Notifications\MouUploaded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FundingRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $fundingRequests = FundingRequest::with(['user', 'business', 'approvedBy'])
            ->when($user->role === 'warga', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->datatable();

        return view('funding-requests.index', compact('fundingRequests'));
    }

    public function create()
    {
        Gate::authorize('create', FundingRequest::class);

        $businesses = Business::where('owner_id', Auth::id())
            ->where('status', 'active')
            ->get();

        return view('funding-requests.create', compact('businesses'));
    }

    public function store(StoreFundingRequestRequest $request)
    {
        Gate::authorize('create', FundingRequest::class);

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $validated['user_id'] = Auth::id();
            $validated['status'] = FundingRequestStatus::SUBMITTED;
            $validated['interest_rate'] = 0; // Default, can be set by admin

            $fundingRequest = FundingRequest::create($validated);

            // Create timeline entry
            FundingRequestTimeline::create([
                'funding_request_id' => $fundingRequest->id,
                'action' => FundingRequestTimelineAction::SUBMITTED,
                'description' => 'Pengajuan pendanaan dibuat',
                'performed_by' => Auth::id(),
                'metadata' => [
                    'amount' => $validated['amount'],
                    'purpose' => $validated['purpose'],
                ],
            ]);

            // Notify admin and operators
            $admins = User::whereIn('role', ['admin', 'operator'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new FundingRequestSubmitted($fundingRequest));
            }

            DB::commit();

            return redirect()
                ->route('funding-requests.show', $fundingRequest)
                ->with('success', 'Pengajuan pendanaan berhasil dibuat dan sedang menunggu persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat pengajuan: '.$e->getMessage());
        }
    }

    public function show(FundingRequest $fundingRequest)
    {
        Gate::authorize('view', $fundingRequest);

        $fundingRequest->load([
            'user',
            'business',
            'approvedBy',
            'rejectedBy',
            'mouUploadedBy',
            'timelines.performer',
            'disbursements.disbursedBy',
            'repayments.verifiedBy',
        ]);

        return view('funding-requests.show', compact('fundingRequest'));
    }

    public function approve(ApproveFundingRequestRequest $request, FundingRequest $fundingRequest)
    {
        Gate::authorize('approve', $fundingRequest);

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $fundingRequest->update([
                'status' => FundingRequestStatus::APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'interest_rate' => $validated['interest_rate'],
                'repayment_duration_months' => $validated['repayment_duration_months'],
            ]);

            // Create timeline entry
            FundingRequestTimeline::create([
                'funding_request_id' => $fundingRequest->id,
                'action' => FundingRequestTimelineAction::APPROVED,
                'description' => 'Pengajuan pendanaan disetujui',
                'performed_by' => Auth::id(),
                'metadata' => [
                    'interest_rate' => $validated['interest_rate'],
                    'repayment_duration_months' => $validated['repayment_duration_months'],
                    'notes' => $validated['notes'] ?? null,
                ],
            ]);

            // Notify warga
            $fundingRequest->user->notify(new FundingRequestApproved($fundingRequest));

            DB::commit();

            return back()->with('success', 'Pengajuan pendanaan berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function reject(RejectFundingRequestRequest $request, FundingRequest $fundingRequest)
    {
        Gate::authorize('reject', $fundingRequest);

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $fundingRequest->update([
                'status' => FundingRequestStatus::REJECTED,
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            // Create timeline entry
            FundingRequestTimeline::create([
                'funding_request_id' => $fundingRequest->id,
                'action' => FundingRequestTimelineAction::REJECTED,
                'description' => 'Pengajuan pendanaan ditolak',
                'performed_by' => Auth::id(),
                'metadata' => [
                    'rejection_reason' => $validated['rejection_reason'],
                ],
            ]);

            // Notify warga
            $fundingRequest->user->notify(new FundingRequestRejected($fundingRequest));

            DB::commit();

            return back()->with('success', 'Pengajuan pendanaan telah ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function uploadMou(Request $request, FundingRequest $fundingRequest)
    {
        Gate::authorize('uploadMou', $fundingRequest);

        $validated = $request->validate([
            'mou_document' => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB max
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            // Delete old MOU if exists
            if ($fundingRequest->mou_document) {
                Storage::disk('public')->delete($fundingRequest->mou_document);
            }

            // Upload new MOU
            $mouPath = Storage::disk('public')->putFileAs(
                'funding_mou',
                $request->file('mou_document'),
                'MOU_'.$fundingRequest->id.'_'.time().'.pdf'
            );

            $fundingRequest->update([
                'mou_document' => $mouPath,
                'mou_uploaded_by' => Auth::id(),
                'mou_uploaded_at' => now(),
            ]);

            // Create timeline entry
            FundingRequestTimeline::create([
                'funding_request_id' => $fundingRequest->id,
                'action' => FundingRequestTimelineAction::MOU_UPLOADED,
                'description' => 'Dokumen MOU telah diunggah',
                'performed_by' => Auth::id(),
                'metadata' => [
                    'mou_document' => $mouPath,
                    'notes' => $validated['notes'] ?? null,
                ],
            ]);

            // Notify warga
            $fundingRequest->user->notify(new MouUploaded($fundingRequest));

            DB::commit();

            return back()->with('success', 'Dokumen MOU berhasil diunggah. Warga akan menerima notifikasi untuk menandatangani.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function signMou(Request $request, FundingRequest $fundingRequest)
    {
        Gate::authorize('signMou', $fundingRequest);

        $validated = $request->validate([
            'signature_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // 10MB max
        ]);

        DB::beginTransaction();
        try {
            // Delete old signature if exists
            if ($fundingRequest->signature_document) {
                Storage::disk('public')->delete($fundingRequest->signature_document);
            }

            // Upload signed MOU
            $signaturePath = Storage::disk('public')->putFileAs(
                'funding_signatures',
                $request->file('signature_document'),
                'SIGNED_MOU_'.$fundingRequest->id.'_'.time().'.'.$request->file('signature_document')->extension()
            );

            $fundingRequest->update([
                'signature_document' => $signaturePath,
                'mou_signed_at' => now(),
                'status' => FundingRequestStatus::READY_TO_DISBURSE,
                'is_mou_approved' => true,
            ]);

            // Create timeline entry
            FundingRequestTimeline::create([
                'funding_request_id' => $fundingRequest->id,
                'action' => FundingRequestTimelineAction::MOU_SIGNED,
                'description' => 'MOU telah ditandatangani oleh warga',
                'performed_by' => Auth::id(),
                'metadata' => [
                    'signature_document' => $signaturePath,
                ],
            ]);

            DB::commit();

            return back()->with('success', 'MOU berhasil ditandatangani. Menunggu pencairan dana oleh operator.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function disburse(DisburseFundingRequestRequest $request, FundingRequest $fundingRequest)
    {
        Gate::authorize('disburse', $fundingRequest);

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $proofPath = null;
            if ($request->hasFile('proof_document')) {
                $proofPath = Storage::disk('public')->putFileAs(
                    'disbursement_proofs',
                    $request->file('proof_document'),
                    'PROOF_'.$fundingRequest->id.'_'.time().'.'.$request->file('proof_document')->extension()
                );
            }

            // Create disbursement record
            $disbursement = FundingDisbursement::create([
                'funding_request_id' => $fundingRequest->id,
                'disbursed_by' => Auth::id(),
                'disbursement_date' => $validated['disbursement_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'account_holder_name' => $validated['account_holder_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'proof_document' => $proofPath,
            ]);

            // Update funding request
            $fundingRequest->update([
                'status' => FundingRequestStatus::DISBURSED,
                'disbursement_date' => $validated['disbursement_date'],
                'disbursed_amount' => $validated['amount'],
            ]);

            // Create timeline entry
            FundingRequestTimeline::create([
                'funding_request_id' => $fundingRequest->id,
                'action' => FundingRequestTimelineAction::DISBURSED,
                'description' => 'Dana telah dicairkan',
                'performed_by' => Auth::id(),
                'metadata' => [
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'disbursement_date' => $validated['disbursement_date'],
                ],
            ]);

            // Notify warga
            $fundingRequest->user->notify(new DisbursementCompleted($fundingRequest));

            DB::commit();

            return back()->with('success', 'Dana berhasil dicairkan. Warga telah menerima notifikasi.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function storeRepayment(StoreRepaymentRequest $request, FundingRequest $fundingRequest)
    {
        Gate::authorize('recordRepayment', $fundingRequest);

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Upload proof document
            $proofPath = Storage::disk('public')->putFileAs(
                'repayment_proofs',
                $request->file('proof_document'),
                'REPAY_'.$fundingRequest->id.'_'.time().'.'.$request->file('proof_document')->extension()
            );

            // Create repayment record
            FundingRepayment::create([
                'funding_request_id' => $fundingRequest->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'proof_document' => $proofPath,
                'notes' => $validated['notes'] ?? null,
            ]);

            $totalRepaid = $fundingRequest->repayments()->sum('amount');

            $fundingRequest->update(['status' => FundingRequestStatus::REPAYING]);

            FundingRequestTimeline::create([
                'funding_request_id' => $fundingRequest->id,
                'action' => FundingRequestTimelineAction::REPAYMENT_MADE,
                'description' => 'Pembayaran cicilan dilakukan',
                'performed_by' => Auth::id(),
                'metadata' => [
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'payment_date' => $validated['payment_date'],
                    'total_repaid' => $totalRepaid,
                    'remaining' => $fundingRequest->disbursed_amount - $totalRepaid,
                ],
            ]);

            DB::commit();

            $message = 'Pembayaran cicilan berhasil dicatat. Menunggu verifikasi operator.';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function verifyRepayment(Request $request, FundingRepayment $repayment)
    {
        $fundingRequest = $repayment->fundingRequest;
        Gate::authorize('verifyRepayment', $fundingRequest);

        DB::beginTransaction();
        try {
            $repayment->update([
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            $totalRepaid = $fundingRequest->repayments()->sum('amount');
            $newStatus = $totalRepaid >= $fundingRequest->disbursed_amount
                ? FundingRequestStatus::COMPLETED
                : FundingRequestStatus::REPAYING;

            $fundingRequest->update([
                'status' => $newStatus,
                'repaid_amount' => $totalRepaid,
            ]);

            FundingRequestTimeline::create([
                'funding_request_id' => $fundingRequest->id,
                'action' => FundingRequestTimelineAction::APPROVED,
                'description' => 'Pembayaran cicilan diverifikasi',
                'performed_by' => Auth::id(),
                'metadata' => [
                    'amount' => $repayment->amount,
                    'payment_method' => $repayment->payment_method,
                    'payment_date' => $repayment->payment_date,
                    'total_repaid' => $totalRepaid,
                    'remaining' => $fundingRequest->disbursed_amount - $totalRepaid,
                ],
            ]);

            if ($newStatus === FundingRequestStatus::COMPLETED) {
                FundingRequestTimeline::create([
                    'funding_request_id' => $fundingRequest->id,
                    'action' => FundingRequestTimelineAction::COMPLETED,
                    'description' => 'Pendanaan telah lunas',
                    'performed_by' => Auth::id(),
                    'metadata' => [],
                    'created_at' => now()->addSecond(), // ensure timeline order
                ]);
            }

            DB::commit();

            return back()->with('success', 'Pembayaran cicilan berhasil diverifikasi.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
