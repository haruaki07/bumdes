<?php

namespace App\Http\Controllers;

use App\Enums\BusinessRegistrationStatus;
use App\Enums\BusinessRegistrationTimelineAction;
use App\Enums\BusinessStatus;
use App\Models\Business;
use App\Models\BusinessRegistration;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class BusinessRegistrationController extends Controller
{
    public function __construct() {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $registrations = BusinessRegistration::with(['businessType', 'applicant', 'approver'])
            ->when($user->role === 'warga', function ($query) use ($user) {
                $query->where('applicant_id', $user->id);
            })
            ->whereNot('is_revised', true)
            ->datatable();

        return view('business-registrations.index', compact('registrations'));
    }

    public function create()
    {
        Gate::authorize('create', BusinessRegistration::class);

        $businessTypes = BusinessType::where('is_active', true)->get();

        return view('business-registrations.create', compact('businessTypes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', BusinessRegistration::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_type_id' => ['required', 'exists:business_types,id'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,png', 'max:5120'], // 5MB max
        ]);

        try {
            DB::beginTransaction();
            $document_url = Storage::disk('public')->putFileAs(
                'business_documents',
                $request->document,
                Auth::id().'_'.time().'_'.$request->document->getClientOriginalName()
            );

            $data = $request->except('document');
            $data['document_url'] = $document_url;
            $data['applicant_id'] = Auth::id();
            $data['status'] = 'pending';

            $registration = BusinessRegistration::create($data);

            $registration->timeline()->create([
                'action' => BusinessRegistrationTimelineAction::SUBMITTED,
                'performed_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('business-registrations.show', $registration)
                ->with('success', 'Pengajuan usaha berhasil dibuat dan menunggu persetujuan admin.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['document' => 'Gagal mengunggah dokumen: '.$e->getMessage()]);
        }
    }

    public function show(BusinessRegistration $businessRegistration)
    {
        Gate::authorize('view', $businessRegistration);

        $businessRegistration->load(['businessType', 'applicant', 'approver', 'timeline.performer', 'revisions']);

        $timeline = $businessRegistration->getTimeline();

        return view('business-registrations.show', compact('businessRegistration', 'timeline'));
    }

    public function destroy(BusinessRegistration $businessRegistration)
    {
        Gate::authorize('delete', $businessRegistration);

        $businessRegistration->delete();

        return redirect()
            ->route('business-registrations.index')
            ->with('success', 'Pengajuan usaha berhasil dihapus.');
    }

    public function approve(BusinessRegistration $businessRegistration)
    {
        Gate::authorize('approve', $businessRegistration);

        DB::transaction(function () use ($businessRegistration) {
            $businessRegistration->update([
                'status' => BusinessRegistrationStatus::APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            // create the actual business
            $business = Business::create([
                'name' => $businessRegistration->name,
                'business_type_id' => $businessRegistration->business_type_id,
                'owner_id' => $businessRegistration->applicant_id,
                'description' => $businessRegistration->description,
                'location' => $businessRegistration->location,
                'contact_phone' => $businessRegistration->contact_phone,
                'contact_email' => $businessRegistration->contact_email,
                'status' => BusinessStatus::ACTIVE,
            ]);

            // link the registration to the business
            $businessRegistration->update(['business_id' => $business->id]);

            $businessRegistration->timeline()->create([
                'action' => BusinessRegistrationTimelineAction::APPROVED,
                'performed_by' => Auth::id(),
                'metadata' => ['business_id' => $business->id],
            ]);
        });

        return redirect()
            ->route('business-registrations.show', $businessRegistration)
            ->with('success', 'Pengajuan usaha berhasil disetujui dan usaha berhasil dibuat.');
    }

    public function reject(Request $request, BusinessRegistration $businessRegistration)
    {
        Gate::authorize('reject', $businessRegistration);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $businessRegistration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $businessRegistration->timeline()->create([
            'action' => BusinessRegistrationTimelineAction::REJECTED,
            'performed_by' => Auth::id(),
            'metadata' => ['rejection_reason' => $validated['rejection_reason']],
        ]);

        return redirect()
            ->route('business-registrations.show', $businessRegistration)
            ->with('success', 'Pengajuan usaha berhasil ditolak.');
    }

    public function revise(BusinessRegistration $businessRegistration)
    {
        Gate::authorize('revise', $businessRegistration);

        $businessTypes = BusinessType::where('is_active', true)->get();

        return view('business-registrations.revise', compact('businessRegistration', 'businessTypes'));
    }

    public function storeRevision(Request $request, BusinessRegistration $businessRegistration)
    {
        Gate::authorize('revise', $businessRegistration);

        $revision = DB::transaction(function () use ($request, $businessRegistration) {
            if ($businessRegistration->is_revised) {
                return redirect()
                    ->route('business-registrations.show', $businessRegistration)
                    ->with('error', 'Pengajuan usaha sudah memiliki revisi. Silahkan lihat revisi terakhir.');
            }

            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'business_type_id' => ['required', 'exists:business_types,id'],
                'description' => ['required', 'string'],
                'location' => ['required', 'string', 'max:255'],
                'contact_phone' => ['required', 'string', 'max:20'],
                'contact_email' => ['nullable', 'email', 'max:255'],
            ]);

            $data['applicant_id'] = Auth::id();
            $data['status'] = 'pending';
            $data['parent_id'] = $businessRegistration->id;
            $data['revision_number'] = $businessRegistration->revision_number + 1;
            $revision = BusinessRegistration::create($data);

            $businessRegistration->update(['is_revised' => true]);

            // create timeline entry for the original registration
            $businessRegistration->timeline()->create([
                'action' => BusinessRegistrationTimelineAction::REVISED,
                'performed_by' => Auth::id(),
                'metadata' => [
                    'parent_id' => $businessRegistration->id,
                    'revision_id' => $revision->id,
                    'revision_number' => $revision->revision_number,
                ],
            ]);

            return $revision;
        });

        return redirect()
            ->route('business-registrations.show', $revision)
            ->with('success', 'Revisi pengajuan usaha berhasil dibuat dan menunggu persetujuan admin.');
    }
}
