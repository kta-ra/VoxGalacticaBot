<?php

namespace KtaraDev\VoxGalacticaBot\Discord;

use KtaraDev\VoxGalacticaBot\Helper\CurlHelper;

/**
 * Работа с Discord API
 */
class DiscordApi {
    const BASE_URL = 'https://discord.com/api/';

    /** @var array */
    private $config;

    /** @var CurlHelper */
    private $curl;

    /** @var array */
    private $authData;

    public function __construct()
    {
        $this->loadConfig();
        $this->curl = new CurlHelper();
        $this->authData = ['bot' => $this->config['token']];
        $this->curl->setAuthData($this->authData);
    }

    private function loadConfig()
    {
        $path = ROOT_DIR . 'config' . SP . 'discord.php';
        $this->config = include($path);
    }

    public function getChannelInfo(int $channelId)
    {
        $path = self::BASE_URL . "channels/$channelId";
        $rawResult = $this->curl->getRequest($path);
        $result = json_decode($rawResult, true);//!TODO

        return $result;
    }

    public function sendMessage(int $channelId, string $message, array $files = [])
    {
        $path = self::BASE_URL . "channels/$channelId/messages";
        $postData = ['content' => $message];
        $rawResult = $this->curl->postRequest($path, $postData, $files);
        $result = json_decode($rawResult, true);//!TODO

        return $result;
    }

    public function crosspostMessage(int $channelId, int $messageId)
    {
        $path = self::BASE_URL . "/channels/$channelId/messages/$messageId/crosspost";
        $rawResult = $this->curl->postRequest($path);
        $result = json_decode($rawResult, true);//!TODO

        return $result;
    }
}
