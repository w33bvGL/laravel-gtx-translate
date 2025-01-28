<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Services\Text;

use Anidzen\GoogleTranslateScraper\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;

class TextTranslate extends BaseService
{
    public function ScrapeText(): JsonResponse
    {
        $parsedText = 'Example parsed text';

        return response()->json([
            'status' => 'success',
            'data' => $parsedText,
        ]);
    }

    //    public function crawlStudioInformation(int $malId): JsonResponse
    //    {
    //        $baseUrl   = config('malCrawler.base_url');
    //        $genresUrl = config('malCrawler.studio_url');
    //        $url       = $baseUrl.$genresUrl.'/'.$malId;
    //
    //        $content = $this->handleHttpRequestErrors($this->httpClient, $url);
    //
    //        if (! $content) {
    //            return response()->json([]);
    //        }
    //
    //        $crawler = new Crawler($content);
    //
    //        $title = $crawler->filter('.title-name')->count() > 0
    //          ? $crawler->filter('.title-name')->text()
    //          : null;
    //
    //        $slug = $this->generateSlug($title);
    //
    //        $image = $crawler->filter('#content .content-left .logo img')->count() > 0
    //          ? $crawler->filter('#content .content-left .logo img')
    //          : null;
    //
    //        $imageSrc = $image?->attr('data-src') ?? $image?->attr('src') ?? 'https://cdn.myanimelist.net/images/company_no_picture.png';
    //
    //        if ($imageSrc === config('malCrawler.no_studio_picture')) {
    //            $imageSrc = null;
    //        }
    //
    //        $imageAlt = $image?->attr('alt');
    //
    //        $infoNode = $crawler->filter('#content .content-left .mb16')->eq(1);
    //
    //        $info = [];
    //
    //        if ($infoNode->count() > 0) {
    //            $infoNode->filter('.spaceit_pad')->each(function ($item) use (&$info) {
    //                $label = $item->filter('span.dark_text')->count() > 0
    //                  ? trim($item->filter('span.dark_text')->text(), ':')
    //                  : null;
    //
    //                $value = $item->filter('span.dark_text')->count() > 0
    //                  ? trim(str_replace($item->filter('span.dark_text')->text(), '', $item->text()))
    //                  : $item->text();
    //
    //                if ($label) {
    //                    $info[strtolower(str_replace(' ', '_', $label))] = trim($value);
    //                }
    //            });
    //        }
    //
    //        $japanese = $info['japanese'] ?? null;
    //        if ($japanese) {
    //            $info['japanese'] = $this->decodeUnicode($japanese);
    //        }
    //
    //        $description = null;
    //        $crawler->filter('#content .content-left .mb16')->eq(1)->filter('.spaceit_pad')->each(function ($item) use (&$description) {
    //            $text = trim($item->text());
    //
    //            if ($item->filter('span.dark_text')->count() === 0 && strlen($text) > 10) {
    //                $description = $text;
    //            }
    //        });
    //
    //        $info['description'] = $description ?? null;
    //
    //        return response()->json([
    //            'malId' => $malId,
    //            'slug' => $slug,
    //            'title' => $title,
    //            'image' => [
    //                'src' => $imageSrc,
    //                'alt' => $imageAlt,
    //            ],
    //            'info' => $info,
    //        ]);
    //    }
}
