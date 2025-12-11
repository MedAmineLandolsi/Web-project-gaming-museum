<?php
include '../../controller/JeuxController.php';
$gamesC = new JeuxController();
$list = $gamesC->listjeux();
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
                    <li><a href="blog.html">BLOG</a></li>
                    <li><a href="events.html">EVENTS</a></li>
                    <li><a href="reclamation.html">RÉCLAMATION</a></li>
                </ul>
            </div>
            
            <div class="nav-right">
                <a href="../backoffice/dashboard.php" style="text-decoration: none;"><button class="btn-auth">
                    <span class="btn-icon">▶</span> SIGN IN / SIGN UP
                </button></a>
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
                <span class="typing-text">Explorez la plus grande collection de jeux vidéo rétro...</span>
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
            
            <div class="cta-buttons">
                <a href="games.php" style="text-decoration: none;"><button class="btn-primary">
                    <span>EXPLORER LA COLLECTION</span>
                    <span class="btn-arrow">→</span>
                </button></a>
                <button class="btn-secondary-hero">
                    <span>VISITE VIRTUELLE</span>
                    <span class="btn-arrow">→</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Search Bar Section -->
    <section class="search-section">
        <div class="search-container">
            <h3 class="search-title">◄ RECHERCHER DANS LA BASE DE DONNÉES ►</h3>
            <div class="search-bar">
                <input type="text" placeholder="Entrez le nom d'un jeu, console, année..." class="search-input">
                <button class="search-button">SEARCH</button>
            </div>
            <div class="quick-filters">
                <button class="filter-chip">Années 70</button>
                <button class="filter-chip">Années 80</button>
                <button class="filter-chip">Années 90</button>
                <button class="filter-chip">Arcade</button>
                <button class="filter-chip">Console</button>
                <button class="filter-chip">PC</button>
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
                        <button class="quick-view">BUY</button>
                    </div>
                </div>
            </div>

            <!-- INFO UNDER THE CARD (like your PAC-MAN example) -->
            <div class="game-info">
                <h3><?= htmlspecialchars($game['nom']); ?></h3>

                <div class="game-meta">
                    <!-- I use prix + stock instead of year + stars -->
                    <span class="game-year">💰 
                        <?= htmlspecialchars($game['prix']); ?> €
                    </span>
                    <span class="game-rating">
                        Stock : <?= htmlspecialchars($game['stock']); ?>
                    </span>
                </div>

                <p class="game-desc">
                    <?= nl2br(htmlspecialchars($game['description'])); ?>
                </p>

                <div class="game-tags">
                    <!-- main tag = categorie -->
                    <span class="tag">
                        <?= htmlspecialchars($game['categorie']); ?>
                    </span>
                    <!-- you can add more tags later if you have them in DB -->
                </div>
            </div>
        </div><!-- end .game-card -->

        <?php 
            } 
        } 
        ?>
    </div><!-- end .games-grid -->
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

    <!-- Events Section -->
    <section class="events-section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-bracket">◄◄◄</span>
                ÉVÉNEMENTS À VENIR
                <span class="title-bracket">►►►</span>
            </h2>
            <p class="section-subtitle">Rejoignez-nous pour célébrer l'histoire du gaming</p>
        </div>
        
        <div class="events-grid">
            <div class="event-card featured-event">
                <div class="event-header">
                    <div class="event-badge">FEATURED</div>
                    <div class="event-date-box">
                        <span class="date-day">25</span>
                        <span class="date-month">NOV</span>
                    </div>
                </div>
                <div class="event-content">
                    <h3>🏆 TOURNOI RETRO ARCADE CHAMPIONSHIP</h3>
                    <p class="event-time">⏰ 14:00 - 22:00</p>
                    <p class="event-description">Compétition épique sur les classiques des années 80. Prix à gagner et lots exclusifs pour les meilleurs joueurs!</p>
                    <div class="event-tags">
                        <span class="event-tag">Compétition</span>
                        <span class="event-tag">Arcade</span>
                        <span class="event-tag">Prix</span>
                    </div>
                    <button class="btn-event">S'INSCRIRE →</button>
                </div>
            </div>

            <div class="event-card">
                <div class="event-header">
                    <div class="event-date-box">
                        <span class="date-day">02</span>
                        <span class="date-month">DEC</span>
                    </div>
                </div>
                <div class="event-content">
                    <h3>📺 EXPOSITION: L'ÈRE NINTENDO</h3>
                    <p class="event-time">⏰ 10:00 - 18:00</p>
                    <p class="event-description">Découvrez l'évolution de Nintendo de la NES à la Switch avec des consoles rares et prototypes.</p>
                    <div class="event-tags">
                        <span class="event-tag">Exposition</span>
                        <span class="event-tag">Nintendo</span>
                    </div>
                    <button class="btn-event">EN SAVOIR PLUS →</button>
                </div>
            </div>

            <div class="event-card">
                <div class="event-header">
                    <div class="event-date-box">
                        <span class="date-day">15</span>
                        <span class="date-month">DEC</span>
                    </div>
                </div>
                <div class="event-content">
                    <h3>🎮 SOIRÉE GAMING MULTIPLAYER</h3>
                    <p class="event-time">⏰ 18:00 - 00:00</p>
                    <p class="event-description">Jouez à vos jeux rétro préférés en multijoueur local. Ambiance conviviale garantie!</p>
                    <div class="event-tags">
                        <span class="event-tag">Social</span>
                        <span class="event-tag">Multijoueur</span>
                    </div>
                    <button class="btn-event">RÉSERVER →</button>
                </div>
            </div>

            <div class="event-card">
                <div class="event-header">
                    <div class="event-date-box">
                        <span class="date-day">20</span>
                        <span class="date-month">DEC</span>
                    </div>
                </div>
                <div class="event-content">
                    <h3>🎓 CONFÉRENCE: GAME DESIGN RETRO</h3>
                    <p class="event-time">⏰ 15:00 - 17:30</p>
                    <p class="event-description">Rencontrez des développeurs légendaires et apprenez les secrets du game design des années 80-90.</p>
                    <div class="event-tags">
                        <span class="event-tag">Éducatif</span>
                        <span class="event-tag">Design</span>
                    </div>
                    <button class="btn-event">BILLETS →</button>
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
                        <li><a href="index.html">► Accueil</a></li>
                        <li><a href="games.html">► Collection de Jeux</a></li>
                        <li><a href="blog.html">► Blog & Actualités</a></li>
                        <li><a href="events.html">► Événements</a></li>
                        <li><a href="#">► À Propos</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">RESSOURCES</h3>
                    <ul class="footer-links">
                        <li><a href="#">► Base de Données</a></li>
                        <li><a href="#">► Archives Historiques</a></li>
                        <li><a href="#">► Guides & Tutoriels</a></li>
                        <li><a href="reclamation.html">► Support & Réclamations</a></li>
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
</body>
</html>