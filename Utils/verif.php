<?php
require_once __DIR__ . '/../config/autoload.php';
use Utils\Session;
?>

<?php if (Session::has('success')): ?>
    <div class="alert alert-success">
        <?php echo Session::getFlash('success'); ?>
    </div>
<?php endif; ?>
<?php if (Session::has('error')): ?>
    <div class="alert alert-danger">
        <?php echo Session::getFlash('error'); ?>
    </div>
<?php endif; ?>


