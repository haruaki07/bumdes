<?php

namespace App\Http\Integrations\Xendit\DTO\Transaction;

use App\Http\Integrations\Xendit\Enums\FeeStatus;

class Fee
{
    public function __construct(
        public readonly float $xenditFee,
        public readonly float $valueAddedTax,
        public readonly float $xenditWithholdingTax,
        public readonly float $thirdPartyWithholdingTax,
        public readonly FeeStatus $status,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            xenditFee: (float) ($data['xendit_fee'] ?? 0),
            valueAddedTax: (float) ($data['value_added_tax'] ?? 0),
            xenditWithholdingTax: (float) ($data['xendit_withholding_tax'] ?? 0),
            thirdPartyWithholdingTax: (float) ($data['third_party_withholding_tax'] ?? 0),
            status: FeeStatus::from($data['status'] ?? 'NOT_APPLICABLE'),
        );
    }

    public function toArray(): array
    {
        return [
            'xendit_fee' => $this->xenditFee,
            'value_added_tax' => $this->valueAddedTax,
            'xendit_withholding_tax' => $this->xenditWithholdingTax,
            'third_party_withholding_tax' => $this->thirdPartyWithholdingTax,
            'status' => $this->status->value,
        ];
    }
}
