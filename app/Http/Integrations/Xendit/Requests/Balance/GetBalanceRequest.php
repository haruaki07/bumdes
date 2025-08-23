<?php

namespace App\Http\Integrations\Xendit\Requests\Balance;

use App\Http\Integrations\Xendit\DTO\Balance\Balance;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetBalanceRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        public string $currency = 'IDR',
    ) {}

    public function resolveEndpoint(): string
    {
        return '/balance';
    }

    protected function defaultQuery(): array
    {
        return [
            'currency' => $this->currency,
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return Balance::fromArray($response->json());
    }
}
