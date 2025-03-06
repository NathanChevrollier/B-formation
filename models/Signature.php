<?php

namespace Models;

use Config\Database;

class Signature {
    private $id;
    private $file_name;
    private $user_id;
    private $schedule_id;
    private $status;
    
    // Constantes pour les statuts
    const STATUS_PENDING = 'pending';
    const STATUS_VALIDATED = 'validated';
    
    // Getters et Setters
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getFileName() {
        return $this->file_name;
    }
    
    public function setFileName($file_name) {
        $this->file_name = $file_name;
        return $this;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }
    
    public function getScheduleId() {
        return $this->schedule_id;
    }
    
    public function setScheduleId($schedule_id) {
        $this->schedule_id = $schedule_id;
        return $this;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function setStatus($status) {
        if (!in_array($status, [self::STATUS_PENDING, self::STATUS_VALIDATED])) {
            $status = self::STATUS_PENDING;
        }
        $this->status = $status;
        return $this;
    }
    
    // Méthodes CRUD
    public function save() {
        $db = Database::getInstance();
        
        if ($this->id) {
            // Mise à jour
            $db->update('signature', [
                'file_name' => $this->file_name,
                'User_id' => $this->user_id,
                'Schedule_id' => $this->schedule_id,
                'status' => $this->status
            ], 'id = ?', [$this->id]);
        } else {
            // Création
            $this->id = $db->insert('signature', [
                'file_name' => $this->file_name,
                'User_id' => $this->user_id,
                'Schedule_id' => $this->schedule_id,
                'status' => $this->status
            ]);
        }
        
        return $this;
    }
    
    public function delete() {
        if ($this->id) {
            $db = Database::getInstance();
            $db->delete('signature', 'id = ?', [$this->id]);
            return true;
        }
        return false;
    }
    
    // Valider la signature
    public function validate() {
        $this->status = self::STATUS_VALIDATED;
        return $this->save();
    }
    
    // Méthodes statiques
    public static function findById($id) {
        $db = Database::getInstance();
        $signatureData = $db->fetch("SELECT * FROM signature WHERE id = ?", [$id]);
        
        if (!$signatureData) {
            return null;
        }
        
        return self::createFromArray($signatureData);
    }
    
    public static function findAll() {
        $db = Database::getInstance();
        $signaturesData = $db->fetchAll("SELECT * FROM signature");
        
        $signatures = [];
        foreach ($signaturesData as $signatureData) {
            $signatures[] = self::createFromArray($signatureData);
        }
        
        return $signatures;
    }
    
    public static function findByUserId($user_id) {
        $db = Database::getInstance();
        $signaturesData = $db->fetchAll("SELECT * FROM signature WHERE User_id = ?", [$user_id]);
        
        $signatures = [];
        foreach ($signaturesData as $signatureData) {
            $signatures[] = self::createFromArray($signatureData);
        }
        
        return $signatures;
    }
    
    public static function findByScheduleId($schedule_id) {
        $db = Database::getInstance();
        $signaturesData = $db->fetchAll("SELECT * FROM signature WHERE Schedule_id = ?", [$schedule_id]);
        
        $signatures = [];
        foreach ($signaturesData as $signatureData) {
            $signatures[] = self::createFromArray($signatureData);
        }
        
        return $signatures;
    }
    
    public static function findByUserAndSchedule($user_id, $schedule_id) {
        $db = Database::getInstance();
        $signatureData = $db->fetch(
            "SELECT * FROM signature WHERE User_id = ? AND Schedule_id = ?", 
            [$user_id, $schedule_id]
        );
        
        if (!$signatureData) {
            return null;
        }
        
        return self::createFromArray($signatureData);
    }
    
    // Récupérer les signatures avec détails
    public static function findAllWithDetails() {
        $db = Database::getInstance();
        $signaturesData = $db->fetchAll(
            "SELECT sig.id AS signature_id, u.email AS student_name, c.name AS class_name, 
                    sub.name AS subject_name, s.start_datetime, s.end_datetime, sig.status
             FROM signature sig
             JOIN user u ON sig.User_id = u.id
             JOIN schedule s ON sig.Schedule_id = s.id
             JOIN class c ON s.class_id = c.id
             JOIN subject sub ON s.Subject_id = sub.id
             ORDER BY s.start_datetime DESC"
        );
        
        return $signaturesData;
    }
    
    // Créer des signatures pour tous les étudiants d'une classe pour un cours
    public static function createForClassAndSchedule($class_id, $schedule_id) {
        $db = Database::getInstance();
        
        // Récupérer tous les étudiants de la classe
        $students = User::findByClass($class_id);
        
        foreach ($students as $student) {
            // Vérifier si une signature existe déjà
            $exists = self::findByUserAndSchedule($student->getId(), $schedule_id);
            
            if (!$exists) {
                // Créer une nouvelle signature
                $signature = new Signature();
                $signature->setFileName('')
                         ->setUserId($student->getId())
                         ->setScheduleId($schedule_id)
                         ->setStatus(self::STATUS_PENDING)
                         ->save();
            }
        }
        
        return true;
    }
    
    // Méthode utilitaire pour créer un objet Signature à partir d'un tableau
    private static function createFromArray($signatureData) {
        $signature = new Signature();
        $signature->setId($signatureData['id']);
        $signature->setFileName($signatureData['file_name']);
        $signature->setUserId($signatureData['User_id']);
        $signature->setScheduleId($signatureData['Schedule_id']);
        $signature->setStatus($signatureData['status']);
        
        return $signature;
    }
    
    // Récupérer l'étudiant associé
    public function getStudent() {
        return User::findById($this->user_id);
    }
    
    // Récupérer le cours associé
    public function getSchedule() {
        return Schedule::findById($this->schedule_id);
    }
}