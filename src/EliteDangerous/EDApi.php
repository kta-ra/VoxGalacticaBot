<?php namespace KtaraDev\VoxGalacticaBot\EliteDangerous;

/**
 * Работа с Elite:Dangerous API
 */
class EDApi {
    const GET_ARTICLES_URL = 'https://cms.zaonce.net/ru-RU/jsonapi/node/galnet_article?&sort=-published_at&page[offset]=0&page[limit]=10';
    const IMAGES_BASE_URL = 'https://hosting.zaonce.net/elite-dangerous/galnet/';

    public function __construct() {}

    public function getArticlesData() : array
    {
        $rawData = file_get_contents(self::GET_ARTICLES_URL);
        if ($rawData) {
            $data = json_decode($rawData, true);
            
            if (is_array($data)) {
                
                if (isset($data['data']) && !empty($data['data'])) {
                    $preparedData = $this->prepareArticles($data['data']);
                    $result = ['success' => true, 'data' => $preparedData];
                } else {
                    $result = ['success' => false, 'message' => 'content error ' . json_encode($data)];
                }

            } else {
                $result = ['success' => false, 'message' => 'json error ' . json_last_error_msg()];
            }

        } else {
            $result = ['success' => false, 'message' => 'get content error'];
        }

        return $result;
    }

    private function prepareArticles(array $articlesData): array
    {
        $resultArticles = [];
        foreach ($articlesData as $articleData) {
            $resultArticles[] = $this->prepareArticle($articleData);
        }
        return $resultArticles;
    }

    private function prepareArticle(array $articleData) : array
    {
        $images = explode(',', $articleData['attributes']['field_galnet_image']);

        $resultArticleData = [
            'timestamp' => strtotime($articleData['attributes']['created']),
            'date' => $articleData['attributes']['created'],
            'content' => [
                'date' => $articleData['attributes']['field_galnet_date'],
                'title' => $articleData['attributes']['title'],
                'body' => $articleData['attributes']['body']['processed'],
                'image' => self::IMAGES_BASE_URL . $images[0] . '.png'
            ]
        ];

        return $resultArticleData;
    }
}
