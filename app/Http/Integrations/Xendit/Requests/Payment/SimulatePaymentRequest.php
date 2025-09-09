<?php

namespace App\Http\Integrations\Xendit\Requests\Payment;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class SimulatePaymentRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $paymentRequestId, protected ?int $amount = null) {}

    protected function defaultHeaders(): array
    {
        return [
            'api-version' => '2024-11-11',
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/v3/payment_requests/'.urlencode($this->paymentRequestId).'/simulate';
    }

    protected function defaultBody(): array
    {
        // Amount is optional on Xendit simulate. Only send if provided.
        return $this->amount ? ['amount' => $this->amount] : [];
    }
}
