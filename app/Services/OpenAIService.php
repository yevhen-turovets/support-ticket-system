<?php

namespace App\Services;

use App\Services\Contracts\AIServiceInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;
use JsonException;
use RuntimeException;
use Illuminate\Support\Facades\Log;

class OpenAIService implements AIServiceInterface
{
    public function __construct(
        private readonly HttpFactory $http
    ) {
    }

    /**
     * @return array{
     *     category: string,
     *     sentiment: string,
     *     reply: string,
     *     urgency: string
     * }
     */
    public function analyzeTicket(string $text): array
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.model');

        if (!is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('OpenAI API key is not configured.');
        }

        if (!is_string($model) || $model === '') {
            throw new RuntimeException('OpenAI model is not configured.');
        }

        try {
            $response = $this->http
                ->withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->systemPrompt(),
                        ],
                        [
                            'role' => 'user',
                            'content' => $text,
                        ],
                    ],
                ])
                ->throw();
        } catch (ConnectionException|RequestException $exception) {
            Log::error('OpenAI API request failed.', [
                'error' => $exception->getMessage(),
                'text_length' => strlen($text),
            ]);

            throw $exception;
        }

        $content = $response->json('choices.0.message.content');

        if (!is_string($content)) {
            throw new RuntimeException('OpenAI response content is missing.');
        }

        $content = trim($content);

        if ($content === '') {
            throw new RuntimeException('OpenAI response content is empty.');
        }

        try {
            $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            Log::error('OpenAI JSON decode failed.', [
                'error' => $exception->getMessage(),
                'text_length' => strlen($text),
            ]);

            throw $exception;
        }

        if (!is_array($decoded)) {
            throw new RuntimeException('OpenAI response must be a JSON object.');
        }

        foreach (['category', 'sentiment', 'reply', 'urgency'] as $key) {
            if (!array_key_exists($key, $decoded) || !is_string($decoded[$key])) {
                throw new RuntimeException("OpenAI response is missing or invalid key: {$key}.");
            }
        }

        return [
            'category' => $decoded['category'],
            'sentiment' => $decoded['sentiment'],
            'reply' => $decoded['reply'],
            'urgency' => $decoded['urgency'],
        ];
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You are a Helpful Customer Support Agent.
Return STRICT raw JSON only.
Do not use markdown.
Do not provide explanations.
Do not use code blocks.
Output MUST be valid JSON only.
Use this exact schema:
{
  "category": "Technical | Billing | General",
  "sentiment": "Positive | Neutral | Negative",
  "reply": "string",
  "urgency": "Low | Medium | High"
}
PROMPT;
    }
}
