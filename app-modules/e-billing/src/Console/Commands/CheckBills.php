<?php

namespace Modules\EBilling\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Modules\EBilling\Jobs\InvoiceReminderJob;
use Modules\EBilling\Models\Invoice;

class CheckBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'e-billing:check-bills {--no-send : Do not send notifications, just check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check unpaid bills and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoices = Invoice::with(['customer'])
            ->unpaid()
            ->get()
            ->filter(function (Invoice $invoice) {
                // Check if today is the due reminder date or past due date
                $reminderDateStart = $invoice->due_date->copy()->subDays($invoice->customer->due_reminder_days);

                return $reminderDateStart->isNowOrPast() || $invoice->due_date->isNowOrPast();
            });

        if ($invoices->isEmpty()) {
            $this->info('No unpaid invoices past due date.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Found %d unpaid invoices past due date.', $invoices->count()));

        if ($this->option('no-send')) {
            $this->table(['Invoice', 'Customer', 'Email', 'Due Date', 'Amount'], $invoices->map(fn ($inv) => [
                $inv->invoice_number,
                $inv->customer?->name,
                $inv->customer?->email,
                $inv->due_date->format('Y-m-d'),
                number_format($inv->amount, 0, ',', '.'),
            ]));
            $this->comment('Dry run complete. No emails dispatched.');

            return self::SUCCESS;
        }

        $jobs = [];
        foreach ($invoices as $invoice) {
            if (! $invoice->customer || ! $invoice->customer->email) {
                $this->warn("Skipping invoice {$invoice->invoice_number}: missing customer email");

                continue;
            }
            $jobs[] = new InvoiceReminderJob($invoice->customer, $invoice);
        }

        if (empty($jobs)) {
            $this->warn('No invoices eligible for email sending.');

            return self::SUCCESS;
        }

        Bus::batch($jobs)
            ->name('Send invoice reminders ('.now()->toDateTimeString().')')
            ->allowFailures()
            ->dispatch();

        $this->info(sprintf('Dispatched %d invoice reminder job(s) to queue.', count($jobs)));

        return self::SUCCESS;
    }
}
