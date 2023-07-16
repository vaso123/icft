<?php

namespace App\Service\ChicagoArtApi;

use App\Exception\ChicagoArtException;
use Exception;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChicagoArtApiClientService
{
    /**
     * @param HttpClientInterface $curlHttpClient
     */
    public function __construct(
        private readonly HttpClientInterface $curlHttpClient
    ) {
    }

    /**
     * @param $url
     * @return string
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function get($url): string
    {
        try {
            $options["headers"] = $this->getHeaders();
            $response = $this->curlHttpClient->request(
                "GET",
                $url,
                $options
            );
            return $response->getContent();
        } catch (Exception $exception) {
            throw new ChicagoArtException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @return string[]
     */
    private function getHeaders(): array
    {
        return ["AIC-User-Agent" => "Test User (noreply@example.com)"];
    }
}
