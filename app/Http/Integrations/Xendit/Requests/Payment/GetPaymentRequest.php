<?php

namespace App\Http\Integrations\Xendit\Requests\Payment;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetPaymentRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $paymentRequestId
    ) {}

    protected function defaultHeaders(): array
    {
        return [
            'api-version' => '2024-11-11',
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/v3/payment_requests/'.urlencode($this->paymentRequestId);
    }
}
