<?php

include_once 'assets/files/bdd.php';

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.html");
    exit();
}

include_once 'assets/files/modify_users.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-secondary text-white py-3 mb-4">
    <div class="container text-center">
        <h1 class="mb-2">Gestion des Utilisateurs</h1>
        <a href="admin.php" class="btn btn-outline-light">Retour à l'Accueil Admin</a>
    </div>
</header>

<div class="container">
    <h2 class="mb-4">Ajouter un Utilisateur</h2>
    <form method="POST" action="assets/files/add_user.php" class="mb-4">
        <div class="row g-3">
            <div class="col-md-2">
                <input type="text" name="surname" class="form-control" placeholder="Nom" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="firstname" class="form-control" placeholder="Prénom" required>
            </div>
            <div class="col-md-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select" required>
                    <option value="admin">Admin</option>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="class_id" class="form-select">
                    <option value="">Aucune</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">Ajouter</button>
            </div>
        </div>
    </form>

    <h2 class="mb-4">Liste des Utilisateurs</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Classe</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr id="user<?php echo $user['id']; ?>">
                <form method="POST" action="assets/files/modify_users.php">
                    <td>
                        <input type="text" name="surname" value="<?php echo $user['surname']; ?>" class="form-control">
                    </td>
                    <td>
                        <input type="text" name="firstname" value="<?php echo $user['firstname']; ?>" class="form-control">
                    </td>
                    <td>
                        <input type="email" name="email" value="<?php echo $user['email']; ?>" class="form-control">
                    </td>
                    <td>
                        <select name="role" class="form-select">
                            <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
                            <option value="student" <?php if ($user['role'] === 'student') echo 'selected'; ?>>Student</option>
                            <option value="teacher" <?php if ($user['role'] === 'teacher') echo 'selected'; ?>>Teacher</option>
                        </select>
                    </td>
                    <td>
                        <select name="class_id" class="form-select">
                            <option value="" <?php if (is_null($user['class_id'])) echo 'selected'; ?>>Aucune</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class['id']; ?>" <?php if ($user['class_id'] == $class['id']) echo 'selected'; ?>>
                                    <?php echo $class['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
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
