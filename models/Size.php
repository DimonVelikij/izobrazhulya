<?php

class Size
{
    /**
     * @param $image_id
     * @param $width
     * @param $height
     * @param $file_name
     * @param $price
     * @param $type
     * @return bool|null
     */
    public static function addSize($image_id, $width, $height, $file_name, $price, $type)
    {
        $db = DB::getConnection();

        $query = "INSERT INTO size (image, width, height, file_name, price, type) VALUES(:image, :width, :height, :file_name, :price, :type)";
        $result = $db->prepare($query);
        $result->bindParam(':image', $image_id, PDO::PARAM_INT);
        $result->bindParam(':width', $width, PDO::PARAM_INT);
        $result->bindParam(':height', $height, PDO::PARAM_INT);
        $result->bindParam(':file_name', $file_name, PDO::PARAM_STR);
        $result->bindParam(':price', $price, PDO::PARAM_INT);
        $result->bindParam(':type', $type, PDO::PARAM_INT);
        if(!$result->execute()) {
            return null;
        }
        return true;
    }
}