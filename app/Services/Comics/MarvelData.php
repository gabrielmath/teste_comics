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
class MarvelData
{
    private AccessComics $accessComics;

    public function __construct(AccessComics $accessComics)
    {
        $this->accessComics = $accessComics;
    }

    /**
     * Retorna todos os dados em formato de **objeto** da URL principal que a API de Marvel disponibiliza
     *
     * @return stdClass
     */
    public function all(): stdClass
    {
        return $this->consult("dataAll", $this->accessComics);
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
            return $this->all()->attributionHTML;
        }
        return $this->all()->attributionText;
    }

    /**
     * Retorna um **array de objetos** com a listagem das HQs
     *
     * @return array
     */
    public function arrayListComics(): array
    {
        return $this->all()->data->results;
    }

    /**
     * Retorna um **objeto** com os dados de **somente uma HQ**
     *
     * @param int $idComic
     * @return stdClass
     */
    public function selectedComic(int $idComic): stdClass
    {
        $urlComic = $this->accessComics->singleComicUrlAPI($idComic);
        $comic = $this->consult("comic-{$idComic}", $urlComic);

        return $comic;
    }

    /**
     * Retorna um link (texto/string) do endereço da imagem da HQ
     *
     * @param int $idComic
     * @return string
     */
    public function imageComic(int $idComic): string
    {
        $comic = $this->selectedComic($idComic)->data->results[0];

        if (!$this->imageExists($comic->images)) {
            return asset('images/image_not_found.png');
        }


        return $this->mountedImage($comic->images[0]);
    }

    /**
     * Retorna o endereço da imagem da HQ baseado no seu link e extensão de arquivo
     *
     * @param object $contentImage
     * @return string
     */
    public function mountedImage(object $contentImage): string
    {
        return $contentImage->path . '.' . $contentImage->extension;
    }


    /**
     * Consulta o **cache do Redis** e o retorna no formato de **objeto**
     *
     * @param string $name
     * @param string $url
     * @return stdClass
     */
    public function consult(string $name, string $url): stdClass
    {
        if (Redis::get($name)) {
            return json_decode(Redis::get($name));
        }

        return $this->createCache($name, $url);
    }

    /**
     * Cria o cache e retorna um **objeto stdClass** do mesmo
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

    /**
     * Verificar se existe alguma imagem da HQ
     *
     * @param array $arrayImageComic
     * @return bool
     */
    private function imageExists(array $arrayImageComic)
    {
        if (count($arrayImageComic) > 0) {
            return true;
        }

        return false;
    }
}
