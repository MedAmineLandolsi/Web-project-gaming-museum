<?php
// voir_reclamation.php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit;
}

// Données exemple (à remplacer par votre base de données)
$reclamations = [
    1 => [
        'date' => '20/11/2025',
        'heure' => '19:04',
        'client' => 'Nourhene',
        'email' => 'nourheneklai@gmail.com',
        'type' => 'retard de livraison',
        'titre' => 'service livraison indisponible',
        'description' => 'les services de livraisons mauvaises',
        'statut' => 'EN ATTENTE'
    ]
];

$id = $_GET['id'] ?? 1;
$reclamation = $reclamations[$id] ?? null;

if (!$reclamation) {
    echo "Réclamation non trouvée";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Réclamation</title>
</head>
<body>

    <nav style="margin-bottom: 20px;">
        <a href="accueil.php">Accueil</a> | 
        <a href="historique_reclamations.php">← Retour à l'historique</a>
    </nav>

    <h1>Détails de la Réclamation</h1>
    
    <div style="border: 1px solid #ccc; padding: 20px; max-width: 600px;">
        <p><strong>Date/Heure:</strong> <?= $reclamation['date'] ?> <?= $reclamation['heure'] ?></p>
        <p><strong>Client:</strong> <?= $reclamation['client'] ?></p>
        <p><strong>Email:</strong> <?= $reclamation['email'] ?></p>
        <p><strong>Type:</strong> <?= $reclamation['type'] ?></p>
        <p><strong>Titre:</strong> <?= $reclamation['titre'] ?></p>
        <p><strong>Description:</strong> <?= $reclamation['description'] ?></p>
        <p><strong>Statut:</strong> <span style="color: orange; font-weight: bold;"><?= $reclamation['statut'] ?></span></p>
    </div>

</body>
</html>