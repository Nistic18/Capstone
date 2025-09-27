<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_GEMINI_API_KEY');
        // ✅ base URL already ends with /models
        $this->baseUrl = env('GOOGLE_GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models');
    }

public function generateText(string $prompt, string $model = 'gemini-2.0-flash')
{
    $payload = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 256,
            "topP" => 0.95,
            "topK" => 64
        ]
    ];

    try {
        $url = "{$this->baseUrl}/{$model}:generateContent?key={$this->apiKey}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $url
            ]);
            return ['error' => 'Gemini API request failed', 'details' => $response->body()];
        }

        $responseData = $response->json();

        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'output_text' => $responseData['candidates'][0]['content']['parts'][0]['text'],
                'full_response' => $responseData
            ];
        }

        return $responseData;

    } catch (\Exception $e) {
        Log::error('Gemini Exception', ['message' => $e->getMessage()]);
        return ['error' => 'Exception occurred during API request', 'message' => $e->getMessage()];
    }
}

}