<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EBilling\Enums\InvoiceStatus;
use Modules\EBilling\Policies\InvoicePolicy;

#[UsePolicy(InvoicePolicy::class)]
class Invoice extends Model
{
    use Datatable, SoftDeletes;

    protected $table = 'ebil_invoices';

    protected $fillable = [
        'invoice_number',
        'due_date',
        'grace_period_end_date',
        'period_start_date',
        'period_end_date',
        'customer_id',
        'customer_detail',
        'package_id',
        'package_detail',
        'amount',
        'status',
        'paid_at',
        'payment_method_code',
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
            'due_date' => 'datetime',
            'grace_period_end_date' => 'datetime',
            'period_start_date' => 'datetime',
            'period_end_date' => 'datetime',
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

    public static function generateInvoiceNumber($currentDate, $count = -1)
    {
        return 'INV'.$currentDate->format('Ym').str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_code', 'code');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', InvoiceStatus::UNPAID);
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
