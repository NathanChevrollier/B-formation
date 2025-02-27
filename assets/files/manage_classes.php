<?php
include_once 'bdd.php';

if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']);
}


// Récupérer toutes les classes
$sql = "SELECT * FROM class";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'add') {
        $class_name = $_POST['class_name'] ?? '';

        if (!empty($class_name)) {
            $sql = "INSERT INTO class (name) SELECT :name WHERE NOT EXISTS (SELECT 1 FROM class WHERE name = :name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $class_name, PDO::PARAM_STR);
            $stmt->execute();

            header("Location: ../../gestion_classes.php");
            exit();
        }
    }

    if ($action === 'update') {
        $class_id = $_POST['class_id'] ?? null;
        $class_name = $_POST['class_name'] ?? '';

        if (!empty($class_id) && !empty($class_name)) {
            $sql = "UPDATE class SET name = :name WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $class_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $class_name, PDO::PARAM_STR);
            $stmt->execute();

            header("Location: ../../gestion_classes.php");
            exit();
        }
    }

    if ($action === 'delete') {
        $class_id = $_POST['class_id'] ?? null;

        if (!empty($class_id)) {
            // Vérifier s'il y a des enregistrements dans schedule
            $sql = "SELECT COUNT(*) FROM schedule WHERE class_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $class_id, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                session_start();
                $_SESSION['error_message'] = "Impossible de supprimer cette classe car elle est associée à des plannings.";
                header("Location: ../../gestion_classes.php");
                exit();
            }

            // Suppression si aucun lien
            $sql = "DELETE FROM class WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $class_id, PDO::PARAM_INT);
            $stmt->execute();

            session_start();
            $_SESSION['success_message'] = "Classe supprimée avec succès.";
            header("Location: ../../gestion_classes.php");
            exit();
        }
    }
}
?>