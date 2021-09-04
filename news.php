<?php
//error_reporting(E_ALL);

$token = '';
$channelId = 0;

$updates = getUpdates();
if (!empty($updates)) {
    foreach ($updates as $articleData) {
        if (mb_strlen($articleData['body']) < 20) continue;
        $message = prepareArticle($articleData['title'], $articleData['date'], $articleData['body']);
        $postResult = postMessage($token, $channelId, $message, [$articleData['pic']]);
        file_put_contents('./log', "\n\n" . date('d-m-Y H:i:s') . '  ' . $postResult, FILE_APPEND);
        $postResult = json_decode($postResult, true);
        if (isset($postResult['id'])) {
            $messageId = $postResult['id'];
            crosspostMessage($token, $channelId, $messageId);
        }
    }
}
file_put_contents('./data/lastChecked', date('d.m.Y H:i:s'));

function getUpdates(): array {
    $result = [];
    $lastUpdated = (int) file_get_contents('./lastUpdated');
    $url = 'https://cms.zaonce.net/ru-RU/jsonapi/node/galnet_article?&sort=-published_at&page[offset]=0&page[limit]=10';
    file_get_contents($url);
    file_put_contents('./log', "\n\n" . date('d-m-Y H:i:s') . '  ' . file_get_contents($url), FILE_APPEND);
    $jsonData = json_decode(file_get_contents($url), true);
    $jsonData['data'] = array_reverse($jsonData['data']);
    foreach ($jsonData['data'] as $article) {
        $publishedAt = strtotime($article['attributes']['created']);
        $isRus = preg_match('/\p{Cyrillic}/u', $article['attributes']['body']['value']);
        if ($publishedAt > $lastUpdated && $isRus) {
            $articleData = [
                'title' => $article['attributes']['title'],
                'date' => $article['attributes']['field_galnet_date'],
                'body' => $article['attributes']['body']['processed']
            ];
            $pic = explode(',', $article['attributes']['field_galnet_image']);
            $articleData['pic'] = 'https://hosting.zaonce.net/elite-dangerous/galnet/' . $pic[0] . '.png';
            $result[] = $articleData;
            $lastUpdated = $publishedAt;
        }
    }
    file_put_contents('./data/lastUpdated', $lastUpdated);
    return $result;
}

function prepareArticle(string $title, string $date, string $body): string {
    $dateFormat = [
        'JAN' => 'Янв',
        'FEB' => 'Фев',
        'MAR' => 'Мар',
        'APR' => 'Апр',
        'MAY' => 'Май',
        'JUN' => 'Июн',
        'JUL' => 'Июл',
        'AUG' => 'Авг',
        'SEP' => 'Сен',
        'OCT' => 'Окт',
        'NOV' => 'Ноя',
        'DEC' => 'Дек'
    ];
    $date = strtr($date, $dateFormat);
    $body = htmlspecialchars_decode(strip_tags(str_replace('<br />', "\n", $body)));
    return "```fix\n$title\n$date\n```\n```\n{$body}⠀```\n";
}

function postMessage(string $token, int $channelId, string $message, array $files = []): string {
    $url = "https://discord.com/api/channels/$channelId/messages";
    $postData = ['content' => $message];
    return sendRequest($token, $url, $postData, $files);
}

function crosspostMessage(string $token, int $channelId, int $messageId): string {
    $url = "https://discord.com/api/channels/$channelId/messages/$messageId/crosspost";
    return sendRequest($token, $url);
}

function sendRequest(string $token, string $url, array $postData = [], array $files = [], bool $postMode = true): string {
    $ch = curl_init();
    
    if (!empty($files)) {
        $postData = ['payload_json' => json_encode($postData)];
        $preparedFiles = [];
        foreach ($files as $file) {
            $preparedFiles[basename($file)] = file_get_contents($file);
        }
        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;
        $dataString = buildData($boundary, $postData, $preparedFiles);
        $headers = buildHeaders($token, strlen($dataString), true, $delimiter);
    } else {
        $dataString = json_encode($postData);
        $headers = buildHeaders($token, strlen($dataString));
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($postMode) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    }
    $out = curl_exec($ch);
    if (curl_errno($ch)) {
        $errorMsg = curl_error($ch);
    }
    if (isset($errorMsg)) {
        file_put_contents('./errorLog', $errorMsg);
    }
    return $out;
}

function buildHeaders(string $token, int $length, bool $isMultipart = false, string $delimiter = NULL): array {
    $headers = [];
    if ($isMultipart) {
        $headers[] = 'Content-Type: multipart/form-data; boundary=' . $delimiter;
    } else {
        $headers[] = 'Content-Type: application/json';
    }
    $headers[] = 'Content-Length: ' . $length;
    $headers[] = 'Authorization: Bot ' . $token;
    return $headers;
}

function buildData(string $boundary, array $fields, array $files): string {
    $data = '';
    $eol = "\r\n";
    $delim = '-------------' . $boundary;
    foreach ($fields as $name => $content) {
        $data .= "--{$delim}{$eol}Content-Disposition: form-data; name=\"{$name}\"{$eol}{$eol}{$content}{$eol}";
    }
    foreach ($files as $name => $content) {
        $data .= "--{$delim}{$eol}Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$name}\"{$eol}Content-Transfer-Encoding: binary{$eol}{$eol}{$content}{$eol}";
    }
    $data .= "--" . $delim . "--" . $eol;
    return $data;
}