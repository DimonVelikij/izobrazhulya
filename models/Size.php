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

    /**
     * @param $image_id
     * @return array
     */
    public static function getSizesByImageId($image_id)
    {
        $db = DB::getConnection();

        $query = "SELECT * FROM size WHERE image=$image_id";
        $result = $db->query($query);
        $result->setFetchMode(PDO::PARAM_STR);

        $sizes = [];
        while ($row = $result->fetch()) {
            $sizes[(int)$row['price']][] = $row['id'];
        }
        krsort($sizes);

        return $sizes;
    }

    /**
     * @param $image_id
     * @param $size_id
     * @param $price
     * @return bool
     */
    public static function updateSizesPriceByImageId($image_id, $size_id, $price)
    {
        $db = DB::getConnection();

        $query = "UPDATE size SET price=:price WHERE image=:image_id AND id=:size_id";
        $result = $db->prepare($query);
        $result->bindParam(':price', $price, PDO::PARAM_INT);
        $result->bindParam(':image_id', $image_id, PDO::PARAM_INT);
        $result->bindParam(':size_id', $size_id, PDO::PARAM_INT);

        return $result->execute();
    }
}