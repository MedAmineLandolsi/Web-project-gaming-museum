<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Article.php';

$database = new Database();
$db = $database->getConnection();

$articleModel = new Article($db);

// Configuration de la pagination
$articles_par_page = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $articles_par_page;

// Récupérer le nombre total d'articles publiés
$total_articles = $articleModel->compterPublies();
$total_pages = ceil($total_articles / $articles_par_page);

// Récupérer les articles pour la page actuelle
$articles = $articleModel->lirePubliesAvecPagination($articles_par_page, $offset)->fetchAll(PDO::FETCH_ASSOC);

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
    <title>TOUS LES ARTICLES - BLOG GAMING</title>
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

        /* Blog Header */
        .blog-header {
            margin-top: 120px;
            text-align: center;
            padding: 4rem 0;
        }

        .blog-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-green);
            text-shadow: 
                0 0 10px var(--primary-green),
                0 0 20px var(--primary-green),
                0 0 40px var(--primary-green);
            margin-bottom: 1.5rem;
            line-height: 1.1;
            animation: glitch 5s infinite;
        }

        @keyframes glitch {
            0%, 90%, 100% { 
                transform: translate(0);
                text-shadow: 
                    0 0 10px var(--primary-green),
                    0 0 20px var(--primary-green);
            }
            92% { 
                transform: translate(-3px, 3px);
                text-shadow: 
                    3px -3px 0 var(--secondary-purple),
                    -3px 3px 0 var(--accent-pink);
            }
            94% { 
                transform: translate(3px, -3px);
                text-shadow: 
                    -3px 3px 0 var(--secondary-purple),
                    3px -3px 0 var(--accent-pink);
            }
        }

        .blog-subtitle {
            color: var(--secondary-purple);
            font-size: 1.5rem;
            font-weight: 500;
            font-family: 'VT323', monospace;
        }

        /* Articles Grid */
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

        .article-title a {
            color: var(--primary-green);
            text-decoration: none;
            transition: color 0.3s;
        }

        .article-title a:hover {
            color: var(--secondary-purple);
            text-shadow: 0 0 10px var(--secondary-purple);
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

        .read-more {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--secondary-purple), var(--accent-pink));
            color: var(--text-white);
            padding: 0.875rem 1.75rem;
            border-radius: 0;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            box-shadow: 0 4px 12px rgba(189, 0, 255, 0.3);
        }

        .read-more:hover {
            background: linear-gradient(135deg, var(--accent-pink), var(--secondary-purple));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(189, 0, 255, 0.4);
        }

        /* Pagination améliorée */
        .pagination {
            text-align: center;
            margin: 3rem 0;
            padding: 2rem 0;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .pagination-btn {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            padding: 1rem 2rem;
            border: none;
            border-radius: 0;
            font-size: 0.7rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 24px rgba(0, 255, 65, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination-btn:hover {
            background: linear-gradient(135deg, #00cc33, var(--primary-green));
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 255, 65, 0.4);
        }

        .pagination-btn:disabled {
            background: var(--text-gray);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .pagination-btn:disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .page-numbers {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .page-number {
            background: var(--card-bg);
            color: var(--text-white);
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            text-decoration: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            transition: all 0.3s;
        }

        .page-number:hover {
            border-color: var(--primary-green);
            background: rgba(0, 255, 65, 0.1);
        }

        .page-number.active {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            border-color: var(--primary-green);
        }

        .page-info {
            color: var(--text-gray);
            margin-top: 1rem;
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
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
            .blog-title {
                font-size: 1.8rem;
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
            
            .blog-header {
                margin-top: 100px;
                padding: 3rem 0;
            }
            
            .pagination-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .page-numbers {
                order: -1;
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
                    <li><a href="blog.php" class="active">ARTICLES</a></li>
                    <li><a href="about.php">À PROPOS</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="submit-article.php">✍️ ÉCRIRE UN ARTICLE</a></li>
                        <li><a href="mes-articles.php">MES ARTICLES</a></li>
                        <li><a href="deconnexion.php" class="logout-btn">DÉCONNEXION (<?php echo $_SESSION['user_prenom']; ?>)</a></li>
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

    <section class="blog-header">
        <div class="container">
            <h1 class="blog-title">TOUS LES ARTICLES</h1>
            <p class="blog-subtitle">Découvrez tous nos articles sur l'univers du gaming</p>
        </div>
    </section>

    <section class="container">
        <div class="articles-grid">
            <?php if (count($articles) > 0): ?>
                <?php foreach ($articles as $article): ?>
                <?php 
                $categorie = isset($article['Categorie']) ? $article['Categorie'] : 'news';
                ?>
                <div class="article-card">
                    <div class="article-image">
                        <?php echo getCategoryIcon($categorie); ?>
                    </div>
                    <div class="article-content">
                        <span class="article-category">
                            <?php echo getCategoryLabel($categorie); ?>
                        </span>
                        <h3 class="article-title">
                            <a href="blog-single.php?id=<?php echo $article['Article_ID']; ?>">
                                <?php echo htmlspecialchars($article['Titre']); ?>
                            </a>
                        </h3>
                        <p class="article-excerpt">
                            <?php
                            $content = strip_tags($article['Contenu']);
                            echo strlen($content) > 150 ? substr($content, 0, 150) . '...' : $content;
                            ?>
                        </p>
                        <div class="article-meta">
                            <span>📅 <?php echo date('d/m/Y', strtotime($article['Date_Publication'])); ?></span>
                            <span>👤 Auteur <?php echo $article['Auteur_ID']; ?></span>
                        </div>
                        <a href="blog-single.php?id=<?php echo $article['Article_ID']; ?>" class="read-more">
                            LIRE LA SUITE →
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-articles">
                    <h3>🎮 AUCUN ARTICLE PUBLIÉ</h3>
                    <p>Il n'y a pas encore d'articles publiés sur le blog.</p>
                    <p style="margin-top: 1.5rem;">
                        <strong>Soyez le premier à <a href="submit-article.php" style="color: var(--primary-green); font-weight: 600;">proposer un article</a> !</strong>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination améliorée -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <div class="pagination-container">
                <!-- Bouton Page Précédente -->
                <?php if ($page > 1): ?>
                    <a href="blog.php?page=<?php echo $page - 1; ?>" class="pagination-btn">
                        ◀️ PAGE PRÉCÉDENTE
                    </a>
                <?php else: ?>
                    <span class="pagination-btn" style="background: var(--text-gray); cursor: not-allowed;">
                        ◀️ PAGE PRÉCÉDENTE
                    </span>
                <?php endif; ?>

                <!-- Numéros de page -->
                <div class="page-numbers">
                    <?php
                    // Afficher maximum 5 pages autour de la page actuelle
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                        <a href="blog.php?page=<?php echo $i; ?>" 
                           class="page-number <?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <!-- Bouton Page Suivante -->
                <?php if ($page < $total_pages): ?>
                    <a href="blog.php?page=<?php echo $page + 1; ?>" class="pagination-btn">
                        PAGE SUIVANTE ▶️
                    </a>
                <?php else: ?>
                    <span class="pagination-btn" style="background: var(--text-gray); cursor: not-allowed;">
                        PAGE SUIVANTE ▶️
                    </span>
                <?php endif; ?>
            </div>

            <div class="page-info">
                Page <?php echo $page; ?> sur <?php echo $total_pages; ?> 
                - <?php echo $total_articles; ?> article<?php echo $total_articles > 1 ? 's' : ''; ?> au total
                
                <?php if ($page == $total_pages): ?>
                    <br><span style="color: var(--primary-green);">✅ Vous avez vu tous les articles !</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
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