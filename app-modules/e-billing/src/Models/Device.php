<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    /** @use HasFactory<\Database\Factories\DeviceFactory> */
    use Datatable, HasFactory, SoftDeletes;

    protected $table = 'ebil_devices';

    protected $fillable = [
        'brand',
        'model',
        'description',
    ];

    protected $dataTableColumns = [
        'brand' => 'searchable|sortable',
        'model' => 'searchable|sortable',
        'description' => 'searchable|sortable',
    ];
}
