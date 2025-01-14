<?php

namespace AniMik\MalCrawler\Services;

use AniMik\MalCrawler\Traits\HasDecodeUnicode;
use AniMik\MalCrawler\Traits\HasHandleHttpRequestErrors;
use AniMik\MalCrawler\Traits\HasSlugTrait;

abstract class BaseService
{
    use hasDecodeUnicode, HasHandleHttpRequestErrors, hasSlugTrait;
}
