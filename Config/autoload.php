<?php
// Charger les constantes (url debase pour vps)
require_once __DIR__ . '/constants.php';

// Fonction d'autoload pour charger automatiquement les classes

spl_autoload_register(function ($className) {
    // Remplace les \ par des /
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    // Si premier dossier est en minuscule, on le met en maj
    $segments = explode(DIRECTORY_SEPARATOR, $className);
    if (isset($segments[0])) {
        $segments[0] = ucfirst($segments[0]);
    }

    $filePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments) . '.php';

    if (file_exists($filePath)) {
        require_once $filePath;
        return true;
    }

    return false;
});

// Démarrer la session
\Utils\Session::start();
