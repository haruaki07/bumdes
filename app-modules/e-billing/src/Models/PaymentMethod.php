<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Model;
use Modules\EBilling\Enums\PaymentMethodFeeType;
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
        'fee_type',
        'fee_amount',
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
        'fee_type' => PaymentMethodFeeType::class,
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate fee for a base amount.
     */
    public function calculateFee(int|float $baseAmount): int
    {
        if (($this->fee_type ?? 'NONE') === PaymentMethodFeeType::PERCENT) {
            return (int) round(($baseAmount * (float) $this->fee_amount) / 100);
        }
        if (($this->fee_type ?? 'NONE') === PaymentMethodFeeType::FIXED) {
            return (int) round((float) $this->fee_amount);
        }

        return 0;
    }
}
