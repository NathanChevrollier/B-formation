<?php
require_once __DIR__ . '/../config/autoload.php';
require_once __DIR__ . '/../utils/verif.php';

use Utils\Auth;
use Models\Schedule;
use Models\Signature;

Auth::requireRole('student');
$user = Auth::getUser();
$user_name = $user->getFirstname() . ' ' . $user->getSurname();
$class = $user->getClass();
$class_name = $class ? $class->getName() : 'Non attribuée';
$classId = $user->getClassId();

$schedule = Schedule::findTodayForClass($classId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/student.css">
</head>
<body class="d-flex flex-column min-vh-100">

<header class="header pb-5 mb-5">
    <?php include 'header.php'; ?>
</header>

<div class="container-fluid mt-4 d-flex flex-column align-items-center flex-grow-1">

    <div class="welcome-section text-center mb-4">
        <h1>Bienvenue !</h1>
        <h3><?php echo htmlspecialchars($user_name); ?></h3>
        <h4>Classe : <span class="text-muted"><?php echo htmlspecialchars($class_name); ?></span></h4>
        <p class="text-muted">Date : <?php echo date("d/m/Y"); ?></p>
        <p>
            Bienvenue sur votre page étudiant. Ici, vous pouvez voir les cours programmés pour aujourd'hui
            et enregistrer votre présence en un clic ! Profitez de votre journée et restez informé de votre emploi du temps !
        </p>
        <hr>
        <p class="text-muted">N'oubliez pas de vérifier les mises à jour de cours et de suivre votre progression.</p>
    </div>

    <div class="today-section text-center w-100">
        <h2 class="mb-4">Cours du jour</h2>
        <div class="card-container d-flex justify-content-center flex-wrap gap-4">
            <?php if (empty($schedule)): ?>
                <div class="alert alert-info w-100 text-center" role="alert">
                    Aucun cours prévu aujourd'hui. Profitez de votre temps libre !
                </div>
            <?php else: ?>
                <?php foreach ($schedule as $entry): ?>
                    <?php 
                        $signature = Signature::findByUserAndSchedule($user->getId(), $entry->getId());
                        $status = $signature ? $signature->getStatus() : 'Non signé';
                        
                        // Déterminer si les signatures sont ouvertes pour ce cours
                        $signaturesOpen = $entry->getSignaturesOpen();
                        
                        // Définir le statut d'affichage et la classe du badge
                        if ($signaturesOpen) {
                            // Si les signatures sont ouvertes
                            if ($status === 'validated') {
                                $displayStatus = 'Signé';
                                $badgeClass = 'bg-success text-white';
                            } else {
                                $displayStatus = 'En attente de signature';
                                $badgeClass = 'bg-warning text-dark';
                            }
                        } else {
                            // Si les signatures ne sont pas encore ouvertes
                            $displayStatus = 'En attente du professeur';
                            $badgeClass = 'bg-secondary text-white';
                        }
                    ?>

                    <span class="badge rounded-pill <?= $badgeClass ?>">
                        <?php echo $displayStatus; ?>
                    </span>

                    <?php if ($signaturesOpen && ($status !== 'validated')): ?>
                        <form method="POST" action="<?= BASE_URL ?>/signature_controller.php" class="mt-3">
                            <input type="hidden" name="action" value="validateSignature">
                            <input type="hidden" name="schedule_id" value="<?= $entry->getId(); ?>">
                            <button type="submit" class="btn btn-primary">Signer</button>
                        </form>
                    <?php elseif ($status === 'validated'): ?>
                        <button class="btn btn-success mt-3" disabled>Déjà signé</button>
                    <?php else: ?>
                        <button class="btn btn-secondary mt-3" disabled>En attente du professeur</button>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<footer class="bg-secondary text-white text-center py-3 mt-auto">
    <div class="container">
        © <?php echo date("Y"); ?> - Système de gestion administratif
    </div>
</footer>

</body>
</html>
