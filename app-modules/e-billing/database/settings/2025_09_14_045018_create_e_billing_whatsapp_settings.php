<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ebil:whatsapp.enabled', false);
        $this->migrator->add('ebil:whatsapp.provider', 'waha');
        $this->migrator->add('ebil:whatsapp.waha_session', 'default');
    }

    public function down(): void
    {
        $this->migrator->delete('ebil:whatsapp.enabled');
        $this->migrator->delete('ebil:whatsapp.provider');
        $this->migrator->delete('ebil:whatsapp.waha_session');
    }
};
