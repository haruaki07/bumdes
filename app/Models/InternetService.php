<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternetService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'installation_address',
        'package_name',
        'monthly_fee',
        'status',
        'suspension_reason',
        'suspension_date',
        'contact_phone',
        'contact_whatsapp',
        'installation_date',
        'next_billing_date',
    ];

    protected $casts = [
        'monthly_fee' => 'decimal:2',
        'suspension_date' => 'date',
        'installation_date' => 'date',
        'next_billing_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InternetPayment::class);
    }
}
