<?php

class Color
{
    /**
     * @param $image_id
     * @param $color
     * @return bool|null
     */
    public static function addColor($image_id, $color)
    {
        $db = DB::getConnection();

        $query = "INSERT INTO color (image, color) VALUES(:image, :color)";
        $result = $db->prepare($query);
        $result->bindParam(':image', $image_id, PDO::PARAM_INT);
        $result->bindParam(':color', $color, PDO::PARAM_INT);
        if(!$result->execute()) {
            return null;
        }
        return true;
    }
}