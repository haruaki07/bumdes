<?php

namespace App\Enums;

enum FundingRequestTimelineAction: string
{
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case MOU_UPLOADED = 'mou_uploaded';
    case MOU_SIGNED = 'mou_signed';
    case DISBURSED = 'disbursed';
    case REPAYMENT_MADE = 'repayment_made';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Diajukan',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::MOU_UPLOADED => 'MOU Diunggah',
            self::MOU_SIGNED => 'MOU Ditandatangani',
            self::DISBURSED => 'Dicairkan',
            self::REPAYMENT_MADE => 'Cicilan Dibayar',
            self::COMPLETED => 'Selesai',
            default => '-',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Pengajuan pendanaan berhasil dibuat',
            self::APPROVED => 'Pengajuan pendanaan disetujui',
            self::REJECTED => 'Pengajuan pendanaan ditolak',
            self::MOU_UPLOADED => 'Dokumen MOU telah diunggah',
            self::MOU_SIGNED => 'MOU telah ditandatangani oleh warga',
            self::DISBURSED => 'Dana telah dicairkan',
            self::REPAYMENT_MADE => 'Cicilan pembayaran telah dilakukan',
            self::COMPLETED => 'Pendanaan telah selesai dan lunas',
            default => '-',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SUBMITTED => 'ti ti-plus',
            self::APPROVED => 'ti ti-check',
            self::REJECTED => 'ti ti-x',
            self::MOU_UPLOADED => 'ti ti-file-upload',
            self::MOU_SIGNED => 'ti ti-file-check',
            self::DISBURSED => 'ti ti-cash',
            self::REPAYMENT_MADE => 'ti ti-coin',
            self::COMPLETED => 'ti ti-circle-check',
            default => 'ti ti-circle',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SUBMITTED => 'blue',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::MOU_UPLOADED => 'cyan',
            self::MOU_SIGNED => 'indigo',
            self::DISBURSED => 'purple',
            self::REPAYMENT_MADE => 'orange',
            self::COMPLETED => 'teal',
            default => 'muted',
        };
    }

    public static function fromString(string $action): self
    {
        return match ($action) {
            'submitted' => self::SUBMITTED,
            'approved' => self::APPROVED,
            'rejected' => self::REJECTED,
            'mou_uploaded' => self::MOU_UPLOADED,
            'mou_signed' => self::MOU_SIGNED,
            'disbursed' => self::DISBURSED,
            'repayment_made' => self::REPAYMENT_MADE,
            'completed' => self::COMPLETED,
            default => self::SUBMITTED,
        };
    }
}
