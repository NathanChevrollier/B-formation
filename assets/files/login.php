<?php

include_once 'bdd.php';

session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email && $password){
        $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        

        if ($email && password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
    
            header("Location: ../../" . $_SESSION['user_role'] . ".php");
            exit();
            } else {
                echo "Email ou mot de passe incorrect";
                header("Location: ../../index.html");
        }
    }
    else{
        echo"Remplissez tous les champs";
    }

   
    
}
?>