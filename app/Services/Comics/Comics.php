<?php

namespace App\Services\Comics;

interface Comics
{
    /**
     * Retorna a URL principal da API
     *
     * @return string
     */
    public function baseUrl(): string;

    /**
     * Retorna a URL de uma **HQ Específica**
     *
     * @param int $idComic
     * @return string
     */
    public function comicUrl(int $idComic): string;
}
