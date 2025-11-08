<?php

namespace App\Models;

use App\Traits\Datatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Datatable, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The datatable columns configuration.
     *
     * @var array
     */
    protected $dataTableColumns = [
        'name' => 'searchable|sortable',
        'email' => 'searchable|sortable',
        'role' => 'searchable|sortable',
        'created_at' => 'sortable',
        'updated_at' => 'sortable',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the warga profile associated with the user.
     */
    public function wargaProfile(): HasOne
    {
        return $this->hasOne(WargaProfile::class);
    }

    /**
     * Check if the warga profile is completed.
     */
    public function hasCompletedProfile(): bool
    {
        if (! $this->wargaProfile) {
            return false;
        }

        // Check if essential profile fields are filled
        return ! empty($this->wargaProfile->nik)
            && ! empty($this->wargaProfile->phone)
            && ! empty($this->wargaProfile->address);
    }
}
