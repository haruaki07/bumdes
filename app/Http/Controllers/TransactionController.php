<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\FundingRequest;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('manageTransactions');

        $query = Transaction::with(['category', 'creator', 'verifier', 'fundingRequest.business']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('transaction_category_id', $request->category_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('verified')) {
            if ($request->verified === '1') {
                $query->whereNotNull('verified_at');
            } else {
                $query->whereNull('verified_at');
            }
        }

        $transactions = $query->datatable(function ($q) {
            $q->orderBy('transaction_date', 'desc');
        });

        $categories = TransactionCategory::active()->orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('manageTransactions');

        $categories = TransactionCategory::active()
            ->orderBy('type')
            ->orderBy('order')
            ->get()
            ->groupBy('type');

        $fundingRequests = FundingRequest::whereIn('status', ['disbursed', 'completed'])
            ->with('business')
            ->get();

        return view('transactions.create', compact('categories', 'fundingRequests'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        Gate::authorize('manageTransactions');

        $validated = $request->validated();
        $validated['created_by'] = Auth::id();

        DB::beginTransaction();
        try {
            // Handle document upload
            if ($request->hasFile('document')) {
                $path = Storage::disk('public')->putFileAs(
                    'transaction-documents',
                    $request->file('document'),
                    'TRX_'.time().'_'.$request->file('document')->getClientOriginalName()
                );
                $validated['document_path'] = $path;
            }

            // Auto-generate reference number if not provided
            if (empty($validated['reference_number'])) {
                $type = $validated['type'] === 'income' ? 'INC' : 'EXP';
                $date = date('Ym');
                $last = Transaction::where('reference_number', 'like', "{$type}-{$date}-%")->count();
                $validated['reference_number'] = sprintf('%s-%s-%04d', $type, $date, $last + 1);
            }

            $transaction = Transaction::create($validated);

            DB::commit();

            return redirect()
                ->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        Gate::authorize('manageTransactions');

        $transaction->load(['category.parent', 'creator', 'verifier', 'fundingRequest.business']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        Gate::authorize('manageTransactions');

        // Don't allow editing verified transactions
        if ($transaction->isVerified()) {
            return back()->with('error', 'Transaksi yang sudah diverifikasi tidak dapat diedit.');
        }

        $categories = TransactionCategory::active()
            ->orderBy('type')
            ->orderBy('order')
            ->get()
            ->groupBy('type');

        $fundingRequests = FundingRequest::whereIn('status', ['disbursed', 'completed'])
            ->with('business')
            ->get();

        return view('transactions.edit', compact('transaction', 'categories', 'fundingRequests'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        Gate::authorize('manageTransactions');

        // Don't allow editing verified transactions
        if ($transaction->isVerified()) {
            return back()->with('error', 'Transaksi yang sudah diverifikasi tidak dapat diedit.');
        }

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Handle document upload
            if ($request->hasFile('document')) {
                // Delete old document
                if ($transaction->document_path) {
                    Storage::disk('public')->delete($transaction->document_path);
                }

                $path = Storage::disk('public')->putFileAs(
                    'transaction-documents',
                    $request->file('document'),
                    'TRX_'.time().'_'.$request->file('document')->getClientOriginalName()
                );
                $validated['document_path'] = $path;
            }

            $transaction->update($validated);

            DB::commit();

            return redirect()
                ->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        Gate::authorize('manageTransactions');

        // Don't allow deleting verified transactions
        if ($transaction->isVerified()) {
            return back()->with('error', 'Transaksi yang sudah diverifikasi tidak dapat dihapus.');
        }

        try {
            // Delete document if exists
            if ($transaction->document_path) {
                Storage::disk('public')->delete($transaction->document_path);
            }

            $transaction->delete();

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Verify a transaction
     */
    public function verify(Transaction $transaction)
    {
        Gate::authorize('manageTransactions');

        if ($transaction->isVerified()) {
            return back()->with('error', 'Transaksi sudah diverifikasi.');
        }

        try {
            $transaction->update([
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            return back()->with('success', 'Transaksi berhasil diverifikasi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Unverify a transaction
     */
    public function unverify(Transaction $transaction)
    {
        Gate::authorize('manageTransactions');

        if (! $transaction->isVerified()) {
            return back()->with('error', 'Transaksi belum diverifikasi.');
        }

        try {
            $transaction->update([
                'verified_by' => null,
                'verified_at' => null,
            ]);

            return back()->with('success', 'Verifikasi transaksi berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
