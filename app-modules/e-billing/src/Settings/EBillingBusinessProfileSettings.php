<?php

namespace Modules\EBilling\Settings;

use Spatie\LaravelSettings\Settings;

class EBillingBusinessProfileSettings extends Settings
{
    public string $name;

    public string $logo;

    public string $description;

    public string $phone;

    public string $email;

    public string $address;

    public static function group(): string
    {
        return 'ebil:business_profile';
    }
}
