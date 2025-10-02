<?php

namespace App\Http\Integrations\WhatsApp\DTO;

class DeviceInfo
{
    public function __construct(
        public string $name,
        public string $serial,
        public string $sender,
        public string $quota,
        public string $expired_date,
        public bool $active,
        public string $status,

    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            serial: $data['serial'] ?? '',
            sender: $data['sender'] ?? '',
            quota: $data['quota'] ?? '',
            expired_date: $data['expired_date'] ?? '',
            active: isset($data['active']) ? filter_var($data['active'], FILTER_VALIDATE_BOOLEAN) : false,
            status: $data['status'] ?? '',
        );
    }
}
