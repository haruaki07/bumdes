<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    /** @use HasFactory<\Database\Factories\DeviceFactory> */
    use Datatable, HasFactory;

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
