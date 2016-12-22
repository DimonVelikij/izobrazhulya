<?php

class ImageType
{
    public static function getImageTypes()
    {
        $db = DB::getConnection();

        $query = "SELECT id, alias FROM image_type";
        $result = $db->prepare($query);
        $result->execute();

        return $result->fetchAll();
    }
}