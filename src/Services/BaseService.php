<?php

declare(strict_types=1);

namespace AniMik\MalCrawler\Services;

use AniMik\MalCrawler\Traits\HasDecodeUnicode;
use AniMik\MalCrawler\Traits\HasHandleHttpRequestErrors;
use AniMik\MalCrawler\Traits\HasSlugTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseService
{
    use hasDecodeUnicode, HasHandleHttpRequestErrors, hasSlugTrait;

    protected HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }
}
