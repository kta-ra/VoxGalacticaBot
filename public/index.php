<?php namespace Ednews;

error_reporting(E_ALL);
date_default_timezone_set('Etc/GMT-3');
mb_internal_encoding("UTF-8");

define('SP', DIRECTORY_SEPARATOR);
$root = $_SERVER['DOCUMENT_ROOT'];
if (SP == '\\') $root .= '/';
define('ROOT', str_replace('/', SP, $root));
define('PUBL', __DIR__ . SP);
define('HTML', str_replace([ROOT, SP], ['', '/'], PUBL));
define('APP', realpath('..' . SP) . SP);
define('DATA', APP . 'data' . SP);
define('SRC', APP . 'src' . SP);

require_once(SRC . 'loader.php');
$app = new App();

if (isset($_POST['update'])) {
    $app->checkUpdates();
}

$html = '
    <p>Последнее обновление: ' . $app->updated . '</p>
    <p>Последняя проверка: ' . $app->checked . '</p>
    <form method="post">
        <input type="submit" name="update" value="Проверить обновление">
    </form>
';

header('Content-Type: text/html; charset=utf-8');
echo $html;