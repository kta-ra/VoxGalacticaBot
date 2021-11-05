<?php namespace KtaraDev\VoxGalacticaBot;

use Api\EdApi;
use Api\DiscordApi;

/**
 * Основной класс приложения
 */
final class App {
    /** @var array */
    private $config;

    /** @var int */
    private $lastChecked;

    /** @var int */
    private $lastUpdated;

    /** @var string */
    private $lastDataHash;

    /** @var int */
    private $lastArticleId;

    /** @var string */
    private $lastArticleExternalId;

    /** @var int */
    private $lastArticleExternalTime;

    public function __construct()
    {
        $this->loadConfig();
    }

    private function loadConfig() : void
    {
        $path = ROOT_DIR . 'config' . SP . 'app.php';
        $this->config = include($path);
        $this->loadState();
    }

    private function loadState() : void
    {
        $path = ROOT_DIR . 'data' . SP . 'state.php';
        $stateData = include($path);
        $this->lastChecked = $stateData['lastChecked'];
        $this->lastUpdated = $stateData['lastUpdated'];
        $this->lastDataHash = $stateData['lastDataHash'];
        $this->lastArticleId = $stateData['lastArticleId'];
        $this->lastArticleExternalId = $stateData['lastArticleExternalId'];
        $this->lastArticleExternalTime = $stateData['lastArticleExternalTime'];
    }

    private function saveState() : void
    {
        $path = ROOT_DIR . 'data' . SP . 'state.php';
        $stateData = [
            'lastChecked' => $this->lastChecked,
            'lastUpdated' => $this->lastUpdated,
            'lastDataHash' => $this->lastDataHash,
            'lastArticleId' => $this->lastArticleId,
            'lastArticleExternalId' => $this->lastArticleExternalId,
            'lastArticleExternalTime' => $this->lastArticleExternalTime
        ]
    }
}
