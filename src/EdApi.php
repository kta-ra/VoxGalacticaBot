<?php namespace Ednews;

class EdApi extends Api {
    /**
     * @var string Базовый путь api
     */
    protected $baseUrl = 'https://cms.zaonce.net/ru-RU/jsonapi/node/galnet_article';
    
    /**
     * @var array Данные запроса
     */
    protected $requestData = [
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
        $result = [];
        if (!is_null($limit)) $this->requestData['page']['limit'] = $limit;
        $url = $this->buildRequestUrl();
        $resultData = $this->curl->sendGetRequest($url);
        if ($resultData['success']) $resultData['result'] = json_decode($resultData['result']);
        return $resultData;
    }
    
    private function buildRequestUrl() {
        return $this->baseUrl . '?' . http_build_query($this->requestData);
    }
}