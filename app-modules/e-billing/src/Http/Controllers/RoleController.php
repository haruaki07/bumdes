<?php

namespace Modules\EBilling\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController
{
    protected $actions = ['read', 'create', 'update', 'delete'];

    protected $groupOrder = ['master_data', 'transactions', 'settings'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::guardName('ebil')->datatable();
        $groupOrder = $this->groupOrder;

        return view('e-billing::roles.index', compact('roles', 'groupOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::guardName('ebil')->get();
        $actions = $this->actions;
        $groupOrder = $this->groupOrder;

        return view('e-billing::roles.create', compact('permissions', 'actions', 'groupOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'guard_name' => 'ebil',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('e-billing.settings.roles.index')->with('success', "Role '{$role->name}' berhasil dibuat!");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('e-billing.settings.roles.index')->with('error', "Role 'admin' tidak dapat diedit!");
        }

        $permissions = Permission::guardName('ebil')->get();
        $actions = $this->actions;
        $groupOrder = $this->groupOrder;

        return view('e-billing::roles.edit', compact('role', 'permissions', 'actions', 'groupOrder'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('e-billing.settings.roles.index')->with('error', "Role 'admin' tidak dapat diedit!");
        }

        $data = $request->validate([
            'name' => ['required', 'string', Rule::unique('roles', 'name')->ignore($request->name, 'name')],
            'description' => 'nullable|string',
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('e-billing.settings.roles.index')->with('success', "Role '{$role->name}' berhasil diperbarui!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('e-billing.settings.roles.index')->with('error', "Role 'admin' tidak dapat dihapus!");
        }

        $role->delete();

        return redirect()->route('e-billing.settings.roles.index')->with('success', "Role '{$role->name}' berhasil dihapus!");
    }
}
