<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\TaskTeam;
use App\Models\TaskSetting;
use App\Http\Requests\StoreDecipherExportRequest;
use App\Jobs\ProcessTaskJob;
use App\Services\SurveyApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Key;


class DecipherExportController extends Controller
{
    public function createDecipherTask()
    {
        return view('decipherExport.createDecipherTask');
    }

    public function store(Request $request)
    {
        try {
            // Format the server and the path
            $fullSurveyUrl = $request->input('survey_path');
            $parsedUrl = parse_url($fullSurveyUrl);
            $server = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            $surveyPath = ltrim($parsedUrl['path'], '/survey/');

            // Set up task data
            $taskData = $request->only([
                'name',
                'start_date',
                'end_date',
                'repeat',
            ]);
            $taskData['type'] = 'Decipher Auto Export';
            $taskData['created_by'] = Auth::id();
            $taskData['is_active'] = true;
            $task = Task::create($taskData);

            // Set up task_settings data
            $extraData = $request->only([
                'format',
                'layout',
                'condition',
                'emails',
            ]);
            $extraData['server'] = $server;
            $extraData['survey_path'] = $surveyPath;
            foreach ($extraData as $key => $value) {
                TaskSetting::create([
                    'task_id' => $task->id,
                    'key' => $key,
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }

            return redirect()->back()->with('success', 'Task created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function view(Task $task)
    {
        // Load task settings
        $settings = TaskSetting::where('task_id', $task->id)->get();
        $taskSettings = [];

        foreach ($settings as $setting) {
            $taskSettings[$setting->key] = $setting->value;
        }



        return view('decipherExport.viewDecipherTask', [
            'task' => $task,
            'taskSettings' => $taskSettings,
        ]);
    }


    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        return view('decipherExport.editDecipherTask', compact('task'));
    }

    /**
     * Update the specified decipher export in storage.
     */
    public function update(Request $request, Task $task)
    {
        try {
            // Format the server and the path
            $fullSurveyUrl = $request->input('survey_path');
            $parsedUrl = parse_url($fullSurveyUrl);
            $server = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            $surveyPath = ltrim($parsedUrl['path'], '/survey/');

            // Update task
            $task->update([
                'name' => $request->name,
                'repeat' => $request->repeat,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

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

            return redirect()->route('decipherExport.view', $task)->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update decipher export: ' . $e->getMessage());
            return redirect()->route('decipherExport.edit', $task)->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }



}
