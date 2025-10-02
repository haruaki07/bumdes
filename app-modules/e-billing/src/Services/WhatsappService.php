<?php

namespace Modules\EBilling\Services;

use App\Http\Integrations\WhatsApp\Requests\Wablas\SendSimpleText;
use App\Http\Integrations\WhatsApp\WablasConnector;
use CCK\LaravelWahaSaloonSdk\Waha\Waha;
use Modules\EBilling\Services\Contracts\WhatsappServiceInterface;
use Modules\EBilling\Settings\EBillingWhatsappSettings;

class WhatsappService implements WhatsappServiceInterface
{
    public function __construct(protected EBillingWhatsappSettings $settings) {}

    public function sendMessage(string $recipient, string $message, array $options = []): bool
    {
        if ($this->settings->provider === 'waha') {
            return $this->sendViaWaha($recipient, $message);
        } elseif ($this->settings->provider === 'wablas') {
            return $this->sendViaWablas($recipient, $message);
        }

        throw new \Exception('No valid WhatsApp provider configured.');
    }

    protected function sendViaWaha(string $recipient, string $message): bool
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
            chatId: $recipient.'@c.us',
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

    protected function sendViaWablas(string $recipient, string $message): bool
    {
        $wablas = new WablasConnector(
            server: $this->settings->wablas_server,
            apiKey: $this->settings->wablas_api_key,
            secretKey: $this->settings->wablas_secret_key
        );

        $req = new SendSimpleText($recipient, $message);
        $response = $wablas->send($req);

        return $response->json('status');
    }
}
