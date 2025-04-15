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

class DecipherExportForm extends Component
{
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

    public function mount()
    {
        // Set default dates
        $this->startDate = now()->format('Y-m-d\TH:i');
        $this->endDate = now()->addMonth()->format('Y-m-d\TH:i');
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

    public function store()
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

            // Set up task data
            $taskData = [
                'name' => $this->name,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'repeat' => $this->repeat,
                'type' => 'Decipher Auto Export',
                'created_by' => Auth::id(),
                'is_active' => true,
            ];
            
            $task = Task::create($taskData);

            // Set up task_settings data
            $extraData = [
                'format' => $this->format,
                'layout' => $this->layout,
                'condition' => $this->condition,
                'emails' => $this->emails,
                'server' => $server,
                'survey_path' => $surveyPath,
            ];
            
            foreach ($extraData as $key => $value) {
                TaskSetting::create([
                    'task_id' => $task->id,
                    'key' => $key,
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }

            session()->flash('success', 'Task created successfully!');
            return redirect()->route('decipherExport.view', $task);
        } catch (\Exception $e) {
            Log::error('Error creating task: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.decipher-export-form');
    }
}
