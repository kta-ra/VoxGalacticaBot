<?php namespace Ednews;

/**
 * Класс-обёртка для curl
 */
class CurlWrapper {
    /** @var CurlHandle */
    private $handle;
    
    /** @var array основные опции запроса */
    private $options = [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false
    ];
    
    /** @var array опции post запроса */
    private $postOptions = [
        CURLOPT_POST => 1
    ];
    
    /** @var array данные post запроса */
    private $postData = [];
    
    /** @var array файлы для post запроса */
    private $files = [];
    
    /** @var array дополнительные заголовки */
    private $headers = [];
    
    /** @var string сообщение об ошибке */
    private $errorMessage = '';
    
    /**
    * Конструктор
    * Иницализация сеанса cURL
    * @return CurlWrapper
    */
    public function __construct() {
        $this->handle = curl_init();
    }
    
    /**
    * Деструктор
    * Закрытие сеанса cURL
    * @return CurlWrapper
    */
    public function __destruct() {
        curl_close($this->handle);
    }
    
    /**
    * Отправка get запроса
    * @param string $url
    * @return string|false
    */
    public function sendGetRequest(string $url) {
        $this->setOptions($url);
        return $this->run();
    }
    
    /**
    * Отправка post запроса
    * @param string $url
    * @return string|false
    */
    public function sendPostRequest(string $url, array $postData = [], array $files = []) {
        $this->postData = $postData;
        $this->files = $files;
        $this->setOptions($url, true);
        return $this->run();
    }
    
    /**
    * Установка параметров curl
    * @param string $url
    * @param bool $isPost
    * @return void
    */
    private function setOptions(string $url, bool $isPost = false) {
        curl_setopt_array($this->handle, $this->options);
        if (!empty($this->headers)) {
            curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        }
        if ($isPost) {
            curl_setopt_array($this->handle, $this->postOptions);
            if (!empty($this->postData)) {
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, $this->postData);
            }
        }
    }
    
    /**
    * Выполнение запроса
    * @return string|false
    */
    private function run() {
        $result = curl_exec($this->handle);
        if (curl_errno($this->handle)) {
            $this->errorMessage = curl_error($ch);
        }
        return $result;
    }
}