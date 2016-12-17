<?php

include_once ROOT . '/components/Twig/Autoloader.php';

class BaseController
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * BaseController constructor.
     */
    public function __construct(Request $request)
    {
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem(ROOT . '/resources/views/');
        $this->twig = new Twig_Environment($loader);
    }

    /**
     * render template
     * @param $path
     * @param array $params
     * @return bool
     */
    protected function render($path, $params = [])
    {
        $path = explode(':', $path);

        $directory = $path[0];
        $file = $path[1];

        $template = $this->twig->loadTemplate($directory . '/' . $file);
        echo $template->render($params);

        return true;
    }
}