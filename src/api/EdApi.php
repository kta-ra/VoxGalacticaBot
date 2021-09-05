<?php namespace Api;

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
     * @return string
     */
    public function getArticles(int $limit = NULL): string {
        if (!is_null($limit)) $this->requestData['page']['limit'] = $limit;
        $url = $this->buildRequestUrl();
        $request = new ApiRequest();
        $request->setMode('get')
                ->setUrl($url)
                ->run();
        if ($request->isSuccess()) {
            return $request->getResult();
        } else {
            return $request->getError();
        }
    }
    
    /**
     * Построение url для запроса
     * @return string
     */
    private function buildRequestUrl(): string {
        return $this->baseUrl . '?' . http_build_query($this->requestData);
    }
}