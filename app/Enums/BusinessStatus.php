<?php

namespace App\Enums;

enum BusinessStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Tidak Aktif',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        };
    }

    public static function fromString(string $status): self
    {
        return match ($status) {
            'active' => self::ACTIVE,
            'inactive' => self::INACTIVE,
        };
    }
}
