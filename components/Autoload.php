<?php

spl_autoload_register(function($class_name) {
    $array_paths = array(
        '/models/',
        '/components/',
        '/controllers/base/'
    );

    //находим файл в каталогах массива выше
    foreach ($array_paths as $path) {
        $path = ROOT . $path . $class_name . '.php';
        if (is_file($path)) {
            include_once $path;
        }
    }
});
