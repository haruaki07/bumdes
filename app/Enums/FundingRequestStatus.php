<?php

namespace App\Enums;

enum FundingRequestStatus: string
{
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case MOU_SIGNED = 'mou_signed';
    case READY_TO_DISBURSE = 'ready_to_disburse';
    case DISBURSED = 'disbursed';
    case REPAYING = 'repaying';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Diajukan',
            self::APPROVED => 'Disetujui',
            self::MOU_SIGNED => 'MOU Ditandatangani',
            self::READY_TO_DISBURSE => 'Siap Dicairkan',
            self::DISBURSED => 'Dicairkan',
            self::REPAYING => 'Dalam Cicilan',
            self::COMPLETED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SUBMITTED => 'info',
            self::APPROVED => 'primary',
            self::MOU_SIGNED => 'azure',
            self::READY_TO_DISBURSE => 'purple',
            self::DISBURSED => 'indigo',
            self::REPAYING => 'warning',
            self::COMPLETED => 'success',
            self::REJECTED => 'danger',
        };
    }

    public static function fromString(string $status): self
    {
        return match ($status) {
            'submitted' => self::SUBMITTED,
            'approved' => self::APPROVED,
            'mou_signed' => self::MOU_SIGNED,
            'ready_to_disburse' => self::READY_TO_DISBURSE,
            'disbursed' => self::DISBURSED,
            'repaying' => self::REPAYING,
            'completed' => self::COMPLETED,
            'rejected' => self::REJECTED,
        };
    }
}
