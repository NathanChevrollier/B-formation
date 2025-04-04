<?php
// test auto deploy

require_once 'config/autoload.php';

// Routeur simple
$route = $_GET['route'] ?? 'home';

switch ($route) {
    case 'users':
        $controller = new Controllers\UserController();
        break;
    case 'classes':
        $controller = new Controllers\ClassController();
        break;

    default:
        // Page d'accueil ou page 404
        include 'views/index.html';
        exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'index';
$controller->$action();
?>