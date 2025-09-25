<?php

namespace Modules\EBilling\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Modules\EBilling\Mail\InvoiceReminderMail;
use Modules\EBilling\Models\Invoice;
use Modules\EBilling\Notifications\Channels\WhatsappChannel;
use Modules\EBilling\Settings\EBillingBusinessProfileSettings;
use Modules\EBilling\Settings\EBillingWhatsappSettings;

class InvoiceReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Invoice $invoice)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', WhatsappChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        return (new InvoiceReminderMail($this->invoice))
            ->to($notifiable->email);
    }

    public function toWhatsApp(object $notifiable): string
    {
        $waSettings = app(EBillingWhatsappSettings::class);
        $bpSettings = app(EBillingBusinessProfileSettings::class);

        $template = $waSettings->invoice_reminder_template
            ?? 'Halo {customer_name}, tagihan {invoice_number} jatuh tempo pada {invoice_due_date}. Lihat: {invoice_public_url}';

        $replacements = [
            '{customer_name}' => $notifiable->name,
            '{invoice_number}' => $this->invoice->invoice_number,
            '{invoice_amount}' => 'Rp'.number_format($this->invoice->amount, 0, ',', '.'),
            '{invoice_due_date}' => optional($this->invoice->due_date)->format('d M Y'),
            '{invoice_grace_period_end_date}' => optional($this->invoice->grace_period_end_date)->format('d M Y'),
            '{invoice_status}' => $this->invoice->status->label(),
            '{invoice_public_url}' => $this->invoice->public_url,
            '{package_name}' => ($this->invoice->package->name ?? data_get($this->invoice->package_detail, 'name', '-')),
            '{business_name}' => $bpSettings->name,
        ];

        return strtr($template, $replacements);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
