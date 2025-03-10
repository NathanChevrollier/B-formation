<?php

require_once 'config/autoload.php';
include_once 'views/index.html';

?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="d-flex flex-column justify-content-center align-items-center vh-100">

    <img class="logo" src="assets/imgs/logo.webp" alt="Logo">

    <form method="post" action="views/login.php" class="login">
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="nom@exemple.com" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Se connecter</button>

        <div class="form-register text-center">
            <a href="views/register.php" target="_self">Pas encore inscrit ? Inscrivez-vous</a>
        </div>

        <p class="mt-4 text-muted text-center">© 2024–2025</p>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-whnZFKvc4YBmTfZzE/OMlP3DZ6ld03z/lNd4x5dS/OtE2V4Z3mcnSv4uNQ5Z2X+U"
        crossorigin="anonymous"></script>
</body>

</html>