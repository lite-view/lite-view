<?php


namespace App\Common;

class General
{
    private static $data;

    private function __construct()
    {
    }

    public static function get($key)
    {
        return self::$data[$key];
    }

    public static function set($key, $value)
    {
        if (isset(self::$data[$key])) {
            trigger_error("General `$key` already exist.", E_USER_ERROR);
        }
        self::$data[$key] = $value;
    }
}