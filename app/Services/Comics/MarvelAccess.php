<?php

declare(strict_types=1);

namespace App\Services\Comics;

use Carbon\Carbon;

/**
 * Classe responsável por lidar com as URLs (com os dados de autenticação) da API
 *
 * @author Gabriel Matheus Silva <gabrielmath@hotmail.com>
 */
class MarvelAccess implements AccessComics
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://gateway.marvel.com/v1/public/comics';
    }

    public function __toString(): string
    {
        return $this->baseComicUrlAPI();
    }

    /**
     * Retorna a URL principal da API
     *
     * @return string
     */
    public function baseComicUrlAPI(): string
    {
        return $this->baseUrl . $this->authenticationHashUrl();
    }

    /**
     * Retorna a URL de uma **HQ Específica**
     *
     * @param int $idComic
     * @return string
     */
    public function singleComicUrlAPI(int $idComic): string
    {
        return $this->baseUrl . '/' . $idComic . $this->authenticationHashUrl();
    }

    /**
     * Retorna a string necessário para a autenticação das URLs da API
     *
     * @return string
     */
    private function authenticationHashUrl(): string
    {
        $ts = Carbon::now();
        $publicKey = config('services.marvel.public_key');
        $privateKey = config('services.marvel.private_key');
        $hash = md5($ts . $privateKey . $publicKey);

        return "?ts={$ts}&apikey={$publicKey}&hash={$hash}";
    }
}
