<?php

include_once 'bdd.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    echo "Accès non autorisé";
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer le cours en cours pour le professeur
$sql = "SELECT id, class_id FROM schedule 
        WHERE User_id = :user_id 
        AND start_datetime <= NOW() 
        AND end_datetime >= NOW() 
        ORDER BY start_datetime LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$current_class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current_class) {
    echo "Aucun cours en cours pour ce professeur.";
    exit();
}

$schedule_id = $current_class['id'];
$class_id = $current_class['class_id'];

// Récupérer les élèves de cette classe
$sql = "SELECT id FROM user WHERE class_id = :class_id AND role = 'student'";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$students) {
    echo "Aucun élève trouvé pour ce cours.";
    exit();
}

// Insérer les signatures pour chaque élève uniquement si elles n'existent pas déjà
$sql = "INSERT INTO signature (User_id, Schedule_id, status) 
        SELECT :user_id, :schedule_id, 'pending' 
        WHERE NOT EXISTS (
            SELECT 1 FROM signature WHERE User_id = :user_id AND Schedule_id = :schedule_id
        )";
$stmt = $pdo->prepare($sql);

foreach ($students as $student) {
    $stmt->bindParam(':user_id', $student['id'], PDO::PARAM_INT);
    $stmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
    $stmt->execute();
}

// Rediriger vers la page précédente après l'enregistrement des signatures
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();

?>
