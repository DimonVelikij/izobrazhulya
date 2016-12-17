<?php

class DB {

    //подключение к БД
    public static function getConnection() {

        //подключаем файл с параметрами подключения
        $paramsPath = ROOT.'/config/config.php';
        $params = include ($paramsPath);
        //выполняем подключение
        $db = new PDO("mysql:host={$params['host']};dbname={$params['dbname']}",$params['user'],$params['password']);
        $db -> exec("set names utf8");
        //возвращаем обект подключения
        return $db;

    }
}
