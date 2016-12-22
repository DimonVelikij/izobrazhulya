<?php

class Status
{
    /**
     * @param $alias
     * @return mixed|null
     */
    public static function getStatusByAlias($alias)
    {
        $db = DB::getConnection();

        $query = "SELECT id, title, alias FROM status WHERE alias=:alias LIMIT 1";
        $result = $db->prepare($query);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->execute();

        $status = $result->fetch();
        if ($status) {
            return $status;
        }
        return null;
    }
}