<?php

class HashTags
{
    /**
     * @param $image_id
     * @param $hash_tag
     * @return bool|null
     */
    public static function addHashTag($image_id, $hash_tag)
    {
        $db = DB::getConnection();

        $query = "INSERT INTO hash_tag (hash, image) VALUES(:hash, :image)";
        $result = $db->prepare($query);
        $result->bindParam(':image', $image_id, PDO::PARAM_INT);
        $result->bindParam(':hash', $hash_tag, PDO::PARAM_STR);
        if(!$result->execute()) {
            return null;
        }
        return true;
    }
}