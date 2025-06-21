<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundingRequest extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'user_id',
    'business_id',
    'amount',
    'purpose',
    'status',
    'rejection_reason',
    'mou_document',
    'is_mou_approved',
    'signature_document',
    'disbursement_date',
    'due_date',
    'disbursed_amount',
    'repaid_amount',
  ];

  protected $casts = [
    'amount' => 'decimal:2',
    'disbursed_amount' => 'decimal:2',
    'repaid_amount' => 'decimal:2',
    'is_mou_approved' => 'boolean',
    'disbursement_date' => 'date',
    'due_date' => 'date',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function business(): BelongsTo
  {
    return $this->belongsTo(Business::class);
  }
}
