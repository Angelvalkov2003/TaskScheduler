<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Cron\CronExpression;
use App\Enums\ExportFormat;

class StoreDecipherExportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Return an array of validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'repeat' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!CronExpression::isValidExpression($value)) {
                        $fail('The repeat field must be a valid CRON expression.');
                    }
                }
            ],
            'survey_path' => 'required|url',
            'format' => 'required|string|in:' . implode(',', ExportFormat::values()),
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
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The "Name" field is required.',
            'start_date.required' => 'Please enter a start date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'survey_path.required' => 'The "Survey URL" field is required.',
            'survey_path.url' => 'Please enter a valid URL.',
            'format.in' => 'Invalid format. Allowed formats: json, xlsx, spss, tripleS.',
            'emails.required' => 'The "Emails" field is required.',
        ];
    }
}