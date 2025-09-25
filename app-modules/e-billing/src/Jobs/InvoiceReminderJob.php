<?php

namespace Modules\EBilling\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EBilling\Models\Invoice;

class InvoiceReminderJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $invoiceId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $invoice = Invoice::with(['customer', 'package'])->find($this->invoiceId);
        if (! $invoice || ! $invoice->customer || empty($invoice->customer->phone)) {
            return; // Nothing to do
        }

        SendWAInvoiceReminderJob::dispatch($invoice);
        SendInvoiceReminderJob::dispatch($invoice);
    }
}
