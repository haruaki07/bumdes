<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    /** @use HasFactory<\Database\Factories\PackageFactory> */
    use Datatable, HasFactory, SoftDeletes;

    protected $table = 'ebil_packages';

    protected $fillable = [
        'name',
        'code',
        'description',
        'bandwidth', // in Mbps
        'price', // in IDR
        'due', // day
    ];

    protected $dataTableColumns = [
        'name' => 'searchable|sortable',
        'code' => 'searchable|sortable',
        'description' => 'searchable|sortable',
        'bandwidth' => 'searchable|sortable',
        'price' => 'searchable|sortable',
        'due' => 'searchable|sortable',
    ];

    protected $casts = [
        'price' => 'float',
    ];
}
