<?php
require_once 'config/autoload.php';
use Utils\Auth;
use Models\Schedule;
use Models\Signature;

Auth::requireRole('student');
$user = Auth::getUser();
$user_name = $user->getFirstname() . ' ' . $user->getSurname();

$class = $user->getClassId();
$class_name = $class ? $class->getName() : 'Non attribuée';

// Récupérer l'emploi du temps
$schedule = Schedule::findByClassId($user->getClassId());

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/student.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <header class="header pb-5 mb-5">
        <?php include 'header.php'; ?>
    </header>

    <div class="container-fluid mt-4 d-flex flex-column align-items-center flex-grow-1">
        
        <div class="welcome-section text-center mb-2">
            <h1>Bienvenue !</h1>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <h4>Classe : <span class="text-muted"><?php echo htmlspecialchars($class_name); ?></span></h4>
            <p class="text-muted">Date : <?php echo date("d/m/Y"); ?></p>
            <p>Bienvenue sur votre page étudiant. Ici, vous pouvez voir les cours programmés pour aujourd'hui et enregistrer votre présence en un clic ! Profitez de votre journée et restez informé de votre emploi du temps !</p>
            <hr>
            <p class="text-muted">N'oubliez pas de vérifier les mises à jour de cours et de suivre votre progression.</p>
        </div>

        <div class="today-section text-center w-100">
            <h2 class="mb-3">Aujourd'hui</h2>
            <div class="card-container d-flex justify-content-around gap-4 flex-wrap">
                <?php foreach ($schedule as $entry): ?>
                    <?php 
                        // Vérifier si une signature existe pour ce cours
                        $signature = Signature::findByUserAndSchedule($user->getId(), $entry['id']);
                        $status = $signature ? $signature->getStatus() : 'Non signé';

                    ?>
                    <div class="card text-center mx-auto" style="width: 100%; max-width: 200px;">
                        <h3><?php echo htmlspecialchars($entry['subject_name']); ?></h3>
                        <h4 class="text-muted"><?php echo date("H:i", strtotime($entry['start_datetime'])); ?> - <?php echo date("H:i", strtotime($entry['end_datetime'])); ?></h4>
                        <p class="text-muted">Statut: <strong><?php echo ucfirst($status); ?></strong></p>
                        <?php if ($status === 'Non signé' || $status === 'pending'): ?>
                            <form method="POST" action="signature_controller.php">
                                <input type="hidden" name="action" value="validateSignature">
                                <button type="submit" class="btn btn-primary mt-3">Signer</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-success mt-3" disabled>Signé</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
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
