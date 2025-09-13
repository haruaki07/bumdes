<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\EBilling\Enums\UserRole;

class User extends Authenticatable
{
    use Datatable, HasFactory, Notifiable;

    protected $table = 'ebil_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dataTableColumns = [
        'name' => 'searchable|sortable',
        'email' => 'searchable|sortable',
        'role' => 'searchable|sortable',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }
}
