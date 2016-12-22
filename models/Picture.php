<?php

class Picture
{
    /**
     * @param $status_id
     * @param $user_id
     * @return string
     */
    public static function addImage($status_id, $user_id)
    {
        $db = DB::getConnection();

        $query = "INSERT INTO image (status, user) VALUES(:status, :user)";
        $result = $db->prepare($query);
        $result->bindParam(':status', $status_id, PDO::PARAM_INT);
        $result->bindParam(':user', $user_id, PDO::PARAM_INT);
        if(!$result->execute()) {
            return null;
        }
        return $db->lastInsertId();
    }

    /**
     * @param $image_id
     * @return bool
     */
    public static function removeImage($image_id)
    {
        $db = DB::getConnection();

        $query = "DELETE FROM image WHERE id=:id";
        $result = $db->prepare($query);
        $result->bindParam(':id', $image_id, PDO::PARAM_INT);
        return $result->execute();
    }
}