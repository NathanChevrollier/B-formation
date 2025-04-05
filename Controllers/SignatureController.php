<?php

namespace Controllers;

use Models\Signature;
use Models\Schedule;
use Models\User;
use Utils\Auth;
use Utils\Session;
use Config\Database;

class SignatureController {
    // Afficher la liste des signatures
    public function index() {
        Auth::requireRole('admin');
        
        $signatures = Signature::findAllWithDetails();
        $students = User::findByRole('student');
        $schedules = Schedule::findAll();
        
        // Inclure la vue
        include 'views/gestion_signature.php';
    }
    
    // Ajouter une signature
    public function add() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? null;
            $schedule_id = $_POST['schedule_id'] ?? null;
            
            if ($user_id && $schedule_id) {
                $signature = new Signature();
                $signature->setUserId($user_id)
                         ->setScheduleId($schedule_id)
                         ->setFileName('')
                         ->setStatus(Signature::STATUS_PENDING)
                         ->save();
                
                Session::setFlash('success', 'Signature ajoutée avec succès.');
            } else {
                Session::setFlash('error', 'Tous les champs sont requis.');
            }
            
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
    
    // Mettre à jour une signature
    public function update() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $signature_id = $_POST['signature_id'] ?? null;
            $status = $_POST['status'] ?? Signature::STATUS_PENDING;
            
            if ($signature_id && in_array($status, [Signature::STATUS_PENDING, Signature::STATUS_VALIDATED, Signature::STATUS_ABSENT])) {
                $signature = Signature::findById($signature_id);
                
                if ($signature) {
                    $signature->setStatus($status);
                    $signature->save();
                    
                    Session::setFlash('success', 'Signature mise à jour avec succès.');
                } else {
                    Session::setFlash('error', 'Signature non trouvée.');
                }
            } else {
                Session::setFlash('error', 'Données invalides.');
            }
            
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
    
    // Supprimer une signature
    public function delete() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $signature_id = $_POST['signature_id'] ?? null;
            
            if ($signature_id) {
                $signature = Signature::findById($signature_id);
                
                if ($signature) {
                    $signature->delete();
                    Session::setFlash('success', 'Signature supprimée avec succès.');
                } else {
                    Session::setFlash('error', 'Signature non trouvée.');
                }
            } else {
                Session::setFlash('error', 'ID de signature manquant.');
            }
            
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
    
    // Enregistrer les signatures pour un cours (utilisé par les professeurs)
    public function registerForClass() {
        Auth::requireRole('teacher');
        
        $user = Auth::getUser();
        $scheduleId = $_POST['schedule_id'] ?? null;
        
        if (!$scheduleId) {
            Session::setFlash('error', 'Cours non spécifié');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        $schedule = Schedule::findById($scheduleId);
        
        if (!$schedule) {
            Session::setFlash('error', 'Cours non trouvé');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Vérifier que le cours appartient bien au professeur
        if ($schedule->getUserId() !== $user->getId()) {
            Session::setFlash('error', 'Vous n\'êtes pas autorisé à gérer ce cours');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Ouvrir les signatures pour ce cours
        $schedule->setSignaturesOpen(true);
        $schedule->save();
        
        // Créer des signatures pour tous les étudiants de la classe
        Signature::createForClassAndSchedule($schedule->getClassId(), $schedule->getId());
        
        Session::setFlash('success', 'Signatures créées et ouvertes avec succès pour ce cours.');
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Valider une signature (utilisé par les étudiants)
    public function validateSignature() {
        Auth::requireRole('student');
        
        $user = Auth::getUser();
        $scheduleId = $_POST['schedule_id'] ?? null;
        
        if (!$scheduleId) {
            Session::setFlash('error', 'Cours non spécifié');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Vérifier si les signatures sont ouvertes pour ce cours
        $schedule = Schedule::findById($scheduleId);
        if (!$schedule || !$schedule->getSignaturesOpen()) {
            Session::setFlash('error', 'Les signatures ne sont pas encore ouvertes pour ce cours.');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Chercher la signature existante
        $signature = Signature::findByUserAndSchedule($user->getId(), $scheduleId);
        
        if ($signature) {
            // Valider la signature existante
            $signature->validate();
            Session::setFlash('success', 'Présence confirmée avec succès.');
        } else {
            // Créer une nouvelle signature validée
            $signature = new Signature();
            $signature->setUserId($user->getId())
                     ->setScheduleId($scheduleId)
                     ->setFileName('')
                     ->setStatus(Signature::STATUS_VALIDATED)
                     ->save();
            
            Session::setFlash('success', 'Présence enregistrée avec succès.');
        }
        
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Fermer les signatures pour un cours
    public function closeSignatures() {
        Auth::requireRole('teacher');
        
        $user = Auth::getUser();
        $scheduleId = $_POST['schedule_id'] ?? null;
        
        if (!$scheduleId) {
            Session::setFlash('error', 'Cours non spécifié');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Récupérer le cours spécifié
        $schedule = Schedule::findById($scheduleId);
        
        if (!$schedule) {
            Session::setFlash('error', 'Cours non trouvé');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Vérifier que le cours appartient bien au professeur
        if ($schedule->getUserId() !== $user->getId()) {
            Session::setFlash('error', 'Vous n\'êtes pas autorisé à gérer ce cours');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Marquer automatiquement les signatures en attente comme absentes
        $pendingSignatures = Signature::findByScheduleAndStatus($scheduleId, Signature::STATUS_PENDING);
        foreach ($pendingSignatures as $signature) {
            $signature->setStatus(Signature::STATUS_ABSENT);
            $signature->save();
        }
        
        // Fermer les signatures pour ce cours
        $schedule->setSignaturesOpen(false);
        $schedule->save();
        
        Session::setFlash('success', 'Signatures fermées pour ce cours. Les élèves qui n\'ont pas signé sont maintenant marqués comme absents.');
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Enregistrer les signatures pour les élèves sélectionnés
    public function registerForSelectedStudents() {
        Auth::requireRole('teacher');
        
        $user = Auth::getUser();
        $scheduleId = $_POST['schedule_id'] ?? null;
        $studentIds = $_POST['student_ids'] ?? [];
        
        if (!$scheduleId) {
            Session::setFlash('error', 'Cours non spécifié');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Récupérer le cours spécifié
        $schedule = Schedule::findById($scheduleId);
        
        if (!$schedule) {
            Session::setFlash('error', 'Cours non trouvé');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Vérifier que le cours appartient bien au professeur
        if ($schedule->getUserId() !== $user->getId()) {
            Session::setFlash('error', 'Vous n\'êtes pas autorisé à gérer ce cours');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Récupérer tous les étudiants de la classe
        $allClassStudents = User::findByClass($schedule->getClassId());
        $allStudentIds = array_map(function($student) {
            return $student->getId();
        }, $allClassStudents);
        
        // Déterminer les étudiants absents (ceux qui ne sont pas sélectionnés)
        $absentStudentIds = array_diff($allStudentIds, $studentIds);
        
        // Ouvrir les signatures pour ce cours
        $schedule->setSignaturesOpen(true);
        $schedule->save();
        
        // Créer des signatures pour les élèves sélectionnés (présents)
        foreach ($studentIds as $studentId) {
            $signature = Signature::findByUserAndSchedule($studentId, $scheduleId);
            
            if (!$signature) {
                $signature = new Signature();
                $signature->setUserId($studentId)
                        ->setScheduleId($scheduleId)
                        ->setFileName('')
                        ->setStatus(Signature::STATUS_PENDING)
                        ->save();
            } else {
                // Si la signature existe déjà, mettre à jour le statut en cas de changement
                if ($signature->getStatus() === Signature::STATUS_ABSENT) {
                    $signature->setStatus(Signature::STATUS_PENDING);
                    $signature->save();
                }
            }
        }
        
        // Marquer les élèves non sélectionnés comme absents
        foreach ($absentStudentIds as $studentId) {
            $signature = Signature::findByUserAndSchedule($studentId, $scheduleId);
            
            if (!$signature) {
                $signature = new Signature();
                $signature->setUserId($studentId)
                        ->setScheduleId($scheduleId)
                        ->setFileName('')
                        ->setStatus(Signature::STATUS_ABSENT)
                        ->save();
            } else {
                $signature->setStatus(Signature::STATUS_ABSENT);
                $signature->save();
            }
        }
        
        Session::setFlash('success', 'Signatures créées et ouvertes pour les élèves sélectionnés. Les autres élèves sont marqués comme absents.');
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}