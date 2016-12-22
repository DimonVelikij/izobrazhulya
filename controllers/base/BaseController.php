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

    /**
     * загрузка изображения
     * @param Price $price
     * @return bool
     */
    protected function upload(Price $price)
    {
        $image = new Image('image');

        $load_image = $image->isImage();

        if (!$load_image) {
            return 'Вы не выбрали изображение для загрузки';
        }

        $load_image = $image->isValid();
        if ($load_image !== true) {
            return $load_image;
        }

        $imageDB = new ImageDB();
        if ($image_id = $imageDB->isInsertImage()) {
            $image->setImageId($image_id);
        }

        if (!$image->upload()) {
            $imageDB->removeImage();
            return 'Неудалось загрузить изображение';
        }

        $insert_images = [];
        $water_prefix = '_water';

        $image->previous(300, 200, 'previous');
        $image->setWaterMark('previous', 'previous'.$water_prefix, 1, 300, 200);

        $w = $image->getWidth();
        $h = $image->getHeight();
        $insert_images['origin'] = [
            'file_name' =>  'origin' . '.' . $image->getExt(),
            'width'     =>  $w,
            'height'    =>  $h,
            'type'      =>  'origin',
            'price'     =>  $price->getPriceLarge()
        ];
        $insert_images['previous'] = [
            'file_name' =>  'previous' . '.' . $image->getExt(),
            'width'     =>  300,
            'height'    =>  200,
            'type'      =>  'previous',
            'price'     =>  $price->getPriceLarge()
        ];
        $insert_images['previous' . $water_prefix] = [
            'file_name' =>  'previous' . $water_prefix . '.' . $image->getExt(),
            'width'     =>  300,
            'height'    =>  200,
            'type'      =>  'previous' . $water_prefix,
            'price'     =>  $price->getPriceLarge()
        ];

        $sizes = [
            'large' =>  1,
            'middle'=>  2,
            'little'=>  4
        ];
        foreach ($sizes as $size => $prop) {
            $image->resize($w / $prop, $h / $prop, $size);
            $getPrice = 'getPrice' . ucfirst($size);
            $insert_images[$size] = [
                'file_name' =>  $size . '.' . $image->getExt(),
                'width'     =>  (int)$w / $prop,
                'height'    =>  (int)$h / $prop,
                'type'      =>  'origin',
                'price'     =>  $price->$getPrice()
            ];
            $image->setWaterMark($size, $size . $water_prefix, $prop);
            $insert_images[$size . $water_prefix] = [
                'file_name' =>  $size . $water_prefix . '.' . $image->getExt(),
                'width'     =>  (int)$w / $prop,
                'height'    =>  (int)$h / $prop,
                'type'      =>  'water',
                'price'     =>  $price->$getPrice()
            ];
        }

        $count_colors = 5;
        $step = 5;
        $colors = $image->getImagePalette('origin', $count_colors, $step);

        $imageDB->insertImageSize($insert_images);
        $imageDB->insertImageColors($colors);

        return (int)$image->getImageId();
    }
}