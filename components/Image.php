<?php

class Image
{
    /**
     * @var $_FILES
     */
    private $image;

    /**
     * @var array
     */
    private $formats = ["image/jpeg"];

    /**
     * @var int
     */
    private $b = 1024;

    /**
     * @var int
     */
    private $mb = 1024;

    /**
     * @var string
     */
    private $ext;

    /**
     * @var array
     */
    private $image_params;

    /**
     * @var int
     */
    private $image_id;

    public function __construct($image_name)
    {
        if (isset($_FILES[$image_name]) && isset($_FILES[$image_name]['tmp_name'])) {
            $this->image = $_FILES[$image_name];
        }
    }

    /**
     * @param $property
     * @return null
     */
    private function getImageProperty($property)
    {
        return isset($this->image[$property]) ? $this->image[$property] : null;
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return $this->image && !empty($this->image['name']) ? true : false;
    }

    /**
     * устанавливаем расширение
     */
    private function setExt()
    {
        $ext = explode('.',$this->getImageProperty('name'));
        $this->ext = mb_strtolower(end($ext));
    }

    /**
     * получаем расширение
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * установка параметров изображения
     */
    private function setImageParams()
    {
        $this->image_params = getimagesize($this->getImageProperty('tmp_name'));
    }

    /**
     * устанавливаем id изображения
     * @param $image_id
     */
    public function setImageId($image_id)
    {
        $this->image_id = $image_id;
    }

    /**
     * получаем id изображения
     * @return string
     */
    public function getImageId()
    {
        return $this->image_id;
    }

    /**
     * корень
     * @return string
     */
    private function getRootPath()
    {
        return ROOT;
    }

    /**
     * путь до изображения
     * @return string
     */
    private function getImageDir()
    {
        return $this->getRootPath().'/media/'.$this->getImageId();
    }

    /**
     * @param $image
     * @return string
     */
    private function getRealPath($image)
    {
        return $this->getImageDir().'/'.$image.'.'.$this->ext;
    }

    /**
     * путь до файла
     * @return bool|string
     */
    public function isValid()
    {
        if (in_array($this->getImageProperty('type'), $this->formats)) {
            if (
                $this->getImageProperty('size') > 0 &&
                $this->getImageProperty('size') < $this->b * $this->mb * 10
            ) {
                $image_params = getimagesize($this->image['tmp_name']);
                $image_width = $image_params[0];
                $image_height = $image_params[1];
                if ($image_width >= 300 && $image_height >= 300) {
                    $this->setExt();
                    $this->setImageParams();
                    return true;
                }
                return 'Ширина и высота должна быть больше 300px';
            }
            return 'Размер превышает 10Мб';
        }
        return 'Неверный формат';
    }

    /**
     * @return bool
     */
    public function upload()
    {
        if (!file_exists($this->getImageDir())) {
            mkdir($this->getImageDir());
        }
        if (move_uploaded_file($this->getImageProperty('tmp_name'), $this->getRealPath('origin'))) {
            return true;
        }
        return false;
    }

    /**
     * минимальная сторона
     * @return mixed
     */
    public function getMinSide()
    {
        return $this->getWidth() > $this->getHeight() ? $this->getHeight() : $this->getWidth();
    }

    /**
     * ширина
     * @return mixed
     */
    public function getWidth()
    {
        return $this->image_params[0];
    }

    /**
     * высота
     * @return mixed
     */
    public function getHeight()
    {
        return $this->image_params[1];
    }

    /**
     * картинка логотипа
     * @return string
     */
    private function getWaterImage()
    {
        return ROOT . '/resources/images/water.png';
    }

    /**
     * временный водяной знак
     * @return string
     */
    private function getWaterTmpImage()
    {
        return ROOT . '/media/tmp_water.png';
    }

    /**
     * изменение размера изображения
     * @param $width
     * @param $height
     * @param $file_type
     */
    public function resize($width, $height, $file_type)
    {
        $originImage = imagecreatefromjpeg($this->getRealPath('origin'));
        $image = imagecreatetruecolor($width, $height);
        imagecopyresampled($image, $originImage, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        imagejpeg($image, $this->getRealPath($file_type));
        imagedestroy($image);
        imagedestroy($originImage);
    }

    /**
     * изменение размера водяного знака
     * @param $w
     * @param $h
     */
    public function resizeWater($w, $h)
    {
        $water_image = $this->getWaterImage();
        $water_image_params = getimagesize($water_image);
        $img = imagecreatefrompng($water_image);
        $tmp = imagecreatetruecolor($w, $h);
        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);
        $transparent = imagecolorallocatealpha($tmp, 255,255,255,127);
        imagefilledrectangle($tmp, 0, 0, $w, $h, $transparent);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $w, $h, $water_image_params[0], $water_image_params[1]);
        imagepng($tmp, $this->getWaterTmpImage());
    }

    /**
     * Устанавливаем водяной знак на изображение
     * @param $file
     * @param $new_file
     * @param $prop
     * @param null $w
     * @param null $h
     */
    public function setWaterMark($file, $new_file, $prop, $w = null, $h = null)
    {
        $w = $w ? $w : $this->getWidth()/$prop;
        $h = $h ? $h : $this->getHeight()/$prop;
        $this->resizeWater($w, $h);

        $stamp = imagecreatefrompng($this->getWaterTmpImage());
        $im = imagecreatefromjpeg($this->getRealPath($file));

        imagecopy($im, $stamp, 0, 0, 0, 0, imagesx($stamp), imagesy($stamp));

        imagejpeg($im, $this->getRealPath($new_file));
        imagedestroy($im);
    }

    /**
     * Создание превью
     * @param $w
     * @param $h
     * @param $file_type
     */
    public function previous($w, $h, $file_type)
    {
        $maxPreviousSide = max($w, $h);
        $minOriginSide = min($this->getWidth(), $this->getHeight());

        $originImage = imagecreatefromjpeg($this->getRealPath('origin'));
        $image = imagecreatetruecolor($maxPreviousSide, $maxPreviousSide);
        imagecopyresampled($image, $originImage, 0, 0, 0, 0, $maxPreviousSide, $maxPreviousSide, $minOriginSide, $minOriginSide);
        $cropImage = imagecrop($image, ['x'=>0, 'y'=>0, 'width'=>$w, 'height'=>$h]);
        imagejpeg($cropImage, $this->getRealPath($file_type));
        imagedestroy($image);
        imagedestroy($cropImage);
        imagedestroy($originImage);
    }

    /**
     * Получение наиболее часто встречающихся цветов
     * @param $file_type
     * @param $count_colors
     * @param $step
     * @return array|bool|null
     */
    public function getImagePalette($file_type, $count_colors, $step)
    {
        $imagePaletteService = new ImagePalette();
        $colors = $imagePaletteService->getImageColors($this->getRealPath($file_type), $count_colors, $step);
        if ($colors) {
            return $colors;
        }
        return null;
    }
}