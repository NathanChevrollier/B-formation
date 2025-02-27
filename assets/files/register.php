<?php

include_once 'bdd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $firstname = $_POST['firstname'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'] ?? 'student';

    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "Cet email est déjà utilisé";
    } else {
        
        $sql = "INSERT INTO user (firstname, surname, email, password, role, class_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$firstname, $surname, $email, $password, $role, $class_id])) {
            header("Location: ../../index.html");
            exit(); 
        } else {
            echo "Erreur";
        }
    }
}
?>
