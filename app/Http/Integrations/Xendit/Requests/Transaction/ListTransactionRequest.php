<?php

namespace App\Http\Integrations\Xendit\Requests\Transaction;

use App\Http\Integrations\Xendit\DTO\Transaction\TransactionList;
use App\Http\Integrations\Xendit\DTO\Transaction\TransactionListParams;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class ListTransactionRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected TransactionListParams $params
    ) {}

    public function resolveEndpoint(): string
    {
        return '/transactions';
    }

    protected function defaultQuery(): array
    {
        return $this->params->toArray();
    }

    public function createDtoFromResponse(Response $response): TransactionList
    {
        return TransactionList::fromArray($response->json());
    }
}
