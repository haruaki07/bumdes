<?php

namespace Modules\EBilling\Settings;

use Spatie\LaravelSettings\Settings;

class EBillingXenditSettings extends Settings
{
    public string $secret;

    public string $webhook_token;

    public static function group(): string
    {
        return 'ebil:xendit';
    }
}
