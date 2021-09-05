<?php namespace Ednews;

class Logger {
    private $dir;
    private $fileNames = [
        'info'  => 'log',
        'error' => 'errorLog'
    ];
    
    public function __construct() {
        $this->dir = DATA;
    }
    
    public function info($message) {
        file_put_contents($this->dir . $this->fileNames['info'], $message);
    }
    
    public function error($message) {
        file_put_contents($this->dir . $this->fileNames['error'], $message);
    }
}