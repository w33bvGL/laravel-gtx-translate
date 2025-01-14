<?php

namespace AniMik\MalCrawler\Traits;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait HasHandleHttpRequestErrors
{
    /**
     * Этот метод выполняет GET-запрос по указанному URL и обрабатывает ошибки,
     * которые могут возникнуть в процессе выполнения запроса. В случае ошибки
     * возвращается `null`. В случае успешного запроса возвращается содержимое ответа.
     *
     * This method performs a GET request to the specified URL and handles any errors
     * that may occur during the request. If an error occurs, it returns `null`.
     * In case of a successful request, it returns the response content.
     */
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
