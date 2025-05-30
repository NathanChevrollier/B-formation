<?php
require_once __DIR__ . '/../config/autoload.php';
require_once __DIR__ . '/../utils/verif.php';
use Models\User;
use Models\Classroom;
use Utils\Auth;
use Utils\Session;

Auth::requireRole('admin');

// Récupérer les données nécessaires
$users = User::findAll();
$classes = Classroom::findAll();
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
        <a href="<?= BASE_URL ?>/views/admin.php" class="btn btn-outline-light">Retour à l'Accueil Admin</a>
    </div>
</header>

<div class="container">
    <h2 class="mb-4">Ajouter un Utilisateur</h2>
    <form method="POST" action="<?= BASE_URL ?>/user_controller.php" class="mb-4">
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
                <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
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
                        <option value="<?php echo $class->getId(); ?>"><?php echo $class->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" name="action" value="add" class="btn btn-primary w-100">Ajouter</button>
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
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <form method="POST" action="user_controller.php">
                        <td>
                            <input type="text" name="surname" value="<?php echo htmlspecialchars($user->getSurname()); ?>" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="firstname" value="<?php echo htmlspecialchars($user->getFirstname()); ?>" class="form-control">
                        </td>
                        <td>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" class="form-control">
                        </td>
                        <td>
                            <select name="role" class="form-select">
                                <option value="admin" <?php if ($user->getRole() === 'admin') echo 'selected'; ?>>Admin</option>
                                <option value="student" <?php if ($user->getRole() === 'student') echo 'selected'; ?>>Student</option>
                                <option value="teacher" <?php if ($user->getRole() === 'teacher') echo 'selected'; ?>>Teacher</option>
                            </select>
                        </td>
                        <td>
                            <select name="class_id" class="form-select">
                                <option value="" <?php if (is_null($user->getClassId())) echo 'selected'; ?>>Aucune</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class->getId(); ?>" <?php if ($user->getClassId() == $class->getId()) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($class->getName()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="id" value="<?php echo $user->getId(); ?>">
                            <button type="submit" name="action" value="update" class="btn btn-success btn-sm">Modifier</button>
                            <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Supprimer</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Aucun utilisateur trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>