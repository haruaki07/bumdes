<?php

namespace Modules\EBilling\Notifications\Channels;

use Modules\EBilling\Notifications\InvoiceReminder;
use Modules\EBilling\Services\Contracts\WhatsappServiceInterface;
use Modules\EBilling\Settings\EBillingWhatsappSettings;

class WhatsappChannel
{
    public function __construct(
        protected WhatsappServiceInterface $whatsappService,
        protected EBillingWhatsappSettings $waSettings
    ) {}

    public function send($notifiable, InvoiceReminder $notification)
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        logger()->info('Sending WhatsApp to '.$notifiable->phone);

        if (! $this->waSettings->enabled) {
            logger()->warning('WhatsApp notifications are disabled. Skipped.');

            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        $recipient = preg_replace('/[^0-9]/', '', (string) $notifiable->phone);
        if ($recipient) {
            $this->whatsappService->sendMessage($recipient.'@c.us', $message);
        }
    }
}
