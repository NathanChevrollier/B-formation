<?php
include_once 'bdd.php';

// Récupération des matières
$sql_subjects = "SELECT * FROM subject";
$stmt_subjects = $pdo->prepare($sql_subjects);
$stmt_subjects->execute();
$subjects = $stmt_subjects->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'add') {
        $subject_name = $_POST['subject_name'] ?? '';

        if (!empty($subject_name)) {
            $sql = "INSERT INTO subject (name) SELECT :name WHERE NOT EXISTS (SELECT 1 FROM subject WHERE name = :name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $subject_name, PDO::PARAM_STR);
            $stmt->execute();

            header("Location: ../../gestion_matières.php");
            exit();
        }
    }

    if ($action === 'update') {
        $subject_id = $_POST['subject_id'] ?? null;
        $subject_name = $_POST['subject_name'] ?? '';

        if (!empty($subject_id) && !empty($subject_name)) {
            $sql = "UPDATE subject SET name = :name WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $subject_name, PDO::PARAM_STR);
            $stmt->bindParam(':id', $subject_id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: ../../gestion_matières.php");
            exit();
        }
    }

    if ($action === 'delete') {
        $subject_id = $_POST['subject_id'] ?? null;

        if (!empty($subject_id)) {
            $sql = "DELETE FROM subject WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $subject_id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: ../../gestion_matières.php");
            exit();
        }
    }
}
?>
