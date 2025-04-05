<?php

namespace Controllers;

use Models\User;
use Utils\Auth;
use Utils\Session;

class AuthController {
    //connexion
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($email && $password) {
                if (Auth::login($email, $password)) {
                    $userRole = Session::get('user_role');
                    $validRoles = ['admin', 'teacher', 'student'];
                    if (in_array($userRole, $validRoles)) {
                        // Utilisez un chemin absolu depuis la racine du projet
                        header("Location: " . BASE_URL . "/views/{$userRole}.php");
                    } else {
                        // Rôle non valide, redirection par défaut
                        header("Location: " . BASE_URL . "/index.php");
                    }
                    exit();
                } else {
                    // Échec de la connexion
                    Session::setFlash('error', 'Email ou mot de passe incorrect');
                    header("Location: " . BASE_URL . "/index.php");
                    exit();
                }
            } else {
                // Champs manquants
                Session::setFlash('error', 'Remplissez tous les champs');
                header("Location: " . BASE_URL . "/index.php");
                exit();
            }
        }
    }
    
    // inscription
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstname = $_POST['firstname'] ?? '';
            $surname = $_POST['surname'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'student';
            
            // Vérification des champs
            if (!$firstname || !$surname || !$email || !$password) {
                Session::setFlash('error', 'Tous les champs sont obligatoires');
                header(header: "Location: " . BASE_URL . "/views/register.php");
                exit();
            }
            // verif mdp
            if ($password !== $confirmPassword) {
                Session::setFlash('error', 'Les mots de passe ne correspondent pas');
                header(header: "Location: " . BASE_URL . "/views/register.php");
                exit();
            }
            
            // Vérifier si l'email est déjà utilisé
            if (User::findByEmail($email)) {
                Session::setFlash('error', 'Cet email est déjà utilisé');
                header(header: "Location: " . BASE_URL . "/views/register.php");
                exit();
            }
            
            // Créer l'utilisateur
            $user = new User();
            $user->setFirstname($firstname)
                 ->setSurname($surname)
                 ->setEmail($email)
                 ->setPassword($password)
                 ->setRole($role)
                 ->save();
            
            // Rediriger vers la page de connexion
            Session::setFlash('success', 'Inscription réussie, vous pouvez vous connecter');
            header("Location: " . BASE_URL . "/index.php");
            exit();
        }
    }
    
    // Déconnexion
    public function logout() {

        Session::destroy();
        
        header("Location: " . BASE_URL . "/index.php");
        exit();
    }
}