<?php
require_once 'config/autoload.php';

use Controllers\ScheduleController;

$controller = new ScheduleController();

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