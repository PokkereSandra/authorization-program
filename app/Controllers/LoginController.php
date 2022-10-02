<?php

namespace App\Controllers;

use App\Auth;
use App\Redirect;
use App\Services\LoginService;
use App\Services\LoginServiceRequest;
use App\Views\TwigView;

class LoginController
{
    private LoginService $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function showLogin(): TwigView
    {
        if (Auth::isAuthorized()) {
            header('Location:/');
        }

        return new TwigView('login', []);
    }

    public function authorization(): Redirect
    {
        $remember_me = false;
        if (isset($_POST['remember_me'])) {
            $remember_me = true;
        }
        $this->loginService->execute(

            new LoginServiceRequest(
                $_POST['your_name'],
                $_POST['your_pass'],
                $remember_me,
            ));
        if (Auth::isAuthorized()) {
            return new Redirect('/');
        }
        return new Redirect('/login');
    }

    public function logout(): Redirect
    {
        Auth::logout();
        return new Redirect("/");
    }

}
