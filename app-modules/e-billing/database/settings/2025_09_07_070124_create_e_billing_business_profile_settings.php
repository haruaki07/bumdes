<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ebil:business_profile.name', 'E-Billing');
        $this->migrator->add('ebil:business_profile.logo', 'assets/e-billing/images/logo.png');
        $this->migrator->add('ebil:business_profile.description', 'Internet Cepat Murah');
        $this->migrator->add('ebil:business_profile.phone', '08123456789');
        $this->migrator->add('ebil:business_profile.email', 'info@e-billing.test');
        $this->migrator->add('ebil:business_profile.address', 'Jl. Contoh No. 123');
    }

    public function down(): void
    {
        $this->migrator->delete('ebil:business_profile.name');
        $this->migrator->delete('ebil:business_profile.logo');
        $this->migrator->delete('ebil:business_profile.description');
        $this->migrator->delete('ebil:business_profile.phone');
        $this->migrator->delete('ebil:business_profile.email');
        $this->migrator->delete('ebil:business_profile.address');
    }
};
