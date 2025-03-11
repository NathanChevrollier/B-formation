<?php
require_once 'config/autoload.php';

use Controllers\SubjectController;

$controller = new SubjectController();

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