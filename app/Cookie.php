<?php

namespace App;

class Cookie
{
    public static function exists($name): bool
    {
        return isset($_COOKIE[$name]);
    }

    public static function get($name): string
    {
        return $_COOKIE[$name];
    }

    public static function put($name, $value, $expiry): bool
    {
        if (setcookie($name, $value, time() + $expiry, '/')) {
            return true;
        }
        return false;
    }

    public static function delete($name): void
    {
        self::put($name, '', time() - 1);
    }
}