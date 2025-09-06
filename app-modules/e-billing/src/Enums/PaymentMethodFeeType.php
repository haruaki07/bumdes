<?php

namespace Modules\EBilling\Enums;

enum PaymentMethodFeeType: string
{
    case NONE = 'NONE';
    case FIXED = 'FIXED';
    case PERCENT = 'PERCENT';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'Tidak Ada',
            self::FIXED => 'Nominal Tetap',
            self::PERCENT => 'Persentase',
        };
    }
}
