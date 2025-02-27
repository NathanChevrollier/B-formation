<?php
include 'bdd.php';

$sql_users = "SELECT * FROM user";
$stmt_users = $pdo->prepare($sql_users);
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

$sql_classes = "SELECT id, name FROM class";
$stmt_classes = $pdo->prepare($sql_classes);
$stmt_classes->execute();
$classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($action === 'update' && $id) {
        $surname = $_POST['surname'] ?? '';
        $firstname = $_POST['firstname'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? '';
        $class_id = $_POST['class_id'] ?? null;

        // Si class_id est vide, on le transforme en null
        if (empty($class_id)) {
            $class_id = null;
        }

        // Validation des autres champs
        if (!empty($surname) && !empty($firstname) && !empty($email) && !empty($role)) {
            $sql = "UPDATE user 
                    SET surname = :surname, 
                        firstname = :firstname, 
                        email = :email, 
                        role = :role, 
                        class_id = :class_id 
                    WHERE id = :id";

            // Préparation de la requête
            $stmt = $pdo->prepare($sql);

            // Liaison des paramètres
            $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
            $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->bindParam(':class_id', $class_id, is_null($class_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Exécution de la requête
            $stmt->execute();

            echo "Utilisateur mis à jour avec succès.";
            header("Location: ../../gestion_utilisateur.php");
        } else {
            echo "Tous les champs sont requis.";
            header("Location: ../../gestion_utilisateur.php");
        }
    } elseif ($action === 'delete' && $id) {
        $sql = "DELETE FROM user WHERE id = :id";

        // Préparation de la requête
        $stmt = $pdo->prepare($sql);

        // Liaison des paramètres
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Exécution de la requête
        $stmt->execute();

        echo "Utilisateur supprimé avec succès.";
        header("Location: ../../gestion_utilisateur.php");
    } else {
        echo "Action non valide ou données manquantes.";
        header("Location: ../../gestion_utilisateur.php");
    }
}
?>
