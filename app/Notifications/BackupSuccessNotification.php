<?php

namespace App\Notifications;

use App\Models\Backup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackupSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Backup $backup
    ) {
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
            ->subject('Backup Completed Successfully')
            ->success()
            ->greeting('Backup Completed!')
            ->line("Your {$this->backup->type->label()} backup has been completed successfully.")
            ->line('**Backup Details:**')
            ->line("- Name: {$this->backup->name}")
            ->line("- Type: {$this->backup->type->label()}")
            ->line("- Size: {$this->backup->file_size_formatted}")
            ->line("- Duration: {$this->backup->duration}")
            ->line("- Completed: {$this->backup->completed_at->format('Y-m-d H:i:s')}")
            ->action('View Backups', url('/admin/backups'))
            ->line('Your data is safe and secure.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'backup_id' => $this->backup->id,
            'backup_name' => $this->backup->name,
            'backup_type' => $this->backup->type->value,
            'backup_size' => $this->backup->file_size_formatted,
            'duration' => $this->backup->duration,
            'completed_at' => $this->backup->completed_at,
            'message' => "{$this->backup->type->label()} backup completed successfully.",
            'icon' => 'check-circle',
            'color' => 'success',
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'backup-success';
    }
}
