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
use Modules\EBilling\Settings\EBillingBusinessProfileSettings;

class InvoiceController extends Controller
{
    public function __construct(private PaymentServiceInterface $paymentService) {}

    /**
     * Create a manual invoice for an active customer for the current month ignoring next_billing_date.
     */
    public function createManual(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:ebil_customers,id',
        ]);

        $customer = Customer::with(['package'])->findOrFail($validated['customer_id']);

        if ($customer->status !== \Modules\EBilling\Enums\CustomerStatus::ACTIVE) {
            return back()->with('error', 'Pelanggan tidak aktif.');
        }

        // Prevent duplicate invoice for same period (month + customer)
        $periodMonth = now()->format('Ym');
        $already = Invoice::where('customer_id', $customer->id)
            ->whereYear('period_end_date', now()->year)
            ->whereMonth('period_end_date', now()->month)
            ->exists();
        if ($already) {
            return back()->with('error', 'Invoice bulan ini sudah ada untuk pelanggan ini.');
        }

        $currentDate = now();
        $count = Invoice::whereMonth('created_at', $currentDate->month)
            ->whereYear('created_at', $currentDate->year)
            ->count();
        $invoiceNumber = Invoice::generateInvoiceNumber($currentDate->copy(), $count);

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'due_date' => $customer->due_date,
            'grace_period_end_date' => $customer->grace_period_end_date,
            'period_start_date' => $customer->period_start_date,
            'period_end_date' => $customer->period_end_date,
            'customer_id' => $customer->id,
            'customer_detail' => $customer,
            'package_id' => $customer->package_id,
            'package_detail' => $customer->package,
            'amount' => $customer->package?->price ?? 0,
            'status' => InvoiceStatus::UNPAID,
        ]);

        // tie invoice to customer as active invoice if none set
        $customer->invoice_number = $invoice->invoice_number;
        $customer->save();

        return redirect()->route('e-billing.invoices.show', $invoice)->with('success', 'Invoice berhasil dibuat.');
    }

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

    public function customerSearch()
    {
        return view('e-billing::invoices.customer.index');
    }

    public function customerShow(Request $request, $customerId)
    {
        // Check if the customerId is an invoice number
        if (str_starts_with($customerId, 'INV')) {
            $invoice = Invoice::where('invoice_number', $customerId)->first();
            if (! $invoice) {
                return view('e-billing::invoices.customer.not-found', [
                    'title' => 'Tagihan tidak ditemukan',
                    'message' => 'Tagihan mungkin telah diarsipkan atau tidak tersedia. Silakan coba lagi nanti atau hubungi admin.',
                    'customerId' => $customerId,
                ]);
            }

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

        $paymentMethods = PaymentMethod::active()->get();
        $invoice->loadMissing('paymentMethod');

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

        $businessProfileSettings = app(EBillingBusinessProfileSettings::class);

        return view('e-billing::invoices.customer.show', compact('customer', 'invoice', 'paymentMethods', 'savedPaymentMethod', 'savedPaymentCode', 'businessProfileSettings'));
    }

    public function requestPayment(Request $request, $customerId)
    {
        $request->validate([
            'payment_method' => 'required|exists:ebil_payment_methods,id',
        ]);

        $customer = Customer::where('customer_id', $customerId)->first();
        if (! $customer) {
            return response()->json(['status' => 'error', 'message' => 'ID pelanggan tidak ditemukan'], 404);
        }

        $invoice = Invoice::where('invoice_number', $customer->invoice_number)->first();
        if (! $invoice) {
            return response()->json(['status' => 'error', 'message' => 'Tagihan tidak ditemukan'], 404);
        }

        if ($invoice->status === InvoiceStatus::PAID) {
            return response()->json(['status' => 'error', 'message' => 'Tagihan sudah dibayar. Tidak dapat melakukan pembayaran ulang.'], 422);
        }

        if ($invoice->status === InvoiceStatus::EXPIRED) {
            return response()->json(['status' => 'error', 'message' => 'Tagihan telah kadaluarsa. Silakan hubungi admin untuk meminta tagihan baru.'], 422);
        }

        $paymentMethod = PaymentMethod::find($request->input('payment_method'));
        if (! $paymentMethod) {
            return response()->json(['status' => 'error', 'message' => 'Metode pembayaran tidak valid'], 400);
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
            $fee = method_exists($paymentMethod, 'calculateFee') ? $paymentMethod->calculateFee($invoice->amount) : 0;
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
                'fee' => $fee,
                'total_amount' => $invoice->amount + $fee,
                'merchant_name' => 'E-Billing',
                'payment_method' => [
                    'id' => $paymentMethod->id,
                    'type' => method_exists($paymentMethod->type, 'value') ? $paymentMethod->type->value : $paymentMethod->type,
                    'name' => $paymentMethod->name ?? $paymentMethod->code,
                    'code' => $paymentMethod->code,
                    'image_url' => $paymentMethod->brand_logo,
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
            $fee = method_exists($paymentMethod, 'calculateFee') ? $paymentMethod->calculateFee($invoice->amount) : 0;
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
                'fee' => $fee,
                'total_amount' => $invoice->amount + $fee,
                'merchant_name' => 'E-Billing',
                'payment_method' => [
                    'id' => $paymentMethod->id,
                    'type' => method_exists($paymentMethod->type, 'value') ? $paymentMethod->type->value : $paymentMethod->type,
                    'name' => $paymentMethod->name ?? $paymentMethod->code,
                    'code' => $paymentMethod->code,
                    'image_url' => $paymentMethod->brand_logo,
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
                'title' => 'Sesi Pembayaran Tidak Ditemukan!',
                'error' => 'Sesi pembayaran tidak ditemukan atau mungkin sudah kedaluwarsa.',
                'session' => null,
            ]);
        }

        $session = Cache::get('ebil:pay:'.$token);
        if (! $session) {
            return view('e-billing::invoices.customer.pay', [
                'title' => 'Sesi Pembayaran Tidak Ditemukan!',
                'error' => 'Sesi pembayaran tidak ditemukan atau mungikn sudah kedaluwarsa.',
                'session' => null,
            ]);
        }

        $session = Crypt::decrypt($session);

        $alreadyPaid = Invoice::where('invoice_number', $session['reference_id'] ?? '')->where('status', InvoiceStatus::PAID)->exists();
        if ($alreadyPaid) {
            Cache::delete('ebil:pay:'.$token);

            return view('e-billing::invoices.customer.pay', [
                'title' => 'Sesi Pembayaran Tidak Ditemukan!',
                'error' => 'Tagihan sudah dibayar.',
                'session' => null,
            ]);
        }

        $invoice = Invoice::where('invoice_number', $session['reference_id'])->first();

        return view('e-billing::invoices.customer.pay', [
            'session' => $session,
            'invoice' => $invoice,
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

        $invoice = Invoice::where('invoice_number', $session['reference_id'] ?? '')->first();

        // For manual confirmation channels, do not query gateway
        if (($session['action']['descriptor'] ?? null) !== 'BANK_TRANSFER_DETAILS') {
            try {
                $res = $this->paymentService->getPaymentStatus($session['id'] ?? '');
                $paymentStatus = $res->status; // e.g., SUCCEEDED, EXPIRED, FAILED, etc.
                // If expired or finished, we can drop the cache soon

                // need to wait webhook call
                if ($paymentStatus === 'SUCCEEDED' || $paymentStatus === 'AUTHORIZED') {
                    if ($invoice->status === InvoiceStatus::PAID) {
                        $paymentStatus = 'SUCCEEDED';
                    } else {
                        $paymentStatus = 'PENDING';
                    }
                }

                if (in_array($paymentStatus, ['SUCCEEDED', 'AUTHORIZED', 'EXPIRED', 'FAILED', 'CANCELED'])) {
                    Cache::delete('ebil:pay:'.$token);
                }
            } catch (\Throwable $e) {
                Log::error($e);
                $pollError = 'Gagal memeriksa status pembayaran.';
            }
        }

        if ($paymentStatus === 'SUCCEEDED') {
            return view('e-billing::invoices.customer.pay-success', compact('invoice'))->render();
        }

        if ($paymentStatus === 'EXPIRED' || $expired) {
            return view('e-billing::invoices.customer.pay-expired', compact('invoice'))->render();
        }

        return response()->json([
            'status' => 'ok',
            'expired' => $expired,
            'payment_status' => $paymentStatus,
            'poll_error' => $pollError,
            'session' => $session,
        ]);
    }

    public function simulatePayment(Request $request)
    {
        $token = $request->query('token');
        if (! $token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak ditemukan.',
            ], 400);
        }

        $cached = Cache::get('ebil:pay:'.$token);
        if (! $cached) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi pembayaran tidak ditemukan atau kedaluwarsa.',
            ], 404);
        }

        $session = Crypt::decrypt($cached);
        $paymentId = $session['id'] ?? null;
        if (! $paymentId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment ID tidak tersedia.',
            ], 400);
        }

        try {
            $amount = (int) ($request->input('amount') ?: ($session['total_amount'] ?? $session['amount'] ?? null));
            if ($session['payment_method']['type'] !== PaymentMethodType::BANK_TRANSFER) {
                $result = $this->paymentService->simulatePayment($paymentId, $amount ?: null);

                return response()->json([
                    'status' => 'ok',
                    'payment_status' => $result->status,
                    'data' => $result ?? null,
                ]);
            } else {
                $invoice = Invoice::where('invoice_number', $session['reference_id'])->first();
                $invoice->status = InvoiceStatus::PAID;
                $invoice->paid_at = now();
                $invoice->payment_method_code = $session['payment_method']['code'] ?? null;
                $invoice->save();

                return view('e-billing::invoices.customer.pay-success', compact('invoice'))->render();
            }
        } catch (\Throwable $e) {
            Log::error('Simulate payment error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mensimulasikan pembayaran.',
            ], 500);
        }
    }

    public function markPaid(Request $request, Invoice $invoice)
    {
        if ($invoice->status === InvoiceStatus::PAID) {
            return back()->with('success', 'Invoice sudah lunas.');
        }
        $invoice->status = InvoiceStatus::PAID;
        $invoice->paid_at = now();

        // Optionally mark latest receipt as approved
        $latestReceipt = TransferReceipt::where('invoice_id', $invoice->id)->latest()->first();
        if ($latestReceipt) {
            $latestReceipt->status = 'approved';
            $latestReceipt->reviewed_by = $request->user()?->id;
            $latestReceipt->reviewed_at = now();
            $latestReceipt->save();
            $invoice->payment_method_code = $latestReceipt->payment_method_code;
        }

        $invoice->save();

        InvoicePaid::dispatch($invoice);

        return back()->with('success', 'Invoice ditandai lunas.');
    }

    public function markUnpaid(Invoice $invoice)
    {
        $invoice->status = InvoiceStatus::UNPAID;
        $invoice->paid_at = null;
        $invoice->payment_method_code = null;
        $invoice->save();

        $invoice->customer->invoice_number = $invoice->invoice_number;
        $invoice->customer->save();

        TransferReceipt::where('invoice_id', $invoice->id)->delete();

        return back()->with('success', 'Invoice ditandai belum lunas.');
    }
}
