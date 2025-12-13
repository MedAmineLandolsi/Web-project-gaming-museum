<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/JeuxController.php';
require_once '../../controller/CommandeController.php';

// Initialize controllers
$userController = new UserController();
$gamesC = new JeuxController();
$commandeC = new CommandeController();

// Check if user is logged in
$isLoggedIn = $userController->isLoggedIn();
$user = null;
$username = '';
$profilePicture = '';
$role = '';

if ($isLoggedIn) {
    $result = $userController->viewProfile($_SESSION['user_id']);
    $user = $result['user'];
    $username = $user['username'];
    $profilePicture = $user['profile_picture_url'] ?? '';
    $role = $user['role'];
    
    // Get user's orders
    $userOrders = $commandeC->getUserCommandes($_SESSION['user_id']);
    $recentOrders = array_slice($userOrders, 0, 3);
    
    // Get user order stats
    $userOrderStats = $commandeC->getUserOrderCount($_SESSION['user_id']);
} else {
    $userOrders = [];
    $recentOrders = [];
    $userOrderStats = ['order_count' => 0, 'total_spent' => 0];
}

// Get games for display
$list = $gamesC->listjeux(null, null, null);
$gameCount = $gamesC->countGames();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ludology Vault - Gaming Museum</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .user-menu {
            position: relative;
        }

        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
        }

        .user-profile-btn:hover {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.2), rgba(189, 0, 255, 0.2));
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.5);
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid var(--primary-green);
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--darker-bg);
            font-size: 0.8rem;
            font-weight: bold;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-name {
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .dropdown-icon {
            font-size: 0.8rem;
            transition: transform 0.3s;
        }

        .user-profile-btn:hover .dropdown-icon {
            transform: translateY(2px);
        }

        .user-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            min-width: 250px;
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            box-shadow: 0 10px 40px rgba(0, 255, 65, 0.4);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s;
            z-index: 1000;
            overflow: hidden;
        }

        .user-dropdown::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 65, 0.03) 2px,
                    rgba(0, 255, 65, 0.03) 4px
                );
            pointer-events: none;
        }

        .user-menu:hover .user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 1.5rem;
            border-bottom: 2px solid var(--primary-green);
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), transparent);
        }

        .dropdown-header-title {
            font-size: 0.6rem;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
        }

        .dropdown-header-subtitle {
            font-size: 0.5rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .dropdown-menu-list {
            list-style: none;
            padding: 0.5rem 0;
        }

        .dropdown-menu-item {
            margin: 0;
        }

        .dropdown-menu-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-light-gray);
            text-decoration: none;
            font-size: 0.6rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .dropdown-menu-link:hover {
            background: rgba(0, 255, 65, 0.1);
            color: var(--primary-green);
            border-left-color: var(--primary-green);
        }

        .dropdown-menu-link.admin {
            border-top: 1px solid var(--border-color);
            color: var(--secondary-purple);
        }

        .dropdown-menu-link.admin:hover {
            background: rgba(189, 0, 255, 0.1);
            color: var(--secondary-purple);
            border-left-color: var(--secondary-purple);
        }

        .dropdown-menu-link.logout {
            border-top: 1px solid var(--border-color);
            color: var(--accent-pink);
        }

        .dropdown-menu-link.logout:hover {
            background: rgba(255, 0, 110, 0.1);
            color: var(--accent-pink);
            border-left-color: var(--accent-pink);
        }

        .dropdown-icon-left {
            font-size: 1rem;
        }
        
        /* User hero stats */
        .user-hero-stats {
            display: flex;
            gap: 1.5rem;
            margin-top: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .user-stat-box {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));
            border: 2px solid var(--primary-green);
            padding: 1rem;
            min-width: 120px;
            text-align: center;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .user-stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 255, 65, 0.3);
        }

        .user-stat-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .user-stat-number {
            font-size: 1.2rem;
            color: var(--primary-green);
            display: block;
            margin-bottom: 0.3rem;
            font-family: 'VT323', monospace;
            font-size: 1.5rem;
        }

        .user-stat-label {
            font-size: 0.5rem;
            color: var(--text-gray);
            font-family: 'Press Start 2P', cursive;
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

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <div class="logo-container">
                    <div class="logo-placeholder">[LOGO]</div>
                    <h1 class="site-title">LUDOLOGY VAULT</h1>
                </div>
            </div>
            
            <div class="nav-center">
                <ul class="nav-menu">
                    <li><a href="index.php" class="active">HOME</a></li>
                    <li><a href="games.php">JEUX</a></li>
                    <li><a href="blog.php">BLOG</a></li>
                    <li><a href="events.php">EVENTS</a></li>
                    <li><a href="reclamation.php">RÉCLAMATION</a></li>
                    <?php if ($isLoggedIn): ?>
                    <li><a href="my-orders.php">MES COMMANDES</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="nav-right">
                <?php if ($isLoggedIn): ?>
                    <div class="user-menu">
                        <button class="user-profile-btn">
                            <div class="user-avatar">
                                <?php if ($profilePicture && file_exists("../../uploads/" . $profilePicture)): ?>
                                    <img src="../../uploads/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($username, 0, 2)); ?>
                                <?php endif; ?>
                            </div>
                            <span class="user-name"><?php echo htmlspecialchars($username); ?></span>
                            <span class="dropdown-icon">▼</span>
                        </button>
                        
                        <div class="user-dropdown">
                            <div class="dropdown-header">
                                <div class="dropdown-header-title">WELCOME BACK</div>
                                <div class="dropdown-header-subtitle"><?php echo htmlspecialchars($username); ?></div>
                            </div>
                            <ul class="dropdown-menu-list">
                                <li class="dropdown-menu-item">
                                    <a href="profile.php" class="dropdown-menu-link">
                                        <span class="dropdown-icon-left">👤</span>
                                        MON PROFIL
                                    </a>
                                </li>
                                <li class="dropdown-menu-item">
                                    <a href="my-orders.php" class="dropdown-menu-link">
                                        <span class="dropdown-icon-left">🛒</span>
                                        MES COMMANDES
                                    </a>
                                </li>
                                <?php if ($role === 'admin'): ?>
                                <li class="dropdown-menu-item">
                                    <a href="../backoffice/dashboard.php" class="dropdown-menu-link admin">
                                        <span class="dropdown-icon-left">⚙</span>
                                        ADMIN DASHBOARD
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="dropdown-menu-item">
                                    <a href="#" class="dropdown-menu-link logout" id="logoutBtn">
                                        <span class="dropdown-icon-left">🚪</span>
                                        DECONNEXION
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" style="text-decoration: none;">
                        <button class="btn-auth">
                            <span class="btn-icon">▶</span> SIGN IN / SIGN UP
                        </button>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="scanline"></div>
        <div class="crt-effect"></div>
        <div class="grid-background"></div>
        
        <div class="hero-content">
            <div class="glitch-wrapper">
                <h2 class="hero-title glitch" data-text="PRÉSERVONS L'HISTOIRE">PRÉSERVONS L'HISTOIRE</h2>
            </div>
            <p class="hero-subtitle">
                <span class="typing-text">
                    <?php if ($isLoggedIn): ?>
                        Bienvenue <?php echo htmlspecialchars($username); ?> ! Explorez notre collection...
                    <?php else: ?>
                        Explorez la plus grande collection de jeux vidéo rétro...
                    <?php endif; ?>
                </span>
            </p>
            
            <div class="hero-stats">
                <div class="stat-box">
                    <div class="stat-icon">🎮</div>
                    <span class="stat-number" data-target="<?= $gameCount ?>">0</span>
                    <span class="stat-label">JEUX ARCHIVÉS</span>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">🕹️</div>
                    <span class="stat-number" data-target="287">0</span>
                    <span class="stat-label">CONSOLES VINTAGE</span>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">📼</div>
                    <span class="stat-number" data-target="1970">0</span>
                    <span class="stat-label">DEPUIS</span>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">👥</div>
                    <span class="stat-number" data-target="50000">0</span>
                    <span class="stat-label">VISITEURS</span>
                </div>
            </div>
            
            <?php if ($isLoggedIn): ?>
            <div class="user-hero-stats">
                <div class="user-stat-box">
                    <div class="user-stat-icon">🛒</div>
                    <span class="user-stat-number"><?= $userOrderStats['order_count'] ?? 0 ?></span>
                    <span class="user-stat-label">COMMANDES</span>
                </div>
                <div class="user-stat-box">
                    <div class="user-stat-icon">💰</div>
                    <span class="user-stat-number"><?= number_format($userOrderStats['total_spent'] ?? 0, 2) ?>€</span>
                    <span class="user-stat-label">TOTAL DÉPENSÉ</span>
                </div>
                <div class="user-stat-box">
                    <div class="user-stat-icon">⭐</div>
                    <span class="user-stat-number">
                        <?php 
                        if (($userOrderStats['order_count'] ?? 0) >= 10) {
                            echo 'VIP';
                        } elseif (($userOrderStats['order_count'] ?? 0) >= 5) {
                            echo 'Régulier';
                        } else {
                            echo 'Membre';
                        }
                        ?>
                    </span>
                    <span class="user-stat-label">STATUT</span>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="cta-buttons">
                <a href="games.php" style="text-decoration: none;">
                    <button class="btn-primary">
                        <span>EXPLORER LA COLLECTION</span>
                        <span class="btn-arrow">→</span>
                    </button>
                </a>
                <?php if ($isLoggedIn): ?>
                <a href="my-orders.php" style="text-decoration: none;">
                    <button class="btn-secondary-hero">
                        <span>MES COMMANDES</span>
                        <span class="btn-arrow">→</span>
                    </button>
                </a>
                <?php else: ?>
                <a href="login.php" style="text-decoration: none;">
                    <button class="btn-secondary-hero">
                        <span>CONNEXION</span>
                        <span class="btn-arrow">→</span>
                    </button>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Featured Games Section -->
    <section class="featured-section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-bracket">◄◄◄</span>
                JEUX LÉGENDAIRES
                <span class="title-bracket">►►►</span>
            </h2>
            <p class="section-subtitle">Les pionniers qui ont façonné l'industrie du gaming</p>
        </div>
        
        <div class="games-grid">
        <?php if (!empty($list)) { 
            foreach ($list as $game) { 
        ?>  
            <div class="game-card">
                <!-- CATEGORY BADGE -->
                <div class="game-badge">
                    <?= htmlspecialchars($game['categorie']); ?>
                </div>

                <!-- IMAGE / PIXEL ART AREA -->
                <div class="game-image">
                    <div class="pixel-art">
                        <div class="pixel-placeholder">
                            <?= htmlspecialchars($game['nom']); ?>
                        </div>

                        <div class="hover-overlay">
                            <button class="quick-view" 
                                    onclick="window.location.href='game-details.php?id=<?= $game['id'] ?>'">
                                ACHETER
                            </button>
                        </div>
                    </div>
                </div>

                <!-- INFO UNDER THE CARD -->
                <div class="game-info">
                    <h3><?= htmlspecialchars($game['nom']); ?></h3>

                    <div class="game-meta">
                        <span class="game-year">💰 
                            <?= htmlspecialchars($game['prix']); ?> €
                        </span>
                        <span class="game-rating">
                            Stock : <?= htmlspecialchars($game['stock']); ?>
                        </span>
                    </div>

                    <p class="game-desc">
                        <?= nl2br(htmlspecialchars(substr($game['description'], 0, 100) . '...')); ?>
                    </p>

                    <div class="game-tags">
                        <span class="tag">
                            <?= htmlspecialchars($game['categorie']); ?>
                        </span>
                    </div>
                    
                    <!-- Add to cart button -->
                    <?php if ($isLoggedIn && $game['stock'] > 0): ?>
                    <form action="cart.php" method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
                        <input type="hidden" name="action" value="add_to_cart">
                        <button type="submit" class="btn-auth" style="width: 100%; padding: 0.5rem;">
                            <span class="btn-icon">🛒</span> AJOUTER AU PANIER
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php 
            } 
        } 
        ?>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="timeline-section">
        <h2 class="section-title">
            <span class="title-bracket">◄◄◄</span>
            CHRONOLOGIE DU GAMING
            <span class="title-bracket">►►►</span>
        </h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-year">1972</div>
                <div class="timeline-content">
                    <h4>NAISSANCE DU GAMING</h4>
                    <p>Pong lance l'ère des jeux vidéo commerciaux</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">1980</div>
                <div class="timeline-content">
                    <h4>L'ÂGE D'OR DES ARCADES</h4>
                    <p>Pac-Man, Space Invaders dominent les salles d'arcade</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">1985</div>
                <div class="timeline-content">
                    <h4>RÉVOLUTION CONSOLE</h4>
                    <p>La NES sauve l'industrie du crash de 1983</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">1991</div>
                <div class="timeline-content">
                    <h4>GUERRE DES CONSOLES</h4>
                    <p>Sonic vs Mario - Sega vs Nintendo</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">1994</div>
                <div class="timeline-content">
                    <h4>ÈRE PLAYSTATION</h4>
                    <p>Sony entre dans l'arène avec la PlayStation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="newsletter-container">
            <div class="newsletter-content">
                <h2>RESTEZ CONNECTÉ AU PASSÉ</h2>
                <p>Inscrivez-vous à notre newsletter pour recevoir les dernières nouvelles, événements et ajouts à notre collection</p>
            </div>
            <div class="newsletter-form">
                <input type="email" placeholder="votre.email@exemple.com" class="newsletter-input">
                <button class="newsletter-button">S'ABONNER →</button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-content">
                <div class="footer-section footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-placeholder">[LOGO]</div>
                        <h3>LUDOLOGY VAULT</h3>
                    </div>
                    <p class="footer-tagline">Préserver l'histoire du jeu vidéo pour les générations futures</p>
                    <div class="social-links">
                        <a href="#" class="social-icon" title="Facebook">
                            <span>FB</span>
                        </a>
                        <a href="#" class="social-icon" title="Twitter">
                            <span>TW</span>
                        </a>
                        <a href="#" class="social-icon" title="Instagram">
                            <span>IG</span>
                        </a>
                        <a href="#" class="social-icon" title="YouTube">
                            <span>YT</span>
                        </a>
                        <a href="#" class="social-icon" title="Discord">
                            <span>DC</span>
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">NAVIGATION</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">► Accueil</a></li>
                        <li><a href="games.php">► Collection de Jeux</a></li>
                        <li><a href="blog.php">► Blog & Actualités</a></li>
                        <li><a href="events.php">► Événements</a></li>
                        <li><a href="#">► À Propos</a></li>
                        <?php if ($isLoggedIn): ?>
                        <li><a href="profile.php">► Mon Profil</a></li>
                        <li><a href="my-orders.php">► Mes Commandes</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">RESSOURCES</h3>
                    <ul class="footer-links">
                        <li><a href="#">► Base de Données</a></li>
                        <li><a href="#">► Archives Historiques</a></li>
                        <li><a href="#">► Guides & Tutoriels</a></li>
                        <li><a href="reclamation.php">► Support & Réclamations</a></li>
                        <li><a href="#">► FAQ</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">MUSÉE</h3>
                    <div class="footer-info">
                        <p class="info-item">
                            <span class="info-icon">🕐</span>
                            <span class="info-content">
                                <strong>Horaires:</strong><br>
                                Lun-Ven: 09:00 - 18:00<br>
                                Sam-Dim: 10:00 - 20:00
                            </span>
                        </p>
                        <p class="info-item">
                            <span class="info-icon">📧</span>
                            <span class="info-content">contact@ludologyvault.tn</span>
                        </p>
                        <p class="info-item">
                            <span class="info-icon">📞</span>
                            <span class="info-content">+216 XX XXX XXX</span>
                        </p>
                        <p class="info-item">
                            <span class="info-icon">📍</span>
                            <span class="info-content">Tunis, Tunisia</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="pixel-divider"></div>
            <div class="footer-bottom-content">
                <p class="copyright">&copy; 2024 LUDOLOGY VAULT - Tous droits réservés</p>
                <div class="footer-bottom-links">
                    <a href="#">Mentions Légales</a>
                    <span>•</span>
                    <a href="#">Politique de Confidentialité</a>
                    <span>•</span>
                    <a href="#">Conditions d'Utilisation</a>
                </div>
                <p class="made-with">Made with <span class="heart">♥</span> for gamers worldwide</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <span>▲</span>
    </button>

    <script src="script.js"></script>
    <script>
        <?php if ($isLoggedIn): ?>
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter?')) {
                const formData = new FormData();
                formData.append('action', 'logout');
                
                fetch('../../controller/user_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = 'index.php';
                });
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>