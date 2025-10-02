<?php

namespace App\Http\Integrations\WhatsApp;

use App\Http\Integrations\WhatsApp\Authenticator\WablasAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class WablasConnector extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors;

    public function __construct(public readonly string $apiKey, public readonly string $secretKey, public readonly string $server) {}

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return "https://{$this->server}.wablas.com/api";
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }

    /**
     * Define the default authentication for the connector.
     */
    protected function defaultAuth(): WablasAuthenticator
    {
        return new WablasAuthenticator($this->apiKey, $this->secretKey);
    }
}
