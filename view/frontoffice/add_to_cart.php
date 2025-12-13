<?php
session_start();
require "../../../Crud-Jeux/controller/JeuxController.php";

$gameC = new JeuxController();

// Handle both GET and POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    if (isset($_POST['game_id'])) {
        $id = $_POST['game_id'];
        $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;
    } else {
        header("Location: ../../../Crud-Jeux/view/frontoffice/index.php");
        exit;
    }
} elseif (isset($_GET['id'])) {
    // Handle direct link clicks
    $id = $_GET['id'];
    $quantity = 1;
} else {
    header("Location: ../../../Crud-Jeux/view/frontoffice/index.php");
    exit;
}

$game = $gameC->showJeux($id);

if (!$game) {
    header("Location: ../../../Crud-Jeux/view/frontoffice/index.php");
    exit;
}

// Check stock availability
if ($game['stock'] <= 0) {
    $_SESSION['error'] = "Le jeu '{$game['nom']}' est en rupture de stock.";
    header("Location: ../../../Crud-Jeux/view/frontoffice/games.php");
    exit;
}

// Check if requested quantity exceeds stock
if (isset($_SESSION['cart'][$id])) {
    $currentQuantity = $_SESSION['cart'][$id]['quantity'];
    if (($currentQuantity + $quantity) > $game['stock']) {
        $_SESSION['error'] = "Stock insuffisant pour '{$game['nom']}'. Disponible : {$game['stock']} unité(s).";
        header("Location: ../../../Crud-Jeux/view/frontoffice/games.php");
        exit;
    }
} elseif ($quantity > $game['stock']) {
    $_SESSION['error'] = "Stock insuffisant pour '{$game['nom']}'. Disponible : {$game['stock']} unité(s).";
    header("Location: ../../../Crud-Jeux/view/frontoffice/games.php");
    exit;
}

// Create cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If game already in cart → increase quantity
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$id] = [
        'id' => $game['id'],
        'nom' => $game['nom'],
        'prix' => $game['prix'],
        'quantity' => $quantity
    ];
}

$_SESSION['success'] = "{$quantity} x '{$game['nom']}' ajouté au panier !";
header("Location: cart.php");
exit;
?>