<?php
require_once 'config/autoload.php';

use Controllers\SignatureController;

$controller = new SignatureController();

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
    case 'validateSignature':
        $controller->validateSignature();
        break;
    case 'registerForClass':
        $controller->registerForClass();
        break;
    default:
        $controller->index();
        break;
}