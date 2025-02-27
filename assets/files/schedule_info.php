<?php

include_once 'assets/files/bdd.php';

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Récupérer les informations de l'utilisateur
$sql = "SELECT firstname, surname FROM user WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

$firstname = $user_info['firstname'] ?? 'Inconnu';
$surname = $user_info['surname'] ?? '';
$user_name = trim($firstname . ' ' . $surname);

// Récupérer l'emploi du temps selon le rôle de l'utilisateur
if ($user_role === 'student') {
    $sql = "SELECT schedule.start_datetime, schedule.end_datetime, 
                   subject.name AS subject_name, class.name AS class_name, user.firstname AS teacher_firstname, user.surname AS teacher_surname
            FROM schedule
            INNER JOIN subject ON schedule.Subject_id = subject.id
            INNER JOIN class ON schedule.class_id = class.id
            INNER JOIN user ON schedule.User_id = user.id
            WHERE schedule.class_id = (SELECT class_id FROM user WHERE id = :user_id)
            ORDER BY schedule.start_datetime";
} else { // Professeur
    $sql = "SELECT schedule.start_datetime, schedule.end_datetime, 
                   subject.name AS subject_name, class.name AS class_name
            FROM schedule
            INNER JOIN subject ON schedule.Subject_id = subject.id
            INNER JOIN class ON schedule.class_id = class.id
            WHERE schedule.User_id = :user_id
            ORDER BY schedule.start_datetime";
}

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
