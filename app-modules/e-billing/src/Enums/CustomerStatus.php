<?php

namespace Modules\EBilling\Enums;

enum CustomerStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ISOLATE = 'isolate';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Tidak Aktif',
            self::ISOLATE => 'Isolir',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            self::ISOLATE => 'warning',
        };
    }

    public static function fromString(string $status): self
    {
        return match ($status) {
            'active' => self::ACTIVE,
            'inactive' => self::INACTIVE,
            'isolate' => self::ISOLATE,
        };
    }
}
