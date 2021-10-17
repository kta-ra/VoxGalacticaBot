<?php namespace Api;

/**
 * Запрос к api
 */
class ApiRequest {
    /** @var string */
    const GET_MODE = 'get';
    
    /** @var string */
    const POST_MODE = 'post';
    
    /** @var string Тип запроса */
    private $mode;
    
    /** @var string Url запроса */
    private $url;
    
    /** @var array */
    private $postData = [];
    
    /** @var string Значение заголовка Authorization */
    private $authString = '';
    
    /** @var array Список файлов для post запроса */
    private $files = [];
    
    /** @var string */
    private $dataString = '';    
    
    /** @var Указатель curl */
    private $handle;
    
    /** @var array Опции curl */
    private $options = [];

    /** @var array Http заголовки для curl */
    private $headers = [];
    
    /** @var bool Флаг успешного завершения запроса */
    private $isSuccess;
    
    /** @var string|false результат завершения запроса */
    private $result;
    
    /**
    * Конструктор
    * Иницализация указателя cURL
    */
    public function __construct() {
        $this->handle = curl_init();
    }
    
    /**
    * Деструктор
    * Освобождение указателя cURL
    */
    public function __destruct() {
        curl_close($this->handle);
    }
    
    /**
     * Получение типа запроса
     * @return string
     */
    public function getMode() {
        return $this->mode;
    }
    
    /**
     * Установка типа запроса
     * @param string
     * @return $this
     */
    public function setMode(string $mode) {
        $this->mode = $mode;
        return $this;
    }
    
    /**
     * Получение url запроса
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }
    
    /**
     * Установка url запроса
     * @param string
     * @return $this
     */
    public function setUrl(string $url) {
        $this->url = $url;
        return $this;
    }
    
    /**
     * Получение данных post
     * @return array
     */
    public function getPostData() {
        return $this->postData;
    }
    
    /**
     * Установка данных post
     * @param array
     * @return $this
     */
    public function setPostData(array $postData) {
        $this->postData = $postData;
        return $this;
    }
    
    /**
     * Получение данных авторизации
     * @return string
     */
    public function getAuthString() {
        return $this->authString;
    }
    
    /**
     * Установка данных авторизации
     * @param string
     * @return $this
     */
    public function setAuthString(string $authString) {
        $this->authString = $authString;
        return $this;
    }
    
    /**
     * Получение списка файлов
     * @return array
     */
    public function getFiles() {
        return $this->files;
    }
    
    /**
     * Установка списка файлов
     * @param array
     * @return $this
     */
    public function setFiles(array $files) {
        $this->files = $files;
        return $this;
    }
    
    /**
     * Получение данных запроса в строке
     * @return string
     */
    public function getDataString() {
        return $this->dataString;
    }
    
    /**
     * Получение опций curl
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }
    
    /**
     * Получение заголовков запроса
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }
    
    /**
     * Проверка успешного выполнения запроса
     * @return bool
     */
    public function isSuccess() {
        return $this->isSuccess;
    }
    
    /**
     * Получение сообщения об ошибке curl
     * @return string
     */
    public function getError() {
        return curl_error($this->handle);
    }
    
    /**
     * Получение результата запроса
     * @return string|false
     */
    public function getResult() {
        return $this->result;
    }
    
    /**
     * Выполнение запроса
     */
    public function run() {
        $this->prepareCurl();
        $this->result = curl_exec($this->handle);
        $this->isSuccess = ! (bool) curl_errno($this->handle);
        return $this;
    }
    
    /**
     * Подготовка данных, опций и заголовков для curl запроса
     */
    private function prepareCurl() {
        $this->options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $this->url
        ];
        if ($this->mode == self::POST_MODE) {
            if (!empty($files)) {
                $preparedFiles = $this->prepareFiles();
                $boundary = uniqid();
                $delimiter = '-------------' . $boundary;
                $this->dataString = $this->buildMultipartData($delimiter, $this->postData, $preparedFiles);
                $this->headers = $this->buildHeaders(strlen($this->dataString), true, $delimiter);
            } else {
                $this->dataString = json_encode($this->postData);
                $this->headers = $this->buildHeaders(strlen($this->dataString));
            }
            $this->options[CURLOPT_POST] = 1;
            $this->options[CURLOPT_POSTFIELDS] = $this->dataString;
        }
        $this->setOptions();
    }
    
    /**
     * Подготовка заголовков для curl запроса
     * @param int $contentLength
     * @param bool $isMultipart флаг для построения заголовков multipart form data
     * @param string $delimiter разделитель составных частей
     * @return array
     */
    private function buildHeaders(int $contentLength, bool $isMultipart = false, string $delimiter = NULL): array {
        $headers = [];
        if ($isMultipart) {
            $headers[] = 'Content-Type: multipart/form-data; boundary=' . $delimiter;
        } else {
            $headers[] = 'Content-Type: application/json';
        }
        $headers[] = 'Content-Length: ' . $contentLength;
        if (!empty($this->authString)) {
            $headers[] = 'Authorization: ' . $this->authString;
        }
        return $headers;
    }
    
    /**
     * Получение содержимого файлов
     * @return array
     */
    private function prepareFiles() {
        $result = [];
        foreach ($this->file as $filePath) {
            $result[basename($filePath)] = file_get_contents($filePath);
        }
        return $result;
    }
    
    /**
     * Подготовка составного содержимого (multipart form data)
     * @param string $delimiter разделитель составных частей
     * @param array $fields массив данных post
     * @param array $files массив содержимого файлов для отправки
     * @return string
     */
    private function buildMultipartData(string $delimiter, array $fields, array $files): string {
        $data = '';
        $eol = "\r\n";
        foreach ($fields as $name => $content) {
            $fieldString  = '--' . $delimiter . $eol;
            $fieldString .= 'Content-Disposition: form-data; name="' . $name . '"' . $eol;
            $fieldString .= $eol . $content . $eol;
            $data .= $fieldString;
        }
        foreach ($files as $name => $content) {
            $fieldString  = '--' . $delimiter . $eol;
            $fieldString .= 'Content-Disposition: form-data; name="' . $name . '"; ';
            $fieldString .= 'filename="' . $name . '"' . $eol;
            $fieldString .= 'Content-Transfer-Encoding: binary' . $eol;
            $fieldString .= $eol . $content . $eol;
            $data .= $fieldString;
        }
        $data .= "--" . $delim . "--" . $eol;
        return $data;
    }
    
    /**
    * Установка параметров curl
    */
    private function setOptions() {
        curl_setopt_array($this->handle, $this->options);
        if (!empty($this->headers)) {
            curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->headers);
        }
    }
}