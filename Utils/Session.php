<?php

namespace Utils;

class Session {
    // Démarrer la session si elle n'est pas déjà démarrée
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // Définir une valeur dans la session
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    // Récupérer une valeur de la session
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    // Vérifier si une clé existe dans la session
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    // Supprimer une valeur de la session
    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    // Détruire la session
    public static function destroy() {
        self::start();
        $_SESSION = [];
        session_destroy();
    }
    
    // Récupérer et supprimer un message flash
    public static function getFlash($key, $default = null) {
        $value = self::get($key, $default);
        self::remove($key);
        return $value;
    }
    
    // Définir un message flash
    public static function setFlash($key, $value) {
        self::set($key, $value);
    }
    
    // Vérifier si l'utilisateur est connecté
    public static function isLoggedIn() {
        return self::has('user_id') && self::has('user_email');
    }
    
    // Vérifier si l'utilisateur a un rôle spécifique
    public static function hasRole($role) {
        return self::isLoggedIn() && self::get('user_role') === $role;
    }
}