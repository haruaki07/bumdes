<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BusinessController extends Controller
{
    public function __construct() {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $businesses = Business::with(['businessType', 'owner'])
            ->when($user->role === 'warga', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->datatable();

        return view('businesses.index', compact('businesses'));
    }

    public function create()
    {
        Gate::authorize('create', Business::class);

        $businessTypes = BusinessType::where('is_active', true)->get();

        return view('businesses.create', compact('businessTypes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Business::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_type_id' => ['required', 'exists:business_types,id'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:255'],
        ]);

        $validated['owner_id'] = Auth::id();
        $validated['status'] = 'active';

        $business = Business::create($validated);

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', 'Usaha berhasil dibuat.');
    }

    public function show(Business $business)
    {
        Gate::authorize('view', $business);

        $business->load(['businessType', 'owner', 'fundingRequests']);

        return view('businesses.show', compact('business'));
    }

    public function edit(Business $business)
    {
        Gate::authorize('update', $business);

        $businessTypes = BusinessType::where('is_active', true)->get();

        return view('businesses.edit', compact('business', 'businessTypes'));
    }

    public function update(Request $request, Business $business)
    {
        Gate::authorize('update', $business);

        $validated = $request->validate([
            'business_type_id' => ['required', 'exists:business_types,id'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:255'],
        ]);

        if ($request->has('name') && (Auth::user()->role === 'admin' || Auth::user()->role === 'operator')) {
            $validated['name'] = $request->validate(['name' => ['required', 'string', 'max:255']])['name'];
        }

        $business->update($validated);

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', 'Data usaha berhasil diperbarui.');
    }

    public function destroy(Business $business)
    {
        Gate::authorize('delete', $business);

        $business->delete();

        return redirect()
            ->route('businesses.index')
            ->with('success', 'Data usaha berhasil dihapus.');
    }
}
