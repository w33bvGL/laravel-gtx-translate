<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Services\Text;

use Anidzen\GoogleTranslateScraper\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;

class TextTranslate extends BaseService
{
    public function generateUrl(string $sourceLanguage, string $targetLanguage, string $text): string
    {
        $encodedText = urlencode($text);

        return config('googleTranslateScraper.base_url')."/?sl={$sourceLanguage}&tl={$targetLanguage}&text={$encodedText}&op=translate";
    }

    public function translate(string $sourceLanguage, string $targetLanguage, string $text): JsonResponse
    {
        $url = $this->generateUrl($sourceLanguage, $targetLanguage, $text);

        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([]);
        }

        $crawler = new Crawler($content);

        $parsedText = $crawler->filter('span[jsname="W297wb"]')->each(function (Crawler $node) {
            $translatedText = $node->text();
            $this->logger->info("Translated text: {$translatedText}");
        });

        return response()->json([
            'status' => 'success',
            'data' => $parsedText,
        ]);
    }
}
