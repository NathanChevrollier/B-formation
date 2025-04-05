<?php

namespace Models;

use Config\Database;

class Subject {
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
            $db->update('subject', [
                'name' => $this->name
            ], 'id = ?', [$this->id]);
        } else {
            // Création - Vérifie si la matière existe déjà
            $existingSubject = self::findByName($this->name);
            if (!$existingSubject) {
                $this->id = $db->insert('subject', [
                    'name' => $this->name
                ]);
            } else {
                $this->id = $existingSubject->getId();
            }
        }
        
        return $this;
    }
    
    public function delete() {
        if ($this->id) {
            $db = Database::getInstance();
            
            // Vérifier si la matière est utilisée dans des plannings
            $countSchedules = $db->fetch("SELECT COUNT(*) as count FROM schedule WHERE Subject_id = ?", [$this->id])['count'];
            
            if ($countSchedules > 0) {
                return false; // La matière est utilisée, ne pas supprimer
            }
            
            $db->delete('subject', 'id = ?', [$this->id]);
            return true;
        }
        return false;
    }
    
    // Méthodes statiques
    public static function findById($id) {
        $db = Database::getInstance();
        $subjectData = $db->fetch("SELECT * FROM subject WHERE id = ?", [$id]);
        
        if (!$subjectData) {
            return null;
        }
        
        return self::createFromArray($subjectData);
    }
    
    public static function findByName($name) {
        $db = Database::getInstance();
        $subjectData = $db->fetch("SELECT * FROM subject WHERE name = ?", [$name]);
        
        if (!$subjectData) {
            return null;
        }
        
        return self::createFromArray($subjectData);
    }
    
    public static function findAll() {
        $db = Database::getInstance();
        $subjectsData = $db->fetchAll("SELECT * FROM subject");
        
        $subjects = [];
        foreach ($subjectsData as $subjectData) {
            $subjects[] = self::createFromArray($subjectData);
        }
        
        return $subjects;
    }
    
    // Récupérer les cours associés à cette matière
    public function getSchedules() {
        $db = Database::getInstance();
        $schedulesData = $db->fetchAll("SELECT * FROM schedule WHERE Subject_id = ?", [$this->id]);
        
        $schedules = [];
        foreach ($schedulesData as $scheduleData) {
            $schedules[] = Schedule::createFromArray($scheduleData);
        }
        
        return $schedules;
    }
    
    // Méthode utilitaire pour créer un objet Subject à partir d'un tableau
    public static function createFromArray($subjectData) {
        $subject = new Subject();
        $subject->setId($subjectData['id']);
        $subject->setName($subjectData['name']);
        
        return $subject;
    }
}