<?php namespace Ednews;

use Api\ApiRequest as Request;

class EdApi {
    /**
     * @var string Базовый путь api
     */
    private $baseUrl = 'https://cms.zaonce.net/ru-RU/jsonapi/node/galnet_article/';
    
    /**
     * @var array Данные запроса
     */
    private $requestData = [
        'sort' => '-published_at',
        'page' => [
            'offset' => 0,
            'limit' => 10
        ],
    ];
    
    /**
     * Получение данных последних статей
     * @var int|null $limit
     * @return array
     */
    public function getArticles(int $limit = NULL) {
        if (!is_null($limit)) $this->requestData['page']['limit'] = $limit;
        $url = $this->buildRequestUrl();
        $request = new Request();
        $request->setMode('get')
                ->setUrl($url)
                ->run();
        if ($request->isSuccess()) {
            $result = json_decode($request->getResult(), true);
            $result['data'] = array_reverse($result['data']); //статьи от старых к новым
            return $result;
        } else {
            return $request->getError();
        }
    }
    
    /**
     * Построение url для запроса
     * @return string
     */
    private function buildRequestUrl() {
        return $this->baseUrl . '?' . http_build_query($this->requestData);
    }
}