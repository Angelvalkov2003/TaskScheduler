<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TaskSetting;
use App\Models\Key;
use App\Jobs\ProcessTaskJob;
use App\Services\SurveyApiService;
use Illuminate\Support\Facades\Log;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'created_by',
        'start_date',
        'end_date',
        'repeat',
        'archived_at',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'archived_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function settings()
    {
        return $this->hasMany(TaskSetting::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'task_team');
    }

    public function logs()
    {
        return $this->hasMany(TaskLog::class);
    }

    /**
     * Start task execution by getting settings, API key, and dispatching the job
     * 
     * @param int|null $userId Optional user ID to use for API key lookup (defaults to task creator)
     * @return array Result with success status and message
     */
    public function startTaskExecution(?int $userId = null): array
    {
        // Get task settings
        $settings = TaskSetting::where('task_id', $this->id)->get();
        $settingsMap = [];

        foreach ($settings as $setting) {
            $settingsMap[$setting->key] = $setting->value;
        }

        // Extract required settings
        $surveyPath = $settingsMap['survey_path'] ?? '';
        $server = $settingsMap['server'] ?? '';
        $format = $settingsMap['format'] ?? 'json';
        $emailRecievers = $settingsMap['emails'] ?? '';
        $taskName = $this->name;

        // Validate required settings
        if (empty($surveyPath) || empty($server)) {
            return [
                'success' => false,
                'message' => "Missing required settings for task ID {$this->id}."
            ];
        }

        // Get API key
        $userId = $userId ?? $this->created_by;
        $apiKeyEntry = Key::where('user_id', $userId)
            ->where('host', $server)
            ->first();

        if (!$apiKeyEntry) {
            return [
                'success' => false,
                'message' => "API key not found for this task."
            ];
        }

        $apiKey = $apiKeyEntry->value;

        // Start the async task and get the ident
        Log::info("Starting async task for task ID {$this->id}, Survey Path: {$surveyPath}, Format: {$format}, Server: {$server}");
        $service = new SurveyApiService($server, $apiKey);
        $taskResponse = $service->startAsyncSurveyDataExport($surveyPath, $format);
        $ident = $taskResponse['ident'] ?? null;
        
        if (!$ident) {
            Log::error("Failed to start async task for task ID {$this->id}, no task ID received.");
            return [
                'success' => false,
                'message' => "Failed to start the task: No task ID received."
            ];
        }

        Log::info("Successfully started async task for task ID {$this->id}. Task ID: {$ident}");

        // Dispatch the job with the ident
        ProcessTaskJob::dispatch(
            $ident,
            $server,
            $apiKey,
            $format,
            $emailRecievers,
            $taskName,
            $this->id
        );

        return [
            'success' => true,
            'message' => "Task has been queued to run.",
            'task_id' => $ident
        ];
    }
}
