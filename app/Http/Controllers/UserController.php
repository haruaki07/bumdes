<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query();

        if ($request->filled('role')) {
            $users->where('role', $request->role);
        }

        $users = $users->datatable();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', User::class);

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,operator,warga'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        Gate::authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        Gate::authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:admin,operator,warga'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        // Check if trying to change admin role
        if ($user->role === 'admin' && $validated['role'] !== 'admin') {
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                return redirect()
                    ->route('users.index')
                    ->with('error', 'Hanya admin yang dapat mengubah peran admin.');
            }

            // Check if this is the last admin
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Tidak dapat menurunkan role admin terakhir.');
            }
        }

        // Check if trying to promote to admin
        if ($user->role !== 'admin' && $validated['role'] === 'admin') {
            if (Auth::user()->role !== 'admin') {
                return redirect()
                    ->route('users.index')
                    ->with('error', 'Hanya admin yang dapat mengubah peran menjadi admin.');
            }
        }

        // Update password only if provided
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        // Prevent deleting self
        if (Auth::id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        // Ensure at least one admin remains
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()
                    ->route('users.index')
                    ->with('error', 'Tidak dapat menghapus admin terakhir.');
            }
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
