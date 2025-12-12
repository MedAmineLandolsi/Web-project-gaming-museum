<?php
session_start();
require "../../../Crud-Jeux/controller/JeuxController.php";

$gameC = new JeuxController();

if (!isset($_GET['id'])) {
    header("Location: ../../../Crud-Jeux/view/frontoffice/index.php");
    exit;
}

$id = $_GET['id'];
$game = $gameC->showJeux($id);

if (!$game) {
    header("Location: ../../../Crud-Jeux/view/frontoffice/index.php");
    exit;
}

// Create cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If game already in cart → increase quantity
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['quantity'] += 1;
} else {
    $_SESSION['cart'][$id] = [
        'id' => $game['id'],
        'nom' => $game['nom'],
        'prix' => $game['prix'],
        'quantity' => 1
    ];
}

header("Location: cart.php");
exit;
?>
