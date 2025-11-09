<?php

namespace App\Notifications;

use App\Models\FundingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MouUploaded extends Notification implements ShouldQueue
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
            ->subject('MOU Pendanaan Telah Tersedia')
            ->greeting('Halo '.$this->fundingRequest->user->name.'!')
            ->line('Dokumen MOU untuk pengajuan pendanaan Anda telah tersedia.')
            ->line('Usaha: '.$this->fundingRequest->business->name)
            ->line('Jumlah Pendanaan: Rp '.number_format($this->fundingRequest->amount, 0, ',', '.'))
            ->action('Lihat & Tandatangani MOU', route('funding-requests.show', $this->fundingRequest))
            ->line('Silakan unduh, baca dengan seksama, tanda tangan, bubuhkan materai, scan, dan unggah kembali dokumen MOU yang sudah ditandatangani.');
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
        ];
    }
}
