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
        'total_price',
        'payment_session_url',
        'status',
    ];

    protected $casts = [
        'customer_detail' => 'json',
        'package_detail' => 'json',
        'status' => InvoiceStatus::class,
    ];
}
