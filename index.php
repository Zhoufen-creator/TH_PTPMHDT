<?php

session_start();

require_once 'app/models/ProductModel.php';
require_once 'app/helpers/SessionHelper.php';

$currentRoute = strtolower($_GET['url'] ?? '');

$publicRoutes = [
    'account/login',
    'account/register',
    'account/googlelogin',
    'account/googlecallback',
    'account/githublogin',
    'account/githubcallback'
];

if (
    !SessionHelper::isLoggedIn()
    && !in_array($currentRoute, $publicRoutes)
) {
    header("Location: /account/login");
    exit();
}

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$controllerName = isset($url[0]) && $url[0] !== ''? ucfirst($url[0]) . 'Controller': 'DefaultController';

$action = isset($url[1]) && $url[1] !== ''? $url[1]: 'index';

$controllerFile = 'app/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    die('Controller not found');
}

require_once $controllerFile;

$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    die('Action not found');
}

call_user_func_array([$controller, $action],array_slice($url, 2));