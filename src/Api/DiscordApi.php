<?php namespace KtaraDev\EdNews\Api;

class DiscordApi {
    /** @var string Базовый путь api */
    private $baseUrl = 'https://discord.com/api/';
    
    /** @var string Токен приложения discord */
    private $token;
    
    /**
    * Конструктор
    * @param string $token токен приложения discord
    */
    public function __construct(string $token) {
        $this->token = $token;
    }
    
    /**
    * Отправка сообщения на канал
    * @param int $channelId
    * @param string $message
    * @param array $files
    * @return string Json результат или строка ошибки !TODO
    */
    public function postMessage(int $channelId, string $message, array $files = []): string {
        $url = $this->baseUrl . "channels/$channelId/messages";
        $postData = ['content' => $message];
        $request = new ApiRequest();
        $request->setMode('post')
                ->setPostData($postData)
                ->setUrl($url)
                ->setAuthString('Bot ' . $this->token)
                ->run();
        if ($request->isSuccess()) {
            return $request->getResult();
        } else {
            return $request->getError();
        }
    }
    
    /**
    * Публикация сообщения на канале (cross-posting)
    * @param int $channelId
    * @param int $messageId
    * @return string Json результат или строка ошибки !TODO
    */
    public function crosspostMessage(int $channelId, int $messageId): string {
        $url = $this->baseUrl . "channels/$channelId/messages/$messageId/crosspost";
        $request = new ApiRequest();
        $request->setMode('post')
                ->setUrl($url)
                ->setAuthString('Bot ' . $this->token)
                ->run();
        if ($request->isSuccess()) {
            return $request->getResult();
        } else {
            return $request->getError();
        }
    }
}
