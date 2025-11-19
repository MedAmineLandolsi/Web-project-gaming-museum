<?php
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM articles ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gestion des messages de succès
$success_message = '';
if(isset($_GET['success'])) {
    if($_GET['success'] === 'edit') {
        $success_message = '✅ Article modifié avec succès !';
    } elseif($_GET['success'] === 'delete') {
        $success_message = '✅ Article supprimé avec succès !';
    } elseif($_GET['success'] === '1') {
        $success_message = '✅ Article créé avec succès !';
    }
}

// Gestion des erreurs
$error_message = '';
if(isset($_GET['error'])) {
    if($_GET['error'] === 'delete') {
        $error_message = '❌ Erreur lors de la suppression de l\'article !';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion Articles - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .admin-container { max-width: 1200px; margin: 0 auto; }
        .admin-header { background: #2c3e50; color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .admin-nav { margin: 20px 0; }
        .admin-nav a { background: #3498db; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px; }
        .article-item { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #00ff88; }
        .actions a { margin-right: 10px; text-decoration: none; padding: 5px 10px; border-radius: 3px; }
        .edit-btn { background: #f39c12; color: white; }
        .delete-btn { background: #e74c3c; color: white; }
        .success-message { 
            background: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            border: 1px solid #c3e6cb;
            font-weight: bold;
        }
        .error-message { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            border: 1px solid #f5c6cb;
            font-weight: bold;
        }
        .stats {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>📝 Gestion des Articles</h1>
            <p>Interface d'administration minimaliste pour le CRUD</p>
        </div>

        <!-- Messages de succès/erreur -->
        <?php if($success_message): ?>
            <div class="success-message">
                <?= $success_message ?>
            </div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="error-message">
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats">
            <h3>📊 Statistiques</h3>
            <p><strong>Nombre total d'articles :</strong> <?= count($articles) ?></p>
            <p><strong>Dernier article :</strong> 
                <?php if(!empty($articles)): ?>
                    "<?= htmlspecialchars($articles[0]['title']) ?>" 
                    (<?= date('d/m/Y', strtotime($articles[0]['created_at'])) ?>)
                <?php else: ?>
                    Aucun article
                <?php endif; ?>
            </p>
        </div>

        <div class="admin-nav">
            <a href="create-article.php">➕ Nouvel Article</a>
            <a href="../views/front/blog.php">👀 Voir le Blog</a>
            <a href="../backoffice/dashboard.php">🏠 Retour au BackOffice Principal</a>
        </div>

        <h2>Articles existants (<?= count($articles) ?>)</h2>
        
        <?php if(!empty($articles)): ?>
            <?php foreach($articles as $article): ?>
            <div class="article-item">
                <h3><?= htmlspecialchars($article['title']) ?></h3>
                <p><strong>Auteur:</strong> <?= htmlspecialchars($article['author']) ?></p>
                <p><strong>Date:</strong> <?= date('d/m/Y à H:i', strtotime($article['created_at'])) ?></p>
                <p><strong>Contenu:</strong> <?= substr(htmlspecialchars($article['content']), 0, 100) ?>...</p>
                
                <div class="actions">
                    <a href="edit-article.php?id=<?= $article['id'] ?>" class="edit-btn">✏️ Modifier</a>
                    <a href="delete-article.php?id=<?= $article['id'] ?>" class="delete-btn" 
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer l\\'article \\'<?= addslashes($article['title']) ?>\\' ? Cette action est irréversible.')">
                       🗑️ Supprimer
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: white; border-radius: 5px;">
                <h3>📝 Aucun article trouvé</h3>
                <p>Créez votre premier article pour commencer !</p>
                <a href="create-article.php" style="background: #00ff88; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">
                    ➕ Créer le premier article
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Suppression automatique des messages après 5 secondes
        setTimeout(function() {
            const successMessage = document.querySelector('.success-message');
            const errorMessage = document.querySelector('.error-message');
            
            if(successMessage) {
                successMessage.style.display = 'none';
            }
            if(errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
</html>