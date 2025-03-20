<?php
require_once __DIR__ . '/../config/autoload.php';
use Utils\Auth;

Auth::requireRole('admin');
$user = Auth::getUser();
$user_name = $user->getEmail();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion du système</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="d-flex flex-column min-vh-100">

    <header class="bg-secondary text-white py-3 mb-5">
        <div class="container text-center">
            <h1 class="mb-2">Panneau d'administration</h1>
            <p class="mb-2">Connecté en tant que <strong><?php echo htmlspecialchars($user_name); ?></strong></p>
            <a href="index.html" class="btn btn-outline-light">Déconnexion</a>
        </div>
    </header>

    <div class="container mb-5">
        <div class="row">

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">Gestion des utilisateurs</h3>
                        <p class="card-text">Créez et gérez les comptes des enseignants et des élèves, et attribuez-les aux classes respectives.</p>
                        <a href="gestion_utilisateur.php" class="btn btn-primary w-100">Gérer les utilisateurs</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">Gestion des classes</h3>
                        <p class="card-text">Ajoutez, modifiez ou supprimez des classes. Associez les enseignants et les élèves aux différentes classes.</p>
                        <a href="gestion_classes.php" class="btn btn-primary w-100">Gérer les classes</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">


            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">Gestion des matières</h3>
                        <p class="card-text">Gérez les Cours enseignées et associez-les aux différents enseignants et classes.</p><br>
                        <a href="gestion_matières.php" class="btn btn-primary w-100">Gérer les matières</a>
                    </div>
                </div>
            </div>


            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">Gestion des emplois du temps</h3>
                        <p class="card-text">Créez et modifiez les emplois du temps des classes, en ajoutant des matières et des horaires pour chaque classe.</p>
                        <a href="gestion_planning.php" class="btn btn-primary w-100">Gérer les emplois du temps</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">


            <div class="col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">Gestion des signatures de présence</h3>
                        <p class="card-text">Suivez les présences et enregistrez les signatures des élèves pour chaque cours.</p>
                        <a href="gestion_signature.php" class="btn btn-primary w-100">Gérer les signatures de présence</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <footer class="bg-secondary text-white text-center py-3 mt-auto">
        <div class="container">
            © 2024 - Système de gestion administratif
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-whnZFKvc4YBmTfZzE/OMlP3DZ6ld03z/lNd4x5dS/OtE2V4Z3mcnSv4uNQ5Z2X+U" crossorigin="anonymous"></script>
</body>
</html>
