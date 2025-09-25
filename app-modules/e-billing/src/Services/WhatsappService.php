<?php

namespace Modules\EBilling\Services;

use CCK\LaravelWahaSaloonSdk\Waha\Waha;
use Modules\EBilling\Services\Contracts\WhatsappServiceInterface;
use Modules\EBilling\Settings\EBillingWhatsappSettings;

class WhatsappService implements WhatsappServiceInterface
{
    public function __construct(protected EBillingWhatsappSettings $settings) {}

    public function sendMessage(string $recipient, string $message, array $options = []): bool
    {
        $waha = new Waha(
            config('services.waha.base_url'),
            config('services.waha.api_key'),
        );

        $waha->misc()->chattingControllerSendSeen(
            chatId: $recipient,
            session: $this->settings->waha_session,
            messageId: null,
            messageIds: null,
            participant: null
        );

        $waha->misc()->chattingControllerStartTyping(
            chatId: $recipient,
            session: $this->settings->waha_session
        );

        $delay = ceil(random_int(5, 50) / 10);
        sleep($delay);

        $waha->misc()->chattingControllerStopTyping(
            chatId: $recipient,
            session: $this->settings->waha_session
        );

        $waha->sendText()->sendTextMessage(
            session: $this->settings->waha_session,
            chatId: $recipient,
            text: $message,
            replyTo: null,
            linkPreview: false,
            linkPreviewHighQuality: null
        );

        return true;
    }
}
