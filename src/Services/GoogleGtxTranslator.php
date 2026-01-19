<?php

declare(strict_types=1);

namespace W33bvgl\GtxTranslate\Services;

use Illuminate\Support\Facades\Http;
use Random\RandomException;
use W33bvgl\GtxTranslate\Contracts\TranslatorInterface;
use W33bvgl\GtxTranslate\DTO\TranslationResult;
use W33bvgl\GtxTranslate\Enums\Language;
use W33bvgl\GtxTranslate\Exceptions\TranslationRequestException;

class GoogleGtxTranslator implements TranslatorInterface
{
    protected string $baseUrl;

    protected array $config;

    public function __construct(array $config)
    {
        $this->config  = $config;
        $this->baseUrl = $config['hidden_api_base_url'] ?? 'https://translate.googleapis.com';
    }

    /**
     * @throws RandomException
     * @throws TranslationRequestException
     */
    public function translate(string $text, string|Language $target, string|Language $source = 'auto'): TranslationResult
    {
        // 1. Приводим типы к строкам (String code)
        $targetCode = $target instanceof Language ? $target->value : $target;
        $sourceCode = $source instanceof Language ? $source->value : $source;

        // 2. Валидация
        $this->validateInput($text, $targetCode);

        // 3. Задержка (Anti-bot)
        if ($this->config['enable_delay'] ?? false) {
            usleep(random_int(
                (int) ($this->config['timeout_min'] ?? 500000),
                (int) ($this->config['timeout_max'] ?? 1000000)
            ));
        }

        $url = "{$this->baseUrl}/translate_a/single";

        try {
            $response = Http::withHeaders([
                'User-Agent' => $this->getRandomUserAgent(),
            ])
                ->retry(2, 100)
                ->get($url, [
                    'client' => 'gtx',
                    'sl' => $sourceCode, // ИСПОЛЬЗУЕМ КОД, А НЕ ОБЪЕКТ
                    'tl' => $targetCode, // ИСПОЛЬЗУЕМ КОД, А НЕ ОБЪЕКТ
                    'dt' => 't',
                    'q' => $text,
                ]);

            if ($response->status() !== 200) {
                throw new TranslationRequestException(
                    "Google GTX API Error: {$response->status()}",
                    $response->status()
                );
            }
        } catch (\Exception $e) {
            if ($e instanceof TranslationRequestException) {
                throw $e;
            }

            throw new TranslationRequestException(
                $e->getMessage(),
                (int) $e->getCode()
            );
        }

        $data = $response->json();

        return new TranslationResult(
            originalText: $text,
            translatedText: $this->parseResponse($data),
            sourceLanguage: $data[2] ?? $sourceCode,
            targetLanguage: $targetCode,
        );
    }

    protected function parseResponse(?array $data): string
    {
        $translatedText = '';

        if (isset($data[0]) && is_array($data[0])) {
            foreach ($data[0] as $segment) {
                if (isset($segment[0])) {
                    $translatedText .= (string) $segment[0];
                }
            }
        }

        return $translatedText;
    }

    /**
     * @throws TranslationRequestException
     */
    protected function validateInput(string $text, string $target): void
    {
        if (empty(trim($text))) {
            throw new TranslationRequestException('Text to translate cannot be empty.');
        }

        $maxLength = $this->config['text_max_length'] ?? 5000;
        if (mb_strlen($text) > $maxLength) {
            throw new TranslationRequestException("Text length exceeds limit of {$maxLength}.");
        }
    }

    protected function getRandomUserAgent(): string
    {
        $agents = $this->config['user_agents'] ?? [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ];

        return (string) $agents[array_rand($agents)];
    }
}
