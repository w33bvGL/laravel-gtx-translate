<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Services;

use Anidzen\GoogleTranslateScraper\Traits\HasDecodeUnicode;
use Anidzen\GoogleTranslateScraper\Traits\HasHandleHttpRequestErrors;
use Anidzen\GoogleTranslateScraper\Traits\HasSlugTrait;
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
