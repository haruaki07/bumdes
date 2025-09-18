<?php

namespace Modules\EBilling\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\EBilling\Models\Invoice;
use Modules\EBilling\Settings\EBillingBusinessProfileSettings;

class InvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        $this->subject('Informasi Tagihan Wifi Anda');
    }

    public function build(EBillingBusinessProfileSettings $settings): self
    {
        return $this->view('e-billing::emails.invoice-reminder')
            ->with([
                'settings' => $settings,
                'invoice' => $this->invoice,
                'customer' => $this->invoice->customer,
                'package' => $this->invoice->package,
            ]);
    }
}
