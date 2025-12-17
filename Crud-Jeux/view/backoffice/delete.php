<?php
include '../../controller/JeuxController.php';
include '../../model/Jeux.php'; 

$error = "";
$gameController = new JeuxController();

if (isset($_GET['id'])) {
    $gameController->deletegame($_GET['id']);
}






// Redirect to list page
header('Location: addgame.php');
     exit;

?>
