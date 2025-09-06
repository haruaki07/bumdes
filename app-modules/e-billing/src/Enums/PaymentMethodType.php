<?php

namespace Modules\EBilling\Enums;

enum PaymentMethodType: string
{
    case BANK_TRANSFER = 'BANK_TRANSFER';
    case RETAIL = 'RETAIL';
    case QR = 'QR';
    case VIRTUAL_ACCOUNT = 'VIRTUAL_ACCOUNT';

    public function color(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'info',
            self::RETAIL => 'success',
            self::QR => 'warning',
            self::VIRTUAL_ACCOUNT => 'danger',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Transfer Bank',
            self::RETAIL => 'Retail',
            self::QR => 'QR',
            self::VIRTUAL_ACCOUNT => 'Virtual Account',
        };
    }
}
