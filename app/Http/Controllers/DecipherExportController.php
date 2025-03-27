<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\TaskTeam;
use App\Models\TaskSetting;
use Illuminate\Support\Facades\Auth;


class DecipherExportController extends Controller
{
    public function createDecipherTask()
    {

        return view('decipherExport.createDecipherTask', []);
    }




    public function store(Request $request)
    {
        // Format the sеrver and the path
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
    }

}
