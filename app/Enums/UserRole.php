<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case OPERATOR = 'operator';
    case WARGA = 'warga';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::OPERATOR => 'Operator',
            self::WARGA => 'Warga',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'success',
            self::OPERATOR => 'info',
            self::WARGA => 'warning',
        };
    }

    public static function fromString(string $status): self
    {
        return match ($status) {
            'admin' => self::ADMIN,
            'operator' => self::OPERATOR,
            'warga' => self::WARGA,
        };
    }
}
