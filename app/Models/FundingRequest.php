<?php

namespace App\Models;

use App\Enums\FundingRequestStatus;
use App\Policies\FundingRequestPolicy;
use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(FundingRequestPolicy::class)]
class FundingRequest extends Model
{
    use Datatable, HasFactory, SoftDeletes;

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
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'mou_uploaded_by',
        'mou_uploaded_at',
        'mou_signed_at',
        'interest_rate',
        'repayment_duration_months',
    ];

    protected $casts = [
        'amount' => 'float',
        'disbursed_amount' => 'float',
        'repaid_amount' => 'float',
        'interest_rate' => 'float',
        'is_mou_approved' => 'boolean',
        'disbursement_date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'mou_uploaded_at' => 'datetime',
        'mou_signed_at' => 'datetime',
        'status' => FundingRequestStatus::class,
    ];

    protected $dataTableColumns = [
        'user.name' => 'searchable|sortable',
        'business.name' => 'searchable|sortable',
        'amount' => 'sortable',
        'status' => 'searchable|sortable',
        'created_at' => 'sortable',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function mouUploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mou_uploaded_by');
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(FundingRequestTimeline::class)->orderBy('created_at', 'desc');
    }

    public function disbursements(): HasMany
    {
        return $this->hasMany(FundingDisbursement::class)->orderBy('disbursement_date', 'desc');
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(FundingRepayment::class)->orderBy('payment_date', 'desc');
    }

    public function getTotalRepaidAttribute(): float
    {
        return (float) $this->repayments()->whereNotNull('verified_at')->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) ($this->disbursed_amount - $this->getTotalRepaidAttribute());
    }

    public function isFullyRepaid(): bool
    {
        return $this->getRemainingAmountAttribute() <= 0;
    }
}
