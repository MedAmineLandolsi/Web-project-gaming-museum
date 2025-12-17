<?php
session_start(); // AJOUTER CETTE LIGNE AU DÉBUT

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../../login.php');
    exit();
}

include_once '../../config/database.php';
include_once '../../models/Evenement.php';
include_once '../../controllers/EvenementController.php';

$database = new Database();
$db = $database->getConnection();

$evenementController = new EvenementController($db);

if(isset($_GET['id'])) {
    // AJOUTER : Vérifier si l'utilisateur peut gérer cet événement
    $canManage = $evenementController->canManage($_GET['id'], $_SESSION['user_id']);
    
    if ($_SESSION['role'] == 'admin' || $canManage) {
        if($evenementController->delete($_GET['id'])) {
            header("Location: ../evenements.php?message=deleted");
        } else {
            header("Location: ../evenements.php?message=error");
        }
    } else {
        header("Location: ../evenements.php?message=no_permission");
    }
} else {
    header("Location: ../evenements.php");
}
exit();
?>