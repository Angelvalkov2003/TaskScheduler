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

class ProcessTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $ident;
    protected string $server;
    protected string $apiKey;
    protected string $format; // Добавяме параметър за формата

    /**
     * Create a new job instance.
     */
    public function __construct(string $ident, string $server, string $apiKey, string $format)
    {
        $this->ident = $ident;
        $this->server = $server;
        $this->apiKey = $apiKey;
        $this->format = $format; // Записваме формата
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
            self::dispatch($this->ident, $this->server, $this->apiKey, $this->format)->delay(now()->addMinutes(5));
            return;
        }

        // взимаме данните
        $result = $service->getTaskResult($this->ident);

        // Formats the json file
        if ($this->format === 'json') {
            if (is_array($result)) {
                $result = json_encode($result, JSON_PRETTY_PRINT);
            }
        }

        // Determine the file extension based on the format
        $fileExtension = match ($this->format) {
            'spss16' => 'sav', // SPSS 16 format
            'json' => 'json',  // JSON format
            'csv' => 'csv',    // CSV format
            default => 'txt',  // Default to text file
        };

        $filePath = "private/surveys/survey_{$this->ident}.{$fileExtension}";

        // Save the result to local storage
        $saveSuccess = Storage::disk('local')->put($filePath, $result);

        // Check if the file was successfully saved
        if ($saveSuccess && Storage::disk('local')->exists($filePath)) {
            \Log::info("File saved successfully: $filePath");
        } else {
            \Log::error("Failed to save file: $filePath");
        }
    }

}