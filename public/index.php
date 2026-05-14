<?php

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/helpers.php';
require_once BASE_PATH . '/controllers/AuthController.php';

AuthController::restoreFromCookie();

$uri    = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
$uri    = ($uri === '') ? '/' : $uri;
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/') {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!empty($_SESSION['user_id']))
        redirect($_SESSION['role'] === 'admin' ? '/admin/products' : '/products');
    redirect('/login');

} elseif ($uri === '/register') {
    $c = new AuthController();
    $method === 'POST' ? $c->register() : $c->registerForm();

} elseif ($uri === '/login') {
    $c = new AuthController();
    $method === 'POST' ? $c->login() : $c->loginForm();

} elseif ($uri === '/logout') {
    (new AuthController())->logout();

} elseif ($uri === '/profile') {
    require_once BASE_PATH . '/controllers/ProfileController.php';
    (new ProfileController())->show();

} elseif ($uri === '/profile/update') {
    require_once BASE_PATH . '/controllers/ProfileController.php';
    (new ProfileController())->update();

} elseif ($uri === '/profile/password') {
    require_once BASE_PATH . '/controllers/ProfileController.php';
    (new ProfileController())->changePassword();



} else {
    http_response_code(404);
    include BASE_PATH . '/views/errors/404.php';
}