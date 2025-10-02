<?php

namespace Modules\EBilling\Settings;

use Spatie\LaravelSettings\Settings;

class EBillingWhatsappSettings extends Settings
{
    public bool $enabled;

    public string $provider; // currently only "waha" and "wablas" are supported

    public string $waha_session;

    public string $invoice_reminder_template;

    public string $wablas_server;

    public string $wablas_api_key;

    public string $wablas_secret_key;

    public static function group(): string
    {
        return 'ebil:whatsapp';
    }
}
