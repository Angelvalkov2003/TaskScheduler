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
        // Get all tasks that are currently active
        $tasks = Task::where('is_active', true)->get();
        $now = now();

        foreach ($tasks as $task) {
            try {
                // Check if task is outside its date range
                if ($task->start_date && $now->lt($task->start_date)) {
                    $task->update(['is_active' => false]);
                    $this->info("Task ID {$task->id} deactivated: Start date not reached");
                    continue;
                }

                if ($task->end_date && $now->gt($task->end_date)) {
                    $task->update(['is_active' => false]);
                    $this->info("Task ID {$task->id} deactivated: End date passed");
                    continue;
                }

                // If task is within date range, check if it's due to run
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
