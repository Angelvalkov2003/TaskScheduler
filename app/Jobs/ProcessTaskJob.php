<?php

namespace App\Jobs;

use App\Services\SurveyApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $ident;
    protected string $server;
    protected string $apiKey;

    /**
     * Create a new job instance.
     */
    public function __construct(string $ident, string $server, string $apiKey)
    {
        $this->ident = $ident;
        $this->server = $server;
        $this->apiKey = $apiKey;
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
            self::dispatch($this->ident, $this->server, $this->apiKey)->delay(now()->addMinutes(5));
            return;
        }

        // взимаме данните
        $result = $service->getTaskResult($this->ident);

        // Логваме резултатите (можеш да ги запишеш в база)
        \Log::info("Task {$this->ident} finished. Result:", ['result' => $result]);

        // Тук можеш да запазиш резултата в базата, да изпратиш имейл и т.н.
    }
}
