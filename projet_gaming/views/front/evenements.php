<?php
session_start();
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/Evenement.php';
include_once __DIR__ . '/../../controllers/EvenementController.php';

$database = new Database();
$db = $database->getConnection();

$evenementController = new EvenementController($db);

// Récupérer tous les événements
$allEvenements = $evenementController->index();

// Pagination - nombre d'événements par page
$perPage = 2;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalEvents = count($allEvenements);
$totalPages = ceil($totalEvents / $perPage);

// Limiter les événements affichés
$start = ($currentPage - 1) * $perPage;
$end = $start + $perPage;
$evenements = array_slice($allEvenements, $start, $perPage);
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

        .event-organizer {
            color: var(--accent-pink);
            margin-bottom: 0.5rem;
            font-size: 0.7rem;
            text-shadow: 0 0 5px var(--accent-pink);
            font-weight: normal;
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

        /* Styles pour la pagination */
        .pagination-container {
            text-align: center;
            margin-top: 3rem;
            padding: 2rem 0;
        }

        .btn-load-more {
            background: linear-gradient(135deg, var(--secondary-purple), var(--accent-pink));
            color: var(--text-white);
            border: none;
            padding: 1.2rem 3rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Press Start 2P', cursive;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.5);
            margin-bottom: 1rem;
        }

        .btn-load-more:hover:not(:disabled) {
            transform: translateY(-5px);
            box-shadow: 0 0 30px rgba(189, 0, 255, 0.8);
            background: linear-gradient(135deg, var(--accent-pink), var(--secondary-purple));
        }

        .btn-load-more:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 0 15px rgba(255, 0, 110, 0.3);
        }

        .btn-load-more::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                transparent 20%,
                rgba(255, 255, 255, 0.2) 50%,
                transparent 80%
            );
            transform: rotate(30deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(30deg); }
            100% { transform: translateX(100%) rotate(30deg); }
        }

        .pagination-info {
            color: var(--text-gray);
            font-size: 0.7rem;
            margin-top: 1rem;
            font-family: 'VT323', monospace;
        }

        .current-page-info {
            color: var(--primary-green);
            font-size: 0.8rem;
            margin: 0.5rem 0;
            text-shadow: 0 0 10px var(--primary-green);
        }

        /* Animation pour les nouveaux événements */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .new-event {
            animation: slideIn 0.5s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 0.8rem 0;
            }

            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }

            .nav-menu a {
                font-size: 0.6rem;
                padding: 0.4rem 0.8rem;
            }

            .nav-right {
                gap: 0.5rem;
            }

            .btn-auth,
            .btn-admin {
                padding: 0.5rem 1rem;
                font-size: 0.5rem;
            }

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
                margin-top: 180px;
                padding: 1rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-logo {
                justify-content: center;
            }

            .social-links {
                justify-content: center;
            }

            .info-item {
                justify-content: center;
                text-align: center;
            }

            .btn-load-more {
                padding: 1rem 2rem;
                font-size: 0.7rem;
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

            .btn-load-more {
                width: 100%;
                padding: 1rem;
                font-size: 0.6rem;
            }

            .footer-bottom-content {
                flex-direction: column;
                gap: 0.8rem;
            }

            .footer-bottom-links {
                flex-direction: column;
                gap: 0.5rem;
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

<!-- Navigation Header (DU PREMIER CODE) -->
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
                <li><a href="../../index.php">ACCUEIL</a></li>
                <li><a href="evenements.php" class="active">ÉVÉNEMENTS</a></li>
                <li><a href="../../index.php#timeline"></a></li>
                <li><a href="../../index.php#newsletter"></a></li>
                <li><a href="../../index.php#contact"></a></li>
            </ul>
        </div>
        <div class="nav-right">
            <!-- MODIFIER : Boutons de connexion/déconnexion -->
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- Si l'utilisateur est connecté -->
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <!-- Seulement pour les admins -->
                    <a href="../../admin/index.php" class="btn-admin">
                        <span class="btn-icon">⚙️</span>
                        ADMIN
                    </a>
                <?php endif; ?>
                <a href="../../logout.php" class="btn-auth">
                    <span class="btn-icon">⚙️</span>
                    DÉCONNEXION
                </a>
            <?php else: ?>
                <!-- Si l'utilisateur n'est pas connecté -->
                <a href="../../login.php" class="btn-auth">
                    <span class="btn-icon">👤</span>
                    CONNEXION
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container events-container">
    <h1 class="page-title">TOUS LES ÉVÉNEMENTS</h1>
    
    <?php if (empty($allEvenements)): ?>
        <div class="event-card no-events">
            <div class="event-content">
                <h3 class="event-name">⚠ AUCUN ÉVÉNEMENT DISPONIBLE</h3>
                <p class="event-description">Il n'y a pas d'événements à afficher pour le moment.</p>
                <p class="event-description">Revenez plus tard pour découvrir de nouveaux événements !</p>
                <a href="../../index.php" class="btn-event-home">RETOUR À L'ACCUEIL</a>
            </div>
        </div>
    <?php else: ?>
        <div class="events-grid" id="events-grid">
            <?php foreach($evenements as $index => $evenement): 
                $evenementObj = $evenementController->show($evenement['id_evenement']);
                if ($evenementObj) {
                    $participationsCount = $evenementObj->countParticipations();
                    $placesRestantes = $evenement['places_max'] - $participationsCount;
                    $isFull = $placesRestantes <= 0;
                    $isPremium = $evenement['prix'] > 0;
                } else {
                    $participationsCount = 0;
                    $placesRestantes = $evenement['places_max'];
                    $isFull = false;
                    $isPremium = $evenement['prix'] > 0;
                }
            ?>
            <div class="event-card <?php echo $isPremium ? 'featured-event' : ''; ?>" 
                 data-event-id="<?php echo $evenement['id_evenement']; ?>">
                <div class="event-header">
                    <div class="event-badge">
                        <?php echo $isPremium ? 'PREMIUM' : 'GRATUIT'; ?>
                    </div>
                </div>
                <div class="event-content">
                    <div class="event-game">🎮 <?php echo htmlspecialchars($evenement['jeu']); ?></div>
                    <div class="event-organizer">👤 Organisé par: <?php echo htmlspecialchars($evenement['organisateur_nom'] ?? 'Admin'); ?></div>
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

        <!-- Section de pagination -->
        <div class="pagination-container">
            <?php if ($totalEvents > $perPage): ?>
                <div class="current-page-info">
                    Affichage des événements <?php echo $start + 1; ?> à <?php echo min($end, $totalEvents); ?> sur <?php echo $totalEvents; ?>
                </div>
                
                <button id="load-more-btn" class="btn-load-more" 
                        data-current-page="<?php echo $currentPage; ?>"
                        data-total-pages="<?php echo $totalPages; ?>">
                    <?php if ($currentPage < $totalPages): ?>
                        📥 CHARGER PLUS D'ÉVÉNEMENTS (<?php echo $totalPages - $currentPage; ?> page<?php echo ($totalPages - $currentPage) > 1 ? 's' : ''; ?> restante<?php echo ($totalPages - $currentPage) > 1 ? 's' : ''; ?>)
                    <?php else: ?>
                        🎉 TOUS LES ÉVÉNEMENTS SONT AFFICHÉS
                    <?php endif; ?>
                </button>
                
                <div class="pagination-info">
                    Page <?php echo $currentPage; ?> sur <?php echo $totalPages; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Footer (DU PREMIER CODE) -->
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
                    <li><a href="../../index.php">► Accueil</a></li>
                    <li><a href="games.php">► Collection de Jeux</a></li>
                    <li><a href="blog.php">► Blog & Actualites</a></li>
                    <li><a href="evenements.php">► Evenements</a></li>
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

    // AJOUTER CE SCRIPT POUR LE CHARGEMENT DYNAMIQUE
    document.addEventListener('DOMContentLoaded', function() {
        const loadMoreBtn = document.getElementById('load-more-btn');
        const eventsGrid = document.getElementById('events-grid');
        const totalEvents = <?php echo $totalEvents; ?>;
        const perPage = <?php echo $perPage; ?>;
        
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', loadMoreEvents);
        }

        function loadMoreEvents() {
            const currentPage = parseInt(loadMoreBtn.dataset.currentPage);
            const totalPages = parseInt(loadMoreBtn.dataset.totalPages);
            const nextPage = currentPage + 1;

            if (nextPage > totalPages) {
                loadMoreBtn.disabled = true;
                loadMoreBtn.innerHTML = '🎉 TOUS LES ÉVÉNEMENTS SONT AFFICHÉS';
                return;
            }

            // Afficher un indicateur de chargement
            const originalText = loadMoreBtn.innerHTML;
            loadMoreBtn.innerHTML = '⌛ CHARGEMENT...';
            loadMoreBtn.disabled = true;

            // Faire une requête AJAX pour charger la page suivante
            fetch(`evenements.php?page=${nextPage}&ajax=1`)
                .then(response => response.text())
                .then(html => {
                    // Extraire les événements du HTML reçu
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newEvents = doc.querySelectorAll('#events-grid .event-card');

                    // Ajouter chaque nouvel événement avec une animation
                    newEvents.forEach((event, index) => {
                        const newEvent = event.cloneNode(true);
                        newEvent.classList.add('new-event');
                        newEvent.style.animationDelay = `${index * 0.1}s`;
                        eventsGrid.appendChild(newEvent);
                        
                        // Observer la nouvelle carte
                        observer.observe(newEvent);
                    });

                    // Mettre à jour le bouton
                    loadMoreBtn.dataset.currentPage = nextPage;
                    
                    if (nextPage >= totalPages) {
                        loadMoreBtn.innerHTML = '🎉 TOUS LES ÉVÉNEMENTS SONT AFFICHÉS';
                        loadMoreBtn.disabled = true;
                    } else {
                        loadMoreBtn.innerHTML = `📥 CHARGER PLUS D'ÉVÉNEMENTS (${totalPages - nextPage} page${(totalPages - nextPage) > 1 ? 's' : ''} restante${(totalPages - nextPage) > 1 ? 's' : ''})`;
                        loadMoreBtn.disabled = false;
                    }

                    // Mettre à jour l'affichage du nombre d'événements
                    const currentDisplay = Math.min(nextPage * perPage, totalEvents);
                    const pageInfo = document.querySelector('.current-page-info');
                    if (pageInfo) {
                        pageInfo.textContent = `Affichage des événements 1 à ${currentDisplay} sur ${totalEvents}`;
                    }

                    // Faire défiler jusqu'aux nouveaux événements
                    const lastEvent = eventsGrid.lastElementChild;
                    if (lastEvent) {
                        lastEvent.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    loadMoreBtn.innerHTML = originalText;
                    loadMoreBtn.disabled = false;
                    alert('Une erreur est survenue lors du chargement des événements.');
                });
        }

        // Version alternative sans AJAX (rechargement de page)
        function loadMoreEventsNoAjax() {
            const currentPage = parseInt(loadMoreBtn.dataset.currentPage);
            const nextPage = currentPage + 1;
            
            if (nextPage <= totalPages) {
                window.location.href = `evenements.php?page=${nextPage}`;
            }
        }
    });
</script>
</body>
</html>