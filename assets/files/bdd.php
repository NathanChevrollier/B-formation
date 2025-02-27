<?php
$dsn = "mysql:host=localhost;dbname=projet_1;charset=utf8";
$username = "Nathan";
$password = "@AsNath17"; 

try {
    // Créer la connexion PDO
    $pdo = new PDO($dsn, $username, $password);
    // Configurer PDO pour afficher les erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}
?>
