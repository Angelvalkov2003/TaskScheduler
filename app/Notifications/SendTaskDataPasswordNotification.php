<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendTaskDataPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $email,
        protected string $taskName,
        protected string $password
    ) {}

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
        return (new MailMessage)
            ->subject("Password for {$this->taskName} Download")
            ->greeting("Hello!")
            ->line("You have received a separate email with a download link for {$this->taskName}.")
            ->line("Here is your password to access the download:")
            ->line($this->password)
            ->line("Please keep this password secure and do not share it with anyone.")
            ->line("Thank you for using our application!");
    }
} 