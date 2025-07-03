<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternetPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'internet_service_id',
        'amount',
        'payment_date',
        'period_start',
        'period_end',
        'payment_method',
        'payment_proof',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function internetService(): BelongsTo
    {
        return $this->belongsTo(InternetService::class);
    }
}
