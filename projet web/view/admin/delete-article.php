<?php
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Récupérer l'ID de l'article à supprimer
$article_id = $_GET['id'] ?? null;

if($article_id) {
    // D'abord, supprimer les commentaires associés (à cause de la clé étrangère)
    $delete_comments_query = "DELETE FROM comments WHERE article_id = ?";
    $stmt_comments = $db->prepare($delete_comments_query);
    $stmt_comments->execute([$article_id]);
    
    // Ensuite, supprimer l'article
    $delete_article_query = "DELETE FROM articles WHERE id = ?";
    $stmt_article = $db->prepare($delete_article_query);
    
    if($stmt_article->execute([$article_id])) {
        header("Location: articles.php?success=delete");
        exit();
    } else {
        header("Location: articles.php?error=delete");
        exit();
    }
} else {
    header("Location: articles.php");
    exit();
}
?>

<!-- Page de redirection - normalement on ne voit jamais cette page -->
<!DOCTYPE html>
<html>
<head>
    <title>Suppression en cours...</title>
    <style>
        body { font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f5f5f5; }
        .message { background: white; padding: 2rem; border-radius: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="message">
        <h2>🔄 Suppression en cours...</h2>
        <p>Redirection vers la liste des articles...</p>
        <p>Si la redirection ne fonctionne pas, <a href="articles.php">cliquez ici</a>.</p>
    </div>
    
    <script>
        // Redirection automatique après 2 secondes
        setTimeout(function() {
            window.location.href = 'articles.php';
        }, 2000);
    </script>
</body>
</html>