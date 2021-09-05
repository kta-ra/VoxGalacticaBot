<?php namespace Ednews;
/**
 * Файл для запуска с cron
 */

date_default_timezone_set('Etc/GMT-3');
mb_internal_encoding("UTF-8");

define('SP', DIRECTORY_SEPARATOR);
define('APP', realpath('..' . SP) . SP);
define('DATA', APP . 'data' . SP);
define('SRC', APP . 'src' . SP);

require_once(SRC . 'loader.php');

$app = new App();
$app->checkUpdates();