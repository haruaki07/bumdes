<?php

namespace Modules\EBilling\Services;

use App\Http\Integrations\Xendit\Requests\Payment\CreatePaymentRequest;
use App\Http\Integrations\Xendit\Requests\Payment\GetPaymentRequest;
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

        $request->body()->set([
            'reference_id' => $input->referenceId,
            'type' => $input->reusable ? 'REUSABLE_PAYMENT_CODE' : 'PAY',
            'country' => 'ID',
            'currency' => 'IDR',
            'request_amount' => $input->amount,
            'channel_code' => $input->paymentMethod->code,
            'channel_properties' => [
                'expires_at' => now()->addHour()->toISOString(),
                ...$channelProperties ?? [],
            ],
            'items' => [
                [
                    'type' => 'DIGITAL_SERVICE',
                    'reference_id' => "$input->package->id",
                    'name' => $input->package->name,
                    'currency' => 'IDR',
                    'net_unit_amount' => (int) $input->package->price,
                    'quantity' => 1,
                    'category' => 'INTERNET_PACKAGE',
                ],
            ],
        ]);

        // reusable qr payments doesn't support closed payment, so we had to remove request_amount
        if ($input->reusable && $input->paymentMethod->type === PaymentMethodType::QR) {
            $request->body()->remove('request_amount');
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
}
