<?php

namespace Modules\EBilling\Http\Controllers;

use App\Http\Integrations\Xendit\Requests\Payment\GetPaymentRequest;
use App\Http\Integrations\Xendit\XenditConnector;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\EBilling\Enums\InvoiceStatus;
use Modules\EBilling\Enums\PaymentMethodType;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Invoice;
use Modules\EBilling\Models\PaymentCode;
use Modules\EBilling\Models\PaymentMethod;
use Modules\EBilling\Services\Contracts\PaymentServiceInterface;
use Modules\EBilling\Services\DTOs\Payment\CreatePaymentRequestIn;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with(['customer', 'package'])->orderBy('created_at', 'desc')->datatable();

        return view('e-billing::invoices.index', compact('invoices'));
    }

    public function show(Request $request, Invoice $invoice)
    {
        $invoice->loadMissing(['customer', 'package']);

        return view('e-billing::invoices.show', compact('invoice'));
    }

    public function customerShow(Request $request, $customerId)
    {
        $customer = Customer::where('customer_id', $customerId)->first();
        if (! $customer) {
            abort(404);
        }

        if (! $customer->invoice_number) {
            abort(404, 'Anda belum memiliki tagihan.');
        }

        $invoice = Invoice::where('invoice_number', $customer->invoice_number)
            ->where('status', InvoiceStatus::UNPAID)
            ->first();
        if (! $invoice) {
            abort(404, 'Tagihan tidak ditemukan.');
        }

        $paymentMethods = PaymentMethod::all();

        return view('e-billing::invoices.customer.show', compact('customer', 'invoice', 'paymentMethods'));
    }

    public function requestPayment(Request $request, $customerId, PaymentServiceInterface $paymentService)
    {
        $request->validate([
            'payment_method' => 'required|exists:ebil_payment_methods,id',
        ]);

        $customer = Customer::where('customer_id', $customerId)->first();
        if (! $customer) {
            abort(404);
        }

        $invoice = Invoice::where('invoice_number', $customer->invoice_number)->first();
        if (! $invoice) {
            abort(404);
        }

        $paymentMethod = PaymentMethod::find($request->input('payment_method'));
        if (! $paymentMethod) {
            abort(404);
        }

        $reusable = $request->boolean('save');
        $reusableCode = null;
        $paymentCode = PaymentCode::where(['customer_id' => $customer->id, 'payment_method_id' => $paymentMethod->id])->first();
        if ($reusable && $paymentCode) {
            $reusableCode = $paymentCode->code;
        } elseif (! $reusable && $paymentCode) {
            $paymentCode->delete();
        }

        try {
            $result = $paymentService->createPaymentRequest(
                new CreatePaymentRequestIn(
                    referenceId: $invoice->invoice_number,
                    amount: $invoice->amount,
                    paymentMethod: $paymentMethod,
                    customer: $customer,
                    package: $customer->package,
                    reusable: $reusable,
                    reusableCode: $reusableCode,
                )
            );
        } catch (\Saloon\Exceptions\SaloonException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        if (in_array($result->status, ['REQUIRES_ACTION', 'ACCEPTING_PAYMENTS'])) {
            $expiresAt = $result->expiresAt ?? now()->addHour()->toISOString();
            $payload = [
                'id' => $result->paymentRequestId,
                'status' => $result->status,
                'action' => $result->action,
                'expires_at' => $expiresAt,
                'reference_id' => $invoice->invoice_number,
                'amount' => $invoice->amount,
                'payment_method' => [
                    'id' => $paymentMethod->id,
                    'type' => method_exists($paymentMethod->type, 'value') ? $paymentMethod->type->value : $paymentMethod->type,
                    'name' => $paymentMethod->name ?? $paymentMethod->code,
                    'code' => $paymentMethod->code,
                ],
                'customer' => [
                    'id' => $customer->id,
                    'customer_id' => $customer->customer_id,
                    'name' => $customer->name,
                ],
            ];

            // Save reusable payment code if applicable
            if ($reusable && ($paymentMethod->type === PaymentMethodType::VIRTUAL_ACCOUNT || $paymentMethod->type === PaymentMethodType::QR)) {
                PaymentCode::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'payment_method_id' => $paymentMethod->id,
                    ],
                    [
                        'code' => substr($result->action['value'], $paymentMethod->prefix_length),
                    ]
                );
            }

            $expiry = Carbon::parse($expiresAt)->toImmutable();
            $ttlMinutes = max(
                1,
                now()->diffInMinutes($expiry, false) + 2 // small buffer
            );
            $token = Str::random(8);
            $encryptedPayload = Crypt::encrypt($payload);

            Cache::put('ebil:pay:'.$token, $encryptedPayload, $ttlMinutes * 60);

            return response()->json([
                'status' => 'success',
                'action' => $result->action,
                'redirect_url' => route('e-billing.invoice.pay', ['token' => $token]),
                'message' => 'Silakan selesaikan pembayaran Anda.',
            ]);
        }

        return response()->json($response ?? ['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses permintaan.']);
    }

    public function pay(Request $request)
    {
        $token = $request->query('token');
        if (! $token) {
            return view('e-billing::invoices.customer.pay', [
                'error' => 'Sesi pembayaran tidak ditemukan atau sudah kedaluwarsa.',
                'session' => null,
            ]);
        }

        $session = Cache::get('ebil:pay:'.$token);
        if (! $session) {
            return view('e-billing::invoices.customer.pay', [
                'error' => 'Sesi pembayaran tidak ditemukan atau sudah kedaluwarsa.',
                'session' => null,
            ]);
        }

        $session = Crypt::decrypt($session);

        return view('e-billing::invoices.customer.pay', [
            'session' => $session,
        ]);
    }

    public function payStatus(Request $request)
    {
        $token = $request->query('token');
        if (! $token) {
            return view('e-billing::invoices.customer.pay', [
                'error' => 'Sesi pembayaran tidak ditemukan atau sudah kedaluwarsa.',
                'session' => null,
            ]);
        }

        $session = Cache::get('ebil:pay:'.$token);
        if (! $session) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi pembayaran tidak ditemukan atau sudah kedaluwarsa.',
            ], 404);
        }

        $session = Crypt::decrypt($session);

        $expiresAt = isset($session['expires_at']) ? Carbon::parse($session['expires_at'])->toImmutable() : null;
        $expired = $expiresAt ? now()->greaterThan($expiresAt) : false;
        $paymentStatus = null;
        $pollError = null;
        try {
            // Query Xendit for latest status
            $x = new XenditConnector;
            $req = new GetPaymentRequest($session['id']);
            $res = $x->send($req);
            $data = $res->json();
            $paymentStatus = $data['status'] ?? null; // e.g., SUCCEEDED, EXPIRED, FAILED, etc.
            // If expired or finished, we can drop the cache soon
            if (in_array($paymentStatus, ['SUCCEEDED', 'AUTHORIZED', 'EXPIRED', 'FAILED', 'CANCELED'])) {
                Cache::delete('ebil:pay:'.$token);
            }
        } catch (\Throwable $e) {
            Log::error($e);
            $pollError = 'Gagal memeriksa status pembayaran.';
        }

        return response()->json([
            'status' => 'ok',
            'expired' => $expired,
            'payment_status' => $paymentStatus,
            'poll_error' => $pollError,
            'session' => $session,
        ]);
    }
}
