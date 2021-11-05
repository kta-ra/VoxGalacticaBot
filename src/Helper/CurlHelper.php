<?php

namespace KtaraDev\VoxGalacticaBot\Helper;

use function curl_init;
use function curl_close;
use function curl_setopt;
use function curl_exec;
use function curl_errno;
use function curl_error;

/**
 * Работа с CURL
 */
class CurlHelper {

    /** @var \CurlHandle */
    private $handle;

    /** @var array */
    private $headers;

    /** @var array */
    private $authData;

    /** @var bool */
    private $isPost = false;

    /** @var int */
    private $postLength;

    /** @var bool */
    private $isMultipart = false;

    /** @var string */
    private $boundary;

    /** @var string */
    private $delimiter;

    public function __construct()
    {
        $this->handle = curl_init();
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, false);
    }

    public function __destruct()
    {
        if ($this->handle) {
            curl_close($this->handle);
        }
    }

    public function setHeaders(array $headers) : CurlHelper
    {
        $this->headers = $headers;
        return $this;
    }

    public function setAuthData(array $authData) : CurlHelper
    {
        $this->authData = $authData;
        return $this;
    }

    public function getRequest(string $url) : mixed
    {
        $this->prepareRequest($url);

        $out = curl_exec($this->handle);

        if (curl_errno($this->handle)) {
            $out = curl_error($this->handle);
        }

        $this->reset();

        return $out;
    }

    public function postRequest(string $url, array $postData = [], array $files = []) : mixed
    {
        $this->isPost = true;
        $this->prepareRequest($url, $postData, $files);

        $out = curl_exec($this->handle);

        if (curl_errno($this->handle)) {
            $out = curl_error($this->handle);
        }

        $this->reset();

        return $out;
    }
    

    private function prepareRequest(string $url, array $postData = [], array $files = []) : CurlHelper
    {
        if ($this->isPost) {
            $postData = $this->preparePostData($postData, $files);
            $this->postLength = strlen($postData);
            curl_setopt($this->handle, CURLOPT_POST, 1);
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, $postData);
        }

        $this->prepareHeaders();

        curl_setopt($this->handle, CURLOPT_URL, $url);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->headers);

        return $this;
    }

    private function reset() : CurlHelper
    {
        $this->isPost = false;
        $this->headers = [];
        curl_reset($this->handle);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, false);
        
        return $this;
    }


    private function prepareHeaders() : CurlHelper
    {
        if ($this->isPost) {
            if ($this->isMultipart) {
                $this->headers[] = 'Content-Type: multipart/form-data; boundary=' . $this->delimiter;
            } else {
                $this->headers[] = 'Content-Type: application/json';
            }
            $this->headers[] = 'Content-Length: ' . $this->postLength;
        }        

        foreach ($this->authData as $type => $token) {
            $this->headers[] = 'Authorization: ' . ucfirst($type) . ' ' . $token;
        }

        return $this;
    }

    private function prepareFiles(array $files) : array
    {
        $preparedFiles = [];
        foreach ($files as $file) {
            $preparedFiles[basename($file)] = file_get_contents($file);
        }
        return $preparedFiles;
    }

    private function preparePostData(array $postData = [], array $files = []) : string
    {
        if (!empty($files)) {
            $this->isMultipart = true;
            $postData = ['payload_json' => json_encode($postData)];
            $preparedFiles = $this->prepareFiles($files);
            $this->boundary = uniqid();
            $this->delimiter = '-------------' . $this->boundary;            
            $eol = "\r\n";
            $dataString = '';

            foreach ($postData as $name => $content) {
                $dataString .= "--{$this->delimiter}{$eol}Content-Disposition: form-data; name=\"{$name}\"{$eol}{$eol}{$content}{$eol}";
            }            
            foreach ($preparedFiles as $name => $content) {
                $dataString .= "--{$this->delimiter}{$eol}Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$name}\"{$eol}Content-Transfer-Encoding: binary{$eol}{$eol}{$content}{$eol}";
            }
            $dataString .= "--" . $this->delimiter . "--" . $eol;
        } else {
            $dataString = json_encode($postData);
        }
        return $dataString;
    }
}
