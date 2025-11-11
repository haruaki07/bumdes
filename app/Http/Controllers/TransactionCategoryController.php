<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionCategoryRequest;
use App\Http\Requests\UpdateTransactionCategoryRequest;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\Gate;

class TransactionCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('manageTransactions');

        $categories = TransactionCategory::with('parent')
            ->orderBy('type')
            ->orderBy('order')
            ->datatable();

        return view('transaction-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('manageTransactions');

        $parentCategories = TransactionCategory::whereNull('parent_id')
            ->active()
            ->orderBy('type')
            ->orderBy('order')
            ->get();

        return view('transaction-categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionCategoryRequest $request)
    {
        Gate::authorize('manageTransactions');

        $validated = $request->validated();

        try {
            TransactionCategory::create($validated);

            return redirect()
                ->route('transaction-categories.index')
                ->with('success', 'Kategori transaksi berhasil dibuat.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionCategory $transactionCategory)
    {
        Gate::authorize('manageTransactions');

        $parentCategories = TransactionCategory::whereNull('parent_id')
            ->active()
            ->where('id', '!=', $transactionCategory->id)
            ->orderBy('type')
            ->orderBy('order')
            ->get();

        $transactionCategory->loadCount(['transactions', 'children']);

        return view('transaction-categories.edit', [
            'category' => $transactionCategory,
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionCategoryRequest $request, TransactionCategory $transactionCategory)
    {
        Gate::authorize('manageTransactions');

        $validated = $request->validated();

        try {
            $transactionCategory->update($validated);

            return redirect()
                ->route('transaction-categories.index')
                ->with('success', 'Kategori transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionCategory $transactionCategory)
    {
        Gate::authorize('manageTransactions');

        // Check if category has transactions
        if ($transactionCategory->transactions()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki transaksi.');
        }

        // Check if category has child categories
        if ($transactionCategory->children()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki sub-kategori.');
        }

        try {
            $transactionCategory->delete();

            return redirect()
                ->route('transaction-categories.index')
                ->with('success', 'Kategori transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Toggle category active status
     */
    public function toggleActive(TransactionCategory $transactionCategory)
    {
        Gate::authorize('manageTransactions');

        try {
            $transactionCategory->update([
                'is_active' => ! $transactionCategory->is_active,
            ]);

            $status = $transactionCategory->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return back()->with('success', "Kategori berhasil {$status}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
