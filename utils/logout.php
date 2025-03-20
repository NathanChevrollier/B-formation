<?php
require_once 'config/autoload.php';
use Controllers\AuthController;

$auth = new AuthController();
$auth->logout();
?>