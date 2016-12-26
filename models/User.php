<?php

class User
{
    /**
     * регистрация
     * @param $f_name
     * @param $s_name
     * @param $login
     * @param $password
     * @return bool|string
     */
    public static function registration($f_name,$s_name,$login,$password)
    {
        $db = DB::getConnection();
        $password = md5(md5($password));
        $query = "INSERT INTO user (id,f_name,s_name,login,password) VALUES(NULL,:f_name,:s_name,:login,:password)";
        $result = $db->prepare($query);
        $result->bindParam(':f_name',$f_name,PDO::PARAM_STR);
        $result->bindParam(':s_name',$s_name,PDO::PARAM_STR);
        $result->bindParam(':login',$login,PDO::PARAM_STR);
        $result->bindParam(':password',$password,PDO::PARAM_STR);

        if($result->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }

    /**
     * проверка логина и пароля
     * @param $login
     * @param $password
     * @return bool
     */
    public static function login($login,$password)
    {
        $db = DB::getConnection();
        $password = md5(md5($password));
        $query = "SELECT * FROM user WHERE login=:login AND password=:password";
        $result = $db->prepare($query);
        $result->bindParam(':login',$login,PDO::PARAM_STR);
        $result->bindParam(':password',$password,PDO::PARAM_STR);
        $result->execute();

        return $result->fetch();
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public static function getUserById($user_id)
    {
        $db = DB::getConnection();
        $query = "SELECT * FROM user WHERE id=:user_id";
        $result = $db->prepare($query);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $result->execute();

        return $result->fetch();
    }

    /**
     * установка авторизованного пользователя
     * @param $value
     */
    public static function setSessionUser($value)
    {
        $_SESSION['user'] = $value;
    }

    /**
     * выход пользователя
     */
    public static function delSessionUser()
    {
        unset($_SESSION['user']);
    }

    /**
     * авторизован ли пользователь
     * @return bool
     */
    public static function getSessionUser()
    {
        if(isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            return $_SESSION['user'];
        }
        return false;
    }
}