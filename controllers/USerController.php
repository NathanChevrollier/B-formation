<?php

namespace Controllers;

use Models\User;
use Models\Classroom;
use Utils\Auth;
use Utils\Session;

class UserController {
    // Afficher la liste des utilisateurs
    public function index() {
        Auth::requireRole('admin');
        
        $users = User::findAll();
        $classes = Classroom::findAll();
        
        // Inclure la vue
        include 'views/gestion_utilisateur.php';
    }
    
    // Ajouter un utilisateur
    public function add() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $surname = $_POST['surname'] ?? '';
            $firstname = $_POST['firstname'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? '';
            $class_id = $_POST['class_id'] ?? null;
            
            //  mot de passe depuis formulaire
            $password = $_POST['password'] ?? '';
            
            // mdp défaut si vide
            if (empty($password)) {
                $password = "password123"; // Mot de passe par défaut
            }
            
            if (empty($class_id)) {
                $class_id = null;
            }
            
            if (!empty($surname) && !empty($firstname) && !empty($email) && !empty($role)) {
                $user = new User();
                $user->setSurname($surname)
                     ->setFirstname($firstname)
                     ->setEmail($email)
                     ->setRole($role)
                     ->setClassId($class_id)
                     ->setPassword($password) // Utilise le mot de passe du formulaire
                     ->save();
                
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            } else {
                Session::setFlash('error', 'Tous les champs sont requis.');
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }
    
    // Mettre à jour un utilisateur
    public function update() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            
            if ($id) {
                $user = User::findById($id);
                
                if ($user) {
                    $surname = $_POST['surname'] ?? '';
                    $firstname = $_POST['firstname'] ?? '';
                    $email = $_POST['email'] ?? '';
                    $role = $_POST['role'] ?? '';
                    $class_id = $_POST['class_id'] ?? null;
                    
                    if (empty($class_id)) {
                        $class_id = null;
                    }
                    
                    if (!empty($surname) && !empty($firstname) && !empty($email) && !empty($role)) {
                        $user->setSurname($surname)
                             ->setFirstname($firstname)
                             ->setEmail($email)
                             ->setRole($role)
                             ->setClassId($class_id)
                             ->save();
                        
                        Session::setFlash('success', 'Utilisateur mis à jour avec succès.');
                    } else {
                        Session::setFlash('error', 'Tous les champs sont requis.');
                    }
                } else {
                    Session::setFlash('error', 'Utilisateur non trouvé.');
                }
            } else {
                Session::setFlash('error', 'ID d\'utilisateur manquant.');
            }
            
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
    
    // Supprimer un utilisateur
    public function delete() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            
            if ($id) {
                $user = User::findById($id);
                
                if ($user) {
                    $user->delete();
                    Session::setFlash('success', 'Utilisateur supprimé avec succès.');
                } else {
                    Session::setFlash('error', 'Utilisateur non trouvé.');
                }
            } else {
                Session::setFlash('error', 'ID d\'utilisateur manquant.');
            }
            
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
}