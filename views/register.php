<?php
require_once __DIR__ . '/../config/autoload.php';
use Utils\Session;
?>

<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex flex-column justify-content-center align-items-center vh-100 bg-light">

    <img class="logo mb-3" src="../assets/imgs/logo.webp" alt="Logo" width="142 px" height="auto">
    <?php 
    // Afficher les messages d'erreur
    if (Session::has('error')): ?>
        <div class="alert alert-danger">
            <?php echo Session::getFlash('error'); ?>
        </div>
    <?php endif; ?>

    <form action="../register_controller.php" method="post" class="login bg-white p-4 rounded shadow-sm w-100"
        style="max-width: 330px;">
        <div class="mb-3">
            <label for="firstname" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Prénom" required>
        </div>

        <div class="mb-3">
            <label for="surname" class="form-label">Nom</label>
            <input type="text" class="form-control" id="surname" name="surname" placeholder="Nom" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="nom@exemple.com" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe"
                required>
        </div>

        <div class="mb-3">
            <label for="confirm-password" class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" id="confirm-password" name="confirm_password"
                placeholder="Confirmez votre mot de passe" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">S'inscrire</button>

        <div class="form-login mt-3 text-center">
            <a href="../index.php" target="_self">Connectez-vous</a>
        </div>

        <p class="mt-4 text-muted text-center">© 2017–2024</p>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-whnZFKvc4YBmTfZzE/OMlP3DZ6ld03z/lNd4x5dS/OtE2V4Z3mcnSv4uNQ5Z2X+U"
        crossorigin="anonymous"></script>
</body>

</html>