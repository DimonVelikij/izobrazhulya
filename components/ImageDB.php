<?php

class ImageDB
{
    private $image_id;
    private $status;
    private $user;
    
    public function __construct()
    {
        $status = Status::getStatusByAlias('inspection');
        if ($status) {
            $this->status = $status['id'];
        }
        if ($user = User::getSessionUser()) {
            $this->user = $user;
        }
        if ($status && $user) {
            $this->image_id = Picture::addImage($this->status, $this->user);
        }
    }
    
    public function removeImage()
    {
        Picture::removeImage($this->image_id);
    }

    public function isInsertImage()
    {
        if ($this->image_id) {
            return $this->image_id;
        }
        return false;
    }

    /**
     * вставляем в базы инфу об изображениях
     * @param $images
     */
    public function insertImageSize($images)
    {
        $image_types = ImageType::getImageTypes();
        foreach ($images as $image => $image_params) {
            $image_type = null;
            foreach ($image_types as $size) {
                if ($size['alias'] == $image_params['type']) {
                    $image_type = $size['id'];
                    break;
                }
            }
            Size::addSize($this->image_id, $image_params['width'], $image_params['height'], $image_params['file_name'], $image_params['price'], $image_type);
        }
    }

    /**
     * вставляем цвета
     * @param $colors
     */
    public function insertImageColors($colors)
    {
        if (!empty($colors)) {
            for ($i = 0; $i < count($colors); $i++) {
                Color::addColor($this->image_id, $colors[$i]);
            }
        }
    }
}