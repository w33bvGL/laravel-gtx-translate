<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Traits;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait HasHandleHttpRequestErrors
{
    protected function handleHttpRequestErrors(HttpClientInterface $httpClient, string $url): ?string
    {
        try {
            $response = $httpClient->request('GET', $url);

            return $response->getContent();
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            return null;
        }
    }
}
