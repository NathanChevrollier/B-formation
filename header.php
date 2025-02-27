<?php

include_once 'assets/files/bdd.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    header("Location: index.html");
    exit();
}
?>

<header class="navbar navbar-expand-lg bg-secondary fixed-top shadow">
    <div class="container-fluid">

        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="assets/imgs/logo_inner_transparent.png" alt="Logo" width="50" height="auto" class="me-2">
            <span class="text-white h4 mb-0">B-Formation</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" href="javascript:history.back()">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="schedule.php">Emploi du temps</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.html">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</header>
