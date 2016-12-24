<?php

class Router {

    /**
     * массив роутов
     * @var array
     */
    private $routes;

    /**
     * Router constructor.
     */
    public function __construct() {
        $routes_path = ROOT . '/routing/allRoutes.php';
        $this->routes = include ($routes_path);
    }

    /**
     * получаем uri
     * @return string
     */
    private function getURI() {
        return !empty($_SERVER['REQUEST_URI']) ? trim($_SERVER['REQUEST_URI'], '/') : null;
    }

    /**
     * собираем request объект
     * @param array $path
     * @return Request
     */
    private function getRequest(array $path)
    {
        $request = new Request();
        $request->setRouteName($path['name']);
        $request->setPage($path['page']);
        $request->setTitle($path['title']);

        return $request;
    }

    /**
     * run application
     */
    public function run() {

        $uri = $this->getURI();

        foreach ($this->routes as $uri_pattern => $path) {
            if(preg_match("~$uri_pattern~", $uri)){
                $internal_route = preg_replace("~$uri_pattern~", $path['handler'], $uri);
                $segments = explode('/', $internal_route);

                $controller_name = ucfirst(array_shift($segments).'Controller');
                $action_name = array_shift($segments).'Action';

                $controller_file = ROOT.'/controllers/'.$controller_name.'.php';

                $request = $this->getRequest($path);

                if (!file_exists($controller_file)) {
                    //рендерим 404
                    break;
                }
                //сделать проверку на админа
                include_once ($controller_file);

                $controller_object = new $controller_name($request);
                
                array_unshift($segments, $request);

                $result = call_user_func_array([$controller_object,$action_name], $segments);

                if(!is_null($result)) {
                    break;
                }
            }
        }
    }
}