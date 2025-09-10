<?php

namespace Modules\EBilling\Services;

use App\Http\Integrations\Xendit\Requests\Payment\CreatePaymentRequest;
use App\Http\Integrations\Xendit\Requests\Payment\GetPaymentRequest;
use App\Http\Integrations\Xendit\Requests\Payment\SimulatePaymentRequest;
use App\Http\Integrations\Xendit\XenditConnector;
use Modules\EBilling\Enums\PaymentMethodType;
use Modules\EBilling\Services\Contracts\PaymentServiceInterface;
use Modules\EBilling\Services\DTOs\Payment\CreatePaymentRequestIn;
use Modules\EBilling\Services\DTOs\Payment\PaymentRequest;

class PaymentService implements PaymentServiceInterface
{
    public function createPaymentRequest(CreatePaymentRequestIn $input): PaymentRequest
    {
        $xendit = new XenditConnector;
        $request = new CreatePaymentRequest;

        // Calculate fee (if any) based on payment method configuration
        $fee = method_exists($input->paymentMethod, 'calculateFee')
            ? $input->paymentMethod->calculateFee($input->amount)
            : 0;
        $totalAmount = $input->amount + $fee;

        if ($input->paymentMethod->type === PaymentMethodType::RETAIL) {
            $channelProperties = ['payer_name' => $input->customer->name];
            if ($input->reusable && $input->reusableCode) {
                $channelProperties['payment_code'] = $input->reusableCode;
            }
        }

        if ($input->paymentMethod->type === PaymentMethodType::VIRTUAL_ACCOUNT) {
            $channelProperties = ['display_name' => $input->customer->name];
            if ($input->reusable && $input->reusableCode) {
                $channelProperties['virtual_account_number'] = $input->reusableCode;
            }
        }

        $items = [
            [
                'type' => 'DIGITAL_SERVICE',
                'reference_id' => "$input->package->id",
                'name' => $input->package->name,
                'currency' => 'IDR',
                'net_unit_amount' => (int) $input->package->price,
                'quantity' => 1,
                'category' => 'INTERNET_PACKAGE',
            ],
        ];

        if ($fee > 0) {
            $items[] = [
                'type' => 'FEE',
                'reference_id' => $input->referenceId,
                'name' => 'FEE',
                'currency' => 'IDR',
                'net_unit_amount' => (int) $fee,
                'quantity' => 1,
                'category' => 'FEE',
            ];
        }

        $request->body()->set([
            'reference_id' => $input->referenceId,
            'type' => $input->reusable ? 'REUSABLE_PAYMENT_CODE' : 'PAY',
            'country' => 'ID',
            'currency' => 'IDR',
            'request_amount' => $totalAmount,
            'channel_code' => $input->paymentMethod->code,
            'channel_properties' => [
                'expires_at' => now()->addHour()->toISOString(),
                ...$channelProperties ?? [],
            ],
            'items' => $items,
            'metadata' => [
                'payment_method_code' => $input->paymentMethod->code,
                'fee' => $fee,
            ],
        ]);

        // reusable qr payments is disabled
        if ($input->reusable && $input->paymentMethod->type === PaymentMethodType::QR) {
            $request->body()->add('type', 'PAY');
        }

        $response = $xendit->send($request);

        if ($response->failed()) {
            $response->throw();
        }

        $data = $response->json();

        return new PaymentRequest(
            paymentRequestId: $data['payment_request_id'],
            status: $data['status'],
            action: isset($data['actions']) && count($data['actions']) > 0 ? $data['actions'][0] : null,
            responseObject: $data,
            expiresAt: $data['channel_properties']['expires_at'] ?? null,
        );
    }

    public function getPaymentStatus(string $paymentId): PaymentRequest
    {
        $xendit = new XenditConnector;
        $request = new GetPaymentRequest($paymentId);

        $response = $xendit->send($request);

        if ($response->failed()) {
            $response->throw();
        }

        $data = $response->json();

        return new PaymentRequest(
            paymentRequestId: $data['payment_request_id'],
            status: $data['status'],
            action: isset($data['actions']) && count($data['actions']) > 0 ? $data['actions'][0] : null,
            responseObject: $data,
            expiresAt: $data['channel_properties']['expires_at'] ?? null,
        );
    }

    public function simulatePayment(string $paymentId, ?int $amount = null): PaymentRequest
    {
        $xendit = new XenditConnector;
        $request = new SimulatePaymentRequest($paymentId, $amount);

        $response = $xendit->send($request);
        if ($response->failed()) {
            $response->throw();
        }

        $data = $response->json();

        return new PaymentRequest(
            paymentRequestId: $data['payment_request_id'] ?? $paymentId,
            status: $data['status'] ?? 'UNKNOWN',
            action: isset($data['actions']) && count($data['actions']) > 0 ? $data['actions'][0] : null,
            responseObject: $data,
            expiresAt: $data['channel_properties']['expires_at'] ?? null,
        );
    }
}
