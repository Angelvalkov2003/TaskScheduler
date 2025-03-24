<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class SurveyApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct(string $baseUrl, string $apiKey)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
    }

    protected function request(string $endpoint, array $queryParams = [])
    {
        $fullUrl = "{$this->baseUrl}/api/v1/surveys/{$endpoint}";

        \Log::info('Survey API Request', [
            'url' => $fullUrl,
            'params' => $queryParams,
        ]);

        $response = Http::withHeaders([
            'x-apikey' => $this->apiKey,
        ])->get($fullUrl, $queryParams);

        // Handle specific HTTP status codes
        switch ($response->status()) {
            case 401:
                abort(401, 'INVALID AUTHENTICATION: Your API key is invalid, expired, or restricted.');
            case 402:
                abort(402, 'PAYMENT REQUIRED: You have exceeded your available API calls.');
            case 403:
                abort(403, 'INVALID AUTHORIZATION: You do not have access to this survey.');
            case 404:
                abort(404, 'NOT FOUND: The requested survey or resource does not exist.');
            case 405:
                abort(405, 'METHOD NOT ALLOWED: The requested action is not supported.');
            case 429:
                abort(429, 'TOO MANY REQUESTS: Please wait and try again.');
            case 501:
                abort(501, 'NOT IMPLEMENTED: This method is not supported.');
            case 400:
                abort(400, 'BAD REQUEST: Something went wrong, the survey may not be accessible.');
        }

        if (!$response->successful()) {
            abort(400, 'Unknown error while fetching data.');
        }

        // Return JSON if applicable, otherwise return raw body
        return $response->header('Content-Type') === 'application/json'
            ? $response->json()
            : $response->body();
    }




    public function getSurveyData(string $projectPath, ?string $format = 'json', ?string $layout = null)
    {
        $queryParams = [];

        if (!empty($format)) {
            $queryParams['format'] = $format;
        }

        if (!empty($layout)) {
            $queryParams['layout'] = $layout;
        }

        return $this->request("{$projectPath}/data", $queryParams);
    }

    public function getSurveyLayouts(string $projectPath)//returns only description(name of the layout) and its id
    {
        $layouts = $this->request("{$projectPath}/layouts");

        return collect($layouts)->map(fn($layout) => [
            'id' => $layout['id'] ?? null,
            'description' => $layout['description'] ?? null,
        ])->all();
    }
}
