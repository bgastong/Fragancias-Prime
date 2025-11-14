<?php 

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

// Cargar el controlador según la URL
$controllerName = $_GET['controller'] ?? 'home';
$actionName     = $_GET['action'] ?? 'index';

// armo nombre de clase y archivo
$controllerClass = ucfirst($controllerName) . 'Controller';
$controllerFile  = APP_PATH . '/controllers/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    die("Controlador no encontrado: $controllerClass");
}

require_once $controllerFile;

$controller = new $controllerClass();

if (!method_exists($controller, $actionName)) {
    die("Acción no encontrada: $actionName");
}

// Ejecutar acción
$controller->$actionName();
?>