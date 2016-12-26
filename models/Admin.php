<?php

class Admin
{
    /**
     * @param $login
     * @param $password
     * @return bool
     */
    public static function getAdmin($login, $password)
    {
        $db = DB::getConnection();

        $password = md5(md5($password));

        $query = "SELECT login,password FROM admin WHERE login=:login AND password=:password";
        $result = $db->prepare($query);

        $result->bindParam(':login', $login, PDO::PARAM_STR);
        $result->bindParam(':password', $password, PDO::PARAM_STR);

        $result->execute();

        if($result->fetchColumn()) {
            return true;
        }

        return false;
    }

    /**
     * @param $rand
     * @return bool
     */
    public static function getAdminByRand($rand)
    {
        $db = DB::getConnection();

        $query = "SELECT login FROM admin WHERE rand=:rand";
        $result = $db->prepare($query);

        $result->bindParam(':rand',$rand,PDO::PARAM_STR);

        $result->execute();

        if($result->fetchColumn()) {
            return true;
        }

        return false;
    }

    /**
     * рандом
     * @return string
     */
    public static function getRand()
    {
        $rand = '';
        $dictionary = ['a','b','c','d','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',0,1,2,3,4,5,6,7,8,9];
        for($i = 0; $i < 50; $i++){
            $rand.=$dictionary[mt_rand(0,count($dictionary)-1)];
        }
        $rand=time().md5($rand);

        return $rand;
    }

    /**
     * установка рандома для админа
     * @param $rand
     * @return bool
     */
    public static function setUserAdminRand($rand)
    {
        $db = DB::getConnection();
        $query = "UPDATE admin SET rand=:rand";
        $result = $db->prepare($query);
        $result->bindParam(':rand',$rand,PDO::PARAM_STR);

        return $result->execute();
    }

    /**
     * установка авторизованного админа
     * @param $value
     */
    public static function setSessionUserAdmin($value)
    {
        $_SESSION['admin'] = $value;
        $_SESSION['time'] = time();
    }

    /**
     * получение рандома аднмина
     * @return mixed
     */
    public static function getSessionUserAdmin()
    {
        if(isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            return $_SESSION['admin'];
        }
        return false;
    }

    /**
     * получение времени регистрации админа
     * @return bool
     */
    public static function getSessionTimeUserAdmin()
    {
        if(isset($_SESSION['time']) && !empty($_SESSION['time'])) {
            return $_SESSION['time'];
        }
        return false;
    }
}