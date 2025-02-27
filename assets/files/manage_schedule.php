<?php
include_once 'assets/files/bdd.php';

// Récupérer les données de l'emploi du temps
$sql_planning = "
    SELECT DISTINCT
        s.id AS schedule_id, 
        c.name AS class_name, 
        sub.name AS subject_name, 
        s.start_datetime, 
        s.end_datetime, 
        u.email AS teacher_name
    FROM schedule s
    JOIN class c ON s.class_id = c.id
    JOIN subject sub ON s.subject_id = sub.id
    JOIN user u ON s.User_id = u.id
";
$stmt_planning = $pdo->prepare($sql_planning);
$stmt_planning->execute();
$planning = $stmt_planning->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les classes
$sql_classes = "SELECT id, name FROM class";
$stmt_classes = $pdo->prepare($sql_classes);
$stmt_classes->execute();
$classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les matières
$sql_subjects = "SELECT id, name FROM subject";
$stmt_subjects = $pdo->prepare($sql_subjects);
$stmt_subjects->execute();
$subjects = $stmt_subjects->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les professeurs
$sql_teachers = "SELECT id, email FROM user WHERE role = 'teacher'";
$stmt_teachers = $pdo->prepare($sql_teachers);
$stmt_teachers->execute();
$teachers = $stmt_teachers->fetchAll(PDO::FETCH_ASSOC);

// Gestion des actions : Ajouter, Modifier, Supprimer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'add') {
        $class_id = $_POST['class_id'];
        $subject_id = $_POST['subject_id'];
        $start_datetime = $_POST['start_datetime'];
        $end_datetime = $_POST['end_datetime'];
        $teacher_id = $_POST['teacher_id']; // Récupérer l'ID du professeur

        $stmt = $pdo->prepare("INSERT INTO schedule (class_id, subject_id, start_datetime, end_datetime, User_id) VALUES (:class_id, :subject_id, :start_datetime, :end_datetime, :teacher_id)");
        $stmt->bindParam(':class_id', $class_id);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->bindParam(':start_datetime', $start_datetime);
        $stmt->bindParam(':end_datetime', $end_datetime);
        $stmt->bindParam(':teacher_id', $teacher_id);
        $stmt->execute();

        // Redirection après l'ajout
        header("Location: gestion_planning.php" . $_SERVER['PHP_SELF']);
        exit();
    } elseif ($action === 'edit') {
        $schedule_id = $_POST['schedule_id'];
        $class_id = $_POST['class_id'];
        $subject_id = $_POST['subject_id'];
        $start_datetime = $_POST['start_datetime'];
        $end_datetime = $_POST['end_datetime'];
        $teacher_id = $_POST['teacher_id'];

        $stmt = $pdo->prepare("UPDATE schedule SET class_id = :class_id, subject_id = :subject_id, start_datetime = :start_datetime, end_datetime = :end_datetime, User_id = :teacher_id WHERE id = :id");
        $stmt->bindParam(':class_id', $class_id);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->bindParam(':start_datetime', $start_datetime);
        $stmt->bindParam(':end_datetime', $end_datetime);
        $stmt->bindParam(':teacher_id', $teacher_id);
        $stmt->bindParam(':id', $schedule_id);
        $stmt->execute();

        // Redirection après la modification
        header("Location: gestion_planning.php" . $_SERVER['PHP_SELF']);
        exit();
    } elseif ($action === 'delete') {
        $schedule_id = $_POST['schedule_id'];

        $stmt = $pdo->prepare("DELETE FROM schedule WHERE id = :id");
        $stmt->bindParam(':id', $schedule_id);
        $stmt->execute();

        // Redirection après la suppression
        header("Location: gestion_planning.php" . $_SERVER['PHP_SELF']);
        exit();
    }
}

?>