<?php
require_once 'config/autoload.php';
use Controllers\AuthController;

error_log("Register controller called");

$auth = new AuthController();
$auth->register();
?>