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

    /**
     * Get the funding requests submitted by this user.
     */
    public function fundingRequests()
    {
        return $this->hasMany(FundingRequest::class);
    }

    /**
     * Get the funding requests approved by this user (admin/operator).
     */
    public function approvedFundingRequests()
    {
        return $this->hasMany(FundingRequest::class, 'approved_by');
    }

    /**
     * Get the funding requests rejected by this user (admin/operator).
     */
    public function rejectedFundingRequests()
    {
        return $this->hasMany(FundingRequest::class, 'rejected_by');
    }

    /**
     * Get the disbursements made by this user (operator).
     */
    public function disbursements()
    {
        return $this->hasMany(FundingDisbursement::class, 'disbursed_by');
    }

    /**
     * Get the repayments verified by this user (operator).
     */
    public function verifiedRepayments()
    {
        return $this->hasMany(FundingRepayment::class, 'verified_by');
    }
}
