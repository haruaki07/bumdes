<?php

namespace Modules\EBilling\Services\Contracts;

interface WhatsappServiceInterface
{
    public function sendMessage(string $to, string $message, array $options = []): bool;
}
