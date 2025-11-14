<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

$controllerName = $_GET['controller'] ?? 'home';
$actionName     = $_GET['action'] ?? 'index';

$controllerClass = ucfirst($controllerName) . 'Controller';
$controllerFile  = APP_PATH . '/control/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    die("Controlador no encontrado: $controllerClass");
}

require_once $controllerFile;

$controller = new $controllerClass();

if (!method_exists($controller, $actionName)) {
    die("Acción no encontrada: $actionName");
}

$controller->$actionName();
