<?php

include_once 'assets/files/bdd.php';

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'student') {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'étudiant
$sql = "SELECT firstname, surname FROM user WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

$firstname = $user_info['firstname'] ?? 'Inconnu';
$surname = $user_info['surname'] ?? '';
$user_name = trim($firstname . ' ' . $surname);

// Récupérer les informations de la classe de l'étudiant
$sql = "SELECT class.name AS class_name FROM class 
        INNER JOIN user ON class.id = user.class_id 
        WHERE user.id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$class_info = $stmt->fetch(PDO::FETCH_ASSOC);
$class_name = $class_info['class_name'] ?? 'Non attribuée';

// Récupérer l'emploi du temps de l'étudiant
$sql = "SELECT schedule.id, schedule.start_datetime, schedule.end_datetime, subject.name AS subject_name
        FROM schedule
        INNER JOIN subject ON schedule.Subject_id = subject.id
        WHERE schedule.class_id = (SELECT class_id FROM user WHERE id = :user_id)
        ORDER BY schedule.start_datetime";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                        $sql = "SELECT status FROM signature WHERE User_id = :user_id AND Schedule_id = :schedule_id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                        $stmt->bindParam(':schedule_id', $entry['id'], PDO::PARAM_INT);
                        $stmt->execute();
                        $signature = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Vérifier si une signature existe ou non
                        if ($signature) {
                            $status = $signature['status'] ?? 'pending';
                        } else {
                            $status = 'Non signé';
                        }
                    ?>
                    <div class="card text-center mx-auto" style="width: 100%; max-width: 200px;">
                        <h3><?php echo htmlspecialchars($entry['subject_name']); ?></h3>
                        <h4 class="text-muted"><?php echo date("H:i", strtotime($entry['start_datetime'])); ?> - <?php echo date("H:i", strtotime($entry['end_datetime'])); ?></h4>
                        <p class="text-muted">Statut: <strong><?php echo ucfirst($status); ?></strong></p>
                        <?php if ($status === 'Non signé' || $status === 'pending'): ?>
                            <form method="POST" action="assets/files/student_signature.php">
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
