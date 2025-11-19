<?php
// CONNEXION À LA BASE DE DONNÉES
if(isset($_GET['id'])) {
    require_once '../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Récupérer l'article
    $article_id = $_GET['id'];
    $query = "SELECT * FROM articles WHERE id = ? LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $article_id);
    $stmt->execute();
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$article) {
        header("Location: blog.php");
        exit();
    }
    
    // Récupérer les commentaires
    $comments_query = "SELECT * FROM comments WHERE article_id = ? ORDER BY created_at DESC";
    $comments_stmt = $db->prepare($comments_query);
    $comments_stmt->bindParam(1, $article_id);
    $comments_stmt->execute();
    $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} else {
    header("Location: blog.php");
    exit();
}

// TRAITEMENT DU FORMULAIRE DE COMMENTAIRE
if($_POST && isset($_POST['action']) && $_POST['action'] === 'createComment') {
    $auteur = trim($_POST['auteur']);
    $contenu = trim($_POST['contenu']);
    $article_id = $_POST['article_id'];
    
    // Validation PHP
    $errors = [];
    
    if(empty($auteur)) {
        $errors['auteur'] = "Le nom est obligatoire";
    } elseif(strlen($auteur) < 2) {
        $errors['auteur'] = "Le nom doit contenir au moins 2 caractères";
    }
    
    if(empty($contenu)) {
        $errors['contenu'] = "Le commentaire est obligatoire";
    } elseif(strlen($contenu) < 5) {
        $errors['contenu'] = "Le commentaire doit contenir au moins 5 caractères";
    }
    
    if(empty($errors)) {
        // Insertion du commentaire
        $insert_query = "INSERT INTO comments (article_id, author, content) VALUES (?, ?, ?)";
        $insert_stmt = $db->prepare($insert_query);
        
        if($insert_stmt->execute([$article_id, $auteur, $contenu])) {
            header("Location: blog-single.php?id=" . $article_id . "&success=1");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> - Musée de Gaming</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .single-blog-section {
            margin-top: 100px;
            padding: 2rem 0;
        }

        .blog-hero-image {
            height: 500px;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .blog-hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .blog-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 0 2rem;
        }

        .blog-category {
            background: var(--primary);
            color: var(--dark);
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .blog-title {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .blog-meta {
            display: flex;
            justify-content: center;
            gap: 2rem;
            color: var(--gray);
            font-size: 1rem;
            flex-wrap: wrap;
        }

        .blog-content {
            max-width: 800px;
            margin: 0 auto 4rem;
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .blog-content p {
            margin-bottom: 1.5rem;
        }

        .blog-content h2 {
            color: var(--primary);
            margin: 2rem 0 1rem;
            font-size: 2rem;
        }

        .blog-content h3 {
            color: var(--secondary);
            margin: 1.5rem 0 1rem;
            font-size: 1.5rem;
        }

        .blog-tags {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 2rem 0;
            justify-content: center;
        }

        .tag {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 15px;
            border: 1px solid var(--primary);
            font-size: 0.9rem;
        }

        /* Section Commentaires */
        .comments-section {
            max-width: 800px;
            margin: 0 auto;
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid var(--border);
        }

        .comments-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--primary);
            text-align: center;
        }

        .comment-form {
            margin-bottom: 3rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--light);
            font-size: 1rem;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--gray);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--dark);
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #00cc66;
            transform: translateY(-2px);
        }

        /* Liste des Commentaires */
        .comments-list {
            space-y: 1.5rem;
        }

        .comment {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid var(--primary);
            margin-bottom: 1.5rem;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .comment-author {
            color: var(--primary);
            font-weight: bold;
            font-size: 1.1rem;
        }

        .comment-date {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .comment-content {
            color: var(--light);
            line-height: 1.6;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            color: var(--secondary);
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .error-message {
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: none;
        }

        .field-error {
            border-color: #ff6b6b !important;
        }

        .success-message {
            background: rgba(46, 213, 115, 0.1);
            border: 1px solid #2ed573;
            color: #2ed573;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .blog-title {
                font-size: 2rem;
            }
            
            .blog-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .blog-hero-image {
                height: 300px;
            }
            
            .comment-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">🎮 Musée de Gaming</div>
                <ul class="nav-links">
                    <li><a href="../index.php">Accueil</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="games.php">Jeux</a></li>
                    <li><a href="about.php">À propos</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="../backoffice/dashboard.php" class="admin-btn">Espace Admin</a></li>
                </ul>
                <div class="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Article Single -->
    <section class="single-blog-section">
        <div class="container">
            <!-- Lien retour -->
            <a href="blog.php" class="back-link">← Retour au blog</a>

            <!-- Message de succès -->
            <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="success-message">
                    ✅ Votre commentaire a été publié avec succès !
                </div>
            <?php endif; ?>

            <!-- Contenu de l'article -->
            <div id="articleContent">
                <!-- Image Hero -->
                <div class="blog-hero-image">
                    <img src="../assets/images/<?= htmlspecialchars($article['image_url'] ?? 'placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($article['title']) ?>"
                         onerror="this.src='../assets/images/placeholder.jpg'">
                </div>

                <!-- En-tête Article -->
                <header class="blog-header">
                    <span class="blog-category">Article</span>
                    <h1 class="blog-title"><?= htmlspecialchars($article['title']) ?></h1>
                    <div class="blog-meta">
                        <span class="blog-date">
                            Publié le <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                        </span>
                        <span class="blog-author">
                            Par <?= htmlspecialchars($article['author']) ?>
                        </span>
                    </div>
                </header>

                <!-- Contenu Article -->
                <article class="blog-content">
                    <?= nl2br(htmlspecialchars($article['content'])) ?>
                </article>
            </div>

            <!-- Section Commentaires -->
            <section class="comments-section">
                <h3 class="comments-title" id="commentsTitle">
                    Commentaires (<?= count($comments) ?>)
                </h3>

                <!-- Formulaire Commentaire SANS HTML5 -->
                <form class="comment-form" id="commentForm" method="POST">
                    <input type="hidden" name="action" value="createComment">
                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                    
                    <div class="form-group">
                        <input type="text" name="auteur" id="commentAuthor" placeholder="Votre nom">
                        <div class="error-message" id="authorError">
                            <?= isset($errors['auteur']) ? $errors['auteur'] : '' ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <textarea name="contenu" id="commentContent" placeholder="Votre commentaire..." rows="5"></textarea>
                        <div class="error-message" id="contentError">
                            <?= isset($errors['contenu']) ? $errors['contenu'] : '' ?>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-primary" onclick="validateCommentForm()">
                        Publier le commentaire
                    </button>
                </form>

                <!-- Liste Commentaires -->
                <div class="comments-list" id="commentsList">
                    <?php if(!empty($comments)): ?>
                        <?php foreach($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <strong class="comment-author"><?= htmlspecialchars($comment['author']) ?></strong>
                                <span class="comment-date">
                                    <?= date('d/m/Y à H:i', strtotime($comment['created_at'])) ?>
                                </span>
                            </div>
                            <p class="comment-content"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-comments">
                            <p style="text-align: center; color: var(--gray);">
                                Soyez le premier à commenter cet article !
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">🎮 Musée de Gaming</div>
                    <p>Votre destination ultime pour la culture gaming</p>
                </div>
                <div class="footer-section">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="games.php">Jeux</a></li>
                        <li><a href="../backoffice/dashboard.php">Admin</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>email@musee-gaming.fr<br>+33 1 23 45 67 89</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Musée de Gaming. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
    
    <script>
        // Validation du formulaire de commentaire SANS HTML5
        function validateCommentForm() {
            let isValid = true;
            
            const author = document.getElementById('commentAuthor').value.trim();
            const content = document.getElementById('commentContent').value.trim();
            
            // Réinitialiser les erreurs
            document.getElementById('authorError').style.display = 'none';
            document.getElementById('contentError').style.display = 'none';
            document.getElementById('commentAuthor').classList.remove('field-error');
            document.getElementById('commentContent').classList.remove('field-error');
            
            // Validation auteur
            if (!author) {
                document.getElementById('authorError').textContent = 'Le nom est obligatoire';
                document.getElementById('authorError').style.display = 'block';
                document.getElementById('commentAuthor').classList.add('field-error');
                isValid = false;
            } else if (author.length < 2) {
                document.getElementById('authorError').textContent = 'Le nom doit contenir au moins 2 caractères';
                document.getElementById('authorError').style.display = 'block';
                document.getElementById('commentAuthor').classList.add('field-error');
                isValid = false;
            }
            
            // Validation contenu
            if (!content) {
                document.getElementById('contentError').textContent = 'Le commentaire est obligatoire';
                document.getElementById('contentError').style.display = 'block';
                document.getElementById('commentContent').classList.add('field-error');
                isValid = false;
            } else if (content.length < 5) {
                document.getElementById('contentError').textContent = 'Le commentaire doit contenir au moins 5 caractères';
                document.getElementById('contentError').style.display = 'block';
                document.getElementById('commentContent').classList.add('field-error');
                isValid = false;
            }
            
            if (isValid) {
                // Soumettre le formulaire si validation OK
                document.getElementById('commentForm').submit();
            }
        }
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Validation en temps réel
            const authorField = document.getElementById('commentAuthor');
            const contentField = document.getElementById('commentContent');
            
            if (authorField) {
                authorField.addEventListener('input', function() {
                    document.getElementById('authorError').style.display = 'none';
                    this.classList.remove('field-error');
                });
            }
            
            if (contentField) {
                contentField.addEventListener('input', function() {
                    document.getElementById('contentError').style.display = 'none';
                    this.classList.remove('field-error');
                });
            }
            
            // Afficher les erreurs PHP si elles existent
            <?php if(isset($errors['auteur'])): ?>
                document.getElementById('authorError').style.display = 'block';
                document.getElementById('commentAuthor').classList.add('field-error');
            <?php endif; ?>
            
            <?php if(isset($errors['contenu'])): ?>
                document.getElementById('contentError').style.display = 'block';
                document.getElementById('commentContent').classList.add('field-error');
            <?php endif; ?>
        });
    </script>
</body>
</html>