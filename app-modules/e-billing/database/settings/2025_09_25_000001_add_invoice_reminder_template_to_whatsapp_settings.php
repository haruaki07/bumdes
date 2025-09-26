<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add(
            'ebil:whatsapp.invoice_reminder_template',
            'Halo {customer_name}, ini adalah pengingat tagihan {invoice_number} sebesar Rp{invoice_amount} jatuh tempo pada {invoice_due_date}. Bayar atau lihat detail: {invoice_public_url}'
        );
    }

    public function down(): void
    {
        $this->migrator->delete('ebil:whatsapp.invoice_reminder_template');
    }
};
