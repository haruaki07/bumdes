<?php

namespace Modules\EBilling\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case OPERATOR = 'operator';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::OPERATOR => 'Operator',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'success',
            self::OPERATOR => 'info',
        };
    }

    public static function fromString(string $status): self
    {
        return match ($status) {
            'admin' => self::ADMIN,
            'operator' => self::OPERATOR,
        };
    }
}
