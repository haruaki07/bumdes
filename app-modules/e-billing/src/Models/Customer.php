<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Modules\EBilling\Enums\CustomerStatus;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use Datatable, HasFactory, Notifiable, SoftDeletes;

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
        'bill_cycle',
        'due_reminder_days',
        'next_billing_date',
        'invoice_number',
        'payment_method_code',
        'grace_period',
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
        'next_billing_date' => 'datetime',
        'status' => CustomerStatus::class,
    ];

    protected static function booted()
    {
        static::creating(function ($customer) {
            $customer->next_billing_date = Customer::getNextBillingDate($customer);
        });
    }

    public static function getNextBillingDate(Customer $customer, ?Carbon $relative = null)
    {
        $relative = $relative ?? now();

        return $relative->addMonth()->setDay((int) $customer->due)->subDays((int) $customer->due_reminder_days ?? 5);
    }

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

    public function getDueDateAttribute(): \Carbon\Carbon
    {
        return now()->copy()->day($this->due);
    }

    public function getGracePeriodEndDateAttribute(): \Carbon\Carbon
    {
        return $this->getDueDateAttribute()->copy()->addDays($this->grace_period);
    }

    public function getPeriodStartDateAttribute(): \Carbon\Carbon
    {
        return $this->getDueDateAttribute()->copy()->subMonthNoOverflow()->day($this->due + 1);
    }

    public function getPeriodEndDateAttribute(): \Carbon\Carbon
    {
        return $this->getDueDateAttribute();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
