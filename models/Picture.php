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

    public static function getImagesResult($result)
    {
        $images = [];

        $hash_tags = [];
        $colors = [];
        $image = [];
        while ($row = $result->fetch()) {
            if (!isset($hash_tags[$row['image_id']]) || !in_array($row['hash'], $hash_tags[$row['image_id']])) {
                $hash_tags[$row['image_id']][] = $row['hash'];
            }
            if (!isset($colors[$row['image_id']]) || !in_array($row['color'], $colors[$row['image_id']])) {
                $colors[$row['image_id']][] = $row['color'];
            }
            if ($row['alias'] == 'origin' && (!isset($image[$row['image_id']]) || !in_array($row['file_name'], $image[$row['image_id']]))) {
                $image[$row['image_id']][$row['file_name']] = [
                    'width' =>  $row['width'],
                    'height'=>  $row['height'],
                    'price' =>  $row['price'],
                    'alias' =>  $row['alias']
                ];
            }
            $images[$row['image_id']] = [
                'status'            =>  $row['status'],
                'status_alias'      =>  $row['status_alias'],
                'previous'          =>  'previous.jpg',
                'previous_water'    =>  'previous_water.jpg'
            ];
        }
        if (!empty($images)) {
            foreach ($images as $image_id => $image_params) {
                unset($image[$image_id]['origin.jpg']);
                $images[$image_id]['hash_tags'] = $hash_tags[$image_id];
                $images[$image_id]['colors'] = $colors[$image_id];
                $images[$image_id]['images'] = $image[$image_id];
            }
        }
        return $images;
    }
    /**
     * @param $user_id
     * @return array
     */
    public static function getImagesByUserId($user_id)
    {
        $db = DB::getConnection();

        $query = "SELECT i.id as image_id, s.alias as status_alias, s.title as status, sz.width, sz.height, sz.file_name, sz.price, it.alias, ht.hash, c.color
                  FROM image as i, user as u, status as s, size as sz, image_type as it, hash_tag as ht, color as c
                  WHERE i.user=u.id and u.id=$user_id and i.status=s.id and i.user=u.id and i.id=sz.image and sz.type=it.id and i.id=ht.image and i.id=c.image
                  ORDER BY i.id DESC";
        $result = $db->query($query);

        $result->setFetchMode(PDO::PARAM_STR);

        return self::getImagesResult($result);
    }

    /**
     * @param $image_id
     * @return array
     */
    public static function getImageById($image_id)
    {
        $db = DB::getConnection();

        $query = "SELECT ht.hash as hash, s.id as size_id, s.price as price, it.alias as alias
                  FROM image as i, hash_tag as ht, size as s, image_type as it
                  WHERE i.id=ht.image AND i.id=$image_id AND i.id=s.image AND s.type=it.id";
        $result = $db->query($query);

        $result->setFetchMode(PDO::PARAM_STR);

        $image_params = [];
        $hash_tags = [];
        $prices = [];
        while ($row = $result->fetch()) {
            if (!in_array($row['hash'], $hash_tags)) {
                $hash_tags[] = $row['hash'];
            }
            if (!in_array($row['price'], $prices)) {
                $prices[] = $row['price'];
            }
        }
        $price_aliases = [
            0   =>  'large',
            1   =>  'middle',
            2   =>  'little'
        ];
        while (!empty($prices)) {
            $max = max($prices);
            $index = array_search($max, $prices);
            $image_params[$price_aliases[$index]] = (int)$max;
            unset($prices[$index]);
        }
        $hash_tags = implode(',', $hash_tags);
        $image_params['hash_tags'] = $hash_tags;

        return $image_params;
    }

    /**
     * @return array
     */
    public static function getImages()
    {
        $db = DB::getConnection();

        $query = "SELECT i.id as image_id, s.alias as status_alias, s.title as status, sz.width, sz.height, sz.file_name, sz.price, it.alias, ht.hash, c.color
                  FROM image as i, user as u, status as s, size as sz, image_type as it, hash_tag as ht, color as c
                  WHERE i.user=u.id and i.status=s.id and i.user=u.id and i.id=sz.image and sz.type=it.id and i.id=ht.image and i.id=c.image
                  ORDER BY i.id DESC";
        $result = $db->query($query);

        $result->setFetchMode(PDO::PARAM_STR);

        return self::getImagesResult($result);
    }

    public static function getImageByStatus($status)
    {
        $db = DB::getConnection();

        $query = "SELECT i.id as image_id, s.alias as status_alias, s.title as status, sz.width, sz.height, sz.file_name, sz.price, it.alias, ht.hash, c.color
                  FROM image as i, user as u, status as s, size as sz, image_type as it, hash_tag as ht, color as c
                  WHERE i.user=u.id and i.status=s.id and i.user=u.id and i.id=sz.image and sz.type=it.id and i.id=ht.image and i.id=c.image and s.alias='$status'
                  ORDER BY i.id DESC";
        $result = $db->query($query);

        $result->setFetchMode(PDO::PARAM_STR);

        return self::getImagesResult($result);
    }

    /**
     * @return array
     */
    public static function getImagesWithStatus()
    {
        $db = DB::getConnection();

        $query = "SELECT COUNT(i.id) as count, s.alias FROM image as i, status as s WHERE i.status=s.id GROUP BY i.status";
        $result = $db->query($query);
        $result->setFetchMode(PDO::PARAM_STR);

        $images = [];
        while ($row = $result->fetch()) {
            $images[$row['alias']] = $row['count'];
        }
        return $images;
    }

    public static function updateImageStatus($image_id, $status_id)
    {
        $db = DB::getConnection();
        $query = "UPDATE image SET status=:status_id WHERE id=:image_id";
        $result = $db->prepare($query);
        $result->bindParam(':status_id', $status_id, PDO::PARAM_INT);
        $result->bindParam(':image_id', $image_id, PDO::PARAM_STR);

        return $result->execute();
    }
}