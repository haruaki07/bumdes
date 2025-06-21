<?php

namespace App\Models;

use App\Enums\BusinessStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'name',
    'business_type_id',
    'owner_id',
    'description',
    'location',
    'contact_phone',
    'contact_email',
    'status',
    'rejection_reason',
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
}
