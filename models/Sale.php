<?php

class Sale
{
    /**
     * @param $image_id
     * @param $sum
     * @return bool|null
     */
    public static function addSale($image_id, $sum)
    {
        $db = DB::getConnection();

        $query = "INSERT INTO sale (date, image, sum) VALUES(NOW(), :image_id, :sum)";
        $result = $db->prepare($query);
        $result->bindParam(':image_id', $image_id, PDO::PARAM_INT);
        $result->bindParam(':sum', $sum, PDO::PARAM_INT);
        if(!$result->execute()) {
            return null;
        }
        return true;
    }

    /**
     * @return array
     */
    public static function getSaleList()
    {
        $db = DB::getConnection();

        $query = "SELECT * 
                  FROM sale as s, image as i, size, image_type as it 
                  WHERE s.image=i.id and i.id=size.image and size.type=it.id and it.alias='previous'";
        $result = $db->query($query);
        $result->setFetchMode(PDO::PARAM_STR);
        $sale_list = [];

        $total_prices = [];
        $total_count_sale = [];
        while ($row = $result->fetch()) {
            $total_prices[$row['image']] = !isset($total_prices[$row['image']]) ? (int)$row['sum'] : $total_prices[$row['image']] + (int)$row['sum'];
            $total_count_sale[$row['image']] = !isset($total_count_sale[$row['image']]) ? 1 : $total_count_sale[$row['image']] + 1;
            $sale_list[$row['image']] = [
                'path'                  =>  '/media/' . $row['image'] . '/' . $row['file_name'],
                'total_price'           =>  $total_prices[$row['image']],
                'total_count_sale'      =>  $total_count_sale[$row['image']],
            ];
        }

        return $sale_list;
    }
}