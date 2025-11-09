<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class GeminiService
{
    private ClientInterface $http;

    public function __construct(ClientInterface $http = null)
    {
        $this->http = $http ?? new Client();
    }

    private function endpoint(string $path): string
    {
        return sprintf(
            '%s/%s?key=%s',
            rtrim(config('services.gemini.base_url'), '/'),
            ltrim($path, '/'),
            config('services.gemini.api_key')
        );
    }

    /**
     * Kirim prompt generik ke Gemini dan kembalikan teks tanggapan pertama.
     */
    public function generate(array $payload): ?string
    {
        try {
            $response = $this->http->request('POST', $this->endpoint('models/'.config('services.gemini.model').':generateContent'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 10,
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        } catch (GuzzleException $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * Convenience method untuk kasus prompt sederhana.
     */
    public function simplePrompt(string $text): ?string
    {
        return $this->generate([
            'contents' => [
                [
                    'parts' => [
                        ['text' => $text],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.5,
                'maxOutputTokens' => 256,
            ],
        ]);
    }

    /**
     * Contoh khusus: sarankan kategori berdasarkan deskripsi.
     */
    public function suggestCategory(string $description, array $choices): ?string
    {
        $prompt = "Anda adalah akuntan desa. Pilih satu kategori dari daftar berikut yang paling cocok untuk deskripsi.\n".
            "Deskripsi: {$description}\n".
            "Kategori tersedia: ".implode(', ', $choices)."\n".
            "Balas hanya dengan nama kategori.";

        return $this->simplePrompt($prompt);
    }

    /**
     * Contoh khusus: buat ringkasan laporan.
     */
    public function summarizeReport(string $summaryPrompt): ?string
    {
        return $this->simplePrompt($summaryPrompt);
    }
}
