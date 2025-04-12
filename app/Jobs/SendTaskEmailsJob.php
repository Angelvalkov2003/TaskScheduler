<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\Link;
use App\Notifications\SendTaskDataNotification;
use App\Notifications\SendTaskDataPasswordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class SendTaskEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    protected string $emailRecipients;
    protected string $taskName;
    protected int $fileId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $emailRecipients, string $taskName, int $fileId)
    {
        $this->emailRecipients = $emailRecipients;
        $this->taskName = $taskName;
        $this->fileId = $fileId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $file = File::findOrFail($this->fileId);
            $this->sendEmailsWithLinks($file);
            Log::info("Emails sent successfully for task: {$this->taskName}");
        } catch (\Exception $e) {
            Log::error("Failed to send emails for task: {$this->taskName}", [
                'error' => $e->getMessage(),
                'file_id' => $this->fileId
            ]);

            throw $e; // rethrow to trigger retry if needed
        }
    }

    /**
     * Send emails with links and passwords
     */
    private function sendEmailsWithLinks(File $file): void
    {
        $recipients = array_filter(array_map('trim', explode(',', $this->emailRecipients)), function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        foreach ($recipients as $email) {
            $link = Link::create([
                'file_id' => $file->id,
                'email' => $email
            ]);

            Notification::route('mail', $email)->notify(new SendTaskDataNotification(
                $email,
                $this->taskName,
                $link->value
            ));

            Notification::route('mail', $email)->notify(new SendTaskDataPasswordNotification(
                $email,
                $this->taskName,
                $link->password
            ));
        }
    }
}
