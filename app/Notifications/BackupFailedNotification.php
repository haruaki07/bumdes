<?php

namespace App\Notifications;

use App\Models\Backup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackupFailedNotification extends Notification implements ShouldQueue
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
            ->subject('Backup Failed')
            ->error()
            ->greeting('Backup Failed!')
            ->line("Your {$this->backup->type->label()} backup has failed.")
            ->line('**Backup Details:**')
            ->line("- Name: {$this->backup->name}")
            ->line("- Type: {$this->backup->type->label()}")
            ->line("- Started: {$this->backup->started_at->format('Y-m-d H:i:s')}")
            ->line("- Failed: {$this->backup->completed_at->format('Y-m-d H:i:s')}")
            ->line('')
            ->line('**Error Message:**')
            ->line($this->backup->error_message ?? 'Unknown error')
            ->action('View Backups', url('/admin/backups'))
            ->line('Please check the logs for more details or contact your system administrator.');
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
            'error_message' => $this->backup->error_message,
            'started_at' => $this->backup->started_at,
            'failed_at' => $this->backup->completed_at,
            'message' => "{$this->backup->type->label()} backup failed.",
            'icon' => 'alert-circle',
            'color' => 'danger',
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'backup-failed';
    }
}
