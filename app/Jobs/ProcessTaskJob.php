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

        // Логваме резултатите (можеш да ги запишеш в база)
        \Log::info("Task {$this->ident} finished. Result:", ['result' => $result]);

        // Определяме разширението на файла спрямо формата
        $fileExtension = match ($this->format) {
            'spss16' => 'sav', // SPSS 16 формат
            'json' => 'json',  // JSON формат
            'csv' => 'csv',    // CSV формат
            default => 'txt',  // по подразбиране текстов файл
        };

        // Записваме резултата в local storage
        $filePath = "private/surveys/survey_{$this->ident}.{$fileExtension}";
        Storage::disk('local')->put($filePath, $result);

        // Проверка дали файлът е записан успешно
        if (Storage::disk('local')->exists($filePath)) {
            \Log::info("File saved successfully: $filePath");
        } else {
            \Log::error("Failed to save file.");
        }

        // Тук можеш да запишеш резултата в базата, да изпратиш имейл и т.н.
    }
}