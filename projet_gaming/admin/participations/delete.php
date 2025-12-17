<?php
session_start(); // AJOUTER CETTE LIGNE AU DÉBUT

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../../login.php');
    exit();
}

include_once '../../config/database.php';
include_once '../../controllers/ParticipationController.php';

$database = new Database();
$db = $database->getConnection();

$participationController = new ParticipationController($db);

if(isset($_GET['id'])) {
    // AJOUTER : Vérifier si l'utilisateur peut annuler cette participation
    $canCancel = $participationController->canCancel($_GET['id'], $_SESSION['user_id']);
    
    // L'admin peut toujours supprimer, les utilisateurs normaux seulement s'ils peuvent annuler
    if ($_SESSION['role'] == 'admin' || $canCancel) {
        if($participationController->delete($_GET['id'])) {
            header("Location: ../participations.php?message=deleted");
        } else {
            header("Location: ../participations.php?message=error");
        }
    } else {
        header("Location: ../participations.php?message=no_permission");
    }
} else {
    header("Location: ../participations.php");
}
exit();
?>