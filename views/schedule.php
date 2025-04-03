<?php
require_once __DIR__ . '/../config/autoload.php';
require_once __DIR__ . '/../utils/verif.php';
use Models\User;
use Models\Classroom;
use Utils\Auth;
use Utils\Session;

// Assurez-vous que l'utilisateur est connecté
Auth::requireLogin();

$user = Auth::getUser();
$user_name = $user->getFirstname() . ' ' . $user->getSurname();
$user_role = $user->getRole();

// Récupérer l'emploi du temps en fonction du rôle
if ($user_role === 'student') {
    // Pour un étudiant, récupérer l'emploi du temps de sa classe
    $schedule = [];
    if ($user->getClassId()) {
        $schedule = \Models\Schedule::findByClassId($user->getClassId());
    }
} elseif ($user_role === 'teacher') {
    // Pour un professeur, récupérer son propre emploi du temps
    $schedule = \Models\Schedule::findByTeacherId($user->getId());
} else {
    $schedule = [];
}

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
            <?php if (empty($schedule)): ?>
                <tr>
                    <td colspan="<?= $user_role === 'student' ? 5 : 4 ?>" class="text-center">
                        Aucun cours prévu
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($schedule as $entry): ?>
                    <tr>
                        <td><?php echo date("d/m/Y", strtotime($entry->getStartDatetime())); ?></td>
                        <td><?php echo date("H:i", strtotime($entry->getStartDatetime())); ?> - <?php echo date("H:i", strtotime($entry->getEndDatetime())); ?></td>
                        <td><?php echo htmlspecialchars($entry->getClass()->getName()); ?></td>
                        <td><?php echo htmlspecialchars($entry->getSubject()->getName()); ?></td>
                        <?php if ($user_role === 'student'): ?>
                            <td><?php echo htmlspecialchars($entry->getTeacher()->getEmail()); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>