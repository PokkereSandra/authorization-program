<?php

use App\Auth;
use App\Cookie;
use App\Redirect;
use App\Repositories\MySqlUsersRepository;
use App\Repositories\UsersRepository;
use DI\Container;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';


$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'App\Controllers\HomepageController@home');
    $r->addRoute('POST', '/register', 'App\Controllers\RegisterController@store');
    $r->addRoute('GET', '/register', 'App\Controllers\RegisterController@showRegisterForm');
    $r->addRoute('GET', '/login', 'App\Controllers\LoginController@showLogin');
    $r->addRoute('POST', '/login', 'App\Controllers\LoginController@authorization');
    $r->addRoute('GET', '/logout', 'App\Controllers\LoginController@logout');
    $r->addRoute('GET', '/profile', 'App\Controllers\ProfileController@profile');
    $r->addRoute('POST', '/profile', 'App\Controllers\ProfileController@edit');
});
if (Cookie::exists('remember_me')) {
    $token = Cookie::get('remember_me');
    $user_id = (new MySqlUsersRepository())->getUserIdByToken($token);
    session_start();
    if (!empty($user_id)) {
        Auth::authorize((int)$user_id);
    }
} else {
    session_start();
}

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo 404 . ' Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo 405 . ' Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        [$controller, $method] = explode('@', $handler);

        $loader = new FilesystemLoader('app/Views');
        $twig = new Environment($loader);

        $twig->addGlobal('errors', $_SESSION['errors'] ?? []);
        $twig->addGlobal('success', $_SESSION['success'] ?? []);

        $container = new Container();

        $container->set(UsersRepository::class, DI\create(MySqlUsersRepository::class));

        $response = $container->get($controller)->$method($vars);

        if ($response instanceof App\Views\TwigView) {
            unset($_SESSION['errors']);
            unset($_SESSION['success']);
            $template = $twig->load($response->getTemplatePath() . ".html.twig");
            echo $template->render($response->getData());
            exit;
        }
        if ($response instanceof Redirect) {
            header('Location: ' . $response->getLocation());
        }
        break;
}
