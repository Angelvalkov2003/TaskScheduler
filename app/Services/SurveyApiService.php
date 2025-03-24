<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

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

        return $this->handleResponse($response);
    }


    protected function handleResponse(Response $response)
    {
        if ($response->status() === 401) {
            abort(401, 'Invalid API Key');
        }

        if ($response->status() === 404) {
            abort(404, 'Resource Not Found');
        }

        if ($response->status() === 429) {
            abort(429, 'Too Many Requests - Rate Limit Exceeded');
        }

        if (!$response->successful()) {
            abort($response->status(), 'API Error: ' . $response->body());
        }

        return $response->header('Content-Type') === 'application/json'
            ? $response->json()
            : $response->body();
    }


    public function getSurveyData(string $projectPath, ?string $format = 'json', ?string $layout = null)
    {
        $queryParams = ['format' => $format];

        if (!empty($layout)) {
            $queryParams['layout'] = $layout;
        }

        return $this->request("{$projectPath}/data", $queryParams);
    }


    public function getSurveyLayouts(string $projectPath)
    {
        $layouts = $this->request("{$projectPath}/layouts");

        return collect($layouts)->map(fn($layout) => [
            'id' => $layout['id'] ?? null,
            'description' => $layout['description'] ?? null,
        ])->all();
    }


    public function startAsyncSurveyDataExport(string $projectPath, ?string $format = 'json', ?string $layout = null)
    {
        $queryParams = [
            'format' => $format,
            'forceTask' => 'true',
        ];

        if (!empty($layout)) {
            $queryParams['layout'] = $layout;
        }

        return $this->request("{$projectPath}/data", $queryParams);
    }


    public function getTaskStatus(string $taskId)
    {
        $response = Http::withHeaders([
            'x-apikey' => $this->apiKey,
        ])->get("{$this->baseUrl}/api/v1/status", [
                    'id' => $taskId
                ]);

        return $this->handleResponse($response)['status'] ?? 'unknown';
    }


    public function getTaskResult(string $taskId)
    {
        $response = Http::withHeaders([
            'x-apikey' => $this->apiKey,
        ])->get("{$this->baseUrl}/api/v1/status/content", [
                    'id' => $taskId
                ]);

        return $this->handleResponse($response);
    }
}
