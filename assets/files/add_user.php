<?php
include 'bdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surname = $_POST['surname'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';
    $class_id = $_POST['class_id'] ?? null;

    if (empty($class_id)) {
        $class_id = null;
    }

    if (!empty($surname) && !empty($firstname) && !empty($email) && !empty($role)) {
        $sql = "INSERT INTO user (surname, firstname, email, role, class_id) 
                VALUES (:surname, :firstname, :email, :role, :class_id)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
        $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':class_id', $class_id, is_null($class_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);

        try {
            $stmt->execute();
            header("Location: ../../gestion_utilisateur.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Tous les champs sont requis.";
    }
}
?>
