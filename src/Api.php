<?php namespace Ednews;

/**
 * Базовый класс для работы с api
 */
abstract class Api {
    /**
     * @var CurlWrapper
     */
    protected $curl;
    
    /**
     * @var string Базовый путь api
     */
    protected $baseUrl;
    
    /**
     * @var array Данные запроса
     */
    protected $requestData = [];
    
    /**
    * Конструктор
    * @param CurlWrapper $curl
    * @return Api
    */
    public function __construct(CurlWrapper $curl) {
        $this->curl = $curl;
    }    
}