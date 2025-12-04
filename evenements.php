<?php
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/Evenement.php';
include_once __DIR__ . '/../../controllers/EvenementController.php';

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
    <title>Tous les Événements - RetroGame Hub</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
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
            min-height: 100vh;
        }

        /* Particules d'arrière-plan */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background-color: var(--primary-green);
            opacity: 0.3;
            animation: float 20s infinite;
            box-shadow: 0 0 10px var(--primary-green);
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 15s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; animation-duration: 18s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; animation-duration: 20s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 1s; animation-duration: 17s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 3s; animation-duration: 16s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; animation-duration: 19s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 2.5s; animation-duration: 21s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 4.5s; animation-duration: 15.5s; }
        .particle:nth-child(9) { left: 90%; animation-delay: 1.5s; animation-duration: 18.5s; }
        .particle:nth-child(10) { left: 95%; animation-delay: 3.5s; animation-duration: 17.5s; }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 0.3;
            }
            90% {
                opacity: 0.3;
            }
            50% {
                transform: translateY(-100vh) translateX(50px);
            }
        }

        /* Container principal */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Section Événements */
        .events-container {
            margin-top: 120px;
            padding: 2rem 0;
            min-height: 70vh;
        }

        .page-title {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2.5rem;
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

        /* Grille des événements */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        /* Carte d'événement */
        .event-card {
            background-color: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 0;
            transition: all 0.4s;
            backdrop-filter: blur(10px);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .event-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .event-card:hover::before {
            opacity: 1;
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 255, 65, 0.4);
            border-color: var(--primary-green);
        }

        .event-header {
            padding: 1.5rem 1.5rem 0;
        }

        .event-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            font-size: 0.6rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .featured-event .event-badge {
            background: linear-gradient(135deg, var(--secondary-purple), var(--accent-pink));
        }

        .event-game {
            color: var(--secondary-purple);
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            font-weight: normal;
            text-shadow: 0 0 10px var(--secondary-purple);
        }

        .event-content {
            padding: 0 1.5rem 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .event-name {
            color: var(--primary-green);
            margin-bottom: 1rem;
            font-size: 1.2rem;
            font-weight: normal;
            text-shadow: 0 0 10px var(--primary-green);
        }

        .event-description {
            color: var(--text-gray);
            margin-bottom: 1.5rem;
            line-height: 1.5;
            flex-grow: 1;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .event-details {
            margin: 1.5rem 0;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 0;
            border-left: 3px solid var(--primary-green);
            border-right: 1px solid var(--border-color);
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .event-details p {
            margin-bottom: 0.5rem;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.6rem;
        }

        .event-details strong {
            color: var(--primary-green);
            min-width: 80px;
            display: inline-block;
            text-shadow: 0 0 5px var(--primary-green);
        }

        .places-info {
            color: var(--primary-green);
            font-weight: normal;
            font-size: 0.7rem;
            text-shadow: 0 0 10px var(--primary-green);
        }

        .event-full {
            color: var(--accent-pink);
            font-weight: normal;
            text-shadow: 0 0 10px var(--accent-pink);
        }

        .event-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .event-tag {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: rgba(0, 255, 65, 0.1);
            border: 1px solid var(--primary-green);
            color: var(--primary-green);
            font-size: 0.5rem;
            border-radius: 0;
        }

        .card-actions {
            margin-top: auto;
            text-align: center;
        }

        /* Boutons */
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            border: none;
            border-radius: 0;
            text-decoration: none;
            font-weight: normal;
            transition: all 0.3s;
            cursor: pointer;
            font-size: 0.7rem;
            text-align: center;
            font-family: 'Press Start 2P', cursive;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
            width: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #00cc33, var(--primary-green));
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.8);
        }

        .btn-disabled {
            background: var(--accent-pink);
            color: var(--text-white);
            cursor: not-allowed;
            opacity: 0.7;
            box-shadow: 0 0 15px rgba(255, 0, 110, 0.5);
            width: 100%;
        }

        .btn-disabled:hover {
            transform: none;
            box-shadow: 0 0 15px rgba(255, 0, 110, 0.5);
        }

        /* Aucun événement */
        .no-events {
            text-align: center;
            padding: 3rem;
            color: var(--text-gray);
            background-color: var(--card-bg);
            border: 2px solid var(--border-color);
        }

        .no-events h3 {
            color: var(--accent-pink);
            margin-bottom: 1rem;
            text-shadow: 0 0 10px var(--accent-pink);
        }

        .no-events p {
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .btn-event-home {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, var(--secondary-purple), var(--accent-pink));
            color: var(--text-white);
            text-decoration: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
            transition: all 0.3s;
        }

        .btn-event-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.5);
        }

        /* Effet de scanline */
        .scanline {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                0deg,
                rgba(0, 0, 0, 0.15),
                rgba(0, 0, 0, 0.15) 1px,
                transparent 1px,
                transparent 2px
            );
            pointer-events: none;
            animation: scan 8s linear infinite;
            z-index: 2;
        }

        @keyframes scan {
            0% { transform: translateY(0); }
            100% { transform: translateY(10px); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .event-details p {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.2rem;
            }

            .events-container {
                margin-top: 140px;
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.3rem;
            }

            .event-card {
                padding: 1rem;
            }

            .event-name {
                font-size: 1rem;
            }

            .event-description {
                font-size: 1rem;
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

<!-- Effet de scanline -->
<div class="scanline"></div>

<!-- Navigation (Header du premier code) -->
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
                <li><a href="../../index.php">ACCUEIL</a></li>
                <li><a href="evenements.php" class="active">ÉVÉNEMENTS</a></li>
                <li><a href="../../index.php#timeline">HISTOIRE</a></li>
                <li><a href="../../index.php#newsletter">NEWSLETTER</a></li>
                <li><a href="../../index.php#contact">CONTACT</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <a href="../../admin/index.php" class="btn-admin">
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

<div class="container events-container">
    <h1 class="page-title">TOUS LES ÉVÉNEMENTS</h1>
    
    <?php if (empty($evenements)): ?>
        <div class="event-card no-events">
            <div class="event-content">
                <h3 class="event-name">⚠ AUCUN ÉVÉNEMENT DISPONIBLE</h3>
                <p class="event-description">Il n'y a pas d'événements à afficher pour le moment.</p>
                <p class="event-description">Revenez plus tard pour découvrir de nouveaux événements !</p>
                <a href="../../index.php" class="btn-event-home">RETOUR À L'ACCUEIL</a>
            </div>
        </div>
    <?php else: ?>
        <div class="events-grid">
            <?php foreach($evenements as $evenement): 
                $participationsCount = $evenementController->show($evenement['id_evenement'])->countParticipations();
                $placesRestantes = $evenement['places_max'] - $participationsCount;
                $isFull = $placesRestantes <= 0;
                $isPremium = $evenement['prix'] > 0;
            ?>
            <div class="event-card <?php echo $isPremium ? 'featured-event' : ''; ?>">
                <div class="event-header">
                    <div class="event-badge">
                        <?php echo $isPremium ? 'PREMIUM' : 'GRATUIT'; ?>
                    </div>
                </div>
                <div class="event-content">
                    <div class="event-game">🎮 <?php echo htmlspecialchars($evenement['jeu']); ?></div>
                    <h3 class="event-name"><?php echo htmlspecialchars($evenement['nom']); ?></h3>
                    <p class="event-description"><?php echo htmlspecialchars($evenement['description']); ?></p>
                    
                    <div class="event-details">
                        <p><strong>📅 DATE:</strong> <?php echo date('d/m/Y H:i', strtotime($evenement['date_debut'])); ?></p>
                        <p><strong>🏁 FIN:</strong> <?php echo date('d/m/Y H:i', strtotime($evenement['date_fin'])); ?></p>
                        <p><strong>📍 LIEU:</strong> <?php echo htmlspecialchars($evenement['lieu']); ?></p>
                        <p><strong>💰 PRIX:</strong> <?php echo number_format($evenement['prix'], 2); ?>€</p>
                        <p class="<?php echo $isFull ? 'event-full' : 'places-info'; ?>">
                            <strong>👥 PLACES:</strong> 
                            <?php echo $placesRestantes; ?> / <?php echo $evenement['places_max']; ?> RESTANTES
                        </p>
                    </div>

                    <div class="event-tags">
                        <span class="event-tag">🎮 <?php echo htmlspecialchars($evenement['jeu']); ?></span>
                        <span class="event-tag">📍 <?php echo htmlspecialchars($evenement['lieu']); ?></span>
                        <span class="event-tag">💰 <?php echo $evenement['prix']; ?>€</span>
                    </div>

                    <div class="card-actions">
                        <?php if(!$isFull): ?>
                            <a href="participer.php?id=<?php echo $evenement['id_evenement']; ?>" class="btn btn-primary">
                                🎯 S'INSCRIRE
                            </a>
                        <?php else: ?>
                            <button class="btn btn-disabled" disabled>🚫 ÉVÉNEMENT COMPLET</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Footer (du premier code) -->
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
                    <li><a href="../../index.php">Accueil</a></li>
                    <li><a href="evenements.php">Événements</a></li>
                    <li><a href="../../index.php#features">À propos</a></li>
                    <li><a href="../../index.php#contact">Contact</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4 class="footer-title">ÉVÉNEMENTS</h4>
                <ul class="footer-links">
                    <li><a href="evenements.php?type=tournoi">Tournois</a></li>
                    <li><a href="evenements.php?type=lan">LAN Parties</a></li>
                    <li><a href="evenements.php?type=workshop">Workshops</a></li>
                    <li><a href="evenements.php?type=expo">Expositions</a></li>
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

    // Animation des cartes au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
            }
        });
    }, observerOptions);

    // Observer toutes les cartes d'événements
    document.querySelectorAll('.event-card').forEach(card => {
        observer.observe(card);
    });
</script>
</body>
</html>