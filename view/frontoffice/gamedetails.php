<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/JeuxController.php';
require_once '../../controller/CommandeController.php';

// Initialize controllers
$userController = new UserController();
$gamesC = new JeuxController();

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
}

// Vérifier si l'ID du jeu est présent
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: games.php');
    exit();
}

$gameId = intval($_GET['id']);

// Récupérer les détails du jeu
$game = $gamesC->getGameById($gameId);

if (!$game) {
    header('Location: games.php?error=game_not_found');
    exit();
}
$screenshots = $gamesC->getGameScreenshots($gameId);

// Get display names for rarity and condition
$rarityDisplay = $gamesC->getRarityDisplay($game['rarity'] ?? 'common');
$conditionDisplay = $gamesC->getConditionDisplay($game['condition'] ?? 'very_good');

// Helper function to extract YouTube ID
function extractYoutubeId($url) {
    if (empty($url)) return '';
    
    $patterns = [
        '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
        '/youtube\.com\/embed\/([^"&?\/\s]{11})/',
        '/youtube\.com\/v\/([^"&?\/\s]{11})/',
        '/youtu\.be\/([^"&?\/\s]{11})/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    
    return '';
}

$youtubeId = extractYoutubeId($game['trailer_url'] ?? '');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game['nom']); ?> - LUDOLOGY VAULT</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #00ff41;
            --secondary-purple: #bd00ff;
            --accent-pink: #ff006e;
            --warning-orange: #ff9e00;
            --danger-red: #ff003c;
            --darker-bg: #0a0a0f;
            --card-bg: #1a1a2e;
            --border-color: #2a2a3e;
            --text-white: #ffffff;
            --text-light-gray: #cccccc;
            --text-gray: #888888;
        }

        /* User menu styles (from games.php) */
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
        
        /* Main content */
        body {
            background-color: var(--darker-bg);
            color: var(--text-white);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .main-content {
            max-width: 1200px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        /* Breadcrumb */
        .breadcrumb {
            margin-bottom: 2rem;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .breadcrumb a {
            color: var(--primary-green);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb .current {
            color: var(--text-light-gray);
        }

        /* Game Details Container */
        .game-details-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        @media (max-width: 900px) {
            .game-details-container {
                grid-template-columns: 1fr;
            }
        }

        /* Game Media Section */
        .game-media-section {
            position: relative;
        }

        .game-cover-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid var(--primary-green);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.3);
            margin-bottom: 1.5rem;
        }

        .game-cover-card img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }

        .game-cover-card:hover img {
            transform: scale(1.02);
        }

        .game-quick-info {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .game-tag {
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
            text-transform: uppercase;
            font-weight: bold;
        }

        .tag-category {
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
        }

        .tag-stock {
            background: rgba(255, 158, 0, 0.1);
            border: 2px solid var(--warning-orange);
            color: var(--warning-orange);
        }

        .tag-price {
            background: rgba(189, 0, 255, 0.1);
            border: 2px solid var(--secondary-purple);
            color: var(--secondary-purple);
        }

        /* Game Info Section */
        .game-info-section {
            background: rgba(26, 26, 46, 0.8);
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
        }

        .game-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 1.8rem;
            color: var(--primary-green);
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .game-id {
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
            color: var(--text-gray);
            margin-bottom: 2rem;
        }

        .game-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .game-meta-grid {
                grid-template-columns: 1fr;
            }
        }

        .meta-card {
            background: rgba(0, 255, 65, 0.05);
            border: 1px solid rgba(0, 255, 65, 0.2);
            border-radius: 8px;
            padding: 1rem;
        }

        .meta-label {
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
            color: var(--text-gray);
            margin-bottom: 0.3rem;
        }

        .meta-value {
            font-size: 1.1rem;
            color: var(--text-light-gray);
            font-weight: bold;
        }

        .price-value {
            color: var(--secondary-purple);
            font-size: 1.5rem;
            font-weight: bold;
        }

        .stock-value {
            color: var(--warning-orange);
            font-weight: bold;
        }

        /* Game Actions */
        .game-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            color: var(--darker-bg);
            font-weight: bold;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 65, 0.4);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
        }

        .btn-secondary:hover {
            background: rgba(0, 255, 65, 0.1);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-light-gray);
        }

        .btn-outline:hover {
            border-color: var(--primary-green);
            color: var(--primary-green);
        }

        .btn-disabled {
            background: rgba(255, 0, 60, 0.1);
            border: 2px solid var(--danger-red);
            color: var(--danger-red);
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Game Description */
        .game-description-section {
            grid-column: 1 / -1;
            background: rgba(26, 26, 46, 0.8);
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            margin-top: 2rem;
        }

        .section-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 1rem;
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title::before {
            content: '▶';
            color: var(--secondary-purple);
        }

        .game-description {
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            line-height: 1.8;
            color: var(--text-light-gray);
        }

        /* YouTube Trailer Section */
        .trailer-section {
            margin: 3rem 0;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05), rgba(189, 0, 255, 0.05));
            border-radius: 12px;
            border: 2px solid var(--primary-green);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.1);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .video-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-radius: 8px;
            border: 3px solid var(--secondary-purple);
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.3);
            background: #000;
            transition: all 0.3s ease;
        }

        .video-container:hover {
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.4);
            transform: translateY(-2px);
        }

        .trailer-info {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'VT323', monospace;
            font-size: 1rem;
            color: var(--text-light-gray);
        }

        .trailer-link {
            color: var(--secondary-purple);
            text-decoration: none;
            border: 1px solid var(--secondary-purple);
            padding: 0.3rem 1rem;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .trailer-link:hover {
            background: rgba(189, 0, 255, 0.1);
            transform: translateY(-1px);
        }

        .trailer-error {
            margin: 2rem 0;
            padding: 1rem;
            background: rgba(255, 0, 60, 0.1);
            border: 2px solid var(--danger-red);
            border-radius: 8px;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            color: var(--danger-red);
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
            border: 2px solid transparent;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: rgba(0, 255, 65, 0.1);
            border-color: var(--primary-green);
            color: var(--primary-green);
        }

        .alert-error {
            background: rgba(255, 0, 60, 0.1);
            border-color: var(--danger-red);
            color: var(--danger-red);
        }

        .alert-info {
            background: rgba(255, 158, 0, 0.1);
            border-color: var(--warning-orange);
            color: var(--warning-orange);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin: 100px auto 30px;
                padding: 0 15px;
            }
            
            .trailer-section {
                padding: 1rem;
                margin: 2rem 0;
            }
            
            .game-title {
                font-size: 1.4rem;
            }
            
            .game-actions {
                flex-direction: column;
            }
            
            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Background particles -->
    <div class="particles">
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Navigation Bar (Same as games.php) -->
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
                    <li><a href="index.php">HOME</a></li>
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
                <a href="cart.php" style="text-decoration: none; margin-left: 1rem;">
                    <button class="btn-auth">
                        <span class="btn-icon">🛒</span> 
                    </button>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php">Accueil</a> › 
            <a href="games.php">Jeux</a> › 
            <span class="current"><?php echo htmlspecialchars($game['nom']); ?></span>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                $messages = [
                    'cart_added' => 'Jeu ajouté au panier avec succès!',
                    'cart_updated' => 'Panier mis à jour avec succès!'
                ];
                echo htmlspecialchars($messages[$_GET['success']] ?? 'Opération réussie!');
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php 
                $messages = [
                    'game_not_found' => 'Jeu non trouvé.',
                    'no_stock' => 'Ce jeu est en rupture de stock.',
                    'cart_error' => 'Erreur lors de l\'ajout au panier.'
                ];
                echo htmlspecialchars($messages[$_GET['error']] ?? 'Une erreur est survenue.');
                ?>
            </div>
        <?php endif; ?>

        <!-- Game Details Container -->
        <div class="game-details-container">
            <!-- Left Column: Game Media -->
            <div class="game-media-section">
                <!-- Game Cover Image -->
                <div class="game-cover-card">
                    <?php if (!empty($game['image']) && file_exists('../../uploads/' . $game['image'])): ?>
                        <img src="../../uploads/<?php echo htmlspecialchars($game['image']); ?>" 
                             alt="<?php echo htmlspecialchars($game['nom']); ?>"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" width=\"400\" height=\"400\" viewBox=\"0 0 400 400\"%3E%3Cdefs%3E%3ClinearGradient id=\"grad\" x1=\"0%25\" y1=\"0%25\" x2=\"100%25\" y2=\"100%25\"%3E%3Cstop offset=\"0%25\" stop-color=\"%2300ff41\"/%3E%3Cstop offset=\"100%25\" stop-color=\"%23bd00ff\"/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width=\"400\" height=\"400\" fill=\"url(%23grad)\"/%3E%3Ctext x=\"50%25\" y=\"50%25\" font-family=\"Courier New\" font-size=\"24\" fill=\"white\" text-anchor=\"middle\" dominant-baseline=\"middle\"%3E%3Ctspan x=\"50%25\" dy=\"-20\"%3E<?php echo urlencode(substr($game['nom'], 0, 20)); ?>%3C/tspan%3E%3Ctspan x=\"50%25\" dy=\"30\"%3ELUDOLOGY VAULT%3C/tspan%3E%3C/text%3E%3C/svg%3E'">
                    <?php else: ?>
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='400' viewBox='0 0 400 400'%3E%3Cdefs%3E%3ClinearGradient id='grad' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%2300ff41'/%3E%3Cstop offset='100%25' stop-color='%23bd00ff'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='400' height='400' fill='url(%23grad)'/%3E%3Ctext x='50%25' y='50%25' font-family='Courier New' font-size='24' fill='white' text-anchor='middle' dominant-baseline='middle'%3E%3Ctspan x='50%25' dy='-20'%3E<?php echo urlencode(substr($game['nom'], 0, 20)); ?>%3C/tspan%3E%3Ctspan x='50%25' dy='30'%3ELUDOLOGY VAULT%3C/tspan%3E%3C/text%3E%3C/svg%3E" 
                             alt="<?php echo htmlspecialchars($game['nom']); ?>">
                    <?php endif; ?>
                </div>

                <!-- Quick Info Tags -->
                <div class="game-quick-info">
                    <span class="game-tag tag-category">
                        <?php echo htmlspecialchars($game['categorie']); ?>
                    </span>
                    <span class="game-tag tag-stock">
                        Stock: <?php echo htmlspecialchars($game['stock']); ?>
                    </span>
                    <span class="game-tag tag-price">
                        <?php echo number_format($game['prix'], 2); ?> €
                    </span>
                </div>
            </div>

            <!-- Right Column: Game Information -->
            <div class="game-info-section">
                <h1 class="game-title"><?php echo htmlspecialchars($game['nom']); ?></h1>
                <div class="game-id">
                    Référence: LV-<?php echo str_pad($game['id'], 6, '0', STR_PAD_LEFT); ?>
                </div>

                <!-- Game Meta Information -->
                <div class="game-meta-grid">
                    <div class="meta-card">
                        <div class="meta-label">Catégorie</div>
                        <div class="meta-value"><?php echo htmlspecialchars($game['categorie']); ?></div>
                    </div>

                    <div class="meta-card">
                        <div class="meta-label">Prix</div>
                        <div class="meta-value price-value"><?php echo number_format($game['prix'], 2); ?> €</div>
                    </div>

                    <div class="meta-card">
                        <div class="meta-label">Disponibilité</div>
                        <div class="meta-value stock-value">
                            <?php if ($game['stock'] > 0): ?>
                                <span style="color: var(--primary-green);">● En stock</span>
                            <?php else: ?>
                                <span style="color: var(--danger-red);">● Rupture de stock</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="meta-card">
                        <div class="meta-label">Quantité en stock</div>
                        <div class="meta-value"><?php echo htmlspecialchars($game['stock']); ?> unités</div>
                    </div>
                </div>

                <!-- Game Actions -->
                <div class="game-actions">
                    <?php if ($game['stock'] > 0): ?>
                        <?php if ($isLoggedIn): ?>
                            <form action="cart.php" method="POST" style="display: inline;">
                                <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
                                <input type="hidden" name="action" value="add_to_cart">
                                <button type="submit" class="action-btn btn-primary">
                                    <span>🛒</span> AJOUTER AU PANIER
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="login.php" class="action-btn btn-primary" style="text-decoration: none;">
                                <span>🔒</span> CONNECTEZ-VOUS POUR ACHETER
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="action-btn btn-disabled" disabled>
                            <span>⛔</span> RUPTURE DE STOCK
                        </button>
                    <?php endif; ?>

                    <a href="games.php" class="action-btn btn-secondary">
                        <span>◀</span> RETOUR AUX JEUX
                    </a>

                    <button id="export-pdf" class="action-btn btn-outline">
                        <span>📄</span> EXPORTER EN PDF
                    </button>
                </div>
            </div>

            <!-- Game Description (Full width) -->
            <div class="game-description-section">
                <h2 class="section-title">SYNOPSIS ET DÉTAILS</h2>
                <div class="game-description">
                    <?php echo nl2br(htmlspecialchars($game['description'])); ?>
                </div>
            </div>
        </div>

        <!-- YouTube Trailer Section -->
        <?php if (!empty($game['trailer_url']) && $youtubeId): ?>
        <section class="trailer-section">
            <h2 class="section-title">
                <span style="font-size: 1.2rem;">🎬</span> BANDE-ANNONCE OFFICIELLE
            </h2>
            
            <div class="video-container">
                <iframe 
                    src="https://www.youtube.com/embed/<?= htmlspecialchars($youtubeId) ?>?rel=0&modestbranding=1&autoplay=0&controls=1&showinfo=0"
                    style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        border: none;
                    "
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    allowfullscreen
                    title="<?= htmlspecialchars($game['nom']) ?> - Bande-annonce officielle">
                </iframe>
            </div>
            
            <div class="trailer-info">
                <div>
                    <span style="color: var(--primary-green);">▶</span>
                    Vidéo YouTube
                </div>
                <a href="<?= htmlspecialchars($game['trailer_url']) ?>" 
                   target="_blank" 
                   class="trailer-link">
                    Ouvrir sur YouTube
                </a>
            </div>
        </section>
        <?php elseif (!empty($game['trailer_url']) && !$youtubeId): ?>
        <!-- Invalid URL Warning -->
        <div class="trailer-error">
            ⚠️ L'URL de la bande-annonce YouTube semble invalide.
        </div>
        <?php endif; ?>
        <!-- Rarity & Collection Value Section -->
<section class="rarity-section" style="
    margin: 3rem 0;
    padding: 2rem;
    background: linear-gradient(135deg, rgba(189, 0, 255, 0.05), rgba(255, 158, 0, 0.05));
    border-radius: 12px;
    border: 2px solid var(--secondary-purple);
    animation: slideUp 0.5s ease-out 0.2s both;
">
    <h2 class="section-title" style="color: var(--secondary-purple);">
        <span style="font-size: 1.2rem;">🏆</span> VALEUR DE COLLECTION
    </h2>
    
    <div class="rarity-grid" style="
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    ">
        <!-- Rarity Card -->
        <div class="rarity-card" style="
            background: rgba(189, 0, 255, 0.1);
            border: 2px solid var(--secondary-purple);
            border-radius: 8px;
            padding: 1.5rem;
        ">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <div class="rarity-badge" style="
                    padding: 0.5rem 1rem;
                    background: <?php 
                        $rarityColors = [
                            'common' => 'rgba(0, 255, 65, 0.2)',
                            'rare' => 'rgba(255, 158, 0, 0.2)',
                            'very_rare' => 'rgba(255, 0, 110, 0.2)',
                            'collector' => 'rgba(189, 0, 255, 0.2)',
                            'museum_piece' => 'rgba(0, 150, 255, 0.2)'
                        ];
                        echo $rarityColors[$game['rarity'] ?? 'common'];
                    ?>;
                    border: 2px solid <?php 
                        $rarityBorderColors = [
                            'common' => 'var(--primary-green)',
                            'rare' => 'var(--warning-orange)',
                            'very_rare' => 'var(--accent-pink)',
                            'collector' => 'var(--secondary-purple)',
                            'museum_piece' => '#0096ff'
                        ];
                        echo $rarityBorderColors[$game['rarity'] ?? 'common'];
                    ?>;
                    color: <?php echo $rarityBorderColors[$game['rarity'] ?? 'common']; ?>;
                    border-radius: 20px;
                    font-family: 'Press Start 2P', cursive;
                    font-size: 0.5rem;
                ">
                    <?php echo htmlspecialchars($rarityDisplay); ?>
                </div>
            </div>
            <div class="rarity-info" style="font-family: 'VT323', monospace; font-size: 1.1rem; color: var(--text-light-gray);">
                <p>Ce jeu est considéré comme <strong style="color: var(--secondary-purple);"><?php echo strtolower($rarityDisplay); ?></strong> dans la communauté des collectionneurs.</p>
            </div>
        </div>

        <!-- Condition Card -->
        <div class="condition-card" style="
            background: rgba(255, 158, 0, 0.1);
            border: 2px solid var(--warning-orange);
            border-radius: 8px;
            padding: 1.5rem;
        ">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <div style="font-size: 1.5rem;">📦</div>
                <h4 style="margin: 0; color: var(--warning-orange); font-family: 'Press Start 2P', cursive; font-size: 0.7rem;">ÉTAT DE LA COPIE</h4>
            </div>
            <div class="condition-info" style="font-family: 'VT323', monospace; font-size: 1.1rem;">
                <p><strong style="color: var(--warning-orange);">État:</strong> <?php echo htmlspecialchars($conditionDisplay); ?></p>
                <?php if (!empty($game['edition'])): ?>
                <p><strong style="color: var(--warning-orange);">Édition:</strong> <?php echo htmlspecialchars($game['edition']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Value Card -->
        <?php if (!empty($game['estimated_value'])): ?>
        <div class="value-card" style="
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--primary-green);
            border-radius: 8px;
            padding: 1.5rem;
        ">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <div style="font-size: 1.5rem;">💰</div>
                <h4 style="margin: 0; color: var(--primary-green); font-family: 'Press Start 2P', cursive; font-size: 0.7rem;">VALEUR ESTIMÉE</h4>
            </div>
            <div class="value-info" style="font-family: 'VT323', monospace; font-size: 1.1rem;">
                <div style="font-size: 1.8rem; color: var(--primary-green); font-weight: bold; margin-bottom: 0.5rem;">
                    <?php echo number_format($game['estimated_value'], 2); ?> €
                </div>
                <p style="color: var(--text-light-gray); font-size: 1rem;">
                    Valeur estimée sur le marché des collectionneurs
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($game['collector_notes'])): ?>
    <div class="collector-notes" style="
        margin-top: 2rem;
        padding: 1.5rem;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        border-left: 4px solid var(--secondary-purple);
    ">
        <h4 style="color: var(--secondary-purple); font-family: 'Press Start 2P', cursive; font-size: 0.7rem; margin-bottom: 1rem;">
            📝 NOTES DU COLLECTIONNEUR
        </h4>
        <p style="font-family: 'VT323', monospace; font-size: 1.2rem; color: var(--text-light-gray); line-height: 1.6;">
            <?php echo nl2br(htmlspecialchars($game['collector_notes'])); ?>
        </p>
    </div>
    <?php endif; ?>
</section>
<!-- Virtual Tour & Screenshots Gallery -->
<?php if (!empty($screenshots)): ?>
<section class="screenshots-section" style="
    margin: 3rem 0;
    padding: 2rem;
    background: linear-gradient(135deg, rgba(0, 150, 255, 0.05), rgba(0, 255, 65, 0.05));
    border-radius: 12px;
    border: 2px solid #0096ff;
    animation: slideUp 0.5s ease-out 0.4s both;
">
    <h2 class="section-title" style="color: #0096ff;">
        <span style="font-size: 1.2rem;">🖼️</span> VISITE VIRTUELLE & CAPTURES D'ÉCRAN
    </h2>
    
    <div class="screenshots-gallery" style="
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    ">
        <?php foreach ($screenshots as $index => $screenshot): ?>
        <div class="screenshot-item" style="
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid rgba(0, 150, 255, 0.3);
            background: rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        " onclick="openLightbox(<?php echo $index; ?>)">
            <div class="screenshot-image" style="
                width: 100%;
                height: 180px;
                overflow: hidden;
                position: relative;
            ">
                <img src="../../uploads/screenshots/<?php echo htmlspecialchars($screenshot['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($screenshot['caption'] ?? 'Screenshot'); ?>"
                     style="
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        transition: transform 0.3s ease;
                     "
                     onmouseover="this.style.transform='scale(1.05)';"
                     onmouseout="this.style.transform='scale(1)';">
                
                <div class="screenshot-overlay" style="
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 150, 255, 0);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: background 0.3s ease;
                ">
                    <div class="zoom-icon" style="
                        font-size: 2rem;
                        color: white;
                        opacity: 0;
                        transform: scale(0.8);
                        transition: all 0.3s ease;
                    ">
                        🔍
                    </div>
                </div>
            </div>
            
            <?php if (!empty($screenshot['caption'])): ?>
            <div class="screenshot-caption" style="
                padding: 1rem;
                background: rgba(0, 0, 0, 0.5);
                font-family: 'VT323', monospace;
                font-size: 1.1rem;
                color: var(--text-light-gray);
                border-top: 1px solid rgba(0, 150, 255, 0.3);
            ">
                <?php echo htmlspecialchars($screenshot['caption']); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    
    <p style="
        text-align: center;
        margin-top: 1.5rem;
        font-family: 'VT323', monospace;
        font-size: 1.1rem;
        color: var(--text-gray);
    ">
        <?php echo count($screenshots); ?> captures d'écran disponibles
    </p>
</section>

<!-- Lightbox Modal -->
<div id="lightbox" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
    justify-content: center;
    align-items: center;
">
    <div class="lightbox-content" style="
        max-width: 90%;
        max-height: 90%;
        position: relative;
    ">
        <button id="close-lightbox" style="
            position: absolute;
            top: -40px;
            right: 0;
            background: var(--danger-red);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            z-index: 10000;
        ">×</button>
        
        <div id="lightbox-image-container" style="text-align: center;">
            <img id="lightbox-image" style="
                max-width: 100%;
                max-height: 80vh;
                border-radius: 8px;
                box-shadow: 0 0 30px rgba(0, 150, 255, 0.5);
            ">
            <div id="lightbox-caption" style="
                margin-top: 1rem;
                font-family: 'VT323', monospace;
                font-size: 1.2rem;
                color: white;
                padding: 0 1rem;
            "></div>
        </div>
        
        <button id="prev-btn" style="
            position: absolute;
            left: -60px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 150, 255, 0.7);
            border: 2px solid #0096ff;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
        " onmouseover="this.style.background='rgba(0, 150, 255, 0.9)';" 
        onmouseout="this.style.background='rgba(0, 150, 255, 0.7)';">◀</button>
        
        <button id="next-btn" style="
            position: absolute;
            right: -60px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 150, 255, 0.7);
            border: 2px solid #0096ff;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
        " onmouseover="this.style.background='rgba(0, 150, 255, 0.9)';" 
        onmouseout="this.style.background='rgba(0, 150, 255, 0.7)';">▶</button>
        
        <div id="lightbox-index" style="
            position: absolute;
            bottom: -40px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        "></div>
    </div>
</div>

<script>
    // Lightbox functionality
    const screenshots = <?php echo json_encode($screenshots); ?>;
    let currentIndex = 0;
    
    function openLightbox(index) {
        currentIndex = index;
        updateLightbox();
        document.getElementById('lightbox').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    function updateLightbox() {
        const screenshot = screenshots[currentIndex];
        const lightboxImage = document.getElementById('lightbox-image');
        const lightboxCaption = document.getElementById('lightbox-caption');
        const lightboxIndex = document.getElementById('lightbox-index');
        
        lightboxImage.src = '../../uploads/screenshots/' + screenshot.image_url;
        lightboxImage.alt = screenshot.caption || 'Screenshot';
        lightboxCaption.textContent = screenshot.caption || '';
        lightboxIndex.textContent = (currentIndex + 1) + ' / ' + screenshots.length;
    }
    
    function nextImage() {
        currentIndex = (currentIndex + 1) % screenshots.length;
        updateLightbox();
    }
    
    function prevImage() {
        currentIndex = (currentIndex - 1 + screenshots.length) % screenshots.length;
        updateLightbox();
    }
    
    // Event Listeners
    document.getElementById('close-lightbox').addEventListener('click', closeLightbox);
    document.getElementById('prev-btn').addEventListener('click', prevImage);
    document.getElementById('next-btn').addEventListener('click', nextImage);
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (document.getElementById('lightbox').style.display === 'flex') {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
        }
    });
</script>

<style>
    /* Screenshot hover effects */
    .screenshot-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 150, 255, 0.3);
        border-color: #0096ff;
    }
    
    .screenshot-item:hover .screenshot-overlay {
        background: rgba(0, 150, 255, 0.4);
    }
    
    .screenshot-item:hover .zoom-icon {
        opacity: 1;
        transform: scale(1);
    }
    
    /* Responsive lightbox buttons */
    @media (max-width: 768px) {
        #prev-btn {
            left: 10px;
        }
        
        #next-btn {
            right: 10px;
        }
        
        #close-lightbox {
            top: 10px;
            right: 10px;
        }
    }
</style>
<?php endif; ?>
    </main>

    <!-- Footer -->
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

    <!-- JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        // Export to PDF functionality
        document.getElementById('export-pdf').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Game data from PHP
            const gameTitle = "<?php echo addslashes($game['nom']); ?>";
            const gameDescription = "<?php echo addslashes($game['description']); ?>";
            const gameCategory = "<?php echo addslashes($game['categorie']); ?>";
            const gamePrice = "<?php echo number_format($game['prix'], 2); ?> €";
            const gameStock = "<?php echo $game['stock']; ?>";
            const gameId = "<?php echo $game['id']; ?>";
            
            // Set document properties
            doc.setProperties({
                title: `${gameTitle} - Fiche technique`,
                subject: 'Détails du jeu',
                author: 'LUDOLOGY VAULT'
            });
            
            // Header
            doc.setFontSize(18);
            doc.setTextColor(0, 255, 65);
            doc.text('LUDOLOGY VAULT', 105, 20, { align: 'center' });
            
            doc.setFontSize(12);
            doc.setTextColor(100, 100, 100);
            doc.text('Fiche technique du jeu', 105, 30, { align: 'center' });
            
            // Separator line
            doc.setDrawColor(0, 255, 65);
            doc.line(20, 35, 190, 35);
            
            // Game title
            doc.setFontSize(16);
            doc.setTextColor(0, 255, 65);
            doc.text(gameTitle, 20, 50);
            
            // Game details
            doc.setFontSize(10);
            doc.setTextColor(0, 0, 0);
            
            let yPos = 65;
            
            // Reference
            doc.text(`Référence: LV-${String(gameId).padStart(6, '0')}`, 20, yPos);
            yPos += 7;
            
            // Category
            doc.text(`Catégorie: ${gameCategory}`, 20, yPos);
            yPos += 7;
            
            // Price
            doc.text(`Prix: ${gamePrice}`, 20, yPos);
            yPos += 7;
            
            // Stock
            doc.text(`Disponibilité: ${gameStock > 0 ? 'En stock' : 'Rupture de stock'} (${gameStock} unités)`, 20, yPos);
            yPos += 10;
            
            // Description title
            doc.setFontSize(12);
            doc.setTextColor(0, 150, 255);
            doc.text('Description:', 20, yPos);
            yPos += 8;
            
            // Description content
            doc.setFontSize(10);
            doc.setTextColor(0, 0, 0);
            const descriptionLines = doc.splitTextToSize(gameDescription, 170);
            doc.text(descriptionLines, 20, yPos);
            yPos += (descriptionLines.length * 6) + 10;
            
            // Footer
            doc.setFontSize(8);
            doc.setTextColor(150, 150, 150);
            doc.text(`Généré le ${new Date().toLocaleDateString('fr-FR')}`, 105, 280, { align: 'center' });
            doc.text('© 2024 LUDOLOGY VAULT - https://ludologyvault.tn', 105, 285, { align: 'center' });
            
            // Save PDF
            const fileName = `LUDOLOGY-${gameTitle.replace(/[^a-z0-9]/gi, '-')}.pdf`;
            doc.save(fileName);
        });

        // Logout functionality
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