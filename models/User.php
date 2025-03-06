<?php

namespace Models;

use Config\Database;
use PDO;

class User {
    private $id;
    private $firstname;
    private $surname;
    private $email;
    private $password;
    private $role;
    private $class_id;
    
    // Getters et Setters
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getFirstname() {
        return $this->firstname;
    }
    
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
        return $this;
    }
    
    public function getSurname() {
        return $this->surname;
    }
    
    public function setSurname($surname) {
        $this->surname = $surname;
        return $this;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function setPassword($password, $hash = true) {
        if ($hash) {
            $this->password = password_hash($password, PASSWORD_BCRYPT);
        } else {
            $this->password = $password;
        }
        return $this;
    }
    
    public function getRole() {
        return $this->role;
    }
    
    public function setRole($role) {
        $this->role = $role;
        return $this;
    }
    
    public function getClassId() {
        return $this->class_id;
    }
    
    public function setClassId($class_id) {
        $this->class_id = $class_id;
        return $this;
    }
    
    // Méthodes CRUD
    public function save() {
        $db = Database::getInstance();
        
        if ($this->id) {
            // Mise à jour
            $data = [
                'firstname' => $this->firstname,
                'surname' => $this->surname,
                'email' => $this->email,
                'role' => $this->role,
                'class_id' => $this->class_id
            ];
            
            // Ajouter le mot de passe uniquement s'il a été modifié
            if ($this->password) {
                $data['password'] = $this->password;
            }
            
            $db->update('user', $data, 'id = ?', [$this->id]);
        } else {
            // Création
            $data = [
                'firstname' => $this->firstname,
                'surname' => $this->surname,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role,
                'class_id' => $this->class_id
            ];
            
            $this->id = $db->insert('user', $data);
        }
        
        return $this;
    }
    
    public function delete() {
        if ($this->id) {
            $db = Database::getInstance();
            $db->delete('user', 'id = ?', [$this->id]);
        }
    }
    
    // Méthodes statiques
    public static function findById($id) {
        $db = Database::getInstance();
        $userData = $db->fetch("SELECT * FROM user WHERE id = ?", [$id]);
        
        if (!$userData) {
            return null;
        }
        
        return self::createFromArray($userData);
    }
    
    public static function findByEmail($email) {
        $db = Database::getInstance();
        $userData = $db->fetch("SELECT * FROM user WHERE email = ?", [$email]);
        
        if (!$userData) {
            return null;
        }
        
        return self::createFromArray($userData);
    }
    
    public static function findAll() {
        $db = Database::getInstance();
        $usersData = $db->fetchAll("SELECT * FROM user");
        
        $users = [];
        foreach ($usersData as $userData) {
            $users[] = self::createFromArray($userData);
        }
        
        return $users;
    }
    
    public static function findByRole($role) {
        $db = Database::getInstance();
        $usersData = $db->fetchAll("SELECT * FROM user WHERE role = ?", [$role]);
        
        $users = [];
        foreach ($usersData as $userData) {
            $users[] = self::createFromArray($userData);
        }
        
        return $users;
    }
    
    public static function findByClass($class_id) {
        $db = Database::getInstance();
        $usersData = $db->fetchAll("SELECT * FROM user WHERE class_id = ?", [$class_id]);
        
        $users = [];
        foreach ($usersData as $userData) {
            $users[] = self::createFromArray($userData);
        }
        
        return $users;
    }
    
    // Méthode utilitaire pour créer un objet User à partir d'un tableau
    private static function createFromArray($userData) {
        $user = new User();
        $user->setId($userData['id']);
        $user->setFirstname($userData['firstname']);
        $user->setSurname($userData['surname']);
        $user->setEmail($userData['email']);
        $user->setPassword($userData['password'], false); // Déjà hashé dans la BDD
        $user->setRole($userData['role']);
        $user->setClassId($userData['class_id']);
        
        return $user;
    }
    
    // Vérifier le mot de passe
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }
}