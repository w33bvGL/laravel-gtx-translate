<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Traits;

trait HasDecodeUnicode
{
    protected function decodeUnicode(string $str): string
    {
        return json_decode('"'.$str.'"');
    }
}
