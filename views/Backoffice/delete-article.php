<?php
// Démarrer la session pour les messages
session_start();

// Inclure la configuration et le modèle
include_once '../../config/database.php';
include_once '../../models/Article.php';

// Vérifier que l'admin est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Vérifier qu'un ID est fourni
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    // Préparer le modèle Article
    $articleModel = new Article($db);
    $articleModel->Article_ID = $_GET['id'];
    
    // Tenter la suppression
    if ($articleModel->supprimer()) {
        $_SESSION['success_message'] = '✅ Article supprimé avec succès !';
    } else {
        $_SESSION['error_message'] = '❌ Erreur lors de la suppression de l\'article.';
    }
    
} else {
    $_SESSION['error_message'] = '❌ ID de l\'article manquant.';
}

// Rediriger vers la page des articles
header('Location: blog-admin.php');
exit();
?>