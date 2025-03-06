<?php

namespace Controllers;

use Models\Subject;
use Utils\Auth;
use Utils\Session;

class SubjectController {
    // Afficher la liste des matières
    public function index() {
        Auth::requireRole('admin');
        
        $subjects = Subject::findAll();
        
        // Inclure la vue
        include 'views/gestion_matières.php';
    }
    
    // Ajouter une matière
    public function add() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject_name = $_POST['subject_name'] ?? '';
            
            if (!empty($subject_name)) {
                $subject = new Subject();
                $subject->setName($subject_name);
                $subject->save();
                
                header("Location: gestion_matières.php");
                exit();
            } else {
                Session::setFlash('error', 'Le nom de la matière est requis.');
                header("Location: gestion_matières.php");
                exit();
            }
        }
    }
    
    // Mettre à jour une matière
    public function update() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject_id = $_POST['subject_id'] ?? null;
            $subject_name = $_POST['subject_name'] ?? '';
            
            if (!empty($subject_id) && !empty($subject_name)) {
                $subject = Subject::findById($subject_id);
                
                if ($subject) {
                    $subject->setName($subject_name);
                    $subject->save();
                    
                    Session::setFlash('success', 'Matière mise à jour avec succès.');
                } else {
                    Session::setFlash('error', 'Matière non trouvée.');
                }
            } else {
                Session::setFlash('error', 'Tous les champs sont requis.');
            }
            
            header("Location: gestion_matières.php");
            exit();
        }
    }
    
    // Supprimer une matière
    public function delete() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject_id = $_POST['subject_id'] ?? null;
            
            if (!empty($subject_id)) {
                $subject = Subject::findById($subject_id);
                
                if ($subject) {
                    if ($subject->delete()) {
                        Session::setFlash('success', 'Matière supprimée avec succès.');
                    } else {
                        Session::setFlash('error', 'Impossible de supprimer cette matière car elle est utilisée dans des cours.');
                    }
                } else {
                    Session::setFlash('error', 'Matière non trouvée.');
                }
            } else {
                Session::setFlash('error', 'ID de matière manquant.');
            }
            
            header("Location: gestion_matières.php");
            exit();
        }
    }
}