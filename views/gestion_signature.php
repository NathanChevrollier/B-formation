<?php 
require_once 'config/autoload.php';
require_once '../utils/verif.php';
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
    <title>Gestion des Signatures de Présence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-secondary text-white py-3 mb-4">
    <div class="container text-center">
        <h1 class="mb-2">Gestion des Signatures de Présence</h1>
        <a href="admin.php" class="btn btn-outline-light">Retour à l'Accueil Admin</a>
    </div>
</header>

<div class="container">
    <h2 class="mb-4">Signatures de Présence</h2>

    <h3>Ajouter une signature</h3>
    <form method="POST" action="signature_controller.php" class="mb-4">
        <input type="hidden" name="action" value="add">
        <div class="mb-3">
            <label for="user_id" class="form-label">Élève :</label>
            <select name="user_id" id="user_id" class="form-select" required>
                <option value="">Sélectionner un élève</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id']; ?>"><?= htmlspecialchars($student['email']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="schedule_id" class="form-label">Cours :</label>
            <select name="schedule_id" id="schedule_id" class="form-select" required>
                <option value="">Sélectionner un cours</option>
                <?php foreach ($schedules as $schedule): ?>
                    <option value="<?= $schedule['id']; ?>"><?= htmlspecialchars($schedule['start_datetime']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nom Élève</th>
                <th>Classe</th>
                <th>Matière</th>
                <th>Date</th>
                <th>Présence</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($signatures as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['student_name']); ?></td>
                <td><?= htmlspecialchars($row['class_name']); ?></td>
                <td><?= htmlspecialchars($row['subject_name']); ?></td>
                <td><?= htmlspecialchars($row['start_datetime']); ?></td>
                <td>
                    <span class="badge <?= $row['status'] === 'validated' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                        <?= htmlspecialchars($row['status'] === 'validated' ? 'Validée' : 'En attente'); ?>
                    </span>
                </td>
                <td>
                    <form method="POST" action="signature_controller.php" class="d-inline">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="signature_id" value="<?= $row['signature_id']; ?>">
                        <select name="status" class="form-select d-inline w-auto">
                            <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : ''; ?>>En attente</option>
                            <option value="validated" <?= $row['status'] === 'validated' ? 'selected' : ''; ?>>Validée</option>
                        </select>
                        <button type="submit" class="btn btn-warning btn-sm">Modifier</button>
                    </form>
                    <form method="POST" action="signature_controller.php" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="signature_id" value="<?= $row['signature_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>