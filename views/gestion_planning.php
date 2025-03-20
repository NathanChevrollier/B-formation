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
    <title>Gestion des Emplois du Temps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-secondary text-white py-3 mb-4">
    <div class="container text-center">
        <h1 class="mb-2">Gestion des Emplois du Temps</h1>
        <a href="admin.php" class="btn btn-outline-light">Retour à l'Accueil Admin</a>
    </div>
</header>

<div class="container">
    <h2 class="mb-4">Emplois du Temps des Classes</h2>

    <!-- Tableau des emplois du temps -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Classe</th>
                <th>Matière</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Professeur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($planning as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['class_name']); ?></td>
                <td><?= htmlspecialchars($row['subject_name']); ?></td>
                <td><?= htmlspecialchars($row['start_datetime']); ?></td>
                <td><?= htmlspecialchars($row['end_datetime']); ?></td>
                <td><?= htmlspecialchars($row['teacher_name']); ?></td>
                <td>
                    <!-- Formulaire dans une modale Bootstrap pour modifier -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['schedule_id']; ?>">Modifier</button>

                    <!-- Formulaire pour supprimer -->
                    <form method="POST" action="schedule_controller.php" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="schedule_id" value="<?= $row['schedule_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>

                    <!-- Modale pour modifier -->
                    <div class="modal fade" id="editModal<?= $row['schedule_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['schedule_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel<?= $row['schedule_id']; ?>">Modifier l'Emploi du Temps</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="schedule_controller.php">
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="schedule_id" value="<?= $row['schedule_id']; ?>">
                                        <div class="mb-3">
                                            <label for="class_id" class="form-label">Classe</label>
                                            <select id="class_id" name="class_id" class="form-select" required>
                                                <?php foreach ($classes as $class): ?>
                                                    <option value="<?= $class['id']; ?>" <?= $class['id'] == $row['class_name'] ? 'selected' : ''; ?>><?= htmlspecialchars($class['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="subject_id" class="form-label">Matière</label>
                                            <select id="subject_id" name="subject_id" class="form-select" required>
                                                <?php foreach ($subjects as $subject): ?>
                                                    <option value="<?= $subject['id']; ?>" <?= $subject['id'] == $row['subject_name'] ? 'selected' : ''; ?>><?= htmlspecialchars($subject['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="teacher_id" class="form-label">Professeur</label>
                                            <select id="teacher_id" name="teacher_id" class="form-select" required>
                                                <?php foreach ($teachers as $teacher): ?>
                                                    <option value="<?= $teacher['id']; ?>" <?= $teacher['id'] == $row['teacher_name'] ? 'selected' : ''; ?>><?= htmlspecialchars($teacher['email']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="start_datetime" class="form-label">Début</label>
                                            <input type="datetime-local" id="start_datetime" name="start_datetime" class="form-control" value="<?= htmlspecialchars($row['start_datetime']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="end_datetime" class="form-label">Fin</label>
                                            <input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control" value="<?= htmlspecialchars($row['end_datetime']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulaire pour ajouter un emploi du temps -->
    <h3 class="mt-4">Ajouter un Emploi du Temps</h3>
    <form method="POST" action="schedule_controller.php">
        <input type="hidden" name="action" value="add">
        <div class="mb-3">
            <label for="class_id" class="form-label">Classe</label>
            <select id="class_id" name="class_id" class="form-select" required>
                <option value="">Sélectionnez une classe</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id']; ?>"><?= htmlspecialchars($class['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="subject_id" class="form-label">Matière</label>
            <select id="subject_id" name="subject_id" class="form-select" required>
                <option value="">Sélectionnez une matière</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['id']; ?>"><?= htmlspecialchars($subject['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="teacher_id" class="form-label">Professeur</label>
            <select id="teacher_id" name="teacher_id" class="form-select" required>
                <option value="">Sélectionnez un professeur</option>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['id']; ?>"><?= htmlspecialchars($teacher['email']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="start_datetime" class="form-label">Début</label>
            <input type="datetime-local" id="start_datetime" name="start_datetime" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end_datetime" class="form-label">Fin</label>
            <input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
