<?php

namespace Models;

use Config\Database;

class Schedule {
    private $id;
    private $start_datetime;
    private $end_datetime;
    private $class_id;
    private $user_id;
    private $subject_id;
    
    // Getters et Setters
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getStartDatetime() {
        return $this->start_datetime;
    }
    
    public function setStartDatetime($start_datetime) {
        $this->start_datetime = $start_datetime;
        return $this;
    }
    
    public function getEndDatetime() {
        return $this->end_datetime;
    }
    
    public function setEndDatetime($end_datetime) {
        $this->end_datetime = $end_datetime;
        return $this;
    }
    
    public function getClassId() {
        return $this->class_id;
    }
    
    public function setClassId($class_id) {
        $this->class_id = $class_id;
        return $this;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }
    
    public function getSubjectId() {
        return $this->subject_id;
    }
    
    public function setSubjectId($subject_id) {
        $this->subject_id = $subject_id;
        return $this;
    }
    
    // Méthodes CRUD
    public function save() {
        $db = Database::getInstance();
        
        if ($this->id) {
            // Mise à jour
            $db->update('schedule', [
                'start_datetime' => $this->start_datetime,
                'end_datetime' => $this->end_datetime,
                'class_id' => $this->class_id,
                'User_id' => $this->user_id,
                'Subject_id' => $this->subject_id
            ], 'id = ?', [$this->id]);
        } else {
            // Création
            $this->id = $db->insert('schedule', [
                'start_datetime' => $this->start_datetime,
                'end_datetime' => $this->end_datetime,
                'class_id' => $this->class_id,
                'User_id' => $this->user_id,
                'Subject_id' => $this->subject_id
            ]);
        }
        
        return $this;
    }
    
    public function delete() {
        if ($this->id) {
            $db = Database::getInstance();
            
            // Supprimer d'abord les signatures associées
            $db->delete('signature', 'Schedule_id = ?', [$this->id]);
            
            // Puis supprimer le cours
            $db->delete('schedule', 'id = ?', [$this->id]);
            return true;
        }
        return false;
    }
    
    // Méthodes statiques
    public static function findById($id) {
        $db = Database::getInstance();
        $scheduleData = $db->fetch("SELECT * FROM schedule WHERE id = ?", [$id]);
        
        if (!$scheduleData) {
            return null;
        }
        
        return self::createFromArray($scheduleData);
    }
    
    public static function findAll() {
        $db = Database::getInstance();
        $schedulesData = $db->fetchAll("SELECT * FROM schedule ORDER BY start_datetime");
        
        $schedules = [];
        foreach ($schedulesData as $scheduleData) {
            $schedules[] = self::createFromArray($scheduleData);
        }
        
        return $schedules;
    }
    
    public static function findByClassId($class_id) {
        $db = Database::getInstance();
        $schedulesData = $db->fetchAll("SELECT * FROM schedule WHERE class_id = ? ORDER BY start_datetime", [$class_id]);
        
        $schedules = [];
        foreach ($schedulesData as $scheduleData) {
            $schedules[] = self::createFromArray($scheduleData);
        }
        
        return $schedules;
    }
    
    public static function findByTeacherId($user_id) {
        $db = Database::getInstance();
        $schedulesData = $db->fetchAll("SELECT * FROM schedule WHERE User_id = ? ORDER BY start_datetime", [$user_id]);
        
        $schedules = [];
        foreach ($schedulesData as $scheduleData) {
            $schedules[] = self::createFromArray($scheduleData);
        }
        
        return $schedules;
    }
    
    public static function findBySubjectId($subject_id) {
        $db = Database::getInstance();
        $schedulesData = $db->fetchAll("SELECT * FROM schedule WHERE Subject_id = ? ORDER BY start_datetime", [$subject_id]);
        
        $schedules = [];
        foreach ($schedulesData as $scheduleData) {
            $schedules[] = self::createFromArray($scheduleData);
        }
        
        return $schedules;
    }
    
    // Trouver les cours en cours
    public static function findCurrentForTeacher($user_id) {
        $db = Database::getInstance();
        $scheduleData = $db->fetch(
            "SELECT * FROM schedule 
            WHERE User_id = ? 
            AND start_datetime <= NOW() 
            AND end_datetime >= NOW() 
            ORDER BY start_datetime LIMIT 1", 
            [$user_id]
        );
        
        if (!$scheduleData) {
            return null;
        }
        
        return self::createFromArray($scheduleData);
    }
    
    public static function findCurrentForClass($class_id) {
        $db = Database::getInstance();
        $scheduleData = $db->fetch(
            "SELECT * FROM schedule 
            WHERE class_id = ? 
            AND start_datetime <= NOW() 
            AND end_datetime >= NOW() 
            ORDER BY start_datetime LIMIT 1", 
            [$class_id]
        );
        
        if (!$scheduleData) {
            return null;
        }
        
        return self::createFromArray($scheduleData);
    }
    
    // Récupérer les cours du jour
    public static function findTodayForTeacher($user_id) {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        
        $schedulesData = $db->fetchAll(
            "SELECT * FROM schedule 
            WHERE User_id = ? 
            AND DATE(start_datetime) = ? 
            ORDER BY start_datetime", 
            [$user_id, $today]
        );
        
        $schedules = [];
        foreach ($schedulesData as $scheduleData) {
            $schedules[] = self::createFromArray($scheduleData);
        }
        
        return $schedules;
    }
    
    public static function findTodayForClass($class_id) {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        
        $schedulesData = $db->fetchAll(
            "SELECT * FROM schedule 
            WHERE class_id = ? 
            AND DATE(start_datetime) = ? 
            ORDER BY start_datetime", 
            [$class_id, $today]
        );
        
        $schedules = [];
        foreach ($schedulesData as $scheduleData) {
            $schedules[] = self::createFromArray($scheduleData);
        }
        
        return $schedules;
    }
    
    // Récupérer des informations détaillées
    public function getFullDetails() {
        $db = Database::getInstance();
        $data = $db->fetch(
            "SELECT 
                s.id AS schedule_id, 
                c.name AS class_name, 
                sub.name AS subject_name, 
                s.start_datetime, 
                s.end_datetime, 
                u.email AS teacher_name
            FROM schedule s
            JOIN class c ON s.class_id = c.id
            JOIN subject sub ON s.subject_id = sub.id
            JOIN user u ON s.User_id = u.id
            WHERE s.id = ?",
            [$this->id]
        );
        
        return $data;
    }
    
    // Récupérer les signatures pour ce cours
    public function getSignatures() {
        return Signature::findByScheduleId($this->id);
    }
    
    // Récupérer la classe associée
    public function getClass() {
        return Classroom::findById($this->class_id);
    }
    
    // Récupérer l'enseignant associé
    public function getTeacher() {
        return User::findById($this->user_id);
    }
    
    // Récupérer la matière associée
    public function getSubject() {
        return Subject::findById($this->subject_id);
    }
    
    // Méthode utilitaire pour créer un objet Schedule à partir d'un tableau
    public static function createFromArray($scheduleData) {
        $schedule = new Schedule();
        $schedule->setId($scheduleData['id']);
        $schedule->setStartDatetime($scheduleData['start_datetime']);
        $schedule->setEndDatetime($scheduleData['end_datetime']);
        $schedule->setClassId($scheduleData['class_id']);
        $schedule->setUserId($scheduleData['User_id']);
        $schedule->setSubjectId($scheduleData['Subject_id']);
        
        return $schedule;
    }

    public static function getClassesByTeacher($teacher_id) {
        $db = Database::getInstance();
        $classIds = $db->fetchAll(
            "SELECT DISTINCT class_id FROM schedule WHERE User_id = ?", 
            [$teacher_id]
        );
        
        $classes = [];
        foreach ($classIds as $classId) {
            $class = Classroom::findById($classId['class_id']);
            if ($class) {
                $classes[] = $class;
            }
        }
        
        return $classes;
    }
}