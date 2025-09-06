<?php

namespace App\Http\Integrations\Xendit\DTO\Transaction;

class TransactionList
{
    /**
     * @param  Transaction[]  $data
     */
    public function __construct(
        public readonly array $data = [],
        public readonly bool $hasMore = false,
        public readonly array $links = []
    ) {}

    public static function fromArray(array $array): static
    {
        $transactions = [];
        foreach ($array['data'] ?? [] as $item) {
            $transactions[] = Transaction::fromArray($item);
        }

        return new static(
            data: $transactions,
            hasMore: (bool) ($array['has_more'] ?? false),
            links: $array['links'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'has_more' => $this->hasMore,
            'data' => array_map(fn ($tx) => $tx->toArray(), $this->data),
            'links' => $this->links,
        ];
    }
}
