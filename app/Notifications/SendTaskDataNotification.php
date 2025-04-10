<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendTaskDataNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $email,
        protected string $taskName,
        protected string $linkValue
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $downloadUrl = config('app.url') . '/download/' . $this->linkValue;

        return (new MailMessage)
            ->subject("Download Link for {$this->taskName}")
            ->greeting("Hello!")
            ->line("Your data for {$this->taskName} is ready for download.")
            ->line("Click the button below to access your download:")
            ->action('Download Data', $downloadUrl)
            ->line("You will receive a separate email with the password to access this download.")
            ->line("Thank you for using Task Scheduler!");
    }

    public function getEmailRecipients(): array
    {
        return array_map('trim', explode(',', $this->email));
    }
}
