<?php

class Validation
{
    /**
     * проверка на число
     * @param $value
     * @return bool
     */
    public static function isNumber($value)
    {
        if(preg_match('/^[0-9]+$/', $value)) {
            return true;
        }
        return false;
    }

    /**
     * проверка на пустоту
     * @param $value
     * @return bool
     */
    public static function isEmpty($value)
    {
        if(!empty($value) && mb_strlen($value) > 0) {
            return true;
        }
        return false;
    }

    /**
     * проверка на тип float
     * @param $value
     * @return bool
     */
    public static function isFloat($value)
    {
        $value = floatval($value);
        if(is_float($value) && !empty($value) && $value > 0) {
            return true;
        }
        return false;
    }

    /**
     * проверка на email
     * проверка email
     * @param $email
     * @return bool
     */
    public static function isEmail($email)
    {
        if(preg_match('/^[a-z0-9_\.\-]+@([a-z0-9\-]+\.)+[a-z]{2,6}$/i', $email)) {
            return true;
        }
        return false;
    }

    /**
     * проверка на логин
     * @param $login
     * @return bool
     */
    public static function isLogin($login)
    {
        if(preg_match('/^[A-Za-z0-9-_]{6,}$/',$login)) {
            return true;
        }
        return false;
    }
}