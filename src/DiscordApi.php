<?php namespace Ednews;

class DiscordApi extends Api {
    /**
     * @var string Базовый путь api
     */
    protected $baseUrl = 'https://discord.com/api/';
    
    /**
     * @var array Данные запроса
     */
    protected $requestData = [
        'token' => ''
    ];
    
    /**
    * Конструктор
    * @param CurlWrapper $curl
    * @param string $token токен приложения discord
    * @return Api
    */
    public function __construct(CurlWrapper $curl, string $token) {
        $this->requestData['token'] = $token;
        parent::__construct($curl);
    } 
    
    public function postMessage(int $channelId, string $message, array $files = []): string {
        $url = $this->baseUrl . "channels/$channelId/messages";
        $postData = ['content' => $message];
        $dataString = json_encode($postData);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bot ' . $this->requestData['token'],
            'Content-Length: ' . strlen($dataString)
        ];
        return $this->curl->sendPostRequest($url, $headers, $dataString);
    }
    
    function crosspostMessage(string $token, int $channelId, int $messageId): string {
    $url = "https://discord.com/api/channels/$channelId/messages/$messageId/crosspost";
    return sendRequest($token, $url);
}
}