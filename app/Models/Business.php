<?php

namespace App\Models;

use App\Policies\BusinessPolicy;
use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(BusinessPolicy::class)]
class Business extends Model
{
    use Datatable, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'business_type_id',
        'owner_id',
        'description',
        'location',
        'contact_phone',
        'contact_email',
        'document_url',
        'status',
        'rejection_reason',
        'name_change_request',
        'name_change_reason',
    ];

    protected $dataTableColumns = [
        'name' => 'searchable|sortable',
        'businessType.name' => 'searchable|sortable',
        'owner.name' => 'searchable|sortable',
        'location' => 'searchable|sortable',
        'status' => 'searchable|sortable',
        'created_at' => 'sortable',
    ];

    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function fundingRequests(): HasMany
    {
        return $this->hasMany(FundingRequest::class);
    }

    public function businessRegistration(): BelongsTo
    {
        return $this->belongsTo(BusinessRegistration::class);
    }

    public function hasNameChangeRequest(): bool
    {
        return ! empty($this->name_change_request);
    }
}
