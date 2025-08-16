<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EBilling\Enums\CustomerStatus;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use Datatable, HasFactory, SoftDeletes;

    protected $table = 'ebil_customers';

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'address',
        'longitude',
        'latitude',
        'map_url',
        'due',
        'serial_number',
        'mac_address',
        'registration_date',
        'status',
        'site_id',
        'package_id',
        'device_id',
    ];

    protected $dataTableColumns = [
        'name' => 'searchable|sortable',
        'email' => 'searchable|sortable',
        'phone' => 'searchable|sortable',
        'package.name' => 'searchable|sortable',
        'due' => 'searchable|sortable',
        'status' => 'searchable|sortable',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
        'due' => 'integer',
        'status' => CustomerStatus::class,
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
