<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ebil:whatsapp.wablas_server', '');
        $this->migrator->add('ebil:whatsapp.wablas_api_key', '');
        $this->migrator->add('ebil:whatsapp.wablas_secret_key', '');
    }

    public function down(): void
    {
        $this->migrator->delete('ebil:whatsapp.wablas_server');
        $this->migrator->delete('ebil:whatsapp.wablas_api_key');
        $this->migrator->delete('ebil:whatsapp.wablas_secret_key');
    }
};
