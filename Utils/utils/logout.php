<?php
require_once __DIR__ . '/../config/autoload.php';
use Controllers\AuthController;

$auth = new AuthController();
$auth->logout();
?>