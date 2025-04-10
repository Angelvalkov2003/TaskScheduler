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
use App\Models\TaskLog;
use App\Models\Task;
use App\Models\TaskSetting;
use App\Models\File;
use App\Models\Link;
use Illuminate\Support\Facades\Log;
use App\Notifications\SendTaskDataPasswordNotification;

class ProcessTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $ident;
    protected string $server;
    protected string $apiKey;
    protected string $format;
    protected string $emailRecievers;
    protected string $taskName;
    protected ?int $taskId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $ident, string $server, string $apiKey, string $format, string $emailRecievers, string $taskName, ?int $taskId = null)
    {
        $this->ident = $ident;
        $this->server = $server;
        $this->apiKey = $apiKey;
        $this->format = $format;
        $this->emailRecievers = $emailRecievers;
        $this->taskName = $taskName;
        $this->taskId = $taskId;
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
            self::dispatch($this->ident, $this->server, $this->apiKey, $this->format, $this->emailRecievers, $this->taskName, $this->taskId)->delay(now()->addMinutes(1));
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
            Log::info("File saved successfully: $filePath");

            // Create TaskLog entry if we have a task ID
            if ($this->taskId) {
                $this->createTaskLogEntry($filePath);
            }



        } else {
            Log::error("Failed to save file: $filePath");

            // Create TaskLog entry with error if we have a task ID
            if ($this->taskId) {
                $this->createTaskLogEntry(null, "Failed to save file: $filePath");
            }
        }
    }

    /**
     * Create a TaskLog entry with the current task settings and run outcome
     */
    private function createTaskLogEntry(?string $filePath, ?string $error = null): void
    {
        try {
            // Get all task settings
            $taskSettings = TaskSetting::where('task_id', $this->taskId)->get();
            $settings = [];

            foreach ($taskSettings as $setting) {
                $settings[$setting->key] = $setting->value;
            }

            // Create the task log entry
            $taskLog = TaskLog::create([
                'task_id' => $this->taskId,
                'run_at' => now(),
                'settings' => $settings,
                'run_outcome' => $error ? ['error' => $error] : ['status' => 'success']
            ]);

            // If we have a file path, create a File record linked to the TaskLog
            if ($filePath) {
                $file = File::create([
                    'tasklog_id' => $taskLog->id,
                    'path' => $filePath
                ]);
                $this->sendEmailsWithLinks($file);
            }
        } catch (\Exception $e) {
            Log::error("Failed to create TaskLog entry: " . $e->getMessage());
        }
    }

    /**
     * Send emails with links and passwords
     */
    private function sendEmailsWithLinks(File $file): void
    {
        $recipients = array_map('trim', explode(',', $this->emailRecievers));
        
        foreach ($recipients as $email) {
            // Create a Link record for this recipient
            $link = Link::create([
                'file_id' => $file->id,
                'email' => $email
            ]);
            
            // Send email with download link
            $linkNotification = new SendTaskDataNotification(
                $email,
                $this->taskName,
                $link->value
            );
            
            // Send email with password
            $passwordNotification = new SendTaskDataPasswordNotification(
                $email,
                $this->taskName,
                $link->password
            );
            
            // Send both notifications
            Notification::route('mail', $email)->notify($linkNotification);
            Notification::route('mail', $email)->notify($passwordNotification);
        }
    }
}