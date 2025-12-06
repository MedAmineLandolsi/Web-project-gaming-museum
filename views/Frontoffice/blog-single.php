<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Article.php';
include_once '../../models/Commentaire.php';

$database = new Database();
$db = $database->getConnection();

$articleModel = new Article($db);
$commentaireModel = new Commentaire($db);

$article = null;
$commentaires = [];
$nb_commentaires = 0;

if (isset($_GET['id'])) {
    $article_id = $_GET['id'];
    
    // Récupérer l'article avec les informations de l'auteur
    $article = $articleModel->lireUnComplet($article_id);
    
    if ($article && $article['Statut'] === 'published') {
        // Récupérer les commentaires avec informations utilisateur
        $commentaires = $commentaireModel->lireParArticle($article_id)->fetchAll(PDO::FETCH_ASSOC);
        $nb_commentaires = $commentaireModel->getCommentCountByArticle($article_id);
    } else {
        header('HTTP/1.0 404 Not Found');
        echo "<h1>Article non trouvé</h1>";
        echo "<p>L'article que vous recherchez n'est pas disponible.</p>";
        exit();
    }
} else {
    header('HTTP/1.0 404 Not Found');
    echo "<h1>Article non trouvé</h1>";
    exit();
}

function getCategoryLabel($category) {
    $categories = [
        'review' => 'TEST & REVIEW',
        'news' => 'ACTUALITÉ',
        'tutorial' => 'TUTORIEL',
        'trends' => 'TENDANCES'
    ];
    return $categories[$category] ?? $category;
}

function getCategoryIcon($category) {
    $icons = [
        'review' => '🎮',
        'news' => '📰',
        'tutorial' => '📚',
        'trends' => '📈'
    ];
    return $icons[$category] ?? '📝';
}

// Traitement du formulaire de commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Vous devez être connecté pour poster un commentaire.";
        header('Location: connexion.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
    
    // Utiliser l'ID de l'utilisateur connecté
    $commentaireModel->User_ID = $_SESSION['user_id'];
    $commentaireModel->Article_ID = $article_id;
    $commentaireModel->Contenu = htmlspecialchars($_POST['contenu']);
    $commentaireModel->Date_Commentaire = date('Y-m-d H:i:s');
    
    if ($commentaireModel->creer()) {
        $_SESSION['success_message'] = "Commentaire publié avec succès !";
        header('Location: blog-single.php?id=' . $article_id);
        exit();
    } else {
        $_SESSION['error_message'] = "Erreur lors de la publication du commentaire.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['Titre']); ?> - BLOG GAMING</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #00FF41;
            --secondary-purple: #BD00FF;
            --accent-pink: #FF006E;
            --dark-bg: #0a0a0a;
            --darker-bg: #050505;
            --card-bg: #1a1a1a;
            --text-white: #ffffff;
            --text-gray: #888888;
            --text-light-gray: #aaaaaa;
            --border-color: #333333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Press Start 2P', cursive;
            background-color: var(--dark-bg);
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 65, 0.02) 2px,
                    rgba(0, 255, 65, 0.02) 4px
                );
            color: var(--text-white);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            background: linear-gradient(180deg, var(--darker-bg) 0%, rgba(10, 10, 10, 0.95) 100%);
            border-bottom: 2px solid var(--primary-green);
            padding: 1.2rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 30px rgba(0, 255, 65, 0.2);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-green);
            text-shadow: 
                0 0 10px var(--primary-green),
                0 0 20px var(--primary-green),
                0 0 30px var(--primary-green);
            letter-spacing: 2px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: var(--text-white);
            text-decoration: none;
            font-size: 0.7rem;
            transition: all 0.3s;
            padding: 0.5rem 0;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-purple));
            transition: width 0.3s;
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .admin-btn {
            background: linear-gradient(135deg, var(--secondary-purple), var(--accent-pink));
            color: var(--text-white) !important;
            padding: 0.9rem 1.8rem;
            border-radius: 0;
            font-weight: bold;
            border: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.4);
            transition: all 0.3s;
        }

        .admin-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 0 30px rgba(189, 0, 255, 0.8);
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--accent-pink), #ff1a75);
            color: var(--text-white) !important;
            padding: 0.9rem 1.8rem;
            border-radius: 0;
            font-weight: bold;
            border: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.4);
            transition: all 0.3s;
        }

        .logout-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 0 30px rgba(255, 0, 110, 0.8);
        }

        /* Article Content */
        .article-header {
            margin-top: 120px;
            padding: 3rem 0;
        }

        .article-category {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Press Start 2P', cursive;
            box-shadow: 0 4px 12px rgba(0, 255, 65, 0.3);
        }

        .article-title {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--primary-green);
            text-shadow: 
                0 0 10px var(--primary-green),
                0 0 20px var(--primary-green);
            font-weight: 800;
            line-height: 1.1;
        }

        .article-meta {
            display: flex;
            gap: 2rem;
            color: var(--text-gray);
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 500;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        .author-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .author-details {
            display: flex;
            flex-direction: column;
        }

        .author-name {
            color: var(--primary-green);
            font-weight: bold;
        }

        .author-username {
            color: var(--text-gray);
            font-size: 0.8rem;
        }

        .article-content {
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 0;
            border: 2px solid var(--border-color);
            margin-bottom: 2.5rem;
            line-height: 1.8;
            font-size: 1.125rem;
            backdrop-filter: blur(20px);
        }

        .article-content p {
            margin-bottom: 1.5rem;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        /* Comments Section */
        .comments-section {
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 0;
            border: 2px solid var(--border-color);
            margin-bottom: 2.5rem;
            backdrop-filter: blur(20px);
        }

        .comments-title {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: var(--primary-green);
            font-weight: 700;
        }

        .comment {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05) 0%, rgba(189, 0, 255, 0.02) 100%);
            padding: 2rem;
            border-radius: 0;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-green);
            border: 2px solid var(--border-color);
            transition: all 0.3s;
        }

        .comment:hover {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.08) 0%, rgba(189, 0, 255, 0.04) 100%);
            transform: translateX(4px);
            border-color: var(--primary-green);
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .comment-author {
            color: var(--primary-green);
            font-weight: 700;
            font-size: 0.9rem;
        }

        .comment-avatar {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary-purple), var(--accent-pink));
            margin-right: 0.5rem;
            vertical-align: middle;
            text-align: center;
            line-height: 30px;
            font-size: 0.8rem;
        }

        .comment-date {
            color: var(--text-gray);
            font-size: 0.7rem;
            font-weight: 500;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .comment-content {
            color: var(--text-white);
            line-height: 1.7;
            font-size: 1rem;
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
        }

        .no-comments {
            text-align: center;
            padding: 3rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        /* Comment Form */
        .comment-form {
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 0;
            border: 2px solid var(--border-color);
            backdrop-filter: blur(20px);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            color: var(--primary-green);
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 0;
            color: var(--text-white);
            font-size: 1rem;
            font-family: 'VT323', monospace;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(0, 255, 65, 0.1);
            background: rgba(255, 255, 255, 0.08);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            font-family: 'VT323', monospace;
            line-height: 1.6;
            font-size: 1.1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            text-decoration: none;
            border-radius: 0;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.7rem;
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(0, 255, 65, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 255, 65, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-purple), #a000d4);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
        }

        .btn-outline:hover {
            background: rgba(0, 255, 65, 0.1);
        }

        .current-user {
            background: rgba(0, 255, 65, 0.1);
            padding: 1rem;
            border: 1px solid var(--primary-green);
            margin-bottom: 1rem;
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
        }

        .login-required {
            text-align: center;
            padding: 2rem;
            background: rgba(189, 0, 255, 0.1);
            border: 1px solid var(--secondary-purple);
        }

        .login-required p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            font-family: 'VT323', monospace;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--secondary-purple);
            text-decoration: none;
            margin-bottom: 2rem;
            transition: color 0.3s;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            background: rgba(189, 0, 255, 0.1);
            border-radius: 0;
            border: 2px solid rgba(189, 0, 255, 0.2);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
        }

        .back-link:hover {
            color: var(--primary-green);
            background: rgba(0, 255, 65, 0.1);
            border-color: rgba(0, 255, 65, 0.2);
        }

        /* Messages */
        .success-message {
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid rgba(0, 255, 65, 0.3);
            color: var(--primary-green);
            padding: 1.5rem 2rem;
            border-radius: 0;
            margin-bottom: 2.5rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            font-family: 'Press Start 2P', cursive;
        }

        .error-message {
            background: rgba(255, 0, 110, 0.1);
            border: 2px solid rgba(255, 0, 110, 0.3);
            color: var(--accent-pink);
            padding: 1.5rem 2rem;
            border-radius: 0;
            margin-bottom: 2.5rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            font-family: 'Press Start 2P', cursive;
        }

        /* Footer */
        .footer {
            background: var(--darker-bg);
            padding: 4rem 0 2rem;
            margin-top: 4rem;
            border-top: 3px solid var(--primary-green);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-section h4 {
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            text-shadow: 0 0 10px var(--primary-green);
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.75rem;
        }

        .footer-section a {
            color: var(--text-gray);
            text-decoration: none;
            transition: color 0.3s;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        .footer-section a:hover {
            color: var(--primary-green);
            transform: translateX(5px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 3rem;
            border-top: 1px solid var(--border-color);
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 4px;
        }

        .mobile-menu span {
            width: 25px;
            height: 3px;
            background: var(--primary-green);
            transition: 0.3s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .article-title {
                font-size: 1.5rem;
            }
            
            .article-meta {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: var(--darker-bg);
                padding: 1.5rem;
                border-top: 2px solid var(--primary-green);
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .mobile-menu {
                display: flex;
            }
            
            .article-header {
                margin-top: 100px;
            }
            
            .article-content,
            .comments-section,
            .comment-form {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Particules d'arrière-plan -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">🎮 LV BLOG GAMING</div>
                <ul class="nav-links">
                    <li><a href="index.php">ACCUEIL</a></li>
                    <li><a href="blog.php">ARTICLES</a></li>
                    <li><a href="about.php">À PROPOS</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="submit-article.php">✍️ ÉCRIRE UN ARTICLE</a></li>
                        <li><a href="mes-articles.php">MES ARTICLES</a></li>
                        <li><a href="deconnexion.php" class="logout-btn">DÉCONNEXION (<?php echo $_SESSION['user_first_name'] ?? 'Utilisateur'; ?>)</a></li>
                    <?php else: ?>
                        <li><a href="submit-article.php">✍️ ÉCRIRE UN ARTICLE</a></li>
                        <li><a href="connexion.php">SE CONNECTER</a></li>
                        <li><a href="inscription.php">S'INSCRIRE</a></li>
                        <li><a href="../Backoffice/login.php" class="admin-btn">ESPACE ADMIN</a></li>
                    <?php endif; ?>
                </ul>
                <div class="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <main class="container">
        <a href="blog.php" class="back-link">← RETOUR AUX ARTICLES</a>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php echo $_SESSION['error_message']; ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <article>
            <header class="article-header">
                <span class="article-category">
                    <?php echo getCategoryLabel($article['Categorie']); ?>
                </span>
                <h1 class="article-title"><?php echo htmlspecialchars($article['Titre']); ?></h1>
                
                <div class="author-info">
                    <div class="author-avatar">
                        <?php echo getCategoryIcon($article['Categorie']); ?>
                    </div>
                    <div class="author-details">
                        <span class="author-name"><?php echo htmlspecialchars($article['auteur_nom'] ?? 'Auteur inconnu'); ?></span>
                        <?php if (!empty($article['auteur_username'])): ?>
                            <span class="author-username">@<?php echo htmlspecialchars($article['auteur_username']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="article-meta">
                    <span>📅 <?php echo date('d/m/Y à H:i', strtotime($article['Date_Publication'])); ?></span>
                    <span>🏷️ <?php echo getCategoryLabel($article['Categorie']); ?></span>
                </div>
            </header>

            <div class="article-content">
                <?php echo nl2br(htmlspecialchars($article['Contenu'])); ?>
            </div>
        </article>

        <section class="comments-section">
            <h2 class="comments-title">💬 COMMENTAIRES (<?php echo $nb_commentaires; ?>)</h2>
            
            <?php if (count($commentaires) > 0): ?>
                <?php foreach ($commentaires as $comment): ?>
                <div class="comment">
                    <div class="comment-header">
                        <span class="comment-author">
                            <span class="comment-avatar">
                                <?php echo substr($comment['auteur_nom'] ?? 'U', 0, 1); ?>
                            </span>
                            <?php echo htmlspecialchars($comment['auteur_nom'] ?? 'Utilisateur inconnu'); ?>
                            <?php if (!empty($comment['username'])): ?>
                                <small style="color: var(--secondary-purple); margin-left: 0.5rem;">
                                    @<?php echo htmlspecialchars($comment['username']); ?>
                                </small>
                            <?php endif; ?>
                        </span>
                        <span class="comment-date"><?php echo date('d/m/Y à H:i', strtotime($comment['Date_Commentaire'])); ?></span>
                    </div>
                    <div class="comment-content">
                        <?php echo nl2br(htmlspecialchars($comment['Contenu'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-comments">
                    <p>Soyez le premier à commenter cet article !</p>
                </div>
            <?php endif; ?>
        </section>

        <section class="comment-form">
            <h2 class="comments-title">📝 AJOUTER UN COMMENTAIRE</h2>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Utilisateur connecté -->
                <div class="current-user">
                    <strong>Vous commentez en tant que :</strong><br>
                    <?php echo htmlspecialchars($_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name']); ?>
                    <?php if (!empty($_SESSION['username'])): ?>
                        <br><small>@<?php echo htmlspecialchars($_SESSION['username']); ?></small>
                    <?php endif; ?>
                </div>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="contenu" class="form-label">VOTRE COMMENTAIRE</label>
                        <textarea class="form-control" id="contenu" name="contenu" required
                                  placeholder="Partagez votre avis..."></textarea>
                    </div>
                    <button type="submit" name="submit_comment" class="btn">PUBLIER LE COMMENTAIRE</button>
                </form>
            <?php else: ?>
                <!-- Utilisateur non connecté -->
                <div class="login-required">
                    <p>Vous devez être connecté pour poster un commentaire.</p>
                    <a href="connexion.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-secondary">
                        🔐 SE CONNECTER
                    </a>
                    <a href="inscription.php" class="btn btn-outline">
                        📝 S'INSCRIRE
                    </a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo" style="font-size: 1.2rem; margin-bottom: 1rem;">🎮 BLOG GAMING</div>
                    <p style="color: var(--text-gray); font-family: 'VT323', monospace; font-size: 1rem;">Votre destination gaming ultime</p>
                </div>
                <div class="footer-section">
                    <h4>NAVIGATION</h4>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="blog.php">Articles</a></li>
                        <li><a href="about.php">À propos</a></li>
                        <li><a href="submit-article.php">Écrire un article</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="mes-articles.php">Mes articles</a></li>
                            <li><a href="deconnexion.php">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="connexion.php">Connexion</a></li>
                            <li><a href="inscription.php">Inscription</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>CONTACT</h4>
                    <p style="color: var(--text-gray); font-family: 'VT323', monospace; font-size: 1rem;">LV@blog-gaming.fr<br>+216 21 121 732</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 BLOG GAMING. TOUS DROITS RÉSERVÉS.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.querySelector('.mobile-menu');
            const navLinks = document.querySelector('.nav-links');
            
            if (mobileMenu && navLinks) {
                mobileMenu.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                });
            }

            // Animation des particules
            const particles = document.querySelectorAll('.particle');
            particles.forEach((particle, index) => {
                particle.style.animationDelay = `${index * 2}s`;
                particle.style.animationDuration = `${15 + Math.random() * 10}s`;
            });
        });
    </script>
</body>
</html>