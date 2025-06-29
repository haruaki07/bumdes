<?php

namespace App\Models;

use App\Policies\BusinessRegistrationPolicy;
use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(BusinessRegistrationPolicy::class)]
class BusinessRegistration extends Model
{
  use HasFactory, SoftDeletes, Datatable;

  protected $fillable = [
    'name',
    'business_type_id',
    'applicant_id',
    'description',
    'location',
    'contact_phone',
    'contact_email',
    'status',
    'rejection_reason',
    'approved_by',
    'approved_at',
    'parent_id',
    'is_revised',
    'revision_number',
    'rejected_at',
    'rejected_by',
  ];

  protected $casts = [
    'approved_at' => 'datetime',
  ];

  protected $dataTableColumns = [
    'name' => 'searchable|sortable',
    'businessType.name' => 'searchable|sortable',
    'applicant.name' => 'searchable|sortable',
    'location' => 'searchable|sortable',
    'status' => 'searchable|sortable',
    'created_at' => 'sortable',
  ];

  public function businessType(): BelongsTo
  {
    return $this->belongsTo(BusinessType::class);
  }

  public function applicant(): BelongsTo
  {
    return $this->belongsTo(User::class, 'applicant_id');
  }

  public function approver(): BelongsTo
  {
    return $this->belongsTo(User::class, 'approved_by');
  }

  public function parent(): BelongsTo
  {
    return $this->belongsTo(BusinessRegistration::class, 'parent_id');
  }

  public function revisions(): HasMany
  {
    return $this->hasMany(BusinessRegistration::class, 'parent_id');
  }

  public function business(): BelongsTo
  {
    return $this->belongsTo(Business::class);
  }

  public function timeline(): HasMany
  {
    return $this->hasMany(BusinessRegistrationTimeline::class);
  }

  public function canBeRevised(): bool
  {
    return in_array($this->status, ['rejected', 'pending']) && !$this->is_revised;
  }

  public function isRevision(): bool
  {
    return !is_null($this->parent_id);
  }

  public function getRootRegistration(): self
  {
    if ($this->isRevision()) {
      return $this->parent->getRootRegistration();
    }
    return $this;
  }

  public function getAllRevisions()
  {
    $root = $this->getRootRegistration();
    return BusinessRegistration::where('id', $root->id)
      ->orWhere('parent_id', $root->id)
      ->orderBy('created_at')
      ->get();
  }

  public function getTimeline()
  {
    $timeline = collect([]);
    $currentTimeline = $this->timeline()->orderBy('created_at', 'desc')->get();
    $timeline->push(...$currentTimeline);

    $parent = $this->parent;
    while ($parent) {
      $pastTimeline = $parent->timeline()->orderBy('created_at', 'desc')->get();
      $timeline->push(...$pastTimeline);
      $parent = $parent->parent;
    }

    return $timeline->sortBy('created_at');
  }
}
