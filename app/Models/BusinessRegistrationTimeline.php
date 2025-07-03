<?php

namespace App\Models;

use App\Enums\BusinessRegistrationTimelineAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessRegistrationTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_registration_id',
        'action',
        'description',
        'performed_by',
        'metadata',
    ];

    protected $casts = [
        'action' => BusinessRegistrationTimelineAction::class,
        'metadata' => 'array',
    ];

    public function businessRegistration(): BelongsTo
    {
        return $this->belongsTo(BusinessRegistration::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
