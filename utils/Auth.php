<?php

namespace Utils;

use Models\User;

class Auth {
    // Tenter de connecter un utilisateur
    public static function login($email, $password) {
        $user = User::findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        if (!$user->verifyPassword($password)) {
            return false;
        }
        
        // Enregistrer l'utilisateur en session
        Session::set('user_id', $user->getId());
        Session::set('user_email', $user->getEmail());
        Session::set('user_role', $user->getRole());
        
        return true;
    }
    
    // Déconnecter l'utilisateur
    public static function logout() {
        Session::destroy();
    }
    
    // Obtenir l'utilisateur connecté
    public static function getUser() {
        if (!Session::isLoggedIn()) {
            return null;
        }
        
        return User::findById(Session::get('user_id'));
    }
    
    // Vérifier si l'utilisateur est connecté
    public static function check() {
        return Session::isLoggedIn();
    }
    
    // Vérifier si l'utilisateur a un rôle spécifique
    public static function hasRole($role) {
        return Session::hasRole($role);
    }
    
    // Rediriger si l'utilisateur n'est pas connecté
    public static function requireLogin() {
        if (!self::check()) {
            header('Location: index.html');
            exit();
        }
    }
    
    // Rediriger si l'utilisateur n'a pas le rôle requis
    public static function requireRole($role) {
        self::requireLogin();
        
        if (!self::hasRole($role)) {
            header('Location: index.html');
            exit();
        }
    }
}