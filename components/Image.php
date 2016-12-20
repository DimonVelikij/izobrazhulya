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
        return $this->image ? true : false;
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
     * установка параметров изображения
     */
    private function setImageParams()
    {
        $this->image_params = getimagesize($this->getImageProperty('tmp_name'));
    }

    /**
     * номер фото
     * @return string
     */
    private function getImageId()
    {
        return '1';
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
                $this->setExt();
                $this->setImageParams();
                return true;
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
     */
    public function setWaterMark($file, $new_file, $prop)
    {
        $this->resizeWater($this->getWidth()/$prop, $this->getHeight()/$prop);

        $stamp = imagecreatefrompng($this->getWaterTmpImage());
        $im = imagecreatefromjpeg($this->getRealPath($file));

        imagecopy($im, $stamp, 0, 0, 0, 0, imagesx($stamp), imagesy($stamp));

        imagejpeg($im, $this->getRealPath($new_file));
        imagedestroy($im);
    }
}