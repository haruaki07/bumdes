<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\EBilling\Enums\UserRole;
use Modules\EBilling\Models\User;

class UserController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::datatable();

        return view('e-billing::users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('e-billing::users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:ebil_users,email'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('e-billing.settings.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('e-billing::users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('e-billing::users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('ebil_users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if ($user->role === UserRole::ADMIN && ($data['role'] ?? $user->role) !== UserRole::ADMIN->value) {
            $adminCount = User::where('role', UserRole::ADMIN->value)->count();
            if ($adminCount <= 1) {
                return redirect()->back()->withInput()->with('error', 'Tidak dapat menurunkan role admin terakhir.');
            }
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password,
        ]);

        return redirect()->route('e-billing.settings.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if (Auth::guard('ebil')->id() === $user->id) {
            return redirect()->route('e-billing.settings.users.index')
                ->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        // Ensure at least one admin remains
        if ($user->role === UserRole::ADMIN) {
            $adminCount = User::where('role', UserRole::ADMIN->value)->count();
            if ($adminCount <= 1) {
                return redirect()->route('e-billing.settings.users.index')
                    ->with('error', 'Tidak dapat menghapus admin terakhir.');
            }
        }

        $user->delete();

        return redirect()->route('e-billing.settings.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
