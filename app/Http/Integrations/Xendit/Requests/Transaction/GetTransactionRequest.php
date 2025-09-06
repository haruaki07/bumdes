<?php

namespace App\Http\Integrations\Xendit\Requests\Transaction;

use App\Http\Integrations\Xendit\DTO\Transaction\Transaction;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetTransactionRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(public string $transactionId) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/transactions/'.$this->transactionId;
    }

    public function createDtoFromResponse(Response $response): Transaction
    {
        return Transaction::fromArray($response->json());
    }
}
