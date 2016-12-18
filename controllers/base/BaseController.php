<?php

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
     * @var Request
     */
    protected $request;

    /**
     * BaseController constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        Autoloader::register();
        $loader = new Twig_Loader_Filesystem(ROOT . '/resources/views/' . $request->getPage() . '/');
        $this->twig = new Twig_Environment($loader);
    }

    /**
     * рендерим шаблон
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
        $params = $this->setRequiredParams($params);
        echo $template->render($params);

        return true;
    }

    /**
     * устанавливаем обязательные параметры для страницы
     * @param array $params
     * @return array
     */
    private function setRequiredParams(array $params)
    {
        $params['title'] = $this->request->getTitle();

        return $params;
    }
}