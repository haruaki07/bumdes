<?php

namespace App\Notifications;

use App\Models\FundingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisbursementCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public FundingRequest $fundingRequest)
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $disbursement = $this->fundingRequest->disbursements()->latest()->first();

        return (new MailMessage)
            ->subject('Dana Pendanaan Telah Dicairkan')
            ->greeting('Halo '.$this->fundingRequest->user->name.'!')
            ->line('Dana pendanaan Anda telah berhasil dicairkan.')
            ->line('Usaha: '.$this->fundingRequest->business->name)
            ->line('Jumlah Dicairkan: Rp '.number_format($disbursement?->amount ?? $this->fundingRequest->disbursed_amount, 0, ',', '.'))
            ->line('Tanggal Pencairan: '.($disbursement?->disbursement_date ?? $this->fundingRequest->disbursement_date)?->format('d/m/Y'))
            ->action('Lihat Detail', route('funding-requests.show', $this->fundingRequest))
            ->line('Jangan lupa untuk melakukan pembayaran cicilan sesuai jadwal yang telah disepakati. Anda dapat mengunggah bukti pembayaran cicilan melalui sistem.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $disbursement = $this->fundingRequest->disbursements()->latest()->first();

        return [
            'funding_request_id' => $this->fundingRequest->id,
            'business_name' => $this->fundingRequest->business->name,
            'amount' => $disbursement?->amount ?? $this->fundingRequest->disbursed_amount,
            'disbursement_date' => $disbursement?->disbursement_date ?? $this->fundingRequest->disbursement_date,
        ];
    }
}
