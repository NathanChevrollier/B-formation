<?php

// Fonction d'autoload pour charger automatiquement les classes

spl_autoload_register(function ($className) {
    // Convertir les espaces de noms en chemins de fichiers
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $className . '.php';
    
    // Vérifier si le fichier existe
    if (file_exists($filePath)) {
        require_once $filePath;
        return true;
    }
    
    return false;
});




// Démarrer la session
\Utils\Session::start();