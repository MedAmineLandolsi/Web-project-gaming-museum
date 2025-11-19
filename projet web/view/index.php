<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musée de Gaming - Accueil</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header Unifié -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">🎮 Musée de Gaming</div>
                <ul class="nav-links">
                    <li><a href="index.php" class="active">Accueil</a></li>
                    <li><a href="frontoffice/blog.php">Blog</a></li>
                    <li><a href="frontoffice/games.php">Jeux</a></li>
                    <li><a href="frontoffice/about.php">À propos</a></li>
                    <li><a href="frontoffice/contact.php">Contact</a></li>
                    <li><a href="backoffice/dashboard.php" class="admin-btn">Espace Admin</a></li>
                </ul>
                <div class="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Bienvenue au Musée de Gaming</h1>
                <p>Découvrez l'univers fascinant du jeu vidéo à travers nos collections, articles et analyses</p>
                <div class="hero-buttons">
                    <a href="frontoffice/blog.php" class="btn btn-primary">Explorer le Blog</a>
                    <a href="frontoffice/games.php" class="btn btn-secondary">Découvrir les Jeux</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2>Nos Espaces</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3>Blog Gaming</h3>
                    <p>Actualités, tests et analyses des derniers jeux vidéo par notre communauté</p>
                    <a href="frontoffice/blog.php" class="feature-link">Visiter le blog →</a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🎮</div>
                    <h3>Base de Données</h3>
                    <p>Recherchez et explorez notre collection complète de jeux vidéo</p>
                    <a href="frontoffice/games.php" class="feature-link">Explorer les jeux →</a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⚙️</div>
                    <h3>Espace Admin</h3>
                    <p>Gérez le contenu du site, les articles et la base de données</p>
                    <a href="backoffice/dashboard.php" class="feature-link">Accéder à l'admin →</a>
                </div>
            </div>
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
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="frontoffice/blog.php">Blog</a></li>
                        <li><a href="frontoffice/games.php">Jeux</a></li>
                        <li><a href="backoffice/dashboard.php">Admin</a></li>
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

    <script src="assets/js/main.js"></script>
</body>
</html>