<?php

namespace App\Service\Url;

class UrlService
{
    /**
     * @param string $chicagoApiUrl
     */
    public function __construct(
        private readonly string $chicagoApiUrl
    ) {
    }

    /**
     * @return string
     */
    public function getChicagoApiUrl(): string
    {
        return $this->chicagoApiUrl;
    }
}
