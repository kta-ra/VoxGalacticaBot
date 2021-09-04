<?php namespace Ednews;

error_reporting(E_ALL);
date_default_timezone_set('Etc/GMT-3');
mb_internal_encoding("UTF-8");

define('SP', DIRECTORY_SEPARATOR);
$root = $_SERVER['DOCUMENT_ROOT'];
if (SP == '\\') $root .= '/';
define('ROOT', \str_replace('/', SP, $root));
define('PUBL', __DIR__ . SP);
define('HTML', \str_replace([ROOT, SP], ['', '/'], PUBL));
define('APP', \realpath('..' . SP) . SP);
define('LIB', APP . 'lib' . SP);

echo 'Hi';