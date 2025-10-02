<?php

namespace App\Http\Integrations\WhatsApp\Requests\Wablas;

use App\Http\Integrations\WhatsApp\DTO\DeviceInfo;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Repositories\ArrayStore;

class DeviceInfoRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/device/info';
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $data = $response->json();

        return DeviceInfo::fromArray($data['data'] ?? []);
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        $status = $response->json('status');

        return $status === false;
    }

    public function config(): ArrayStore
    {
        return new ArrayStore([
            'token_only' => true,
        ]);
    }
}
