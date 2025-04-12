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
            try {
                $cron = new CronExpression($task->repeat);
                if ($cron->isDue()) {
                    $result = $task->startTaskExecution();
                    
                    if ($result['success']) {
                        $this->info("Successfully started task ID {$task->id}. Task ID: {$result['task_id']}");
                    } else {
                        $this->error("Failed to start task ID {$task->id}: {$result['message']}");
                    }
                }
            } catch (\Throwable $e) {
                $this->error("Exception for task ID {$task->id}: " . $e->getMessage());
                continue;
            }
        }
    }
}
