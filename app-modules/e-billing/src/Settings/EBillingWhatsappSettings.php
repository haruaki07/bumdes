<?php

namespace Modules\EBilling\Settings;

use Spatie\LaravelSettings\Settings;

class EBillingWhatsappSettings extends Settings
{
    public bool $enabled;

    public string $provider; // currently only "waha" is supported

    public string $waha_session;

    /**
     * Template pesan pengingat invoice WhatsApp.
     * Gunakan placeholder berikut (akan diganti otomatis):
     * {customer_name}, {invoice_number}, {invoice_amount}, {invoice_due_date}, {invoice_grace_period_end_date}, {invoice_status}, {invoice_public_url}, {package_name}, {business_name}
     */
    public string $invoice_reminder_template;

    public static function group(): string
    {
        return 'ebil:whatsapp';
    }
}
