<?php

namespace Modules\EBilling\Services\DTOs\Payment;

class PaymentRequest
{
    public function __construct(
        public string $paymentRequestId,
        public string $status,
        public ?array $action,
        public mixed $responseObject,
        public ?string $expiresAt = null,
    ) {}
}
