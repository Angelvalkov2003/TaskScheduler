<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Services\SurveyApiService;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SurveyApiServiceTest extends TestCase
{
    protected string $validApiKey = 'r5w715v9vnhmcqshr6m0d44p9knt38qa99wyfuchx4mjcqdg8ybc2wz4tgd09e56';
    protected string $baseUrl = 'https://gmidev.decipherinc.com';
    protected string $projectPath = 'bor/training/v3/avalkov/FinalTest';

    #[Test]
    public function it_returns_survey_data_successfully()
    {
        Http::fake([
            "{$this->baseUrl}/api/v1/surveys/{$this->projectPath}/data?format=json" => Http::response(['data' => 'mocked response'], 200),
        ]);

        $service = new SurveyApiService($this->baseUrl, $this->validApiKey);
        $response = $service->getSurveyData($this->projectPath);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals('mocked response', $response['data']);
    }

    #[Test]
    public function it_aborts_on_unauthorized_request()
    {
        Http::fake([
            "{$this->baseUrl}/api/v1/surveys/{$this->projectPath}/data?format=json" => Http::response(null, 401),
        ]);

        $service = new SurveyApiService($this->baseUrl, $this->validApiKey);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Incorrect key');

        $service->getSurveyData($this->projectPath);
    }


    #[Test]
    public function it_fetches_real_survey_data()
    {
        $service = new SurveyApiService($this->baseUrl, $this->validApiKey);
        $response = $service->getSurveyData('bor/training/v3/avalkov/JustEat', "json", 20309);

        dd($response); // View the actual API response
    }


    #[Test]
    public function it_fetches_real_survey_layouts()
    {
        $service = new SurveyApiService($this->baseUrl, $this->validApiKey);
        $response = $service->getSurveyLayouts('bor/training/v3/avalkov/JustEat');


        dd($response); // View the actual API response
    }
}
