<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SamsatTransaction extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'user_id',
    'vehicle_number',
    'vehicle_type',
    'vehicle_year',
    'tax_amount',
    'admin_fee',
    'total_amount',
    'payment_date',
    'tax_period_start',
    'tax_period_end',
    'receipt_number',
    'payment_method',
    'notes',
  ];

  protected $casts = [
    'vehicle_year' => 'integer',
    'tax_amount' => 'decimal:2',
    'admin_fee' => 'decimal:2',
    'total_amount' => 'decimal:2',
    'payment_date' => 'date',
    'tax_period_start' => 'date',
    'tax_period_end' => 'date',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
