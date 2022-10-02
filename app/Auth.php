<?php

namespace App;

use App\Repositories\MySqlUsersRepository;

class Auth
{
    public static function isAuthorized(): bool
    {
        return isset($_SESSION['auth_id']);
    }

    public static function authorize(int $id): void
    {
        $_SESSION['auth_id'] = $id;
    }

    public static function logout(): void
    {
        (new MySqlUsersRepository())->deleteToken($_SESSION['auth_id']);
        unset($_SESSION['auth_id']);
        Cookie::delete('remember_me');
    }

}
