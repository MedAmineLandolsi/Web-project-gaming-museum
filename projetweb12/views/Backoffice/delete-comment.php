<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Commentaire.php';

// Vérifier l'authentification admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Vérifier qu'un ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = '❌ ID DU COMMENTAIRE MANQUANT.';
    header('Location: comments-admin.php');
    exit();
}

$comment_id = intval($_GET['id']);
$redirect_url = 'comments-admin.php';

// Vérifier s'il y a un paramètre de redirection
if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
    $redirect_url = $_GET['redirect'];
}

try {
    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    // Préparer le modèle Commentaire
    $commentaireModel = new Commentaire($db);
    $commentaireModel->ID = $comment_id;
    
    // Récupérer les infos du commentaire avant suppression (pour log)
    $commentaireModel->lireUn();
    $article_id = $commentaireModel->Article_ID;
    
    // Tenter la suppression
    if ($commentaireModel->supprimer()) {
        $_SESSION['success_message'] = '✅ COMMENTAIRE SUPPRIMÉ AVEC SUCCÈS !';
        
        // Journalisation de l'action (optionnel)
        if (isset($_SESSION['admin_username'])) {
            $log_message = "Admin " . $_SESSION['admin_username'] . " a supprimé le commentaire #" . $comment_id . " de l'article #" . $article_id;
            error_log(date('Y-m-d H:i:s') . " - " . $log_message . "\n", 3, "../../logs/admin_actions.log");
        }
    } else {
        $_SESSION['error_message'] = '❌ ERREUR LORS DE LA SUPPRESSION DU COMMENTAIRE.';
    }
    
} catch (Exception $e) {
    $_SESSION['error_message'] = '❌ ERREUR SYSTÈME : ' . htmlspecialchars($e->getMessage());
}

// Rediriger vers la page appropriée
if (isset($_GET['article_id'])) {
    header('Location: article-comments.php?id=' . intval($_GET['article_id']));
} else {
    header('Location: ' . $redirect_url);
}
exit();
?>