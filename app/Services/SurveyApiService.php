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
        $response = Http::withHeaders([
            'x-apikey' => $this->apiKey,
        ])->get("{$this->baseUrl}/api/v1/surveys/{$endpoint}", $queryParams);

        if ($response->status() === 401) {
            abort(401, 'Incorrect key');
        }

        if (!$response->successful()) {
            abort(400, 'Request failed: ' . $response->body());
        }

        return $response->json();
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

    public function getSurveyLayouts(string $projectPath)
    {
        $layouts = $this->request("{$projectPath}/layouts");

        return collect($layouts)->map(fn($layout) => [
            'id' => $layout['id'] ?? null,
            'description' => $layout['description'] ?? null,
        ])->all();
    }
}
