<?php
require_once 'config/autoload.php';

use Controllers\ClassController;

$controller = new ClassController();

$action = $_POST['action'] ?? '';
switch ($action) {
    case 'add':
        $controller->add();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        $controller->index();
        break;
}