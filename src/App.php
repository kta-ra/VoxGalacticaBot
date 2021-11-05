<?php namespace KtaraDev\VoxGalacticaBot;

use Api\EdApi;
use Api\DiscordApi;

/**
 * Основной класс приложения
 */
final class App {
    /** @var array */
    private $config;

    public function __construct()
    {
        $this->loadConfig();
    }

    private function loadConfig()
    {
        $path = ROOT_DIR . 'config' . SP . 'app.php';
        $this->config = include($path);
    }
}
