<?php

namespace Modules\EBilling\Services\Contracts;

use Modules\EBilling\Services\DTOs\Payment\CreatePaymentRequestIn;
use Modules\EBilling\Services\DTOs\Payment\PaymentRequest;

interface PaymentServiceInterface
{
    /**
     * Create a new payment session
     */
    public function createPaymentRequest(CreatePaymentRequestIn $input): PaymentRequest;

    /**
     * Get the payment status
     */
    public function getPaymentStatus(string $paymentId): PaymentRequest;
}
