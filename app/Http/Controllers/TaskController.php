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
    public function force(Task $task)
    {
        $result = $task->startTaskExecution();

        if ($result['success']) {
            return redirect()->route('tasks.index')
                ->with('success', 'Task has been queued to run immediately.');
        } else {
            return redirect()->route('tasks.index')
                ->with('error', $result['message']);
        }
    }

    public function destroy(Task $task)
    {
        try {
            $task->delete();
            return redirect()->route('tasks.index')
                ->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')
                ->with('error', 'Failed to delete task: ' . $e->getMessage());
        }
    }
}