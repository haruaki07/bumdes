<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundingDisbursement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'funding_request_id',
        'disbursed_by',
        'disbursement_date',
        'amount',
        'payment_method',
        'bank_name',
        'account_number',
        'account_holder_name',
        'notes',
        'proof_document',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'disbursement_date' => 'date',
    ];

    public function fundingRequest(): BelongsTo
    {
        return $this->belongsTo(FundingRequest::class);
    }

    public function disbursedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }
}
