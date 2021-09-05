<?php namespace Ednews;

/**
 * Основной класс приложения
 */
class App {
    /** @var config object */
    private $config;
    
    /** @var state object */
    private $state;
    
    /** @var CurlWrapper */
    private $curl;
    
    /** @var Logger */
    private $logger;
    
    /** @var EdApi */
    private $edApi;
    
    /** @var DiscordApi */
    private $discordApi;
    
    /**
    * Конструктор
    * Загрузка конфига и состояния приложения
    * Иницализация объектов Logger, EdApi, DiscordApi
    * @return App
    */
    public function __construct() {
        $this->getConfig();
        $this->getState();
        $this->curl = new CurlWrapper();
        $this->Logger = new Logger();
        $this->EdApi = new EdApi($this->curl);
        //$this->discordApi = new DiscordApi();
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
            //
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
        echo '<pre>';
        var_dump($this->EdApi->getArticles());
    }
}