<?php
// Validation et gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? filter_var($_POST['action'], FILTER_SANITIZE_STRING) : null;
    
    if ($action === 'delete' && isset($_POST['signature_id']) && is_numeric($_POST['signature_id'])) {
        $signature_id = intval($_POST['signature_id']);
        $stmt = $pdo->prepare("DELETE FROM signature WHERE id = :id");
        $stmt->bindParam(':id', $signature_id, PDO::PARAM_INT);
        $stmt->execute();
    } elseif ($action === 'edit' && isset($_POST['signature_id'], $_POST['status']) && is_numeric($_POST['signature_id'])) {
        $signature_id = intval($_POST['signature_id']);
        $new_status = in_array($_POST['status'], ['pending', 'validated']) ? $_POST['status'] : 'pending';
        
        $stmt = $pdo->prepare("UPDATE signature SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $signature_id, PDO::PARAM_INT);
        $stmt->execute();
    } elseif ($action === 'add' && isset($_POST['user_id'], $_POST['schedule_id'])) {
        $user_id = intval($_POST['user_id']);
        $schedule_id = intval($_POST['schedule_id']);
        
        $stmt = $pdo->prepare("INSERT INTO signature (User_id, Schedule_id, status) VALUES (:user_id, :schedule_id, 'pending')");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// Récupération des signatures
$sql_signatures = "
    SELECT sig.id AS signature_id, u.email AS student_name, c.name AS class_name, sub.name AS subject_name, 
           s.start_datetime, s.end_datetime, sig.status
    FROM signature sig
    JOIN user u ON sig.User_id = u.id
    JOIN schedule s ON sig.Schedule_id = s.id
    JOIN class c ON s.class_id = c.id
    JOIN subject sub ON s.Subject_id = sub.id
    ORDER BY s.start_datetime DESC
";
$stmt_signatures = $pdo->prepare($sql_signatures);
$stmt_signatures->execute();
$signatures = $stmt_signatures->fetchAll(PDO::FETCH_ASSOC);

// Récupération des étudiants et cours pour l'ajout
$stmt_users = $pdo->prepare("SELECT id, email FROM user WHERE role = 'student'");
$stmt_users->execute();
$students = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

$stmt_schedules = $pdo->prepare("SELECT id, start_datetime FROM schedule");
$stmt_schedules->execute();
$schedules = $stmt_schedules->fetchAll(PDO::FETCH_ASSOC);
?>