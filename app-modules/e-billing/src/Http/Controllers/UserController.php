<?php

namespace Modules\EBilling\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\EBilling\Enums\UserRole;
use Modules\EBilling\Models\User;

class UserController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read-users,ebil', only: ['index', 'show']),
            new Middleware('permission:create-users,ebil', only: ['create', 'store']),
            new Middleware('permission:update-users,ebil', only: ['edit', 'update']),
            new Middleware('permission:delete-users,ebil', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::guardName('ebil')->get();
        $users = User::query();

        if ($request->filled('role')) {
            $users->role($request->role);
        }

        $users = $users->datatable();

        return view('e-billing::users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::guardName('ebil')->get();

        return view('e-billing::users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:ebil_users,email'],
            'role' => ['required', 'exists:roles,name'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('e-billing.settings.users.index')
            ->with('success', 'User berhasil ditambahkan!');
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
        $roles = Role::guardName('ebil')->get();

        return view('e-billing::users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('ebil_users', 'email')->ignore($user->id)],
            'role' => ['required', 'exists:roles,name'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $authUserRole = $request->user('ebil')->getRoleNames()->first();

        if ($user->getRoleNames()->first() === 'admin' && $data['role'] !== 'admin') {
            if ($authUserRole !== 'admin') {
                return redirect()->route('e-billing.settings.users.index')
                    ->with('error', 'Hanya admin yang dapat mengubah peran admin.');
            }

            $adminCount = User::role('admin')->count();
            if ($adminCount <= 1) {
                return redirect()->back()->withInput()->with('error', 'Tidak dapat menurunkan role admin terakhir.');
            }
        } elseif ($user->getRoleNames()->first() !== 'admin' && $data['role'] === 'admin') {
            if ($authUserRole !== 'admin') {
                return redirect()->route('e-billing.settings.users.index')
                    ->with('error', 'Hanya admin yang dapat mengubah peran menjadi admin.');
            }
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password,
        ]);

        $user->syncRoles($data['role']);

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
