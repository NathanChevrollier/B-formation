<?php

namespace Controllers;

use Models\Schedule;
use Models\Classroom;
use Models\Subject;
use Models\User;
use Utils\Auth;
use Utils\Session;

use Config\Database;

class ScheduleController {
    // Afficher la liste des emplois du temps
    public function index() {
        Auth::requireRole('admin');
        
        // Récupérer les données pour l'affichage
        $planning = $this->getFullPlanningData();
        $classes = Classroom::findAll();
        $subjects = Subject::findAll();
        $teachers = User::findByRole('teacher');
        
        // Inclure la vue
        include 'views/gestion_planning.php';
    }
    
    // Afficher l'emploi du temps d'un utilisateur
    public function showUserSchedule() {
        Auth::requireLogin();
        
        $user = Auth::getUser();
        $schedule = [];
        
        if ($user->getRole() === 'student') {
            // Récupérer l'emploi du temps de l'étudiant
            if ($user->getClassId()) {
                $schedule = $this->getStudentScheduleData($user->getId());
            }
        } elseif ($user->getRole() === 'teacher') {
            // Récupérer l'emploi du temps du professeur
            $schedule = $this->getTeacherScheduleData($user->getId());
        }
        
        // Inclure la vue
        include 'views/schedule.php';
    }
    
    // Ajouter un emploi du temps
    public function add() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $class_id = $_POST['class_id'] ?? null;
            $subject_id = $_POST['subject_id'] ?? null;
            $teacher_id = $_POST['teacher_id'] ?? null;
            $start_datetime = $_POST['start_datetime'] ?? null;
            $end_datetime = $_POST['end_datetime'] ?? null;
            
            if ($class_id && $subject_id && $teacher_id && $start_datetime && $end_datetime) {
                $schedule = new Schedule();
                $schedule->setClassId($class_id)
                         ->setSubjectId($subject_id)
                         ->setUserId($teacher_id)
                         ->setStartDatetime($start_datetime)
                         ->setEndDatetime($end_datetime)
                         ->save();
                
                Session::setFlash('success', 'Emploi du temps ajouté avec succès.');
            } else {
                Session::setFlash('error', 'Tous les champs sont requis.');
            }
            
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
    
    // Mettre à jour un emploi du temps
    public function update() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $schedule_id = $_POST['schedule_id'] ?? null;
            $class_id = $_POST['class_id'] ?? null;
            $subject_id = $_POST['subject_id'] ?? null;
            $teacher_id = $_POST['teacher_id'] ?? null;
            $start_datetime = $_POST['start_datetime'] ?? null;
            $end_datetime = $_POST['end_datetime'] ?? null;
            
            if ($schedule_id && $class_id && $subject_id && $teacher_id && $start_datetime && $end_datetime) {
                $schedule = Schedule::findById($schedule_id);
                
                if ($schedule) {
                    $schedule->setClassId($class_id)
                             ->setSubjectId($subject_id)
                             ->setUserId($teacher_id)
                             ->setStartDatetime($start_datetime)
                             ->setEndDatetime($end_datetime)
                             ->save();
                    
                    Session::setFlash('success', 'Emploi du temps mis à jour avec succès.');
                } else {
                    Session::setFlash('error', 'Emploi du temps non trouvé.');
                }
            } else {
                Session::setFlash('error', 'Tous les champs sont requis.');
            }
            
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
    
    // Supprimer un emploi du temps
    public function delete() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $schedule_id = $_POST['schedule_id'] ?? null;
            
            if ($schedule_id) {
                $schedule = Schedule::findById($schedule_id);
                
                if ($schedule) {
                    $schedule->delete();
                    Session::setFlash('success', 'Emploi du temps supprimé avec succès.');
                } else {
                    Session::setFlash('error', 'Emploi du temps non trouvé.');
                }
            } else {
                Session::setFlash('error', 'ID d\'emploi du temps manquant.');
            }
            
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
    
    // Récupérer les données complètes de l'emploi du temps
    private function getFullPlanningData() {
        $schedules = Schedule::findAll();
        $result = [];
        
        foreach ($schedules as $schedule) {
            $result[] = [
                'schedule_id' => $schedule->getId(),
                'class_name' => $schedule->getClass()->getName(),
                'subject_name' => $schedule->getSubject()->getName(),
                'start_datetime' => $schedule->getStartDatetime(),
                'end_datetime' => $schedule->getEndDatetime(),
                'teacher_name' => $schedule->getTeacher()->getEmail()
            ];
        }
        
        return $result;
    }
    
    // Récupérer les données de l'emploi du temps d'un étudiant
    private function getStudentScheduleData($user_id) {
        $db = Database::getInstance()->getConnection();
        $sql = "
            SELECT schedule.start_datetime, schedule.end_datetime, 
                   subject.name AS subject_name, class.name AS class_name, 
                   user.firstname AS teacher_firstname, user.surname AS teacher_surname
            FROM schedule
            INNER JOIN subject ON schedule.Subject_id = subject.id
            INNER JOIN class ON schedule.class_id = class.id
            INNER JOIN user ON schedule.User_id = user.id
            WHERE schedule.class_id = (SELECT class_id FROM user WHERE id = ?)
            ORDER BY schedule.start_datetime
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Récupérer les données de l'emploi du temps d'un professeur
    private function getTeacherScheduleData($user_id) {
        $db = Database::getInstance()->getConnection();
        $sql = "
            SELECT schedule.start_datetime, schedule.end_datetime, 
                   subject.name AS subject_name, class.name AS class_name
            FROM schedule
            INNER JOIN subject ON schedule.Subject_id = subject.id
            INNER JOIN class ON schedule.class_id = class.id
            WHERE schedule.User_id = ?
            ORDER BY schedule.start_datetime
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}