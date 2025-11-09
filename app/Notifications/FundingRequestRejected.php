<?php

namespace App\Notifications;

use App\Models\FundingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FundingRequestRejected extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Pengajuan Pendanaan Ditolak')
            ->greeting('Halo '.$this->fundingRequest->user->name.'!')
            ->line('Mohon maaf, pengajuan pendanaan Anda tidak dapat disetujui.')
            ->line('Usaha: '.$this->fundingRequest->business->name)
            ->line('Jumlah Pengajuan: Rp '.number_format($this->fundingRequest->amount, 0, ',', '.'))
            ->line('Alasan: '.$this->fundingRequest->rejection_reason)
            ->action('Lihat Detail', route('funding-requests.show', $this->fundingRequest))
            ->line('Anda dapat mengajukan kembali dengan memperbaiki hal-hal yang menjadi alasan penolakan.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'funding_request_id' => $this->fundingRequest->id,
            'business_name' => $this->fundingRequest->business->name,
            'amount' => $this->fundingRequest->amount,
            'rejection_reason' => $this->fundingRequest->rejection_reason,
        ];
    }
}
