<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\EBilling\Enums\InvoiceStatus;
use Modules\EBilling\Models\Invoice;
use Modules\EBilling\Models\TransferReceipt;

class TransferReceiptController extends Controller
{
    /**
     * Store a new transfer receipt (customer-facing API).
     */
    public function store(Request $request, string $invoiceNumber)
    {
        try {
            $request->validate([
                'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
                'note' => ['nullable', 'string', 'max:500'],
                'payment_method' => ['required', 'string', Rule::exists('ebil_payment_methods', 'id')],
            ]);

            DB::beginTransaction();

            $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
            if (! $invoice) {
                return response()->json(['status' => 'error', 'message' => 'Invoice tidak ditemukan.'], 404);
            }

            if ($invoice->status === InvoiceStatus::PAID) {
                return response()->json(['status' => 'error', 'message' => 'Invoice sudah lunas.'], 422);
            }

            $path = Storage::disk('public')->putFileAs(
                'transfer-receipts',
                $request->file('file'),
                Str::random(5).'_'.strtolower($request->file('file')->getClientOriginalName())
            );

            $receipt = TransferReceipt::create([
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'file_path' => $path,
                'original_name' => $request->file('file')->getClientOriginalName(),
                'mime_type' => $request->file('file')->getMimeType(),
                'size_bytes' => $request->file('file')->getSize(),
                'note' => $request->input('note'),
                'status' => 'pending',
                'payment_method_id' => $request->input('payment_method'),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti transfer berhasil diunggah. Menunggu verifikasi admin.',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['status' => 'error', 'message' => 'Gagal mengunggah bukti transfer.'], 500);
        }
    }
}
