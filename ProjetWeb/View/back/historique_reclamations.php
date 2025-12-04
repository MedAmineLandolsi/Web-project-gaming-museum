<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Réclamations</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <h1>Historique des Réclamations</h1>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Type</th>
                <th>Titre</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>20/11/2025</td>
                <td>Nourhene</td>
                <td>retard de livraison</td>
                <td>service livraison indisponible</td>
                <td>EN ATTENTE</td>
                <td><a href="voir_reclamation.php?id=1">✅ Voir</a></td>
            </tr>
        </tbody>
    </table>
    
    <br>
    <a href="espace_admin.php">← Retour</a>

</body>
</html>