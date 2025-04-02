<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Services\SurveyApiService;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Storage;

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
    /*
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
       }*/


    /*
        #[Test]
        public function it_fetches_real_survey_data()
        {
            $service = new SurveyApiService($this->baseUrl, $this->validApiKey);
            $response = $service->getSurveyData('bor/training/v3/avalkov/JustEat', "json", 20309);

            dd($response); // View the actual API response
        }*/

    /*

#[Test]
public function it_fetches_real_survey_layouts()
{
    $service = new SurveyApiService($this->baseUrl, $this->validApiKey);
    $response = $service->getSurveyLayouts('bor/training/v3/avalkov/JustEat');


    dd($response); // View the actual API response
}
*/


    #[Test]
    public function async_it_fetches_real_survey_data()
    {
        $service = new SurveyApiService($this->baseUrl, $this->validApiKey);

        // Старт на задачата, в случая за SPSS 16
        $task = $service->startAsyncSurveyDataExport('bor/training/v3/avalkov/JustEat', 'spss16');  // или 'csv', 'json', и др.
        $taskId = $task['ident'] ?? null;

        if (!$taskId) {
            $this->fail('Failed to start async task, no task ID received.');
        }

        $maxAttempts = 30;
        $attempts = 0;
        $startTime = microtime(true);

        do {
            usleep(100_000); // Sleep for 100 milliseconds (0.1 seconds)
            $status = $service->getTaskStatus($taskId);
            $attempts++;

            if ($attempts >= $maxAttempts) {
                $this->fail("Timeout: Async task $taskId took too long to finish.");
            }

        } while ($status !== 'finished');

        $endTime = microtime(true);
        $elapsedTime = round(($endTime - $startTime) * 1000, 2);

        echo "\nTask {$taskId} completed in {$elapsedTime} ms.\n";

        // Вземаме резултата от задачата (може да е SPSS, CSV, JSON и т.н.)
        $fileData = $service->getTaskResult($taskId);

        // Проверка дали данните са архивирани (например SPSS .zip файл)
        if (strpos($fileData, 'PK') === 0) {  // 'PK' е началото на ZIP файловете
            // Това означава, че данните са архивирани (ZIP файл)
            $fileExtension = 'zip';
        } else {
            // В противен случай, предполагаме CSV формат
            $fileExtension = 'csv';
        }

        // Определяне на пътя към файла с правилното разширение
        $filePath = "private/surveys/survey_{$taskId}.{$fileExtension}";

        // Записваме файла в local storage
        Storage::disk('local')->put($filePath, $fileData);

        // Проверка дали файлът е записан успешно
        if (Storage::disk('local')->exists($filePath)) {
            echo "\nFile saved successfully: $filePath\n";
        } else {
            $this->fail("Failed to save file.");
        }

        dd($fileData);  // За да покажете данните от файла и да видите дали са архивирани или не
    }





}
