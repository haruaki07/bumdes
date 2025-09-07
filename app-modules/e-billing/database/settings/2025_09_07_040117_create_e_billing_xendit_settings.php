<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ebil:xendit.secret', '');
        $this->migrator->add('ebil:xendit.webhook_token', '');
    }

    public function down(): void
    {
        $this->migrator->delete('ebil:xendit.secret');
        $this->migrator->delete('ebil:xendit.webhook_token');
    }
};
