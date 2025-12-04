<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Article.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$articleModel = new Article($db);

// Traitement de la suppression
if(isset($_POST['supprimer']) && isset($_POST['article_id'])) {
    $article_id = $_POST['article_id'];
    $auteur_id = $_SESSION['user_id'];
    
    // Utiliser la méthode supprimerParAuteur pour sécurité
    if($articleModel->supprimerParAuteur($article_id, $auteur_id)) {
        $_SESSION['success_message'] = "✅ ARTICLE SUPPRIMÉ AVEC SUCCÈS !";
    } else {
        $_SESSION['error_message'] = "❌ ERREUR : Impossible de supprimer l'article ou vous n'avez pas les droits.";
    }
    
    // Rediriger vers la page d'où on vient
    $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'blog.php';
    header("Location: $redirect_to");
    exit();
} else {
    header('Location: blog.php');
    exit();
}
?>