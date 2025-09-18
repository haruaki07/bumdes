<?php

namespace Modules\EBilling\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Dibuka',
            self::IN_PROGRESS => 'Diproses',
            self::RESOLVED => 'Selesai',
            self::CLOSED => 'Ditutup',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'warning',
            self::IN_PROGRESS => 'primary',
            self::RESOLVED => 'success',
            self::CLOSED => 'secondary',
        };
    }
}
