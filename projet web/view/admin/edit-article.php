<?php
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Récupérer l'article à modifier
$article_id = $_GET['id'] ?? null;
if(!$article_id) {
    header("Location: articles.php");
    exit();
}

$query = "SELECT * FROM articles WHERE id = ? LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $article_id);
$stmt->execute();
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$article) {
    header("Location: articles.php");
    exit();
}

// Traitement du formulaire de modification
if($_POST) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author = trim($_POST['author']);
    $image_url = trim($_POST['image_url']) ?: 'placeholder.jpg';
    
    // Validation PHP
    $errors = [];
    
    if(empty($title)) {
        $errors['title'] = "Le titre est obligatoire";
    } elseif(strlen($title) < 3) {
        $errors['title'] = "Le titre doit contenir au moins 3 caractères";
    }
    
    if(empty($content)) {
        $errors['content'] = "Le contenu est obligatoire";
    } elseif(strlen($content) < 10) {
        $errors['content'] = "Le contenu doit contenir au moins 10 caractères";
    }
    
    if(empty($author)) {
        $errors['author'] = "L'auteur est obligatoire";
    }
    
    if(empty($errors)) {
        $query = "UPDATE articles SET title = ?, content = ?, author = ?, image_url = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if($stmt->execute([$title, $content, $author, $image_url, $article_id])) {
            header("Location: articles.php?success=edit");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier Article - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        textarea { height: 200px; resize: vertical; }
        .error { color: red; font-size: 0.9em; margin-top: 5px; }
        .btn { background: #00ff88; color: black; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-cancel { background: #95a5a6; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>✏️ Modifier l'article</h1>
        
        <form method="POST" id="editForm">
            <div class="form-group">
                <label>Titre *</label>
                <input type="text" name="title" id="title" 
                       value="<?= htmlspecialchars($_POST['title'] ?? $article['title']) ?>">
                <div class="error" id="titleError">
                    <?= $errors['title'] ?? '' ?>
                </div>
            </div>
            
            <div class="form-group">
                <label>Contenu *</label>
                <textarea name="content" id="content"><?= htmlspecialchars($_POST['content'] ?? $article['content']) ?></textarea>
                <div class="error" id="contentError">
                    <?= $errors['content'] ?? '' ?>
                </div>
            </div>
            
            <div class="form-group">
                <label>Auteur *</label>
                <input type="text" name="author" id="author" 
                       value="<?= htmlspecialchars($_POST['author'] ?? $article['author']) ?>">
                <div class="error" id="authorError">
                    <?= $errors['author'] ?? '' ?>
                </div>
            </div>
            
            <div class="form-group">
                <label>Image (URL)</label>
                <input type="text" name="image_url" 
                       value="<?= htmlspecialchars($_POST['image_url'] ?? $article['image_url']) ?>" 
                       placeholder="image.jpg">
            </div>
            
            <button type="button" class="btn" onclick="validateForm()">💾 Enregistrer les modifications</button>
            <a href="articles.php" class="btn-cancel" style="margin-left: 10px;">❌ Annuler</a>
        </form>
    </div>

    <script>
        function validateForm() {
            let isValid = true;
            
            const title = document.getElementById('title').value.trim();
            const content = document.getElementById('content').value.trim();
            const author = document.getElementById('author').value.trim();
            
            // Reset errors
            document.querySelectorAll('.error').forEach(error => {
                error.style.display = 'none';
            });
            
            // Validation titre
            if (!title) {
                document.getElementById('titleError').textContent = 'Le titre est obligatoire';
                document.getElementById('titleError').style.display = 'block';
                isValid = false;
            } else if (title.length < 3) {
                document.getElementById('titleError').textContent = 'Le titre doit contenir au moins 3 caractères';
                document.getElementById('titleError').style.display = 'block';
                isValid = false;
            }
            
            // Validation contenu
            if (!content) {
                document.getElementById('contentError').textContent = 'Le contenu est obligatoire';
                document.getElementById('contentError').style.display = 'block';
                isValid = false;
            } else if (content.length < 10) {
                document.getElementById('contentError').textContent = 'Le contenu doit contenir au moins 10 caractères';
                document.getElementById('contentError').style.display = 'block';
                isValid = false;
            }
            
            // Validation auteur
            if (!author) {
                document.getElementById('authorError').textContent = 'L\'auteur est obligatoire';
                document.getElementById('authorError').style.display = 'block';
                isValid = false;
            }
            
            if (isValid) {
                document.getElementById('editForm').submit();
            }
        }
        
        // Validation en temps réel
        document.addEventListener('DOMContentLoaded', function() {
            const fields = ['title', 'content', 'author'];
            fields.forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    element.addEventListener('input', function() {
                        document.getElementById(field + 'Error').style.display = 'none';
                    });
                }
            });
        });
    </script>
</body>
</html>