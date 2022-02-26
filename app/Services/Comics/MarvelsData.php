<?php

namespace App\Services\Comics;

use Cache;
use Http;
use Illuminate\Support\Facades\Redis;
use stdClass;

/**
 * Classe responsável por lidar com os dados das HQs da Marvel
 *
 * @author Gabriel Matheus Silva <gabrielmath@hotmail.com>
 */
class MarvelsData
{
    private Comics $comicsUrl;

    public function __construct(Comics $comicsUrl)
    {
        $this->comicsUrl = $comicsUrl;
    }

    /**
     * Retorna todos os dados em formato de **objeto** da URL principal que a API de Marvel disponibiliza
     *
     * @return stdClass
     */
    public function getAllData(): stdClass
    {
        return $this->consultCache("dataAll", $this->comicsUrl);
    }

    /**
     * Retornaa a atribuição da marca em relação ao dados da API
     * _(recupera o dado para o programador inserir no rodapé do site)_
     *
     * @param bool $html default: true
     * @return string
     */
    public function comicAttribution($html = true): string
    {
        if ($html) {
            return $this->getAllData()->attributionHTML;
        }
        return $this->getAllData()->attributionText;
    }

    /**
     * Retorna um **array de objetos** com a listagem das HQs
     *
     * @return array
     */
    public function arrayObjectListComics(): array
    {
        return $this->getAllData()->data->results;
    }

    /**
     * Retorna um **objeto** com os dados de **somente uma HQ**
     *
     * @param int $idComic
     * @return stdClass
     */
    public function selectedComic(int $idComic): stdClass
    {
        $urlComic = $this->comicsUrl->comicSingleUrl($idComic);
        $dataComic = $this->consultCache("comic-{$idComic}", $urlComic);

        return $dataComic;
    }

    /**
     * Consulta o **cache do Redis** e o retorna no formato de **objeto**
     *
     * @param string $name
     * @param string $url
     * @return stdClass
     */
    public function consultCache(string $name, string $url): stdClass
    {
        if (Redis::get($name)) {
            return json_decode(Redis::get($name));
        }

        return $this->createCache($name, $url);
    }

    /**
     *
     *
     * @param string $name
     * @param string $url
     * @return stdClass
     */
    private function createCache(string $name, string $url): stdClass
    {
        $data = Http::get($url)->body();
//        Cache::put($name, $data, 180);
        Redis::set($name, $data, 108000);

        return json_decode(Redis::get($name));
    }
}
