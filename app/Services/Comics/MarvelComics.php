<?php

declare(strict_types=1);

namespace App\Services\Comics;

use Carbon\Carbon;

/**
 * Classe responsável por lidar com as URLs (com os dados de autenticação) da API
 *
 * @author Gabriel Matheus Silva <gabrielmath@hotmail.com>
 */
class MarvelComics implements Comics
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://gateway.marvel.com/v1/public/comics';
    }

    public function __toString(): string
    {
        return $this->baseUrl();
    }

    /**
     * Retorna a URL principal da API
     *
     * @return string
     */
    public function baseUrl(): string
    {
        return $this->baseUrl . $this->hashUrl();
    }

    /**
     * Retorna a URL de uma **HQ Específica**
     *
     * @param int $idComic
     * @return string
     */
    public function comicSingleUrl(int $idComic): string
    {
        return $this->baseUrl . '/' . $idComic . $this->hashUrl();
    }

    /**
     * Retorna a string necessário para a autenticação das URLs da API
     *
     * @return string
     */
    private function hashUrl(): string
    {
        $ts = Carbon::now();
        $publicKey = config('services.marvel.public_key');
        $privateKey = config('services.marvel.private_key');
        $hash = md5($ts . $privateKey . $publicKey);

        return "?ts={$ts}&apikey={$publicKey}&hash={$hash}";
    }
}
