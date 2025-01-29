<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Services\Text;

use Anidzen\GoogleTranslateScraper\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Anidzen\GoogleTranslateScraper\Exceptions\TextTranslationException; // Подключаем исключение

class TextTranslateHideApi extends BaseService
{
    private function generateUrl(string $sourceLanguage, string $targetLanguage, string $text): string
    {
        $encodedText = urlencode($text);

        return config('googleTranslateScraper.hidden_api_base_url')."/translate_a/single?client=gtx&sl={$sourceLanguage}&tl={$targetLanguage}&dt=t&q={$encodedText}";
    }

    /**
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @param string $text
     * @return JsonResponse
     */
    public function translate(string $sourceLanguage, string $targetLanguage, string $text): JsonResponse
    {
        try {
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

            if (! $response) {
                throw new TextTranslationException('Translation failed: No response from API.');
            }

            $data = json_decode($response, true);
            if (! isset($data[0]) || ! is_array($data[0])) {
                throw new TextTranslationException('Translation failed: Invalid response format.');
            }

            $translatedText = implode('', array_column($data[0], 0));

            return response()->json([
                'status' => 'success',
                'sourceLanguage' => $sourceLanguage,
                'targetLanguage' => $targetLanguage,
                'data' => $this->decodeUnicode($translatedText),
            ], JSON_UNESCAPED_UNICODE);

        } catch (TextTranslationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'An unexpected error occurred.']);
        }
    }

    private function validateInput(string $sourceLanguage, string $targetLanguage, string $text): void
    {
        $textMaxLength = config('googleTranslateScraper.text_max_length');
        $textLength    = strlen($text);

        if (empty($text)) {
            throw new \InvalidArgumentException('Text cannot be empty.');
        }

        if (empty($sourceLanguage) || empty($targetLanguage)) {
            throw new \InvalidArgumentException('Source and target languages are required.');
        }

        if ($textLength > $textMaxLength) {
            throw new \InvalidArgumentException("Text cannot be longer than '{$textMaxLength}' characters. Your text length '{$textLength}' characters.");
        }

        if ($sourceLanguage === $targetLanguage) {
            throw new \InvalidArgumentException('Source language and target language cannot be the same.');
        }

        if (! $this->isSupportedLanguage($sourceLanguage)) {
            throw new \InvalidArgumentException("Source language '{$sourceLanguage}' is not supported.");
        }

        if (! $this->isSupportedLanguage($targetLanguage)) {
            throw new \InvalidArgumentException("Target language '{$targetLanguage}' is not supported.");
        }
    }

    private function getRandomUserAgentHeader(): array
    {
        $userAgents = config('googleTranslateScraper.user_agents');

        return ['User-Agent' => $userAgents[array_rand($userAgents)]];
    }
}
