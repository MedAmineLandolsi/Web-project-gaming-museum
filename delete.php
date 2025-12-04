<?php
include_once '../../config/database.php';
include_once '../../controllers/ParticipationController.php';

$database = new Database();
$db = $database->getConnection();

$participationController = new ParticipationController($db);

if(isset($_GET['id'])) {
    if($participationController->delete($_GET['id'])) {
        header("Location: ../participations.php?message=deleted");
    } else {
        header("Location: ../participations.php?message=error");
    }
} else {
    header("Location: ../participations.php");
}
exit();
?>