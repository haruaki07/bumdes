<?php

namespace App\Http\Integrations\WhatsApp\Requests\Wablas;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class SendSimpleText extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(protected string $phone, protected string $message) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/send-message';
    }

    public function defaultQuery(): array
    {
        return [
            'phone' => $this->phone,
            'message' => $this->message,
        ];
    }
}
