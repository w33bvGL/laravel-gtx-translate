<?php

declare(strict_types=1);

namespace AniMik\MalCrawler\Traits;

trait HasSlugTrait
{
    /**
     * Генерирует слуг (переводит в нижний регистр, заменяет пробелы на дефисы).
     *
     * Converts the string to lowercase and replaces spaces/underscores with hyphens.
     */
    protected function generateSlug(string $slug): string
    {

        $slug = str_replace(',', '', strtolower($slug));
        $slug = str_replace([' ', '_'], '-', $slug);

        return trim($slug);
    }
}
