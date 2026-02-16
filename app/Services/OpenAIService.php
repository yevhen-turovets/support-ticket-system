<?php

namespace App\Services;

use App\Services\Contracts\AIServiceInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use JsonException;
use RuntimeException;

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

        $response = $this->http
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a Helpful Customer Support Agent. '
                            .'Respond with strict JSON only. '
                            .'No markdown. '
                            .'No explanations. '
                            .'Use this exact schema: '
                            .'{"category":"...","sentiment":"...","reply":"...","urgency":"Low|Medium|High"}.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $text,
                    ],
                ],
            ])
            ->throw();

        $content = $response->json('choices.0.message.content');

        if (!is_string($content) || $content === '') {
            throw new RuntimeException('OpenAI response content is missing.');
        }

        try {
            $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('OpenAI response is not valid JSON.', 0, $exception);
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
}
