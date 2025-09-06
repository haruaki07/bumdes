<?php

namespace App\Http\Integrations\Xendit;

use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class XenditConnector extends Connector
{
    use AcceptsJson;

    public function __construct(
        protected ?string $secret = null
    ) {
        if (! $this->secret) {
            $this->secret = config('services.xendit.secret');
        }

        if (! $this->secret) {
            throw new \Exception('Xendit secret is not configured.');
        }
    }

    protected function defaultAuth(): BasicAuthenticator
    {
        return new BasicAuthenticator($this->secret, '');
    }

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return (string) config('services.xendit.url');
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
