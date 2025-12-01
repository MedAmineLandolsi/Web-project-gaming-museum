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

// Utiliser l'ID de l'utilisateur connecté
$auteur_id = $_SESSION['user_id'];
$nom_utilisateur = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];

// Récupérer les articles de l'auteur
$articles = $articleModel->lireParAuteur($auteur_id)->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la suppression
if(isset($_POST['supprimer'])) {
    $article_id = $_POST['article_id'];
    if($articleModel->supprimerParAuteur($article_id, $auteur_id)) {
        header("Location: mes-articles.php");
        exit();
    } else {
        $error_message = "❌ ERREUR : Impossible de supprimer l'article ou vous n'avez pas les droits.";
    }
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Articles - BLOG GAMING</title>
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

        /* Mes Articles Section */
        .mes-articles-section {
            margin-top: 120px;
            padding: 3rem 0;
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-green);
            text-shadow: 0 0 15px var(--primary-green);
        }

        .user-welcome {
            text-align: center;
            color: var(--secondary-purple);
            margin-bottom: 3rem;
            font-size: 1.125rem;
            font-family: 'VT323', monospace;
            background: rgba(189, 0, 255, 0.1);
            padding: 1rem;
            border: 1px solid var(--secondary-purple);
        }

        .user-welcome strong {
            color: var(--primary-green);
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 2.5rem;
            padding: 2rem 0 4rem;
        }

        .article-card {
            background: var(--card-bg);
            border-radius: 0;
            overflow: hidden;
            transition: all 0.4s;
            border: 2px solid var(--border-color);
            backdrop-filter: blur(20px);
        }

        .article-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 16px 40px rgba(0, 255, 65, 0.4);
            border-color: var(--primary-green);
        }

        .article-image {
            height: 220px;
            background: linear-gradient(135deg, var(--darker-bg), var(--card-bg));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            position: relative;
            overflow: hidden;
            border-bottom: 2px solid var(--primary-green);
        }

        .article-content {
            padding: 2rem;
        }

        .article-category {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            padding: 0.5rem 1.25rem;
            border-radius: 0;
            font-size: 0.6rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Press Start 2P', cursive;
            box-shadow: 0 4px 12px rgba(0, 255, 65, 0.3);
        }

        .article-title {
            font-size: 1rem;
            margin-bottom: 1.25rem;
            color: var(--primary-green);
            font-weight: 700;
            line-height: 1.3;
        }

        .article-excerpt {
            color: var(--text-gray);
            margin-bottom: 1.5rem;
            line-height: 1.7;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        .article-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.6rem;
            color: var(--text-gray);
            border-top: 1px solid var(--border-color);
            padding-top: 1.25rem;
            margin-bottom: 1.5rem;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .article-status {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 0;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .status-published {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
        }

        .status-pending {
            background: linear-gradient(135deg, #FFA500, #FF8C00);
            color: var(--darker-bg);
        }

        .status-draft {
            background: linear-gradient(135deg, var(--text-gray), #666666);
            color: var(--text-white);
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            border-radius: 0;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            border: none;
            cursor: pointer;
        }

        .btn-modifier {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            box-shadow: 0 4px 12px rgba(0, 255, 65, 0.3);
        }

        .btn-modifier:hover {
            background: linear-gradient(135deg, #00cc33, var(--primary-green));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 255, 65, 0.4);
        }

        .btn-supprimer {
            background: linear-gradient(135deg, var(--accent-pink), #ff1a75);
            color: var(--text-white);
            box-shadow: 0 4px 12px rgba(255, 0, 110, 0.3);
        }

        .btn-supprimer:hover {
            background: linear-gradient(135deg, #ff1a75, var(--accent-pink));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 0, 110, 0.4);
        }

        .no-articles {
            text-align: center;
            padding: 6rem 2rem;
            color: var(--text-gray);
            grid-column: 1 / -1;
        }

        .no-articles h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary-green);
        }

        .no-articles p {
            font-size: 1.125rem;
            margin-bottom: 1.5rem;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
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
            .section-title {
                font-size: 1.5rem;
            }
            
            .articles-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
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
            
            .mes-articles-section {
                margin-top: 100px;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
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
                    <li><a href="submit-article.php">✍️ ÉCRIRE UN ARTICLE</a></li>
                    <li><a href="mes-articles.php" class="active">MES ARTICLES</a></li>
                    <li><a href="deconnexion.php" class="logout-btn">DÉCONNEXION (<?php echo $_SESSION['user_prenom']; ?>)</a></li>
                </ul>
                <div class="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <section class="mes-articles-section">
        <div class="container">
            <h1 class="section-title">📝 MES ARTICLES</h1>
            
            <div class="user-welcome">
                <strong>Bienvenue <?php echo $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']; ?> !</strong><br>
                Gérez tous vos articles publiés et en attente de modération
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="articles-grid">
                <?php if (count($articles) > 0): ?>
                    <?php foreach ($articles as $article): ?>
                    <?php 
                    $categorie = isset($article['Categorie']) ? $article['Categorie'] : 'news';
                    $status_class = 'status-' . $article['Statut'];
                    ?>
                    <div class="article-card">
                        <div class="article-image">
                            <?php echo getCategoryIcon($categorie); ?>
                        </div>
                        <div class="article-content">
                            <span class="article-category">
                                <?php echo getCategoryLabel($categorie); ?>
                            </span>
                            <span class="article-status <?php echo $status_class; ?>">
                                <?php echo strtoupper($article['Statut']); ?>
                            </span>
                            <h3 class="article-title">
                                <?php echo htmlspecialchars($article['Titre']); ?>
                            </h3>
                            <p class="article-excerpt">
                                <?php
                                $content = strip_tags($article['Contenu']);
                                echo strlen($content) > 150 ? substr($content, 0, 150) . '...' : $content;
                                ?>
                            </p>
                            <div class="article-meta">
                                <span>📅 <?php echo date('d/m/Y', strtotime($article['Date_Publication'])); ?></span>
                                <span>👤 <?php echo $nom_utilisateur; ?></span>
                            </div>
                            <div class="actions">
                                <a href="modifier-article.php?id=<?php echo $article['Article_ID']; ?>" class="btn btn-modifier">
                                    ✏️ MODIFIER
                                </a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="article_id" value="<?php echo $article['Article_ID']; ?>">
                                    <button type="submit" name="supprimer" class="btn btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                        🗑️ SUPPRIMER
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-articles">
                        <h3>🎮 AUCUN ARTICLE TROUVÉ</h3>
                        <p>Vous n'avez pas encore écrit d'articles.</p>
                        <p style="margin-top: 1.5rem;">
                            <strong>Commencez par <a href="submit-article.php" style="color: var(--primary-green); font-weight: 600;">proposer un article</a> !</strong>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

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
                        <li><a href="mes-articles.php">Mes articles</a></li>
                        <li><a href="deconnexion.php">Déconnexion</a></li>
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