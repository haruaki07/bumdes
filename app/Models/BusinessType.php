<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessType extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'name',
    'description',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function businesses(): HasMany
  {
    return $this->hasMany(Business::class);
  }
}
