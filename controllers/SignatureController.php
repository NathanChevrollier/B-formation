<?php

namespace Controllers;

use Models\Signature;
use Models\Schedule;
use Models\User;
use Utils\Auth;
use Utils\Session;

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
            
            header("Location: ../views/gestion_signature.php");
            exit();
        }
    }
    
    // Mettre à jour une signature
    public function update() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $signature_id = $_POST['signature_id'] ?? null;
            $status = $_POST['status'] ?? Signature::STATUS_PENDING;
            
            if ($signature_id && in_array($status, [Signature::STATUS_PENDING, Signature::STATUS_VALIDATED])) {
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
            
            header("Location: ../views/gestion_signature.php");
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
            
            header("Location: ../views/gestion_signature.php");
            exit();
        }
    }
    
    // Enregistrer les signatures pour un cours (utilisé par les professeurs)
    public function registerForClass() {
        Auth::requireRole('teacher');
        
        $user = Auth::getUser();
        
        // Trouver le cours en cours pour ce professeur
        $currentClass = Schedule::findCurrentForTeacher($user->getId());
        
        if ($currentClass) {
            // Créer des signatures pour tous les étudiants de la classe
            Signature::createForClassAndSchedule($currentClass->getClassId(), $currentClass->getId());
            
            Session::setFlash('success', 'Signatures créées avec succès.');
        } else {
            Session::setFlash('error', 'Aucun cours en cours.');
        }
        
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Valider une signature (utilisé par les étudiants)
    public function validateSignature() {
        Auth::requireRole('student');
        
        $user = Auth::getUser();
        
        // Trouver le cours en cours pour la classe de l'étudiant
        $currentClass = Schedule::findCurrentForClass($user->getClassId());
        
        if ($currentClass) {
            // Chercher la signature existante
            $signature = Signature::findByUserAndSchedule($user->getId(), $currentClass->getId());
            
            if ($signature) {
                // Valider la signature
                $signature->validate();
                
                Session::setFlash('success', 'Présence confirmée avec succès.');
            } else {
                // Créer une nouvelle signature validée
                $signature = new Signature();
                $signature->setUserId($user->getId())
                         ->setScheduleId($currentClass->getId())
                         ->setFileName('')
                         ->setStatus(Signature::STATUS_VALIDATED)
                         ->save();
                
                Session::setFlash('success', 'Présence enregistrée avec succès.');
            }
        } else {
            Session::setFlash('error', 'Aucun cours en cours.');
        }
        
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}