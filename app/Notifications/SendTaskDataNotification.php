<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Storage;

class SendTaskDataNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $emails;
    protected string $taskName;
    protected string $filePath;
    protected string $fileName;

    public function __construct(string $emails, string $taskName, string $filePath)
    {
        $this->emails = $emails;
        $this->taskName = $taskName;
        $this->filePath = $filePath;
        $this->fileName = basename($filePath);
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $fullPath = Storage::disk('local')->path($this->filePath);

        return (new MailMessage)
            ->subject("Automatic export - {$this->taskName}")
            ->line('The export is ready and attached.')
            ->attach($fullPath, [
                'as' => $this->fileName,
                'mime' => mime_content_type($fullPath),
            ]);
    }

    public function getEmailRecipients(): array
    {
        return array_map('trim', explode(',', $this->emails));
    }
}
