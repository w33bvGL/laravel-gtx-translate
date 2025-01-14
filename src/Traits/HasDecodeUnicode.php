<?php

namespace AniMik\MalCrawler\Traits;

trait HasDecodeUnicode
{
    /**
     * Декодирует строку, экранированную в Unicode.
     *
     * Decodes a Unicode-escaped string.
     */
    protected function decodeUnicode(string $str): string
    {
        return json_decode('"'.$str.'"');
    }
}
