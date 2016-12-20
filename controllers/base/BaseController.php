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
        $params['is_login'] = User::getSessionUser();

        return $params;
    }
    
    protected function upload()
    {
        $image = new Image('image');
        //если картинка загружена, то делаем уменьшение фото
        if ($image->isImage() && $image->isValid() === true && $image->upload()) {
            $water_prefix = '_water';
            $w = $image->getWidth();
            $h = $image->getHeight();
            $sizes = [
                'large' =>  1,
                'middle'=>  2,
                'little'=>  4
            ];
            foreach ($sizes as $size => $prop) {
                $image->resize($w / $prop, $h / $prop, $size);
                $image->setWaterMark($size, $size . $water_prefix, $prop);
            }
        }
    }
}