<?php

namespace App\Service\ChicagoArtApi;

use App\Service\Url\UrlService;
use Exception;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ChicagoArtApiService
{
    /**
     * @param UrlService $urlService
     * @param JsonDecode $jsonDecode
     * @param ChicagoArtApiClientService $chicagoArtApiClientService
     */
    public function __construct(
        private readonly UrlService $urlService,
        private readonly JsonDecode $jsonDecode,
        private readonly ChicagoArtApiClientService $chicagoArtApiClientService
    ) {
    }


    /**
     * @param int $id
     * @return array|string[]
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getById(int $id): array
    {
        try {
            $url = $this->urlService->getChicagoApiUrl() . "artworks/" . $id
                . "?fields=id,title,artist_title,thumbnail";
            $content = $this->chicagoArtApiClientService->get($url);
            $decodedContent = $this->jsonDecode->decode($content, JsonEncoder::FORMAT);
            return (array)$decodedContent->data;
        } catch (Exception $exception) {
            return $this->getExceptionData($exception);
        }
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array|array[]|string[]
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getByPage(int $page, int $limit): array
    {
        try {
            $url = $this->urlService->getChicagoApiUrl() . "artworks/?page=" . $page . "&limit=" . $limit
                . "&fields=id,title,artist_title,thumbnail";
            $content = $this->chicagoArtApiClientService->get($url);
            $decodedContent = $this->jsonDecode->decode($content, JsonEncoder::FORMAT);
            return ["pagination" => (array)$decodedContent->pagination, "data" => (array)$decodedContent->data];
        } catch (Exception $exception) {
            return $this->getExceptionData($exception);
        }
    }

    /**
     * @param $postData
     * @return array|int
     */
    public function parsePostData($postData): int|array
    {
        try {
            $decodedJson = (array)$this->jsonDecode->decode($postData, JsonEncoder::FORMAT);
            if (empty($decodedJson["id"])) {
                return ["status" => 400, "Id not found in POST pody."];
            }
            return $decodedJson["id"];
        } catch (Exception $exception) {
            return ["status" => 400, "message" => "Malformed POST body. It is not a JSON. " . $exception->getMessage()];
        }
    }

    /**
     * @param Exception $exception
     * @return array
     */
    private function getExceptionData(Exception $exception): array
    {
        return ["status" => $exception->getCode(), "message" => $exception->getMessage()];
    }
}
