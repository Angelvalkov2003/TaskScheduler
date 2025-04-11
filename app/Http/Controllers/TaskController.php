<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskSetting;
use App\Models\Key;
use App\Jobs\ProcessTaskJob;
use App\Services\SurveyApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tasks.index', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Force a task to run immediately
     */
    public function force(Task $task)
    {
        try {
            // Get task settings
            $settings = TaskSetting::where('task_id', $task->id)->get();
            $settingsMap = [];
            
            foreach ($settings as $setting) {
                $settingsMap[$setting->key] = $setting->value;
            }
            
            // Extract required settings
            $surveyPath = $settingsMap['survey_path'] ?? '';
            $server = $settingsMap['server'] ?? '';
            $format = $settingsMap['format'] ?? 'json';
            $emailRecievers = $settingsMap['emails'] ?? '';
            $taskName = $task->name;
            
            // Get API key
            $apiKeyEntry = Key::where('user_id', Auth::id())
                ->where('host', $server)
                ->first();
                
            if (!$apiKeyEntry) {
                return redirect()->route('tasks.index')
                    ->with('error', 'API key not found for this task.');
            }
            
            $apiKey = $apiKeyEntry->value;
            
            // Start the async task and get the ident
            Log::info("Starting async task for task ID {$task->id}, Survey Path: {$surveyPath}, Format: {$format}, Server: {$server}");
            $service = new SurveyApiService($server, $apiKey);
            $taskResponse = $service->startAsyncSurveyDataExport($surveyPath, $format);
            $ident = $taskResponse['ident'] ?? null;

            if (!$ident) {
                Log::error("Failed to start async task for task ID {$task->id}, no task ID received.");
                return redirect()->route('tasks.index')
                    ->with('error', 'Failed to start the task: No task ID received.');
            }
            
            Log::info("Successfully started async task for task ID {$task->id}. Task ID: {$ident}");
            
            // Dispatch the job with the ident
            ProcessTaskJob::dispatch(
                $ident,
                $server,
                $apiKey,
                $format,
                $emailRecievers,
                $taskName,
                $task->id
            );
            
            return redirect()->route('tasks.index')
                ->with('success', 'Task has been queued to run immediately.');
                
        } catch (\Exception $e) {
            Log::error("Failed to force task: " . $e->getMessage());
            return redirect()->route('tasks.index')
                ->with('error', 'Failed to force task: ' . $e->getMessage());
        }
    }
}