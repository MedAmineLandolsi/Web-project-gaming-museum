<?php
session_start();  // AJOUTER CETTE LIGNE AU DÉBUT

include_once 'config/database.php';
include_once 'models/Evenement.php';
include_once 'controllers/EvenementController.php';

$database = new Database();
$db = $database->getConnection();

$evenementController = new EvenementController($db);
$evenements = $evenementController->index();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ludology vault -section events</title>
    <link rel="stylesheet" href="assets/css/style.css">
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

    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <div class="logo-container">
                    <div class="logo-placeholder">🎮</div>
                    <div class="site-title">ludology vault</div>
                </div>
            </div>
            <div class="nav-center">
                <ul class="nav-menu">
                    <li><a href="index.php" class="active">ACCUEIL</a></li>
                    <li><a href="views/front/evenements.php">ÉVÉNEMENTS</a></li>
                    <li><a href="#timeline"></a></li>
                    <li><a href="#newsletter"></a></li>
                    <li><a href="#contact"></a></li>
                </ul>
            </div>
            <div class="nav-right">
                <!-- MODIFIER : Boutons de connexion/déconnexion -->
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- Si l'utilisateur est connecté -->
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <!-- Seulement pour les admins -->
                        <a href="admin/index.php" class="btn-admin">
                            <span class="btn-icon">⚙️</span>
                            ADMIN
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn-auth">
                        <span class="btn-icon">⚙️</span>
                        DÉCONNEXION
                    </a>
                <?php else: ?>
                    <!-- Si l'utilisateur n'est pas connecté -->
                    <a href="login.php" class="btn-auth">
                        <span class="btn-icon">👤</span>
                        CONNEXION
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="grid-background"></div>
        <div class="scanline"></div>
        <div class="crt-effect"></div>
        <div class="hero-content">
            <div class="glitch-wrapper">
                <h1 class="hero-title">EVENTS SECTION</h1>
            </div>
            <p class="hero-subtitle">
                <span class="typing-text">ÉVÉNEMENTS GAMING ÉPIQUES</span>
            </p>
            
            <div class="hero-stats">
                <div class="stat-box">
                    <div class="stat-icon">🎮</div>
                    <div class="stat-number"><?php echo count($evenements); ?>+</div>
                    <div class="stat-label">ÉVÉNEMENTS</div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number">2.5K</div>
                    <div class="stat-label">JOUEURS ACTIFS</div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">🏆</div>
                    <div class="stat-number">50+</div>
                    <div class="stat-label">TOURNOIS</div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-number">4.8</div>
                    <div class="stat-label">NOTE MOYENNE</div>
                </div>
            </div>

            <div class="cta-buttons">
                <a href="views/front/evenements.php" class="btn-primary">
                    EXPLORER LES ÉVÉNEMENTS
                    <span class="btn-arrow">→</span>
                </a>
                <a href="#features" class="btn-secondary-hero">
                    EN SAVOIR PLUS
                    <span class="btn-arrow">→</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="featured-section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-bracket">[</span>
                POURQUOI NOUS CHOISIR ?
                <span class="title-bracket">]</span>
            </h2>
            <p class="section-subtitle">L'EXPÉRIENCE GAMING ULTIME</p>
        </div>

        <div class="games-grid">
            <div class="game-card">
                <div class="game-badge">PRO</div>
                <div class="game-image">
                    <div class="pixel-art">
                        <div class="pixel-placeholder">🏆 TOURNOIS</div>
                    </div>
                </div>
                <div class="game-info">
                    <h3>TOURNOIS COMPÉTITIFS</h3>
                    <div class="game-meta">
                        <span class="game-year">2024</span>
                        <span class="game-rating">★★★★★</span>
                    </div>
                    <p class="game-desc">
                        Participez à des compétitions officielles avec cash prizes et récompenses exclusives.
                    </p>
                    <div class="game-tags">
                        <span class="tag">CASH PRIZE</span>
                        <span class="tag">PRO</span>
                    </div>
                </div>
            </div>

            <div class="game-card">
                <div class="game-badge">SOCIAL</div>
                <div class="game-image">
                    <div class="pixel-art">
                        <div class="pixel-placeholder">👥 COMMUNAUTÉ</div>
                    </div>
                </div>
                <div class="game-info">
                    <h3>COMMUNAUTÉ ACTIVE</h3>
                    <div class="game-meta">
                        <span class="game-year">2024</span>
                        <span class="game-rating">★★★★★</span>
                    </div>
                    <p class="game-desc">
                        Rejoignez une communauté passionnée de gamers et faites de nouvelles rencontres.
                    </p>
                    <div class="game-tags">
                        <span class="tag">SOCIAL</span>
                        <span class="tag">FRIENDLY</span>
                    </div>
                </div>
            </div>

            <div class="game-card">
                <div class="game-badge">PREMIUM</div>
                <div class="game-image">
                    <div class="pixel-art">
                        <div class="pixel-placeholder">⚡ ÉQUIPEMENT</div>
                    </div>
                </div>
                <div class="game-info">
                    <h3>ÉQUIPEMENT PRO</h3>
                    <div class="game-meta">
                        <span class="game-year">2024</span>
                        <span class="game-rating">★★★★★</span>
                    </div>
                    <p class="game-desc">
                        Bénéficiez d'installations haut de gamme et d'équipements professionnels.
                    </p>
                    <div class="game-tags">
                        <span class="tag">HIGH-END</span>
                        <span class="tag">PRO GEAR</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Événements à Venir -->
    <section class="events-section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-bracket">[</span>
                ÉVÉNEMENTS À VENIR
                <span class="title-bracket">]</span>
            </h2>
            <p class="section-subtitle">NE MANQUEZ PAS LES PROCHAINS ÉVÉNEMENTS</p>
        </div>

        <div class="events-grid">
            <?php foreach(array_slice($evenements, 0, 3) as $evenement): 
                $organizer_name = $evenement['organisateur_nom'] ?? 'Admin';
            ?>
            <div class="event-card <?php echo $evenement['prix'] > 0 ? 'featured-event' : ''; ?>">
                <div class="event-header">
                    <div class="event-badge">
                        <?php echo $evenement['prix'] > 0 ? 'PREMIUM' : 'GRATUIT'; ?>
                    </div>
                    <div class="event-date-box">
                        <div class="date-day"><?php echo date('d', strtotime($evenement['date_debut'])); ?></div>
                        <div class="date-month"><?php echo date('M', strtotime($evenement['date_debut'])); ?></div>
                    </div>
                </div>
                <div class="event-content">
                    <h3><?php echo htmlspecialchars($evenement['jeu']); ?></h3>
                    <div class="event-organizer">
                        👤 Organisé par: <?php echo htmlspecialchars($organizer_name); ?>
                    </div>
                    <div class="event-time">
                        📅 <?php echo date('d/m/Y H:i', strtotime($evenement['date_debut'])); ?>
                    </div>
                    <p class="event-description">
                        <?php echo htmlspecialchars(substr($evenement['description'], 0, 150)); ?>...
                    </p>
                    <div class="event-tags">
                        <span class="event-tag">🎮 <?php echo htmlspecialchars($evenement['jeu']); ?></span>
                        <span class="event-tag">📍 <?php echo htmlspecialchars($evenement['lieu']); ?></span>
                        <span class="event-tag">💰 <?php echo $evenement['prix']; ?>€</span>
                    </div>
                    <a href="views/front/participer.php?id=<?php echo $evenement['id_evenement']; ?>" class="btn-event">
                        S'INSCRIRE MAINTENANT
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="view-all-container">
            <a href="views/front/evenements.php" class="btn-view-all">
                VOIR TOUS LES ÉVÉNEMENTS
            </a>
        </div>
    </section>
    
    <!-- Bouton Historique -->
    <section class="history-button-section" id="history">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-bracket">[</span>
                VOTRE HISTORIQUE
                <span class="title-bracket">]</span>
            </h2>
            <p class="section-subtitle">CONSUULTEZ VOS PARTICIPATIONS PASSÉES</p>
        </div>
        
        <div class="history-btn-container">
            <div class="history-btn-card">
                <div class="history-btn-content">
                    <div class="history-btn-icon">📊</div>
                    <h3>VOIR MON HISTORIQUE COMPLET</h3>
                    <p class="history-btn-description">
                        Consultez tous les événements auxquels vous avez participé, 
                        téléchargez vos certificats et suivez votre progression.
                    </p>
                    <a href="views/front/historique.php" class="btn-history-page">
                        <span class="btn-icon">🔍</span>
                        ACCÉDER À MON HISTORIQUE
                    </a>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="history-quick-stats">
                        <div class="quick-stat">
                            <span class="quick-stat-number"><?php echo isset($_SESSION['participation_count']) ? $_SESSION['participation_count'] : '0'; ?></span>
                            <span class="quick-stat-label">ÉVÉNEMENTS</span>
                        </div>
                        <div class="quick-stat">
                            <span class="quick-stat-number"><?php echo isset($_SESSION['tournament_wins']) ? $_SESSION['tournament_wins'] : '0'; ?></span>
                            <span class="quick-stat-label">VICTOIRES</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer du premier code -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-content">
                <div class="footer-section footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-placeholder">[LOGO]</div>
                        <h3>LUDOLOGY VAULT</h3>
                    </div>
                    <p class="footer-tagline">Preserver l'histoire du jeu video pour les generations futures</p>
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
                        <li><a href="blog.php">► Blog & Actualites</a></li>
                        <li><a href="events.php">► Evenements</a></li>
                        <li><a href="#">► A Propos</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">RESSOURCES</h3>
                    <ul class="footer-links">
                        <li><a href="#">► Base de Donnees</a></li>
                        <li><a href="#">► Archives Historiques</a></li>
                        <li><a href="#">► Guides & Tutoriels</a></li>
                        <li><a href="reclamation.php">► Support & Reclamations</a></li>
                        <li><a href="#">► FAQ</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">MUSEE</h3>
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
                <p class="copyright">&copy; 2024 LUDOLOGY VAULT - Tous droits reserves</p>
                <div class="footer-bottom-links">
                    <a href="#">Mentions Legales</a>
                    <span>•</span>
                    <a href="#">Politique de Confidentialite</a>
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

    <script src="../../animation.js"></script>
    <script>
        // Scroll to Top
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Show scroll button
        window.addEventListener('scroll', function() {
            const scrollButton = document.getElementById('scrollTop');
            if (window.scrollY > 300) {
                scrollButton.classList.add('visible');
            } else {
                scrollButton.classList.remove('visible');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Gestion du formulaire d'historique
        document.getElementById('historyForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const resultsDiv = document.getElementById('historyResults');
            
            let formData = new FormData();
            
            <?php if(isset($_SESSION['user_id'])): ?>
                formData.append('user_id', '<?php echo $_SESSION['user_id']; ?>');
            <?php else: ?>
                const email = document.getElementById('userEmail').value.trim();
                if (!email) {
                    resultsDiv.innerHTML = '';
                    return;
                }
                formData.append('email', email);
            <?php endif; ?>
            
            resultsDiv.innerHTML = `
                <div class="loading-container">
                    <div class="pixel-loader"></div>
                    <p>Recherche de votre historique...</p>
                </div>
            `;
            
            fetch('get_history.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.events.length > 0) {
                        displayHistoryResults(data.events);
                    } else {
                        resultsDiv.innerHTML = `
                            <div class="no-history">
                                <div class="no-history-icon">📭</div>
                                <h3>Aucune participation trouvée</h3>
                                <p>Nous n'avons trouvé aucun événement associé à votre compte.</p>
                                <a href="views/front/evenements.php" class="btn-event">
                                    DÉCOUVRIR LES ÉVÉNEMENTS
                                </a>
                            </div>
                        `;
                    }
                } else {
                    resultsDiv.innerHTML = `
                        <div class="no-history">
                            <div class="no-history-icon">📊</div>
                            <h3>Historique indisponible</h3>
                            <p>Veuillez réessayer ultérieurement.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultsDiv.innerHTML = `
                    <div class="no-history">
                        <div class="no-history-icon">📡</div>
                        <h3>Connexion interrompue</h3>
                        <p>Veuillez vérifier votre connexion internet.</p>
                    </div>
                `;
            });
        });

        function displayHistoryResults(events) {
            const resultsDiv = document.getElementById('historyResults');
            
            let html = `
                <div class="history-stats">
                    <div class="history-stat">
                        <span class="stat-number">${events.length}</span>
                        <span class="stat-label">ÉVÉNEMENTS</span>
                    </div>
                    <div class="history-stat">
                        <span class="stat-number">${events.filter(e => new Date(e.date_fin) < new Date()).length}</span>
                        <span class="stat-label">TERMINÉS</span>
                    </div>
                    <div class="history-stat">
                        <span class="stat-number">${events.filter(e => new Date(e.date_debut) > new Date()).length}</span>
                        <span class="stat-label">À VENIR</span>
                    </div>
                </div>
                
                <div class="history-events-grid">
            `;
            
            events.forEach(event => {
                const eventDate = new Date(event.date_debut);
                const isPast = eventDate < new Date();
                
                html += `
                    <div class="history-event-card ${isPast ? 'past-event' : 'upcoming-event'}">
                        <div class="history-event-header">
                            <div class="history-event-badge ${isPast ? 'badge-past' : 'badge-upcoming'}">
                                ${isPast ? 'TERMINÉ' : 'À VENIR'}
                            </div>
                            <div class="history-event-date">
                                ${eventDate.toLocaleDateString('fr-FR', { 
                                    day: 'numeric', 
                                    month: 'short',
                                    year: 'numeric'
                                })}
                            </div>
                        </div>
                        
                        <div class="history-event-content">
                            <h4>${event.nom}</h4>
                            ${event.organisateur_nom ? `<div class="event-organizer-small">👤 ${event.organisateur_nom}</div>` : ''}
                            <div class="history-event-info">
                                <div class="info-item">
                                    <span class="info-icon">🎮</span>
                                    <span>${event.jeu}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-icon">📍</span>
                                    <span>${event.lieu}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-icon">💰</span>
                                    <span>${event.prix}€</span>
                                </div>
                            </div>
                            
                            <p class="history-event-description">
                                ${event.description ? event.description.substring(0, 120) + '...' : 'Pas de description disponible'}
                            </p>
                            
                            <div class="history-event-meta">
                                <span class="meta-item">📅 ${event.date_debut ? event.date_debut.substring(0, 16) : 'Date inconnue'}</span>
                                <span class="meta-item">👥 Places: ${event.places_max || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `</div>`;
            resultsDiv.innerHTML = html;
        }

        // Animation pour les statistiques du héros
        function animateCounter(element, target, duration = 2000) {
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target + '+';
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current) + '+';
                }
            }, 16);
        }

        // Animer les statistiques au défilement
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumbers = document.querySelectorAll('.stat-number');
                    statNumbers.forEach(stat => {
                        const target = parseInt(stat.textContent);
                        animateCounter(stat, target);
                    });
                    observer.disconnect();
                }
            });
        });

        const heroSection = document.querySelector('.hero-stats');
        if (heroSection) {
            observer.observe(heroSection);
        }

        // Empêcher les messages d'erreur HTML natifs
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('userEmail');
            
            if (emailInput) {
                emailInput.addEventListener('invalid', function(e) {
                    e.preventDefault();
                });
                
                emailInput.addEventListener('blur', function() {
                    if (this.value && !this.value.includes('@')) {
                        this.classList.add('format-invalid');
                    } else {
                        this.classList.remove('format-invalid');
                    }
                });
            }
        });
    </script>
</body>
</html>