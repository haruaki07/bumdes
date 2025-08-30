<?php

namespace Modules\EBilling\Services\DTOs\Payment;

use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Package;
use Modules\EBilling\Models\PaymentMethod;

class CreatePaymentRequestIn
{
    public function __construct(
        public string $referenceId,
        public int $amount,
        public PaymentMethod $paymentMethod,
        public Customer $customer,
        public Package $package,
        public ?bool $reusable = null,
        public ?string $reusableCode = null,
        public ?array $metadata = null
    ) {}
}
