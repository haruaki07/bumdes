<?php

namespace Modules\EBilling\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\EBilling\Services\Contracts\PaymentServiceInterface;
use Modules\EBilling\Services\Contracts\WhatsappServiceInterface;
use Modules\EBilling\Services\PaymentService;
use Modules\EBilling\Services\WhatsappService;

class EBillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(WhatsappServiceInterface::class, WhatsappService::class);
    }
}
