<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\EBilling\Enums\InvoiceStatus;
use Modules\EBilling\Events\InvoicePaid;
use Modules\EBilling\Models\Invoice;

class WebhookController extends Controller
{
    public function xendit(Request $request)
    {
        $tokenHeader = $request->header('x-callback-token');
        $expected = config('services.xendit.webhook_token');
        if (! empty($expected) && $tokenHeader !== $expected) {
            Log::warning('Xendit webhook token mismatch', [
                'provided' => $tokenHeader ? 'present' : 'missing',
            ]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->json()->all();
        if (! is_array($payload)) {
            return response()->json(['message' => 'Bad Request'], 400);
        }

        $event = $payload;
        if (! isset($event['event']) || ! isset($event['data']) || ! is_array($event['data'])) {
            Log::info('Xendit webhook ignored: missing event or data', ['keys' => array_keys($event)]);

            return response()->json(['message' => 'Ignored'], 200);
        }

        $data = $event['data'];
        $referenceId = $data['reference_id'] ?? null;
        $status = strtoupper((string) ($data['status'] ?? ''));

        if (! $referenceId || ! $status) {
            Log::warning('Xendit webhook missing reference_id or status', ['event' => $event]);

            return response()->json(['message' => 'Ignored'], 200);
        }

        $invoice = Invoice::where('invoice_number', $referenceId)->first();
        if (! $invoice) {
            Log::warning('Invoice not found for webhook reference_id', ['reference_id' => $referenceId, 'status' => $status]);

            return response()->json(['message' => 'Ignored'], 200);
        }

        $newStatus = match ($status) {
            'AUTHORIZED', 'PENDING' => InvoiceStatus::UNPAID,
            'SUCCEEDED' => InvoiceStatus::PAID,
            default => null,
        };

        if ($newStatus) {
            // don't update paid invoice
            if ($invoice->status === InvoiceStatus::PAID && $newStatus !== InvoiceStatus::PAID) {
                return response()->json(['message' => 'OK']);
            }

            if ($newStatus === InvoiceStatus::PAID) {
                $invoice->paid_at = now();
                $invoice->payment_method_code = $data['metadata']['payment_method_code'] ?? null;
            }

            $invoice->status = $newStatus;
            $invoice->save();

            if ($invoice->status === InvoiceStatus::PAID) {
                InvoicePaid::dispatch($invoice);
            }
        } else {
            Log::info('Xendit webhook status not mapped. Ignoring...', ['status' => $status]);
        }

        return response()->json(['message' => 'OK']);
    }
}
