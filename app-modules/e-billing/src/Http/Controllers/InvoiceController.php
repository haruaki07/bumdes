<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\EBilling\Enums\InvoiceStatus;
use Modules\EBilling\Enums\PaymentMethodType;
use Modules\EBilling\Events\InvoicePaid;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Invoice;
use Modules\EBilling\Models\PaymentCode;
use Modules\EBilling\Models\PaymentMethod;
use Modules\EBilling\Models\TransferReceipt;
use Modules\EBilling\Services\Contracts\PaymentServiceInterface;
use Modules\EBilling\Services\DTOs\Payment\CreatePaymentRequestIn;

class InvoiceController extends Controller
{
    public function __construct(private PaymentServiceInterface $paymentService) {}

    public function index(Request $request)
    {
        $invoices = Invoice::with(['customer', 'package'])->orderBy('created_at', 'desc')->datatable();

        return view('e-billing::invoices.index', compact('invoices'));
    }

    public function show(Request $request, Invoice $invoice)
    {
        $invoice->loadMissing(['customer', 'package']);
        $receipts = TransferReceipt::where('invoice_id', $invoice->id)->orderBy('created_at', 'desc')->get();

        return view('e-billing::invoices.show', compact('invoice', 'receipts'));
    }

    public function customerShow(Request $request, $customerId)
    {
        // Check if the customerId is an invoice number
        if (str_starts_with($customerId, 'INV')) {
            $invoice = Invoice::where('invoice_number', $customerId)->first();
            $customer = Customer::find($invoice->customer_id);
        } else {
            $customer = Customer::where('customer_id', $customerId)->first();
            if (! $customer) {
                return view('e-billing::invoices.customer.not-found', [
                    'title' => 'Pelanggan tidak ditemukan',
                    'message' => 'Nomor pelanggan tidak valid atau tidak terdaftar. Pastikan Anda menggunakan tautan yang benar atau hubungi admin untuk bantuan.',
                    'customerId' => $customerId,
                ]);
            }

            if (! $customer->invoice_number) {
                return view('e-billing::invoices.customer.not-found', [
                    'title' => 'Belum ada tagihan aktif',
                    'message' => 'Saat ini Anda belum memiliki tagihan. Jika Anda merasa ini sebuah kesalahan, silakan hubungi admin.',
                    'customer' => $customer,
                    'customerId' => $customerId,
                ]);
            }

            $invoice = Invoice::where('invoice_number', $customer->invoice_number)->first();
        }

        if (! $invoice) {
            return view('e-billing::invoices.customer.not-found', [
                'title' => 'Tagihan tidak ditemukan',
                'message' => 'Tagihan mungkin telah diarsipkan atau tidak tersedia. Silakan coba lagi nanti atau hubungi admin.',
                'customer' => $customer,
                'customerId' => $customerId,
            ]);
        }

        $paymentMethods = PaymentMethod::all();

        // Resolve saved payment method (simple "remember me" for channels)
        $savedPaymentMethod = null;
        $savedPaymentCode = null;
        if ($customer && ! empty($customer->payment_method_code)) {
            $savedPaymentMethod = PaymentMethod::where('code', $customer->payment_method_code)->first();
            if ($savedPaymentMethod) {
                $savedPaymentCode = PaymentCode::where([
                    'customer_id' => $customer->id,
                    'payment_method_id' => $savedPaymentMethod->id,
                ])->first();
            }
        }

        return view('e-billing::invoices.customer.show', compact('customer', 'invoice', 'paymentMethods', 'savedPaymentMethod', 'savedPaymentCode'));
    }

    public function requestPayment(Request $request, $customerId)
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

        // Prevent requesting payment for paid or expired invoices
        $currentStatus = $invoice->status->value;
        if (in_array($currentStatus, ['PAID', 'LUNAS'])) {
            return response()->json(['status' => 'error', 'message' => 'Tagihan sudah dibayar. Tidak dapat melakukan pembayaran ulang.'], 422);
        }
        if (in_array($currentStatus, ['EXPIRED', 'KADALUARSA'])) {
            return response()->json(['status' => 'error', 'message' => 'Tagihan telah kadaluarsa. Silakan hubungi admin untuk meminta tagihan baru.'], 422);
        }

        $paymentMethod = PaymentMethod::find($request->input('payment_method'));
        if (! $paymentMethod) {
            abort(404);
        }

        $savePaymentMethod = $request->boolean('save');
        $reusableCode = null;
        $paymentCode = PaymentCode::where(['customer_id' => $customer->id, 'payment_method_id' => $paymentMethod->id])->first();
        if ($savePaymentMethod && $paymentCode) {
            $reusableCode = $paymentCode->code;
        } elseif (! $savePaymentMethod && $paymentCode) {
            $paymentCode->delete();
        }

        // If this payment method needs manual confirmation (e.g., bank transfer),
        // we don't call the payment gateway. Present bank details to the customer instead.
        if ($paymentMethod->need_confirmation) {
            $expiresAt = now()->addDay()->toISOString();
            $payload = [
                'id' => 'manual-'.Str::uuid()->toString(),
                'status' => 'REQUIRES_ACTION',
                'action' => [
                    'type' => 'PRESENT_TO_CUSTOMER',
                    'descriptor' => 'BANK_TRANSFER_DETAILS',
                    'value' => [
                        'bank' => $paymentMethod->name ?? 'Bank',
                        'account_number' => $paymentMethod->account_number ?? '',
                        'account_name' => config('app.name'),
                        'note' => 'Cantumkan nomor invoice '.$invoice->invoice_number.' pada keterangan transfer.',
                    ],
                ],
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

            if ($savePaymentMethod) {
                $customer->payment_method_code = $paymentMethod->code;
                $customer->save();
            }

            $expiry = Carbon::parse($expiresAt)->toImmutable();
            $ttlMinutes = max(1, now()->diffInMinutes($expiry, false) + 2);
            $token = Str::random(8);
            $encryptedPayload = Crypt::encrypt($payload);
            Cache::put('ebil:pay:'.$token, $encryptedPayload, $ttlMinutes * 60);

            return response()->json([
                'status' => 'success',
                'action' => $payload['action'],
                'redirect_url' => route('e-billing.invoice.pay', ['token' => $token]),
                'message' => 'Silakan transfer sesuai petunjuk, lalu konfirmasi ke admin.',
            ]);
        }

        try {
            $result = $this->paymentService->createPaymentRequest(
                new CreatePaymentRequestIn(
                    referenceId: $invoice->invoice_number,
                    amount: $invoice->amount,
                    paymentMethod: $paymentMethod,
                    customer: $customer,
                    package: $customer->package,
                    reusable: $savePaymentMethod,
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
            if ($savePaymentMethod && ($paymentMethod->type === PaymentMethodType::VIRTUAL_ACCOUNT || $paymentMethod->type === PaymentMethodType::QR)) {
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

            // Persist customer's preferred payment method when requested
            if ($savePaymentMethod) {
                $customer->payment_method_code = $paymentMethod->code;
                $customer->save();
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

    public function removeSavedPaymentMethod(Request $request, $customerId)
    {
        $customer = Customer::where('customer_id', $customerId)->first();
        if (! $customer) {
            return response()->json(['status' => 'error', 'message' => 'Pelanggan tidak ditemukan.'], 404);
        }

        if (empty($customer->payment_method_code)) {
            return response()->json(['status' => 'success', 'message' => 'Tidak ada metode tersimpan.']);
        }

        $method = PaymentMethod::where('code', $customer->payment_method_code)->first();
        // Clear preferred method on customer
        $customer->payment_method_code = null;
        $customer->save();

        // Optionally remove reusable code for that method
        if ($method) {
            PaymentCode::where([
                'customer_id' => $customer->id,
                'payment_method_id' => $method->id,
            ])->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Metode pembayaran tersimpan telah dihapus.',
        ]);
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

        $alreadyPaid = Invoice::where('invoice_number', $session['reference_id'] ?? '')->where('status', InvoiceStatus::PAID)->exists();
        if ($alreadyPaid) {
            Cache::delete('ebil:pay:'.$token);

            return view('e-billing::invoices.customer.pay', [
                'error' => 'Tagihan sudah dibayar.',
                'session' => null,
            ]);
        }

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

        // For manual confirmation channels, do not query gateway
        if (($session['action']['descriptor'] ?? null) !== 'BANK_TRANSFER_DETAILS') {
            try {
                $res = $this->paymentService->getPaymentStatus($session['id'] ?? '');
                $paymentStatus = $res->status; // e.g., SUCCEEDED, EXPIRED, FAILED, etc.
                // If expired or finished, we can drop the cache soon
                if (in_array($paymentStatus, ['SUCCEEDED', 'AUTHORIZED', 'EXPIRED', 'FAILED', 'CANCELED'])) {
                    Cache::delete('ebil:pay:'.$token);
                }
            } catch (\Throwable $e) {
                Log::error($e);
                $pollError = 'Gagal memeriksa status pembayaran.';
            }
        }

        return response()->json([
            'status' => 'ok',
            'expired' => $expired,
            'payment_status' => $paymentStatus,
            'poll_error' => $pollError,
            'session' => $session,
        ]);
    }

    public function markPaid(Request $request, Invoice $invoice)
    {
        if ($invoice->status === InvoiceStatus::PAID) {
            return back()->with('success', 'Invoice sudah lunas.');
        }
        $invoice->status = InvoiceStatus::PAID;
        $invoice->paid_at = now();
        $invoice->save();

        // Optionally mark latest receipt as approved
        $latestReceipt = TransferReceipt::where('invoice_id', $invoice->id)->latest()->first();
        if ($latestReceipt) {
            $latestReceipt->status = 'approved';
            $latestReceipt->reviewed_by = $request->user()?->id;
            $latestReceipt->reviewed_at = now();
            $latestReceipt->save();
        }

        InvoicePaid::dispatch($invoice);

        return back()->with('success', 'Invoice ditandai lunas.');
    }
}
