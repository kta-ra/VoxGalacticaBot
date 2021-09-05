<?php

namespace {
  function edNewsLoader($className) {
    $path = str_replace('Ednews\\', '', $className);
    $path = \preg_replace('/([a-z])([A-Z])/u', "$1_$2", $path);
    $path = \strtolower($path);
    $path = SRC . str_replace('\\', SP, $path) . '.php';
    if (file_exists($path)) include($path);
  }
  
  spl_autoload_register('edNewsLoader');
}