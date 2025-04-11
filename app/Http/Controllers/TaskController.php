<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskSetting;
use App\Models\Key;
use App\Models\TaskLog;
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
     * View task details
     */
    public function view(Task $task)
    {
        // Load task settings
        $settings = TaskSetting::where('task_id', $task->id)->get();
        $taskSettings = [];
        
        foreach ($settings as $setting) {
            $taskSettings[$setting->key] = $setting->value;
        }
        
        // Load recent task logs
        $taskLogs = TaskLog::where('task_id', $task->id)
            ->orderBy('run_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('decipherExport.viewDecipherTask', [
            'task' => $task,
            'taskSettings' => $taskSettings,
            'taskLogs' => $taskLogs
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

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        // Get task settings
        $settings = TaskSetting::where('task_id', $task->id)->get();
        $taskSettings = [];
        
        foreach ($settings as $setting) {
            $taskSettings[$setting->key] = $setting->value;
        }
        
        return view('decipherExport.editDecipherTask', compact('task', 'taskSettings'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'survey_path' => 'required|string',
            'format' => 'required|string',
            'layout' => 'required|string',
            'condition' => 'required|string',
            'emails' => 'required|string',
            'repeat' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        try {
            // Update task
            $task->update([
                'name' => $request->name,
                'repeat' => $request->repeat,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            
            // Parse survey path to get server and survey path
            $surveyPathParts = explode('/survey/', $request->survey_path);
            $server = $surveyPathParts[0];
            $surveyPath = $surveyPathParts[1] ?? '';
            
            // Update task settings
            $settingsToUpdate = [
                'server' => $server,
                'survey_path' => $surveyPath,
                'format' => $request->format,
                'layout' => $request->layout,
                'condition' => $request->condition,
                'emails' => $request->emails,
            ];
            
            foreach ($settingsToUpdate as $key => $value) {
                TaskSetting::updateOrCreate(
                    ['task_id' => $task->id, 'key' => $key],
                    ['value' => $value]
                );
            }
            
            return redirect()->route('tasks.view', $task)->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('tasks.edit', $task)->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }
}