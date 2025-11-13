<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class OpenAIService
{
    private ClientInterface $http;

    public function __construct(ClientInterface $http = null)
    {
        $this->http = $http ?? new Client();
    }

    private function endpoint(string $path): string
    {
        return rtrim(config('services.openai.base_url'), '/').'/'.ltrim($path, '/');
    }

    public function chat(array $messages, array $options = []): ?string
    {
        $payload = array_merge([
            'model' => config('services.openai.model'),
            'messages' => $messages,
            'temperature' => 0.5,
            'max_tokens' => 256,
        ], $options);

        try {
            $response = $this->http->request('POST', $this->endpoint('chat/completions'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.config('services.openai.api_key'),
                ],
                'json' => $payload,
                'timeout' => 10,
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return $data['choices'][0]['message']['content'] ?? null;
        } catch (GuzzleException $exception) {
            report($exception);

            return null;
        }
    }

    public function simplePrompt(string $prompt, array $options = []): ?string
    {
        return $this->chat([
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => $prompt],
        ], $options);
    }

    public function suggestCategory(string $description, array $choices): ?string
    {
        $prompt = "Anda adalah akuntan desa. Pilih satu kategori dari daftar berikut yang paling cocok untuk deskripsi.\n".
            "Deskripsi: {$description}\n".
            "Kategori tersedia: ".implode(', ', $choices)."\n".
            "Balas hanya dengan nama kategori.";

        return $this->simplePrompt($prompt, ['temperature' => 0.3]);
    }

    public function summarizeReport(string $summaryPrompt): ?string
    {
        return $this->simplePrompt($summaryPrompt, ['temperature' => 0.4, 'max_tokens' => 200]);
    }
}
