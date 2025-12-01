<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Article.php';

$database = new Database();
$db = $database->getConnection();

$articleModel = new Article($db);
$articles = $articleModel->lireDerniers(3)->fetchAll(PDO::FETCH_ASSOC);

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
    <title>LV BLOG GAMING - ACCUEIL</title>
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

        /* Hero Section */
        .hero {
            background: radial-gradient(ellipse at center, rgba(0, 255, 65, 0.1) 0%, transparent 70%),
                        linear-gradient(180deg, var(--darker-bg) 0%, var(--dark-bg) 100%);
            height: 80vh;
            display: flex;
            align-items: center;
            margin-top: 80px;
            position: relative;
            border-bottom: 2px solid var(--primary-green);
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-green);
            text-shadow: 
                0 0 10px var(--primary-green),
                0 0 20px var(--primary-green),
                0 0 40px var(--primary-green);
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

        .hero p {
            font-size: 1rem;
            color: var(--secondary-purple);
            margin-bottom: 2rem;
            font-family: 'VT323', monospace;
            font-size: 1.5rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 1.2rem 2.5rem;
            text-decoration: none;
            border-radius: 0;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn:hover {
            transform: translateY(-3px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.5);
        }

        .btn-primary:hover {
            box-shadow: 0 0 50px rgba(0, 255, 65, 0.8);
            transform: translateY(-3px) scale(1.05);
        }

        .btn-secondary {
            background: transparent;
            color: var(--secondary-purple);
            border: 2px solid var(--secondary-purple);
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.3);
        }

        .btn-secondary:hover {
            background: var(--secondary-purple);
            color: var(--darker-bg);
            box-shadow: 0 0 40px rgba(189, 0, 255, 0.8);
        }

        /* Articles Section */
        .latest-articles {
            padding: 4rem 0;
            background: var(--card-bg);
        }

        .latest-articles h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 3rem;
            color: var(--primary-green);
            text-shadow: 0 0 15px var(--primary-green);
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .article-card {
            background: var(--darker-bg);
            border-radius: 0;
            overflow: hidden;
            transition: all 0.4s;
            border: 2px solid var(--border-color);
            position: relative;
        }

        .article-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 255, 65, 0.4);
            border-color: var(--primary-green);
        }

        .article-image {
            height: 200px;
            background: linear-gradient(135deg, var(--darker-bg), var(--card-bg));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            border-bottom: 2px solid var(--primary-green);
        }

        .article-content {
            padding: 1.5rem;
        }

        .article-category {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            padding: 0.5rem 1rem;
            border-radius: 0;
            font-size: 0.6rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
        }

        .article-title {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: var(--primary-green);
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
            margin-bottom: 1rem;
            line-height: 1.6;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        .article-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.6rem;
            color: var(--text-gray);
            border-top: 1px solid var(--border-color);
            padding-top: 1rem;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .no-articles {
            text-align: center;
            padding: 3rem;
            color: var(--text-gray);
            grid-column: 1 / -1;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .no-articles a {
            color: var(--primary-green);
            text-decoration: none;
        }

        .no-articles a:hover {
            color: var(--secondary-purple);
        }

        /* Features Section */
        .features {
            padding: 4rem 0;
            background: linear-gradient(180deg, var(--dark-bg), var(--darker-bg));
        }

        .features h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 3rem;
            color: var(--primary-green);
            text-shadow: 0 0 15px var(--primary-green);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 0;
            text-align: center;
            border: 2px solid var(--border-color);
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 255, 65, 0.4);
            border-color: var(--primary-green);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            color: var(--primary-green);
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .feature-card p {
            color: var(--text-gray);
            margin-bottom: 1.5rem;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        .feature-link {
            color: var(--secondary-purple);
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
        }

        .feature-link:hover {
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        /* Footer */
        .footer {
            background: var(--darker-bg);
            padding: 3rem 0 1rem;
            margin-top: 3rem;
            border-top: 3px solid var(--primary-green);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h4 {
            color: var(--primary-green);
            margin-bottom: 1rem;
            text-shadow: 0 0 10px var(--primary-green);
            font-size: 0.9rem;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
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
            padding-top: 2rem;
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
        }

        .mobile-menu span {
            width: 25px;
            height: 3px;
            background: var(--primary-green);
            margin: 3px 0;
            transition: 0.3s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 1.8rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .hero-buttons .btn {
                width: 100%;
                max-width: 300px;
            }

            .articles-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: var(--darker-bg);
                padding: 1rem;
                border-top: 2px solid var(--primary-green);
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .mobile-menu {
                display: flex;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .logo {
                font-size: 0.9rem;
            }

            .nav-links a {
                font-size: 0.6rem;
            }

            .hero h1 {
                font-size: 1.5rem;
            }

            .hero p {
                font-size: 1.2rem;
            }

            .btn {
                padding: 1rem 2rem;
                font-size: 0.7rem;
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
                    <li><a href="index.php" class="active">ACCUEIL</a></li>
                    <li><a href="blog.php">ARTICLES</a></li>
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

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>BIENVENUE SUR BLOG GAMING</h1>
                <p>Découvrez l'univers fascinant du jeu vidéo à travers nos articles, tests et analyses</p>
                <div class="hero-buttons">
                    <a href="blog.php" class="btn btn-primary">
                        EXPLORER LES ARTICLES
                        <span class="btn-arrow">→</span>
                    </a>
                    <a href="submit-article.php" class="btn btn-secondary">
                        ÉCRIRE UN ARTICLE
                        <span class="btn-arrow">→</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="latest-articles">
        <div class="container">
            <h2>DERNIERS ARTICLES</h2>
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
                                echo strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
                                ?>
                            </p>
                            <div class="article-meta">
                                <span>📅 <?php echo date('d/m/Y', strtotime($article['Date_Publication'])); ?></span>
                                <span>👤 Auteur <?php echo $article['Auteur_ID']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-articles">
                        <h3>🎮 AUCUN ARTICLE PUBLIÉ</h3>
                        <p>Il n'y a pas encore d'articles publiés sur le blog.</p>
                        <p style="margin-top: 1.5rem;">
                            <strong>Soyez le premier à <a href="submit-article.php">proposer un article</a> !</strong>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (count($articles) > 0): ?>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="blog.php" class="btn btn-primary">VOIR TOUS LES ARTICLES</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2>NOS ESPACES</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3>ARTICLES GAMING</h3>
                    <p>Actualités, tests et analyses des derniers jeux vidéo par notre communauté</p>
                    <a href="blog.php" class="feature-link">VOIR LES ARTICLES →</a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">✍️</div>
                    <h3>ÉCRIRE UN ARTICLE</h3>
                    <p>Partagez votre passion et contribuez à notre communauté gaming</p>
                    <a href="submit-article.php" class="feature-link">COMMENCER À ÉCRIRE →</a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⚙️</div>
                    <h3>ESPACE ADMIN</h3>
                    <p>Gérez le contenu du site, les articles et la base de données</p>
                    <a href="../Backoffice/login.php" class="feature-link">ACCÉDER À L'ADMIN →</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">🎮 BLOG GAMING</div>
                    <p style="color: var(--text-gray); font-family: 'VT323', monospace; font-size: 1rem; margin-top: 1rem;">
                        Votre destination gaming ultime
                    </p>
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
                    <p style="color: var(--text-gray); font-family: 'VT323', monospace; font-size: 1rem;">
                        LV@blog-gaming.fr<br>+216 21 121 732
                    </p>
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