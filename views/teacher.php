<?php
require_once __DIR__ . '/../config/autoload.php';
require_once __DIR__ . '/../utils/verif.php';
use Utils\Auth;
use Models\Schedule;

Auth::requireRole('teacher');
$user = Auth::getUser();
$user_name = $user->getFirstname() . ' ' . $user->getSurname();

// Récupérer les cours du jour
$schedule = Schedule::findTodayForTeacher($user->getId());

// Récupérer les signatures du cours en cours
$currentClass = Schedule::findCurrentForTeacher($user->getId());
$signatures = $currentClass ? $currentClass->getSignatures() : [];

// Récupérer les classes et élèves
$classes = [];
// Pour chaque classe que le professeur enseigne
$teacherClasses = Schedule::getClassesByTeacher($user->getId());
foreach ($teacherClasses as $class) {
    $classes[$class->getName()] = $class->getStudents();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Professeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="header pb-5 mb-5">
    <?php include 'header.php'; ?>
</header>

<?php if (Utils\Session::has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo Utils\Session::getFlash('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (Utils\Session::has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo Utils\Session::getFlash('error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="container my-5">
    <h1 class="text-center mb-4">Bienvenue, <?php echo htmlspecialchars($user_name); ?> !</h1>
    <div class="row">
        <div class="col-lg-6 col-md-12 mb-4">
            <h2>Cours du jour</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Heure</th>
                        <th>Classe</th>
                        <th>Matière</th>
                        <th>Signatures</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedule as $entry): ?>
                        <tr>
                            <td><?php echo date("H:i", strtotime($entry->getStartDatetime())); ?> - <?php echo date("H:i", strtotime($entry->getEndDatetime())); ?></td>
                            <td><?php echo htmlspecialchars($entry->getClass()->getName()); ?></td>
                            <td><?php echo htmlspecialchars($entry->getSubject()->getName()); ?></td>
                            <td>
                                <?php if ($entry->getSignaturesOpen()): ?>
                                    <span class="badge bg-success">Ouvertes</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Fermées</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$entry->getSignaturesOpen()): ?>
                                    <!-- Bouton qui déclenche la modale -->
                                    <button type="button" class="btn btn-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#selectStudentsModal<?= $entry->getId(); ?>">
                                        Lancer signatures
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-success btn-sm" disabled>Actives</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-lg-6 col-md-12 mb-4">
            <h2>Signatures de Présence</h2>
            <?php 
            $hasActiveSignatures = false;
            
            foreach ($schedule as $entry): 
                if ($entry->getSignaturesOpen()): 
                    $hasActiveSignatures = true;
                    $signatures = $entry->getSignaturesWithStudentDetails();
            ?>
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <strong><?php echo htmlspecialchars($entry->getSubject()->getName()); ?></strong>
                        - <?php echo date("H:i", strtotime($entry->getStartDatetime())); ?> 
                        - Classe <?php echo htmlspecialchars($entry->getClass()->getName()); ?>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($signatures)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Aucun élève n'a encore signé</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($signatures as $signature): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($signature['surname'] . ' ' . $signature['firstname']); ?></td>
                                            <td><?php echo htmlspecialchars($signature['email']); ?></td>
                                            <td>
                                                <?php if ($signature['status'] === 'validated'): ?>
                                                    <span class="badge bg-success">Présent</span>
                                                <?php elseif ($signature['status'] === 'absent'): ?>
                                                    <span class="badge bg-danger">Absent</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">En attente</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <form method="POST" action="/b-formation/signature_controller.php">
                            <input type="hidden" name="action" value="closeSignatures">
                            <input type="hidden" name="schedule_id" value="<?= $entry->getId(); ?>">
                            <button type="submit" class="btn btn-warning btn-sm">Fermer les signatures</button>
                        </form>
                    </div>
                </div>
            <?php 
                endif;
            endforeach; 
            
            if (!$hasActiveSignatures):
            ?>
                <div class="alert alert-info">
                    Aucune signature active. Veuillez lancer les signatures pour un cours afin de voir les détails des présences.
                </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-6 col-md-12 mb-4">
            <h2>Classes et Élèves</h2>
            
            <div class="accordion" id="classesAccordion">
                <?php 
                $i = 0;
                foreach ($teacherClasses as $class): 
                    $i++;
                    $students = $class->getStudents();
                ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $i ?>">
                            <button class="accordion-button <?= ($i > 1) ? 'collapsed' : '' ?>" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse<?= $i ?>" 
                                    aria-expanded="<?= ($i === 1) ? 'true' : 'false' ?>" 
                                    aria-controls="collapse<?= $i ?>">
                                Classe <?= htmlspecialchars($class->getName()) ?> (<?= count($students) ?> élèves)
                            </button>
                        </h2>
                        <div id="collapse<?= $i ?>" 
                            class="accordion-collapse collapse <?= ($i === 1) ? 'show' : '' ?>" 
                            aria-labelledby="heading<?= $i ?>" 
                            data-bs-parent="#classesAccordion">
                            <div class="accordion-body p-0">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($students)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center">Aucun élève dans cette classe</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($students as $student): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($student->getSurname()) ?></td>
                                                    <td><?= htmlspecialchars($student->getFirstname()) ?></td>
                                                    <td><?= htmlspecialchars($student->getEmail()) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($teacherClasses)): ?>
                    <div class="alert alert-info">
                        Vous n'enseignez actuellement dans aucune classe.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modales pour sélectionner les élèves -->
<?php foreach ($schedule as $entry): ?>
    <?php if (!$entry->getSignaturesOpen()): ?>
        <?php $classStudents = $entry->getClass()->getStudents(); ?>
        
        <div class="modal fade" id="selectStudentsModal<?= $entry->getId(); ?>" tabindex="-1" aria-labelledby="selectStudentsModalLabel<?= $entry->getId(); ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="selectStudentsModalLabel<?= $entry->getId(); ?>">
                            Sélectionner les élèves présents - <?= htmlspecialchars($entry->getSubject()->getName()); ?>
                            (<?= date("H:i", strtotime($entry->getStartDatetime())); ?>)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="signatureForm<?= $entry->getId(); ?>" method="POST" action="/b-formation/signature_controller.php">
                            <input type="hidden" name="action" value="registerForSelectedStudents">
                            <input type="hidden" name="schedule_id" value="<?= $entry->getId(); ?>">
                            
                            <div class="mb-3 d-flex justify-content-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="selectAllBtn<?= $entry->getId(); ?>">Tout sélectionner</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn<?= $entry->getId(); ?>">Tout désélectionner</button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;"></th>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($classStudents)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Aucun élève dans cette classe</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($classStudents as $student): ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input student-checkbox-<?= $entry->getId(); ?>" 
                                                                   type="checkbox" 
                                                                   name="student_ids[]" 
                                                                   value="<?= $student->getId(); ?>" 
                                                                   id="student<?= $student->getId(); ?>_<?= $entry->getId(); ?>"
                                                                   checked>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($student->getSurname()); ?></td>
                                                    <td><?= htmlspecialchars($student->getFirstname()); ?></td>
                                                    <td><?= htmlspecialchars($student->getEmail()); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Lancer les signatures</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            // Scripts pour les boutons "tout sélectionner" et "tout désélectionner"
            document.getElementById('selectAllBtn<?= $entry->getId(); ?>').addEventListener('click', function() {
                document.querySelectorAll('.student-checkbox-<?= $entry->getId(); ?>').forEach(checkbox => {
                    checkbox.checked = true;
                });
            });
            
            document.getElementById('deselectAllBtn<?= $entry->getId(); ?>').addEventListener('click', function() {
                document.querySelectorAll('.student-checkbox-<?= $entry->getId(); ?>').forEach(checkbox => {
                    checkbox.checked = false;
                });
            });
        </script>
    <?php endif; ?>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>