<?php

namespace Modules\EBilling\Services\Contracts;

use Modules\EBilling\Services\DTOs\Payment\CreatePaymentRequestIn;
use Modules\EBilling\Services\DTOs\Payment\CreatePaymentRequestOut;

interface PaymentServiceInterface
{
    /**
     * Create a new payment session
     */
    public function createPaymentRequest(CreatePaymentRequestIn $input): CreatePaymentRequestOut;
}
