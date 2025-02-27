<?php

include_once 'bdd.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../student.php'));
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifier si un cours est en cours pour l'étudiant
$sql = "SELECT id FROM schedule 
        WHERE class_id = (SELECT class_id FROM user WHERE id = :user_id)
        AND start_datetime <= NOW() 
        AND end_datetime >= NOW()
        ORDER BY start_datetime LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$current_class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current_class) {
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../student.php'));
    exit();
}

$schedule_id = $current_class['id'];

// Vérifier si une signature existe déjà pour ce cours
$sql = "SELECT status FROM signature WHERE User_id = :user_id AND Schedule_id = :schedule_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
$stmt->execute();
$signature = $stmt->fetch(PDO::FETCH_ASSOC);

// Si la signature existe, on la met à jour en "validated"
if ($signature) {
    $sql = "UPDATE signature SET status = 'validated' WHERE User_id = :user_id AND Schedule_id = :schedule_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
    $stmt->execute();
}

// Vérification de la mise à jour
$sql = "SELECT status FROM signature WHERE User_id = :user_id AND Schedule_id = :schedule_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
$stmt->execute();
$updated_status = $stmt->fetch(PDO::FETCH_ASSOC);

// Si la mise à jour n'a pas fonctionné, on redirige sans modifier
if (!$updated_status || $updated_status['status'] !== 'validated') {
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../student.php'));
    exit();
}

// Redirection vers la page précédente après la signature réussie
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();

?>
