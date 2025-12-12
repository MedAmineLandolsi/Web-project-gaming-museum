<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/CommandeController.php';
require_once __DIR__ . '/../../model/Commande.php';

session_start();

// Check if the cart is empty
if (empty($_SESSION['cart'])) {
    die("Le panier est vide.");
}

// Get email from POST
$clientEmail = $_POST['clientEmail'] ?? null;
if (!$clientEmail) {
    die("Aucun email fourni.");
}

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../phpmailer/src/PHPMailer.php';
require __DIR__ . '/../../phpmailer/src/SMTP.php';
require __DIR__ . '/../../phpmailer/src/Exception.php';

$controller = new CommandeController();
$commandeDetails = '';
$grandTotal = 0;

// Build order details + save DB
foreach ($_SESSION['cart'] as $item) {
    $produit_id = $item['id'];
    $quantity = $item['quantity'];
    $total = $item['prix'] * $quantity;
    $date = date("Y-m-d H:i:s");

    $commandeDetails .= "Produit: " . htmlspecialchars($item['nom']) . "\n";
    $commandeDetails .= "Prix: " . number_format($item['prix'], 2) . " €\n";
    $commandeDetails .= "Quantité: " . $quantity . "\n";
    $commandeDetails .= "Total: " . number_format($total, 2) . " €\n\n";

    $grandTotal += $total;

    $commande = new Commande($produit_id, $total, $quantity, $date);
    $controller->addCommande($commande);
}

// Email content
$emailSubject = "Confirmation de votre commande | LUDOLOGY VAULT";

$emailBody = "Merci pour votre commande !\n\n" .
"Détails de votre commande:\n\n" .
$commandeDetails .
"Total Général: " . number_format($grandTotal, 2) . " €\n\n" .
"Votre commande a bien été enregistrée.\n" .
"Merci de faire vos achats chez LUDOLOGY VAULT !\n";

// SEND EMAIL WITH PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP CONFIG
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'wissemhanachi666@gmail.com';
    $mail->Password   = 'qpuqzbesjpocidqd
'; // 16 digits
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Email settings
    $mail->setFrom('wissemhanachi666@gmail.com', 'Ludology Vault');
    $mail->addAddress($clientEmail);

    $mail->isHTML(false);
    $mail->Subject = $emailSubject;
    $mail->Body    = $emailBody;

    $mail->send();

} catch (Exception $e) {
    echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
    exit;
}

// Clear cart
$_SESSION['cart'] = [];

// Redirect
header("Location: ../../../Crud-Jeux/view/frontoffice/games.php");
exit;

?>
