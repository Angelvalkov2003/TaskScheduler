<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Storage;

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
            throw new HttpException(401, 'Invalid API Key');
        }

        if ($response->status() === 404) {
            throw new HttpException(404, 'Resource Not Found');
        }

        if ($response->status() === 429) {
            throw new HttpException(429, 'Too Many Requests - Rate Limit Exceeded');
        }

        if (!$response->successful()) {
            throw new HttpException($response->status(), 'API Error: ' . $response->body());
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
        $queryParams = ['select' => 'id,description'];
        $layouts = $this->request("{$projectPath}/layouts", $queryParams);

        return $layouts;
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

    public function getTaskDataSaved(string $taskId, string $format): string
    {

        $url = "{$this->baseUrl}/api/v1/status/content?id={$taskId}";
        $filepath = "survey_{$taskId}.{$format}";
        $fullPath = Storage::disk('survey_data')->path($filepath);

        Http::withHeaders([
            'x-apikey' => $this->apiKey,
        ])->sink($fullPath)->get($url);

        return $fullPath;


    }
}
