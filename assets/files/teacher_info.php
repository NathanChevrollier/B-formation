<?php

$user_id = $_SESSION['user_id'];
$current_date = date("Y-m-d");
$current_time = date("H:i:s");

// Récupérer les informations de l'enseignant
$sql = "SELECT firstname, surname FROM user WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

$firstname = $user_info['firstname'] ?? 'Inconnu';
$surname = $user_info['surname'] ?? '';
$user_name = trim($firstname . ' ' . $surname);

// Récupérer les cours du jour
$sql = "SELECT schedule.id, schedule.start_datetime, schedule.end_datetime, 
               subject.name AS subject_name, class.name AS class_name
        FROM schedule
        INNER JOIN subject ON schedule.Subject_id = subject.id
        INNER JOIN class ON schedule.class_id = class.id
        WHERE schedule.User_id = :user_id AND DATE(schedule.start_datetime) = :current_date
        ORDER BY schedule.start_datetime";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':current_date', $current_date);
$stmt->execute();
$schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le cours en cours (et non le prochain pour les signatures)
$sql = "SELECT schedule.id, subject.name AS subject_name, class.name AS class_name
        FROM schedule
        INNER JOIN subject ON schedule.Subject_id = subject.id
        INNER JOIN class ON schedule.class_id = class.id
        WHERE schedule.User_id = :user_id 
        AND schedule.start_datetime <= NOW() 
        AND schedule.end_datetime >= NOW()
        ORDER BY schedule.start_datetime LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$current_class = $stmt->fetch(PDO::FETCH_ASSOC);

$signatures = [];
if ($current_class) {
    // Récupérer les signatures UNIQUEMENT pour le cours en cours
    $sql = "SELECT user.firstname, user.surname, signature.status
            FROM signature
            INNER JOIN user ON signature.User_id = user.id
            WHERE signature.Schedule_id = :schedule_id
            ORDER BY user.surname";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':schedule_id', $current_class['id'], PDO::PARAM_INT);
    $stmt->execute();
    $signatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les classes et élèves triés par classe
$sql = "SELECT class.name AS class_name, user.firstname, user.surname
        FROM user
        INNER JOIN class ON user.class_id = class.id
        WHERE class.id IN (SELECT DISTINCT class_id FROM schedule WHERE User_id = :user_id)
        ORDER BY class.name, user.surname";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les élèves par classe
$classes = [];
foreach ($students as $student) {
    $classes[$student['class_name']][] = $student;
}

?>
