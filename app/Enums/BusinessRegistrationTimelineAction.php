<?php

namespace App\Enums;

enum BusinessRegistrationTimelineAction: string
{
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case REVISED = 'revised';

    public function label(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Dibuat',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::REVISED => 'Direvisi',
            default => '-',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Pengajuan usaha berhasil dibuat',
            self::APPROVED => 'Pengajuan usaha disetujui dan usaha berhasil dibuat',
            self::REJECTED => 'Pengajuan usaha ditolak',
            self::REVISED => 'Pengajuan usaha direvisi',
            default => '-',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SUBMITTED => 'ti ti-plus',
            self::APPROVED => 'ti ti-check',
            self::REJECTED => 'ti ti-x',
            self::REVISED => 'ti ti-refresh',
            default => 'ti ti-circle',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SUBMITTED => 'blue',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::REVISED => 'cyan',
            default => 'muted',
        };
    }

    public static function fromString(string $status): self
    {
        return match ($status) {
            'submitted' => self::SUBMITTED,
            'approved' => self::APPROVED,
            'rejected' => self::REJECTED,
            'revised' => self::REVISED,
            default => self::SUBMITTED,
        };
    }
}
