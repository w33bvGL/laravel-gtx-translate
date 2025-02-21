<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Services\Text;

use Anidzen\GoogleTranslateScraper\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Random\RandomException;

class TextTranslateHideApiService extends BaseService
{
    private function generateUrl(string $sourceLanguage, string $targetLanguage, string $text): string
    {
        $encodedText = urlencode($text);

        return config('googleTranslateScraper.hidden_api_base_url')."/translate_a/single?client=gtx&sl={$sourceLanguage}&tl={$targetLanguage}&dt=t&q={$encodedText}";
    }

    private function extractTranslations(array $responseData): array
    {
        $translations = [];

        if (isset($responseData[0]) && is_array($responseData[0])) {
            foreach ($responseData[0] as $translationItem) {
                if (isset($translationItem[0])) {
                    $translations[] = $translationItem[0];
                }
            }
        }

        return $translations;
    }

    /**
     * @throws RandomException
     */
    public function translate(string $sourceLanguage, string $targetLanguage, string $text): JsonResponse
    {
        $validationResult = $this->validateInput($sourceLanguage, $targetLanguage, $text);
        if ($validationResult) {
            return response()->json($validationResult);
        }

        $url = $this->generateUrl($sourceLanguage, $targetLanguage, $text);

        usleep(random_int(config('googleTranslateScraper.timeout_min'), config('googleTranslateScraper.timeout_max')));

        $headers = $this->getRandomUserAgentHeader();

        $options = [
            'headers' => $headers,
        ];

        if ($proxy = config('googleTranslateScraper.proxy')) {
            $options['proxy'] = $proxy;
        }

        $response = $this->handleHttpRequestErrors($this->httpClient, $url, $options);

        $data = json_decode($response, true);

        $translatedText = trim(implode(' ', array_column($data[0], 0)));
        $translations   = $this->extractTranslations($data);

        return response()->json([
            'status' => 'success',
            'sourceLanguage' => $sourceLanguage,
            'targetLanguage' => $targetLanguage,
            'data' => implode($translations),
            'translatedText' => $translatedText,
        ]);
    }

    private function validateInput(string $sourceLanguage, string $targetLanguage, string $text): ?array
    {
        $textMaxLength = config('googleTranslateScraper.text_max_length');
        $textLength    = strlen($text);

        if (empty($text)) {
            return ['status' => 'error', 'message' => 'Text cannot be empty.'];
        }

        if (empty($sourceLanguage) || empty($targetLanguage)) {
            return ['status' => 'error', 'message' => 'Source and target languages are required.'];
        }

        if ($textLength > $textMaxLength) {
            return ['status' => 'error', 'message' => "Text cannot be longer than '{$textMaxLength}' characters. Your text length '{$textLength}' characters."];
        }

        if ($sourceLanguage === $targetLanguage) {
            return ['status' => 'error', 'message' => 'Source language and target language cannot be the same.'];
        }

        if (! $this->isSupportedLanguage($sourceLanguage)) {
            return ['status' => 'error', 'message' => "Source language '{$sourceLanguage}' is not supported."];
        }

        if (! $this->isSupportedLanguage($targetLanguage)) {
            return ['status' => 'error', 'message' => "Target language '{$targetLanguage}' is not supported."];
        }

        return null;
    }

    /**
     * @internal
     */
    private function getRandomUserAgentHeader(): array
    {
        $userAgents = config('googleTranslateScraper.user_agents');

        return ['User-Agent' => $userAgents[array_rand($userAgents)]];
    }
}
