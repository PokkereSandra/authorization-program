<?php

namespace App\Controllers;

use App\Auth;
use App\Views\TwigView;

class HomepageController
{
    public function home(): TwigView
    {
        return new TwigView('home', ['user' => Auth::isAuthorized()]);
    }

}
