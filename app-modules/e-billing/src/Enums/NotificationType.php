<?php

namespace Modules\EBilling\Enums;

enum NotificationType: string
{
    case INVOICE_PAID = 'invoice-paid';

    public function label(): string
    {
        return match ($this) {
            self::INVOICE_PAID => 'Tagihan Lunas',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::INVOICE_PAID => 'green',
        };
    }
}
