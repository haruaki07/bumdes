<?php

namespace App\Http\Integrations\Xendit\DTO\Transaction;

use App\Http\Integrations\Xendit\Enums\CashFlow;
use App\Http\Integrations\Xendit\Enums\ChannelCategory;
use App\Http\Integrations\Xendit\Enums\Currency;
use App\Http\Integrations\Xendit\Enums\SettlementStatus;
use App\Http\Integrations\Xendit\Enums\TransactionStatus;
use App\Http\Integrations\Xendit\Enums\TransactionType;

class Transaction
{
    public function __construct(
        public readonly string $id,
        public readonly string $productId,
        public readonly TransactionType $type,
        public readonly string $channelCode,
        public readonly ?string $referenceId,
        public readonly ?string $accountIdentifier,
        public readonly Currency $currency,
        public readonly float $amount,
        public readonly float $netAmount,
        public readonly Currency $netAmountCurrency,
        public readonly CashFlow $cashflow,
        public readonly TransactionStatus $status,
        public readonly ChannelCategory $channelCategory,
        public readonly string $businessId,
        public readonly string $created,
        public readonly string $updated,
        public readonly Fee $fee,
        public readonly ?SettlementStatus $settlementStatus,
        public readonly ?string $estimatedSettlementTime,
        public readonly ?array $productData,
        public readonly ?string $captureId,
        public readonly ?string $paymentRequestId,
        public readonly ?string $reusablePaymentLinkId,
        public readonly ?string $paymentLinkId,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            productId: $data['product_id'],
            type: TransactionType::from($data['type']),
            channelCode: $data['channel_code'] ?? '',
            referenceId: $data['reference_id'] ?? null,
            accountIdentifier: $data['account_identifier'] ?? null,
            currency: Currency::from($data['currency']),
            amount: (float) ($data['amount'] ?? 0),
            netAmount: (float) ($data['net_amount'] ?? 0),
            netAmountCurrency: Currency::from($data['net_amount_currency'] ?? $data['currency']),
            cashflow: CashFlow::from($data['cashflow']),
            status: TransactionStatus::from($data['status']),
            channelCategory: ChannelCategory::from($data['channel_category']),
            businessId: $data['business_id'],
            created: $data['created'],
            updated: $data['updated'],
            fee: Fee::fromArray($data['fee'] ?? []),
            settlementStatus: SettlementStatus::from($data['settlement_status'] ?? null),
            estimatedSettlementTime: $data['estimated_settlement_time'] ?? null,
            productData: $data['product_data'] ?? null,
            captureId: $data['capture_id'] ?? null,
            paymentRequestId: $data['payment_request_id'] ?? null,
            reusablePaymentLinkId: $data['reusable_payment_link_id'] ?? null,
            paymentLinkId: $data['payment_link_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'type' => $this->type->value,
            'channel_code' => $this->channelCode,
            'reference_id' => $this->referenceId,
            'account_identifier' => $this->accountIdentifier,
            'currency' => $this->currency->value,
            'amount' => $this->amount,
            'net_amount' => $this->netAmount,
            'net_amount_currency' => $this->netAmountCurrency->value,
            'cashflow' => $this->cashflow->value,
            'status' => $this->status->value,
            'channel_category' => $this->channelCategory->value,
            'business_id' => $this->businessId,
            'created' => $this->created,
            'updated' => $this->updated,
            'fee' => $this->fee->toArray(),
            'settlement_status' => $this->settlementStatus?->value,
            'estimated_settlement_time' => $this->estimatedSettlementTime,
            'product_data' => $this->productData,
            'capture_id' => $this->captureId,
            'payment_request_id' => $this->paymentRequestId,
            'reusable_payment_link_id' => $this->reusablePaymentLinkId,
            'payment_link_id' => $this->paymentLinkId,
        ];
    }
}
