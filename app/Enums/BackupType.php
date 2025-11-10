<?php

namespace App\Enums;

enum BackupType: string
{
    case DATABASE = 'database';
    case FILES = 'files';
    case FULL = 'full';

    public function label(): string
    {
        return match ($this) {
            self::DATABASE => 'Database',
            self::FILES => 'Files & Media',
            self::FULL => 'Full (Database + Files)',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DATABASE => 'database',
            self::FILES => 'folder',
            self::FULL => 'archive',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DATABASE => 'primary',
            self::FILES => 'warning',
            self::FULL => 'red',
        };
    }
}
