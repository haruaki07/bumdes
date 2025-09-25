<?php

namespace Modules\EBilling\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\EBilling\Mail\InvoiceReminderMail;
use Modules\EBilling\Models\Invoice;

class SendInvoiceReminderJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function handle(): void
    {
        $invoice = $this->invoice;

        Mail::to($invoice->customer->email)->send(new InvoiceReminderMail($invoice));
    }
}
