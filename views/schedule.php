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
    <title>Emploi du temps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="header pb-5 mb-5">
    <?php include 'header.php'; ?>
</header>

<div class="container my-5">
    <h1 class="text-center mb-4">Emploi du temps de <?php echo htmlspecialchars($user_name); ?></h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Classe</th>
                <th>Matière</th>
                <?php if ($user_role === 'student'): ?>
                    <th>Professeur</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedule as $entry): ?>
                <tr>
                    <td><?php echo date("d/m/Y", strtotime($entry['start_datetime'])); ?></td>
                    <td><?php echo date("H:i", strtotime($entry['start_datetime'])); ?> - <?php echo date("H:i", strtotime($entry['end_datetime'])); ?></td>
                    <td><?php echo htmlspecialchars($entry['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($entry['subject_name']); ?></td>
                    <?php if ($user_role === 'student'): ?>
                        <td><?php echo htmlspecialchars($entry['teacher_firstname'] . ' ' . $entry['teacher_surname']); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
