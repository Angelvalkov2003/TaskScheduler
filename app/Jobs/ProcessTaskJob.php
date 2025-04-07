<?php

namespace App\Jobs;

use App\Services\SurveyApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SendTaskDataNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;

class ProcessTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $ident;
    protected string $server;
    protected string $apiKey;
    protected string $format;
    protected string $emailRecievers;
    protected string $taskName;

    /**
     * Create a new job instance.
     */
    public function __construct(string $ident, string $server, string $apiKey, string $format, string $emailRecievers, string $taskName)
    {
        $this->ident = $ident;
        $this->server = $server;
        $this->apiKey = $apiKey;
        $this->format = $format;
        $this->emailRecievers = $emailRecievers;
        $this->taskName = $taskName;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $service = new SurveyApiService($this->server, $this->apiKey);

        // Проверяваме статуса на задачата
        $status = $service->getTaskStatus($this->ident);

        if ($status !== 'finished') {
            // Ако не е приключила, пренасрочваме Job-а да се изпълни отново след 5 минути
            self::dispatch($this->ident, $this->server, $this->apiKey, $this->format)->delay(now()->addMinutes(1));
            return;
        }

        // взимаме данните
        $result = $service->getTaskResult($this->ident);

        /*
        if ($this->format === 'json') {
            if (is_array($result)) {
                $result = json_encode($result, JSON_PRETTY_PRINT);
                $fileExtension = 'json';
            }
        }*/

        // Determine the file extension based on the format
        $fileExtension = match ($this->format) {
            'spss16' => 'sav', // SPSS 16 format
            'csv' => 'csv',    // CSV format
            default => 'txt',  // Default to text file
        };

        $filePath = "survey_{$this->ident}.{$fileExtension}";

        // Save the result to local storage
        $saveSuccess = Storage::disk('survey_data')->put($filePath, $result);

        // Check if the file was successfully saved
        if ($saveSuccess && Storage::disk('survey_data')->exists($filePath)) {
            \Log::info("File saved successfully: $filePath");

            // Изпращаме имейл с прикачен файл
            $notification = new SendTaskDataNotification(
                $this->emailRecievers,
                $this->taskName,
                $filePath
            );

            foreach ($notification->getEmailRecipients() as $email) {
                Notification::route('mail', $email)->notify($notification);
            }

        } else {
            \Log::error("Failed to save file: $filePath");
        }
    }

}