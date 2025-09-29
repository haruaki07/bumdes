<?php

namespace Modules\EBilling\Formatters;

use Illuminate\Notifications\DatabaseNotification;
use Modules\EBilling\Enums\NotificationType;

class NotificationFormatter
{
    public static function format(DatabaseNotification $notification): string
    {
        return match (NotificationType::tryFrom($notification->type)) {
            NotificationType::INVOICE_PAID => self::formatInvoicePaid($notification),
            default => '',
        };
    }

    protected static function formatInvoicePaid(DatabaseNotification $notification): string
    {
        $type = NotificationType::from($notification->type);

        return "<b>{$type->label()}</b> - Tagihan dengan nomor {$notification->data['invoice_number']} telah dibayar.";
    }
}
