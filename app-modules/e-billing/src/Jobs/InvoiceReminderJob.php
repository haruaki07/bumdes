<?php

namespace Modules\EBilling\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Invoice;
use Modules\EBilling\Notifications\InvoiceReminder;

class InvoiceReminderJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Customer $customer, protected Invoice $invoices)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->customer->notify(new InvoiceReminder($this->invoices));
    }
}
