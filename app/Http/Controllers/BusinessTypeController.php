<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BusinessTypeController extends Controller
{
    public function __construct() {}

    public function index(Request $request)
    {
        $businessTypes = BusinessType::withCount('businesses')->datatable();

        return view('business-types.index', compact('businessTypes'));
    }

    public function create()
    {
        Gate::authorize('create', BusinessType::class);

        return view('business-types.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', BusinessType::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:business_types,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $businessType = BusinessType::create($validated);

        return redirect()
            ->route('business-types.show', $businessType)
            ->with('success', 'Jenis usaha berhasil dibuat.');
    }

    public function show(BusinessType $businessType)
    {
        Gate::authorize('view', $businessType);

        $businessType->loadCount('businesses');

        return view('business-types.show', compact('businessType'));
    }

    public function edit(BusinessType $businessType)
    {
        Gate::authorize('update', $businessType);

        return view('business-types.edit', compact('businessType'));
    }

    public function update(Request $request, BusinessType $businessType)
    {
        Gate::authorize('update', $businessType);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:business_types,name,'.$businessType->id],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $businessType->update($validated);

        return redirect()
            ->route('business-types.show', $businessType)
            ->with('success', 'Jenis usaha berhasil diperbarui.');
    }

    public function destroy(BusinessType $businessType)
    {
        Gate::authorize('delete', $businessType);

        if ($businessType->businesses()->count() > 0) {
            return redirect()
                ->route('business-types.index')
                ->with('error', 'Jenis usaha tidak dapat dihapus karena masih memiliki usaha terkait.');
        }

        $businessType->delete();

        return redirect()
            ->route('business-types.index')
            ->with('success', 'Jenis usaha berhasil dihapus.');
    }
}
