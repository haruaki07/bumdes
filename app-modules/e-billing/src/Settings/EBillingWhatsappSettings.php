<?php

namespace Modules\EBilling\Settings;

use Spatie\LaravelSettings\Settings;

class EBillingWhatsappSettings extends Settings
{
    public bool $enabled;

    public string $provider; // currently only "waha" is supported

    public string $waha_session;

    public static function group(): string
    {
        return 'ebil:whatsapp';
    }
}
