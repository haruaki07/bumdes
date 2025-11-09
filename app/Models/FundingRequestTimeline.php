<?php

namespace App\Models;

use App\Enums\FundingRequestTimelineAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundingRequestTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'funding_request_id',
        'action',
        'description',
        'performed_by',
        'metadata',
    ];

    protected $casts = [
        'action' => FundingRequestTimelineAction::class,
        'metadata' => 'array',
    ];

    public function fundingRequest(): BelongsTo
    {
        return $this->belongsTo(FundingRequest::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
