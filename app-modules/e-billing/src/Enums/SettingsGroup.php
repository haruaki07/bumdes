<?php

namespace Modules\EBilling\Enums;

enum SettingsGroup: string
{
    case ACCOUNT = 'account';
    case BUSINESS_PROFILE = 'business-profile';
    case PAYMENT_GATEWAY = 'payment-gateway';
    case WHATSAPP = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::ACCOUNT => 'Akun',
            self::BUSINESS_PROFILE => 'Profil Usaha',
            self::PAYMENT_GATEWAY => 'Payment Gateway',
            self::WHATSAPP => 'WhatsApp',
        };
    }
}
