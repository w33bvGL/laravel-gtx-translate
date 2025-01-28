<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Traits;

trait HasSlugTrait
{
    protected function generateSlug(string $slug): string
    {

        $slug = str_replace(',', '', strtolower($slug));
        $slug = str_replace([' ', '_'], '-', $slug);

        return trim($slug);
    }
}
