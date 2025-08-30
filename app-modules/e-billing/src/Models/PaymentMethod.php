<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Model;
use Modules\EBilling\Enums\PaymentMethodType;

class PaymentMethod extends Model
{
    use Datatable;

    protected $table = 'ebil_payment_methods';

    protected $fillable = [
        'name',
        'code',
        'description',
        'brand_logo',
        'type',
        'account_number',
        'need_confirmation',
        'is_active',
    ];

    protected $dataTableColumns = [
        'name' => 'searchable|sortable',
        'code' => 'searchable|sortable',
        'type' => 'searchable|sortable',
        'is_active' => 'sortable',
    ];

    protected $casts = [
        'type' => PaymentMethodType::class,
        'need_confirmation' => 'boolean',
        'is_active' => 'boolean',
    ];
}
