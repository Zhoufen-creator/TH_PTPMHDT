<?php

session_start();

require_once 'app/models/ProductModel.php';
require_once 'app/helpers/SessionHelper.php';

require_once 'app/controllers/CategoryApiController.php';
require_once "app/controllers/ProductApiController.php";

$currentRoute = strtolower($_GET['url'] ?? '');

$publicRoutes = [
    'account/login',
    'account/register',
    'account/save',
    'account/checklogin',
    'account/googlelogin',
    'account/googlecallback',
    'account/githublogin',
    'account/githubcallback'
];

if (!SessionHelper::isLoggedIn() && !in_array($currentRoute, $publicRoutes)) 
{
    header("Location: /account/login");
    exit();
}

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$controllerName = isset($url[0]) && $url[0] !== ''? ucfirst($url[0]) . 'Controller': 'DefaultController';

$action = isset($url[1]) && $url[1] !== ''? $url[1]: 'index';

if ($controllerName === "ApiController" && isset($url[1])) {
    $apiControllerName = ucfirst($url[1]) . 'ApiController';
    if (file_exists("app/controllers/" . $apiControllerName . ".php")) {
        require_once "app/controllers/" . $apiControllerName . ".php";
        $controller = new $apiControllerName();

        $method = $_SERVER['REQUEST_METHOD'];
        $id = $url[2] ?? null;

        switch ($method) {
            case 'GET':
                if ($id) {
                    $action = 'show';
                } else {
                    $action = 'index';
                }
                break;
            case 'POST':
                $action = 'store';
                break;
            case 'PUT':
                if ($id) {
                    $action = 'update';
                } 
                break;
            case 'DELETE':
                if ($id) {
                    $action = 'destroy';
                } 
                break;
            default:
                http_response_code(405);
                echo json_encode(["message" => "Method not allowed."]);
                exit();
        }

        if (method_exists($controller, $action)) {
            if ($id) {
                call_user_func_array([$controller, $action], [$id]);
            } else {
                call_user_func_array([$controller, $action], []);
            }
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Action not found."]);
        }
        exit();
    } else {
        http_response_code(404);
        echo json_encode(["message" => "API Controller not found."]);
        exit();
    }
}

if (file_exists("app/controllers/" . $controllerName . ".php")) {
    require_once "app/controllers/" . $controllerName . ".php";
    $controller = new $controllerName();
} else {
    die('Controller not found');
}

if (!method_exists($controller, $action)) {
    die('Action not found');
}

call_user_func_array([$controller, $action],array_slice($url, 2));