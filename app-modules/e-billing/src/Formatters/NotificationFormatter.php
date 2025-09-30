<?php

namespace Modules\EBilling\Formatters;

use Illuminate\Notifications\DatabaseNotification;
use Modules\EBilling\Enums\NotificationType;

class NotificationFormatter
{
    public static function format(DatabaseNotification $notification): string
    {
        $type = NotificationType::tryFrom($notification->type);

        return match ($type) {
            NotificationType::INVOICE_PAID => self::formatInvoicePaid($type, $notification->data),
            default => '',
        };
    }

    public static function formatInvoicePaid(NotificationType $type, array $data): string
    {
        return "<b>{$type->label()}</b> - Tagihan dengan nomor {$data['invoice_number']} telah dibayar.";
    }
}
