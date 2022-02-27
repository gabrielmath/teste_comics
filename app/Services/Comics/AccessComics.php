<?php

namespace App\Services\Comics;

interface AccessComics
{
    /**
     * Retorna a URL principal da API
     *
     * @return string
     */
    public function baseComicUrlAPI(): string;

    /**
     * Retorna a URL de uma **HQ Específica**
     *
     * @param int $idComic
     * @return string
     */
    public function singleComicUrlAPI(int $idComic): string;
}
