<?php
// utils/CSRF.php
namespace Utils;

class CSRF {
    // Générer un token CSRF
    public static function generateToken() {
        if (!Session::has('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            Session::set('csrf_token', $token);
        }
        return Session::get('csrf_token');
    }
    
    // Vérifier un token CSRF
    public static function verifyToken($token) {
        if (Session::has('csrf_token') && Session::get('csrf_token') === $token) {
            // Régénérer un nouveau token après vérification
            self::generateToken();
            return true;
        }
        return false;
    }
}