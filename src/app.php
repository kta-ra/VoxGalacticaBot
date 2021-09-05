<?php namespace Ednews;

use Api\EdApi as EdApi;
use Api\DiscordApi as DiscordApi;

/**
 * Основной класс приложения
 */
class App {
    /** @var config object */
    private $config;
    
    /** @var state object */
    private $state;
    
    /** @var Logger */
    private $Logger;
    
    /** @var EdApi */
    private $EdApi;
    
    /** @var DiscordApi */
    private $DiscordApi;
    
    /**
    * Конструктор
    * Загрузка конфига и состояния приложения
    * Иницализация объектов Logger, EdApi, DiscordApi
    * @return App
    */
    public function __construct() {
        $this->getConfig();
        $this->getState();
        $this->Logger = new Logger();
        $this->EdApi = new EdApi();
        $this->DiscordApi = new DiscordApi($this->config->token);
    }
    
    /**
    * Получение информации о состоянии
    * @return string|false
    */
    public function __get($key) {
        $result = false;
        switch ($key) {
            case 'checked':
                $result = date('d.m.Y H:i:s', $this->state->checked);
            break;
            case 'updated':
                $result = date('d.m.Y H:i:s', $this->state->updated);
            break;
        }
        return $result;
    }
    
    /**
    * Проверка наличия новых статей
    * @return void
    */
    public function checkUpdates() {
        $updates = $this->getUpdates();
        if (!empty($updates)) {
            foreach ($updates as $articleData) {
                if (mb_strlen($articleData['body']) < 20) continue;
                $message = $this->prepareArticle($articleData['title'], $articleData['date'], $articleData['body']);
                $discordResult = $this->DiscordApi->postMessage($this->config->channelId, $message);
                $discordResult = json_decode($discordResult, true);
                if (isset($discordResult['id'])) {
                    $messageId = $discordResult['id'];
                    $crosspostResult = $this->DiscordApi->crosspostMessage($this->config->channelId, $messageId);
                }
            }
        }
        $this->state->checked = time();
        $this->updateState();
    }
    
    /**
    * Получение конфига
    * @return void
    */
    private function getConfig() {
        $this->config = json_decode(file_get_contents(DATA . 'config.json'));
    }
    
    /**
    * Получение информации о состоянии приложения
    * @return void
    */
    private function getState() {
        $this->state = json_decode(file_get_contents(DATA . 'state.json'));
    }
    
    /**
    * Обновление информации о состоянии приложения
    * @return void
    */
    private function updateState() {
        file_put_contents(DATA . 'state.json', json_encode($this->state, JSON_PRETTY_PRINT));
    }
    
    /**
    * Получение новых статей
    * @return array
    */
    private function getUpdates() {
        $result = [];
        $articlesFullData = json_decode($this->EdApi->getArticles(), true);
        $articles = array_reverse($articlesFullData['data']);
        foreach ($articles as $article) {
            $createdAt = strtotime($article['attributes']['created']);
            $isRus = preg_match('/\p{Cyrillic}/u', $article['attributes']['body']['value']);
            if ($createdAt > $this->state->updated && $isRus) {
                $articleData = [
                    'title' => $article['attributes']['title'],
                    'date' => $article['attributes']['field_galnet_date'],
                    'body' => $article['attributes']['body']['processed']
                ];
                $pic = explode(',', $article['attributes']['field_galnet_image']);
                $articleData['pic'] = 'https://hosting.zaonce.net/elite-dangerous/galnet/' . $pic[0] . '.png';
                $result[] = $articleData;
                $this->state->updated = $createdAt;
            }
        }
        return $result;
    }
    
    /**
    * Оформление статьи для discord
    * @param string $title
    * @param string $date Дата новости
    * @param string $body Тело статьи
    * @return string
    */
    private function prepareArticle(string $title, string $date, string $body): string {
    $dateFormat = [
        'JAN' => 'Янв',
        'FEB' => 'Фев',
        'MAR' => 'Мар',
        'APR' => 'Апр',
        'MAY' => 'Май',
        'JUN' => 'Июн',
        'JUL' => 'Июл',
        'AUG' => 'Авг',
        'SEP' => 'Сен',
        'OCT' => 'Окт',
        'NOV' => 'Ноя',
        'DEC' => 'Дек'
    ];
    $date = strtr($date, $dateFormat);
    $body = htmlspecialchars_decode(strip_tags(str_replace('<br />', "\n", $body)));
    return "```fix\n{$title}\n{$date}\n```\n```\n{$body}⠀```\n";
}
}