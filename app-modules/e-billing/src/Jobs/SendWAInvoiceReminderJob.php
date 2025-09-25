<?php

namespace Modules\EBilling\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EBilling\Models\Invoice;
use Modules\EBilling\Services\Contracts\WhatsappServiceInterface;
use Modules\EBilling\Settings\EBillingBusinessProfileSettings;
use Modules\EBilling\Settings\EBillingWhatsappSettings;

class SendWAInvoiceReminderJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Invoice $invoice)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsappServiceInterface $whatsappService, EBillingWhatsappSettings $waSettings, EBillingBusinessProfileSettings $bpSettings): void
    {
        $invoice = $this->invoice;
        if (! $waSettings->enabled) {
            return;
        }

        $template = $waSettings->invoice_reminder_template
            ?? 'Halo {customer_name}, tagihan {invoice_number} jatuh tempo pada {invoice_due_date}. Lihat: {invoice_public_url}';

        $replacements = [
            '{customer_name}' => $invoice->customer->name,
            '{invoice_number}' => $invoice->invoice_number,
            '{invoice_amount}' => 'Rp'.number_format($invoice->amount, 0, ',', '.'),
            '{invoice_due_date}' => optional($invoice->due_date)->format('d M Y'),
            '{invoice_grace_period_end_date}' => optional($invoice->grace_period_end_date)->format('d M Y'),
            '{invoice_status}' => $invoice->status->label(),
            '{invoice_public_url}' => $invoice->public_url,
            '{package_name}' => ($invoice->package->name ?? data_get($invoice->package_detail, 'name', '-')),
            '{business_name}' => $bpSettings->name,
        ];

        $message = strtr($template, $replacements);

        $recipient = preg_replace('/[^0-9]/', '', (string) $invoice->customer->phone);
        if ($recipient) {
            $whatsappService->sendMessage($recipient.'@c.us', $message);
        }
    }
}
