<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    protected function checkRateLimit($key, $limit = null)
    {
        $limit = $limit ?: env('GEMINI_RATE_LIMIT', 5);

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return false;
        }

        RateLimiter::hit($key, 60);
        return true;
    }

public function generate(Request $request)
{
    $request->validate([
        'prompt' => 'required|string|max:2000',
        'model' => 'nullable|string'
    ]);

    $key = 'gemini_' . $request->ip();
    if (!$this->checkRateLimit($key)) {
        return response()->json(['error' => 'Rate limit exceeded. Please try again later.'], 429);
    }

    $model = $request->input('model', 'gemini-2.0-flash'); // default to flash
    $result = $this->gemini->generateText($request->prompt, $model);

    $reply = 'No response available';
    if (isset($result['output_text'])) {
        $reply = $result['output_text'];
    } elseif (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $reply = $result['candidates'][0]['content']['parts'][0]['text'];
    } elseif (isset($result['error'])) {
        $reply = 'Error: ' . $result['error'];
        if (isset($result['details'])) {
            Log::error('Gemini API Error Details', ['details' => $result['details']]);
        }
    }

    return response()->json([
        'output_text' => $reply,
        'success' => !isset($result['error'])
    ]);
}

}