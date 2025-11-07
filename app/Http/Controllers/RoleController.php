<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    /**
     * Get the list of available roles with their descriptions
     */
    private function getRoles(): array
    {
        return [
            [
                'id' => 'admin',
                'name' => 'Admin',
                'description' => 'Administrator dengan akses penuh ke semua fitur sistem',
                'color' => 'red',
            ],
            [
                'id' => 'operator',
                'name' => 'Operator',
                'description' => 'Operator dengan akses terbatas untuk mengelola data usaha',
                'color' => 'blue',
            ],
            [
                'id' => 'warga',
                'name' => 'Warga',
                'description' => 'Warga dengan akses terbatas untuk melihat dan mengajukan usaha',
                'color' => 'green',
            ],
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $roles = collect($this->getRoles())->map(function ($role) {
            $role['users_count'] = User::where('role', $role['id'])->count();

            return $role;
        });

        return view('roles.index', compact('roles'));
    }
}
