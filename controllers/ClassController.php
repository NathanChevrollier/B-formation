<?php

namespace Controllers;

use Models\Classroom;
use Utils\Auth;
use Utils\Session;

class ClassController {
    // Afficher la liste des classes
    public function index() {
        Auth::requireRole('admin');
        
        $classes = Classroom::findAll();
        
        // Inclure la vue
        include 'views/gestion_classes.php';
    }
    
    // Ajouter une classe
    public function add() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $class_name = $_POST['class_name'] ?? '';
            
            if (!empty($class_name)) {
                $class = new Classroom();
                $class->setName($class_name);
                $class->save();
                
                header("Location: gestion_classes.php");
                exit();
            } else {
                Session::setFlash('error', 'Le nom de la classe est requis.');
                header("Location: gestion_classes.php");
                exit();
            }
        }
    }
    
    // Mettre à jour une classe
    public function update() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $class_id = $_POST['class_id'] ?? null;
            $class_name = $_POST['class_name'] ?? '';
            
            if (!empty($class_id) && !empty($class_name)) {
                $class = Classroom::findById($class_id);
                
                if ($class) {
                    $class->setName($class_name);
                    $class->save();
                    
                    Session::setFlash('success', 'Classe mise à jour avec succès.');
                } else {
                    Session::setFlash('error', 'Classe non trouvée.');
                }
            } else {
                Session::setFlash('error', 'Tous les champs sont requis.');
            }
            
            header("Location: gestion_classes.php");
            exit();
        }
    }
    
    // Supprimer une classe
    public function delete() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $class_id = $_POST['class_id'] ?? null;
            
            if (!empty($class_id)) {
                $class = Classroom::findById($class_id);
                
                if ($class) {
                    if ($class->delete()) {
                        Session::setFlash('success', 'Classe supprimée avec succès.');
                    } else {
                        Session::setFlash('error', 'Impossible de supprimer cette classe car elle est utilisée dans des plannings.');
                    }
                } else {
                    Session::setFlash('error', 'Classe non trouvée.');
                }
            } else {
                Session::setFlash('error', 'ID de classe manquant.');
            }
            
            header("Location: gestion_classes.php");
            exit();
        }
    }
}