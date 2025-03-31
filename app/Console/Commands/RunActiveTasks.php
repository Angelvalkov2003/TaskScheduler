<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\TaskSetting;
use App\Models\Key;
use App\Jobs\TaskJob;
use App\Services\SurveyApiService;
use Cron\CronExpression;
use App\Jobs\ProcessTaskJob;

class RunActiveTasks extends Command
{
    protected $signature = 'tasks:run';
    protected $description = 'Run scheduled tasks with status is_active = true';

    public function handle()
    {
        // Взима всички активни задачи
        $tasks = Task::where('is_active', true)->get();

        foreach ($tasks as $task) {
            $cron = new CronExpression($task->repeat);
            if ($cron->isDue()) {
                // Взима необходимите настройки за таска
                $surveyPath = TaskSetting::where('task_id', $task->id)->where('key', 'survey_path')->value('value');
                $format = TaskSetting::where('task_id', $task->id)->where('key', 'format')->value('value') ?? 'json';
                $layout = TaskSetting::where('task_id', $task->id)->where('key', 'layout')->value('value');
                $server = TaskSetting::where('task_id', $task->id)->where('key', 'server')->value('value');

                // Взима API ключа и проверява дали host съвпада с типа на таска
                $apiKeyEntry = Key::where('user_id', $task->created_by)->where('host', $server)->first();
                $apiKey = $apiKeyEntry ? $apiKeyEntry->value : null;

                if (!$apiKey) {
                    $this->error("API key is missing for task ID {$task->id} and server {$server}.");
                    continue;
                }

                if ($surveyPath && $server) {
                    $service = new SurveyApiService($server, $apiKey);
                    $taskResponse = $service->startAsyncSurveyDataExport($surveyPath);//добави ,$format,$layout
                    $ident = $taskResponse['ident'] ?? null; //ключът който получаваме от АПИто за следващи проверки

                    if (!$ident) {
                        $this->error("Failed to start async task for task ID {$task->id}, no task ID received.");
                    } else {
                        $this->info("Successfully started async task for task ID {$task->id}. Task ID: {$ident}");
                        ProcessTaskJob::dispatch($ident, $server, $apiKey);
                    }
                } else {
                    $this->error("Missing required settings for task ID {$task->id}.");
                }
            }
        }
    }
}
