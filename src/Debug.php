<?php

namespace {
    function d()
    {
        $args = func_get_args();
        echo '<pre>';
        foreach ($args as $arg) {
            var_dump($arg);
            echo '<hr>';
        }
    }

    function dd()
    {
        $args = func_get_args();
        echo '<pre>';
        foreach ($args as $arg) {
            var_dump($arg);
            echo '<hr>';
        }
        die();
    }

    
}
