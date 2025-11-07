<?php

namespace App\Models;

use App\Policies\BusinessTypePolicy;
use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(BusinessTypePolicy::class)]
class BusinessType extends Model
{
    use Datatable, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $dataTableColumns = [
        'name' => 'searchable|sortable',
        'description' => 'searchable|sortable',
        'is_active' => 'searchable|sortable',
        'created_at' => 'sortable',
    ];

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
}
