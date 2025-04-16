<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\SurveyApiService;
use App\Models\Key;
use App\Models\Task;
use App\Models\TaskSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Requests\StoreDecipherExportRequest;

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

        $settings = TaskSetting::where('task_id', $task->id)->get();
        $this->taskSettings = [];

        foreach ($settings as $setting) {
            $this->taskSettings[$setting->key] = $setting->value;
        }

        $this->name = $task->name;
        $this->repeat = $task->repeat;
        $this->startDate = $task->start_date?->format('Y-m-d\TH:i');
        $this->endDate = $task->end_date?->format('Y-m-d\TH:i');

        $this->format = $this->taskSettings['format'] ?? 'csv';
        $this->layout = $this->taskSettings['layout'] ?? 'standard';
        $this->condition = $this->taskSettings['condition'] ?? 'qualified';
        $this->emails = $this->taskSettings['emails'] ?? '';

        $this->surveyPath = ($this->taskSettings['server'] ?? '') . '/survey/' . ($this->taskSettings['survey_path'] ?? '');

        $this->validateSurveyPath();
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
            $parsedUrl = parse_url($this->surveyPath);

            if (!$parsedUrl || !isset($parsedUrl['scheme'], $parsedUrl['host'], $parsedUrl['path'])) {
                throw new \Exception('Invalid survey URL format.');
            }

            $server = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            $surveyPath = Str::after($parsedUrl['path'], '/survey/');

            $key = Key::where('user_id', Auth::id())
                ->where('host', $server)
                ->first();

            if (!$key) {
                $this->errorMessage = 'No API key found for this server. Please add an API key in your settings.';
                return;
            }

            $this->apiKey = $key->value;

            $service = new SurveyApiService($server, $this->apiKey);
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
            $this->addError('surveyPath', 'Please validate the survey link before submitting the form.');
            return;
        }

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

        $request = new StoreDecipherExportRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        if ($validator->fails()) {
            $this->handleValidationErrors($validator);
            return;
        }

        try {
            $parsedUrl = parse_url($this->surveyPath);
            $server = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            $surveyPath = Str::after($parsedUrl['path'], '/survey/');

            $this->task->update([
                'name' => $this->name,
                'repeat' => $this->repeat,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
            ]);

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
                    ['value' => is_array($value) ? json_encode($value) : $value]
                );
            }

            session()->flash('success', 'Task updated successfully!');
            return redirect()->route('decipherExport.view', $this->task);
        } catch (\Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage());
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
        return view('livewire.decipher-export-edit-form');
    }
}
