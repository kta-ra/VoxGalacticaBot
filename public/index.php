<?php namespace KtaraDev\VoxGalacticaBot;
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Etc/GMT-3');
mb_internal_encoding("UTF-8");

require realpath('..')  . DIRECTORY_SEPARATOR . 'src/Debug.php';
require realpath('..')  . DIRECTORY_SEPARATOR . 'src/Loader.php';

$app = new App();
/*

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
*/