<?php
require_once 'config/autoload.php';
use Models\User;
use Models\Classroom;
use Utils\Auth;
use Utils\Session;

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.html");
    exit();
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-secondary text-white py-3 mb-4">
    <div class="container text-center">
        <h1 class="mb-2">Gestion des Classes</h1>
        <a href="admin.php" class="btn btn-outline-light">Retour à l'Accueil Admin</a>
    </div>
</header>

<div class="container">
    <h2 class="mb-4">Ajouter une Classe</h2>
    <form method="POST" action="class_controller.php" class="mb-4">
        <div class="row g-3">
            <div class="col-md-10">
                <input type="text" name="class_name" class="form-control" placeholder="Nom de la classe" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="action" value="add" class="btn btn-primary w-100">Ajouter</button>
            </div>
        </div>
    </form>

    <h2 class="mb-4">Liste des Classes</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nom de la Classe</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($classes as $class): ?>
            <tr>
                <form method="POST" action="class_controller.php">
                    <td>
                        <input type="text" name="class_name" value="<?php echo $class['name']; ?>" class="form-control">
                    </td>
                    <td>
                        <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                        <button type="submit" name="action" value="update" class="btn btn-success btn-sm">Modifier</button>
                        <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Supprimer</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
