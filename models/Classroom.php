<?php

namespace Models;

use Config\Database;

class Classroom {
    private $id;
    private $name;
    
    // Getters et Setters
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    // Méthodes CRUD
    public function save() {
        $db = Database::getInstance();
        
        if ($this->id) {
            // Mise à jour
            $db->update('class', [
                'name' => $this->name
            ], 'id = ?', [$this->id]);
        } else {
            // Création
            $this->id = $db->insert('class', [
                'name' => $this->name
            ]);
        }
        
        return $this;
    }
    
    public function delete() {
        if ($this->id) {
            $db = Database::getInstance();
            
            // Vérifier s'il y a des enregistrements dans schedule
            $count = $db->fetch("SELECT COUNT(*) as count FROM schedule WHERE class_id = ?", [$this->id])['count'];
            
            if ($count > 0) {
                return false; // Ne pas supprimer si utilisé dans des plannings
            }
            
            $db->delete('class', 'id = ?', [$this->id]);
            return true;
        }
        return false;
    }
    
    // Méthodes statiques
    public static function findById($id) {
        $db = Database::getInstance();
        $classData = $db->fetch("SELECT * FROM class WHERE id = ?", [$id]);
        
        if (!$classData) {
            return null;
        }
        
        return self::createFromArray($classData);
    }
    
    public static function findByName($name) {
        $db = Database::getInstance();
        $classData = $db->fetch("SELECT * FROM class WHERE name = ?", [$name]);
        
        if (!$classData) {
            return null;
        }
        
        return self::createFromArray($classData);
    }
    
    public static function findAll() {
        $db = Database::getInstance();
        $classesData = $db->fetchAll("SELECT * FROM class");
        
        $classes = [];
        foreach ($classesData as $classData) {
            $classes[] = self::createFromArray($classData);
        }
        
        return $classes;
    }
    
    // Récupérer les étudiants de cette classe
    public function getStudents() {
        return User::findByClass($this->id);
    }
    
    // Récupérer les cours de cette classe
    public function getSchedules() {
        $db = Database::getInstance();
        $schedulesData = $db->fetchAll("SELECT * FROM schedule WHERE class_id = ?", [$this->id]);
        
        $schedules = [];
        foreach ($schedulesData as $scheduleData) {
            $schedules[] = Schedule::createFromArray($scheduleData);
        }
        
        return $schedules;
    }
    
    // Méthode utilitaire pour créer un objet Classroom à partir d'un tableau
    private static function createFromArray($classData) {
        $class = new Classroom();
        $class->setId($classData['id']);
        $class->setName($classData['name']);
        
        return $class;
    }
}