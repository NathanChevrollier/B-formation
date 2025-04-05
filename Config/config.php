<?php

$secure_config_file = __DIR__ . '/config_secure.php';
$secure_config = file_exists($secure_config_file) ? include $secure_config_file : [];

return [
    'db' => [
        'host' => $secure_config['db_host'] ?? '',
        'name' => $secure_config['db_name'] ?? '',
        'user' => $secure_config['db_user'] ?? '',
        'pass' => $secure_config['db_pass'] ?? ''
    ]
];
