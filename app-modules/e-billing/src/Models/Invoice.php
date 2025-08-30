<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EBilling\Enums\InvoiceStatus;

class Invoice extends Model
{
    use Datatable, SoftDeletes;

    protected $table = 'ebil_invoices';

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'customer_detail',
        'package_id',
        'package_detail',
        'amount',
        'payment_session_url',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'customer_detail' => 'json',
        'package_detail' => 'json',
        'status' => InvoiceStatus::class,
        'paid_at' => 'datetime',
    ];

    protected $dataTableColumns = [
        'invoice_number' => 'searchable|sortable',
        'customer.name' => 'searchable|sortable',
        'package.name' => 'searchable|sortable',
        'amount' => 'sortable',
        'status' => 'searchable|sortable',
        'paid_at' => 'sortable',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
