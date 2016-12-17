<?php

    return array_merge(
        getRoutes('admin'),
        getRoutes('front')
    );

    /**
     * собираем массив с роутами
     * @param $part
     * @return array
     */
    function getRoutes($part)
    {
        $front_routes = [];
        $route_dir = ROOT . '/routing/' . $part . '/';

        $files = getRouteFiles($route_dir);

        for ($i = 0; $i < count($files); $i++) {
            $file_paths = include_once ($route_dir . $files[$i]);
            foreach ($file_paths as $path => $path_params) {
                $path_params['page'] = $part;
                $front_routes[$path] = $path_params;
            }
        }

        return $front_routes;
    }

    /**
     * читаем директорию с роутами
     * @param $route_dir
     * @return array
     */
    function getRouteFiles($route_dir)
    {
        $files = [];
        if (is_dir($route_dir)) {
            $dir_content = scandir($route_dir);
            for ($i = 0; $i < count($dir_content); $i++) {
                if ($dir_content[$i] == '.' || $dir_content[$i] == '..') {
                    continue;
                }
                $current_file = new \SplFileInfo($dir_content[$i]);
                if ($current_file->getExtension() == 'php') {
                    $files[] = $dir_content[$i];
                }
            }
        }
        return $files;
    }