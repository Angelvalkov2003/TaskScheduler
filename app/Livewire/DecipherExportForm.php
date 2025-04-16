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
use App\Http\Requests\StoreDecipherExportRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $this->resetErrorBag('surveyPath'); // Clear previous validation error

        if (!filter_var($this->surveyPath, FILTER_VALIDATE_URL)) {
            $this->addError('surveyPath', 'The survey path must be a valid URL.');
            return;
        }

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
            $this->errorMessage = 'Error validating survey link: ' . $e->getMessage();
            $this->successMessage = '';
        } finally {
            $this->isLoading = false;
        }
    }

    public function store()
    {
        if (!$this->isValidated) {
            $this->addError('surveyPath', 'Please validate the survey link before submitting the form.');
            return;
        }

        // Map component properties to validation field names
        $data = [
            'name' => $this->name,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'repeat' => $this->repeat,
            'survey_path' => $this->surveyPath,
            'format' => $this->format,
            'layout' => $this->layout,
            'condition' => $this->condition,
            'emails' => $this->emails,
        ];

        // Validate dates separately first
        if (strtotime($this->endDate) < strtotime($this->startDate)) {
            $this->addError('endDate', 'The end date must be after or equal to the start date.');
            return;
        }

        $request = new StoreDecipherExportRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        if ($validator->fails()) {
            $this->handleValidationErrors($validator);
            return;
        }

        try {
            $parsedUrl = parse_url($this->surveyPath);
            if (!$parsedUrl || !isset($parsedUrl['scheme'], $parsedUrl['host'], $parsedUrl['path'])) {
                throw new \Exception('Invalid survey URL format.');
            }

            $server = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            $surveyPath = Str::after($parsedUrl['path'], '/survey/');

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
            $this->addError('general', 'An error occurred: ' . $e->getMessage());
        }
    }

    protected function handleValidationErrors($validator)
    {
        foreach ($validator->errors()->toArray() as $field => $messages) {
            foreach ($messages as $message) {
                // Map the validation field names back to component property names
                $componentField = match($field) {
                    'start_date' => 'startDate',
                    'end_date' => 'endDate',
                    'survey_path' => 'surveyPath',
                    default => $field
                };
                $this->addError($componentField, $message);
            }
        }
    }

    public function render()
    {
        return view('livewire.decipher-export-form');
    }
}
