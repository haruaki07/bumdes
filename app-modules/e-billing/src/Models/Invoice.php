<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
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
        'status',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'customer_detail' => AsArrayObject::class,
            'package_detail' => AsArrayObject::class,
            'status' => InvoiceStatus::class,
            'paid_at' => 'datetime',
        ];
    }

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

    /**
     * Get the public URL for the invoice.
     */
    public function getPublicUrlAttribute(): string
    {
        if (! empty($this->attributes['public_url'] ?? null)) {
            return $this->attributes['public_url'];
        }

        return route('e-billing.invoice.customer-show', $this->invoice_number);
    }
}
