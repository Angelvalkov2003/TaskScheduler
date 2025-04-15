<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\SurveyApiService;
use App\Models\Key;
use App\Models\Task;
use App\Models\TaskSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cron\CronExpression;
use App\Enums\ExportFormat;

class DecipherExportEditForm extends Component
{
    public $task;
    public $taskSettings;
    public $surveyPath = '';
    public $name = '';
    public $format = 'csv';
    public $layout = 'standard';
    public $condition = 'qualified';
    public $emails = '';
    public $repeat = '* * * * *';
    public $startDate = '';
    public $endDate = '';
    public $layouts = [];
    public $isLoading = false;
    public $errorMessage = '';
    public $successMessage = '';
    public $apiKey = '';
    public $isValidated = false;

    public function mount(Task $task)
    {
        $this->task = $task;
        
        // Get task settings
        $settings = TaskSetting::where('task_id', $task->id)->get();
        $this->taskSettings = [];
        
        foreach ($settings as $setting) {
            $this->taskSettings[$setting->key] = $setting->value;
        }
        
        // Set form values from task
        $this->name = $task->name;
        $this->repeat = $task->repeat;
        $this->startDate = $task->start_date->format('Y-m-d\TH:i');
        $this->endDate = $task->end_date->format('Y-m-d\TH:i');
        
        // Set form values from task settings
        $this->format = $this->taskSettings['format'] ?? 'csv';
        $this->layout = $this->taskSettings['layout'] ?? 'standard';
        $this->condition = $this->taskSettings['condition'] ?? 'qualified';
        $this->emails = $this->taskSettings['emails'] ?? '';
        
        // Construct survey path
        $this->surveyPath = ($this->taskSettings['server'] ?? '') . '/survey/' . ($this->taskSettings['survey_path'] ?? '');
        
        // Validate the survey path on mount
        $this->validateSurveyPath();
    }

    public function updatedSurveyPath()
    {
        $this->isValidated = false;
        $this->successMessage = '';
        $this->validate([
            'surveyPath' => 'required|url',
        ]);
        
        // Debounce the validation to avoid too many requests
        $this->validateSurveyPath();
    }

    public function validateSurveyPath()
    {
        if (empty($this->surveyPath)) {
            return;
        }

        $this->isLoading = true;
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->layouts = [];
        $this->isValidated = false;

        try {
            // Parse the URL to get server and path
            $parsedUrl = parse_url($this->surveyPath);
            $server = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            $surveyPath = ltrim($parsedUrl['path'], '/survey/');

            // Get API key for the user and server
            $key = Key::where('user_id', Auth::id())
                ->where('host', $server)
                ->first();

            if (!$key) {
                $this->errorMessage = 'No API key found for this server. Please add an API key in your settings.';
                $this->isLoading = false;
                return;
            }

            $this->apiKey = $key->value;

            // Create API service
            $service = new SurveyApiService($server, $this->apiKey);

            // Get available layouts
            $this->layouts = $service->getSurveyLayouts($surveyPath);
            
            $this->successMessage = 'Survey link is valid. Available layouts loaded.';
            $this->isValidated = true;
        } catch (\Exception $e) {
            Log::error('Error validating survey path: ' . $e->getMessage());
            $this->errorMessage = 'Error validating survey link: ' . $e->getMessage();
            $this->successMessage = '';
        } finally {
            $this->isLoading = false;
        }
    }

    public function update()
    {
        if (!$this->isValidated) {
            $this->errorMessage = 'Please validate the survey link before submitting the form.';
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'repeat' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!CronExpression::isValidExpression($value)) {
                        $fail('The repeat field must be a valid CRON expression.');
                    }
                }
            ],
            'surveyPath' => 'required|url',
            'format' => 'required|string',
            'layout' => 'nullable|string',
            'condition' => 'nullable|string',
            'emails' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $emails = array_map('trim', explode(',', $value));
                    foreach ($emails as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $fail('Invalid email: ' . $email);
                        }
                    }
                }
            ],
        ]);

        try {
            // Format the server and the path
            $parsedUrl = parse_url($this->surveyPath);
            $server = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            $surveyPath = ltrim($parsedUrl['path'], '/survey/');

            // Update task
            $this->task->update([
                'name' => $this->name,
                'repeat' => $this->repeat,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
            ]);
            
            // Update task settings
            $settingsToUpdate = [
                'server' => $server,
                'survey_path' => $surveyPath,
                'format' => $this->format,
                'layout' => $this->layout,
                'condition' => $this->condition,
                'emails' => $this->emails,
            ];
            
            foreach ($settingsToUpdate as $key => $value) {
                TaskSetting::updateOrCreate(
                    ['task_id' => $this->task->id, 'key' => $key],
                    ['value' => $value]
                );
            }
            
            session()->flash('success', 'Task updated successfully!');
            return redirect()->route('decipherExport.view', $this->task);
        } catch (\Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.decipher-export-edit-form');
    }
} 