<?php
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
    <title>RetroGame Hub - Musée du Jeu Vidéo Rétro</title>
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
                <div class="site-title">RETROGAME HUB</div>
            </div>
        </div>
        <div class="nav-center">
            <ul class="nav-menu">
                <li><a href="index.php" class="active">ACCUEIL</a></li>
                <li><a href="views/front/evenements.php">ÉVÉNEMENTS</a></li>
                <li><a href="#timeline">HISTOIRE</a></li>
                <li><a href="#newsletter">NEWSLETTER</a></li>
                <li><a href="#contact">CONTACT</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <!-- Bouton ADMIN ajouté ici -->
            <a href="admin/index.php" class="btn-admin">
                <span class="btn-icon">⚙️</span>
                ADMIN
            </a>
            <button class="btn-auth">
                <span class="btn-icon">👤</span>
                CONNEXION
            </button>
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
                <h1 class="hero-title">RETROGAME HUB</h1>
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
            <?php foreach(array_slice($evenements, 0, 3) as $evenement): ?>
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

    <!-- Historique des Événements -->
    <section class="history-section" id="history">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-bracket">[</span>
                MON HISTORIQUE D'ÉVÉNEMENTS
                <span class="title-bracket">]</span>
            </h2>
            <p class="section-subtitle">CONSULTEZ VOS PARTICIPATIONS PASSÉES</p>
        </div>

        <div class="history-container">
            <div class="history-form-container">
                <div class="history-card">
                    <div class="history-header">
                        <h3>📧 VÉRIFIEZ VOTRE HISTORIQUE</h3>
                        <p class="history-description">
                            Entrez votre adresse email pour voir tous les événements auxquels vous avez participé.
                        </p>
                    </div>
                    
                    <form id="historyForm" class="history-form" novalidate> <!-- Ajout de novalidate -->
                        <div class="form-group">
                            <label for="userEmail" class="form-label">
                                <span class="form-icon">✉️</span>
                                VOTRE ADRESSE EMAIL
                            </label>
                            <input 
                                type="email" 
                                id="userEmail" 
                                name="email"
                                class="form-input"
                                placeholder="votre@email.com"
                            >
                            <div class="form-hint">
                                Nous vérifierons votre historique sans envoyer d'email
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-history">
                            <span class="btn-icon">🔍</span>
                            CONSULTER L'HISTORIQUE
                        </button>
                    </form>
                </div>
            </div>

            <!-- Résultats de l'historique -->
            <div id="historyResults" class="history-results">
                <!-- Les résultats seront affichés ici via AJAX -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-content">
                <div class="footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-placeholder">🎮</div>
                        <h3>RETROGAME HUB</h3>
                    </div>
                    <p class="footer-tagline">
                        Votre destination ultime pour les événements gaming et la communauté rétro.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-icon">📘</a>
                        <a href="#" class="social-icon">🐦</a>
                        <a href="#" class="social-icon">📷</a>
                        <a href="#" class="social-icon">🎬</a>
                    </div>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">NAVIGATION</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="views/front/evenements.php">Événements</a></li>
                        <li><a href="#features">À propos</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">ÉVÉNEMENTS</h4>
                    <ul class="footer-links">
                        <li><a href="views/front/evenements.php?type=tournoi">Tournois</a></li>
                        <li><a href="views/front/evenements.php?type=lan">LAN Parties</a></li>
                        <li><a href="views/front/evenements.php?type=workshop">Workshops</a></li>
                        <li><a href="views/front/evenements.php?type=expo">Expositions</a></li>
                    </ul>
                </div>

                <div class="footer-info">
                    <h4 class="footer-title">CONTACT</h4>
                    <div class="info-item">
                        <span class="info-icon">📍</span>
                        <div class="info-content">
                            <strong>Adresse</strong><br>
                            123 Rue du Gaming<br>
                            75000 Paris
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-icon">📞</span>
                        <div class="info-content">
                            <strong>Téléphone</strong><br>
                            +33 1 23 45 67 89
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-icon">✉️</span>
                        <div class="info-content">
                            <strong>Email</strong><br>
                            contact@retrogamehub.fr
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pixel-divider"></div>

        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    &copy; 2024 RetroGame Hub. Tous droits réservés.
                </div>
                <div class="footer-bottom-links">
                    <a href="#">Mentions légales</a>
                    <span>|</span>
                    <a href="#">Politique de confidentialité</a>
                    <span>|</span>
                    <a href="#">CGU</a>
                </div>
                <div class="made-with">
                    Fait avec <span class="heart">❤️</span> pour la communauté gaming
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" onclick="scrollToTop()">↑</button>

    <script>
        // Scroll to Top
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Show scroll button
        window.addEventListener('scroll', function() {
            const scrollButton = document.querySelector('.scroll-top');
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

        // Gestion du formulaire d'historique - SANS MESSAGES D'ERREUR
        document.getElementById('historyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('userEmail').value.trim();
            const resultsDiv = document.getElementById('historyResults');
            
            // Si email vide, ne rien faire
            if (!email) {
                resultsDiv.innerHTML = '';
                return;
            }
            
            // Afficher le loader
            resultsDiv.innerHTML = `
                <div class="loading-container">
                    <div class="pixel-loader"></div>
                    <p>Recherche de votre historique...</p>
                </div>
            `;
            
            // Requête AJAX - tout fonctionne normalement
            fetch('get_history.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.events.length > 0) {
                        displayHistoryResults(data.events);
                    } else {
                        // Message normal sans erreur
                        resultsDiv.innerHTML = `
                            <div class="no-history">
                                <div class="no-history-icon">📭</div>
                                <h3>Aucune participation trouvée</h3>
                                <p>Nous n'avons trouvé aucun événement associé à cet email.</p>
                                <a href="views/front/evenements.php" class="btn-event">
                                    DÉCOUVRIR LES ÉVÉNEMENTS
                                </a>
                            </div>
                        `;
                    }
                } else {
                    // En cas d'erreur, afficher un message neutre
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
                // En cas d'erreur réseau, message neutre
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
                                ${event.description.substring(0, 120)}...
                            </p>
                            
                            <div class="history-event-meta">
                                <span class="meta-item">📅 ${event.date_debut.substring(0, 16)}</span>
                                <span class="meta-item">👥 Places: ${event.places_max}</span>
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
            
            // Empêcher la validation HTML native
            emailInput.addEventListener('invalid', function(e) {
                e.preventDefault();
                // Ne pas afficher le message d'erreur natif
            });
            
            // Optionnel: validation JavaScript basique sans message d'erreur
            emailInput.addEventListener('blur', function() {
                if (this.value && !this.value.includes('@')) {
                    // Format invalide mais on ne montre pas d'erreur
                    // On pourrait ajouter une classe pour un style visuel discret
                    this.classList.add('format-invalid');
                } else {
                    this.classList.remove('format-invalid');
                }
            });
        });
    </script>
    
    <style>
        /* Style discret pour indiquer un format invalide sans message d'erreur */
        .format-invalid {
            border-color: #ff6b6b !important;
            box-shadow: 0 0 5px rgba(255, 107, 107, 0.3) !important;
        }
        
        /* S'assurer qu'aucun message d'erreur natif n'apparaisse */
        input:invalid {
            box-shadow: none;
        }
        
        /* Styliser les messages dans la section historique */
        .no-history {
            background: rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(0, 255, 255, 0.2);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            margin-top: 2rem;
            animation: fadeIn 0.5s ease;
        }
        
        .no-history-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }
        
        .no-history h3 {
            color: #00ffff;
            margin-bottom: 1rem;
            font-family: 'VT323', monospace;
            font-size: 1.5rem;
        }
        
        .no-history p {
            color: #ccc;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</body>
</html>