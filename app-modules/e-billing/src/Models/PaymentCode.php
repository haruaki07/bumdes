<?php

namespace Modules\EBilling\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentCode extends Model
{
    protected $table = 'ebil_payment_codes';

    protected $fillable = [
        'code',
        'customer_id',
        'payment_method_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
