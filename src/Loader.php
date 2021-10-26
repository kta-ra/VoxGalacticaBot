<?php

namespace {
  function voxGalacticaBotLoader($className) {
    $path = str_replace('KtaraDev\\VoxGalacticaBot', '', $className);
    $path = __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, $path) . '.php';
    if (file_exists($path)) include($path);
  }
  
  spl_autoload_register('voxGalacticaBotLoader');
}
