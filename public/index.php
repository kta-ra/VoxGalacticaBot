<?php namespace KtaraDev\EdNews;
\error_reporting(E_ALL);
\ini_set('error_reporting', E_ALL);

date_default_timezone_set('Etc/GMT-3');
mb_internal_encoding("UTF-8");

define('SP', DIRECTORY_SEPARATOR);
define('APP', realpath('..' . SP) . SP);
define('DATA', APP . 'data' . SP);
//define('SRC', APP . 'src' . SP);

require APP . 'vendor/autoload.php';

$app = new App();

$html = '
    <p>Последнее обновление: ' . $app->updated . '</p>
    <p>Последняя проверка: ' . $app->checked . '</p>
';

if (isset($_POST['send'])) {
    //
} else {
    $html .= '
        <form method="post">
            <input type="submit" name="send" value="проверить">
        </form>
    ';
}

header('Content-Type: text/html; charset=utf-8');
echo $html;
