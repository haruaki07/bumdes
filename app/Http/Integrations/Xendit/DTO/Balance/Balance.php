<?php

namespace App\Http\Integrations\Xendit\DTO\Balance;

class Balance
{
    public function __construct(
        public readonly int $balance
    ) {}

    public static function fromArray(array $array): static
    {
        return new static(
            balance: $array['balance'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'balance' => $this->balance,
        ];
    }
}
