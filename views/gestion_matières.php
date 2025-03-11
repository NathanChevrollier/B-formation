<?php
require_once 'config/autoload.php';
use Models\User;
use Models\Classroom;
use Utils\Auth;
use Utils\Session;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Matières</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-secondary text-white py-3 mb-4">
    <div class="container text-center">
        <h1 class="mb-2">Gestion des matières</h1>
        <a href="admin.php" class="btn btn-outline-light">Retour à l'accueil admin</a>
    </div>
</header>

<div class="container">
    <!-- Ajouter une matière -->
    <h2 class="mb-4">Ajouter une matière</h2>
    <form method="POST" action="subject_controller.php" class="mb-4">
        <div class="row g-3">
            <div class="col-md-10">
                <input type="text" name="subject_name" class="form-control" placeholder="Nom de la matière" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="action" value="add" class="btn btn-primary w-100">Ajouter</button>
            </div>
        </div>
    </form>

    <!-- Liste des matières -->
    <h2 class="mb-4">Liste des Matières</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nom de la Matière</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $subject): ?>
            <tr>
                <form method="POST" action="subject_controller.php">
                    <td>
                        <input type="text" name="subject_name" value="<?php echo $subject['name']; ?>" class="form-control">
                    </td>
                    <td>
                        <input type="hidden" name="subject_id" value="<?php echo $subject['id']; ?>">
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
