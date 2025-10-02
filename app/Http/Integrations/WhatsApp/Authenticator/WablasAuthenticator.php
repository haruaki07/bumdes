<?php

namespace App\Http\Integrations\WhatsApp\Authenticator;

use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;

class WablasAuthenticator implements Authenticator
{
    public function __construct(public readonly string $apiKey, public readonly string $secretKey) {}

    public function set(PendingRequest $pendingRequest): void
    {
        $token = "{$this->apiKey}.{$this->secretKey}";
        if ($pendingRequest->config()->get('token_only', false)) {
            $token = $this->apiKey;
        }

        if (str_contains($pendingRequest->getUrl(), 'v2')) {
            $pendingRequest->headers()->add('Authorization', 'Bearer '.$token);
        } else {
            $pendingRequest->query()->add('token', $token);
        }
    }
}
