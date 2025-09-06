<?php

namespace App\Http\Integrations\Xendit\DTO\Transaction;

class TransactionListParams
{
    public function __construct(
        public readonly array $types = [],
        public readonly array $statuses = [],
        public readonly array $channelCategories = [],
        public readonly ?string $referenceId = null,
        public readonly ?string $productId = null,
        public readonly ?string $accountIdentifier = null,
        public readonly ?string $currency = null,
        public readonly ?float $amount = null,
        public readonly ?string $createdGte = null,
        public readonly ?string $createdLte = null,
        public readonly ?string $updatedGte = null,
        public readonly ?string $updatedLte = null,
        public readonly ?int $limit = 10,
        public readonly ?string $afterId = null,
        public readonly ?string $beforeId = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'types' => $this->types,
            'statuses' => $this->statuses,
            'channel_categories' => $this->channelCategories,
            'reference_id' => $this->referenceId,
            'product_id' => $this->productId,
            'account_identifier' => $this->accountIdentifier,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'created[gte]' => $this->createdGte,
            'created[lte]' => $this->createdLte,
            'updated[gte]' => $this->updatedGte,
            'updated[lte]' => $this->updatedLte,
            'limit' => $this->limit,
            'after_id' => $this->afterId,
            'before_id' => $this->beforeId,
        ], fn ($value) => $value !== null && $value !== []);
    }
}
