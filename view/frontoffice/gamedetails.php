
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
        .pixel-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }

        .game-card {
            position: relative;
        }

        .game-desc {
            opacity: 0;
            height: 0;
            overflow: hidden;
            transform: translateY(10px);
            transition: all 0.35s ease-in-out;
            pointer-events: none;
        }

        .game-card:hover .game-desc {
            opacity: 1;
            height: auto;
            transform: translateY(0);
        }
        
        /* User menu styles (copied from index) */
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
        
        /* Header search bar */
        .header-search {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .header-search-input {
            padding: 0.5rem 1rem;
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            width: 200px;
        }
        
        .header-search-btn {
            padding: 0.5rem 1rem;
            background: var(--primary-green);
            border: 2px solid var(--primary-green);
            color: var(--darker-bg);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
            cursor: pointer;
        }
        
        .header-search-btn:hover {
            background: #00cc33;
        }
        
        /* Games header */
        .games-header {
            margin: 2rem 0;
            text-align: center;
        }
        
        .games-title {
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 1rem;
        }
        
        .games-count {
            font-size: 1rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
        
        /* Sort and filter bar */
        .sort-filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2rem 0;
            padding: 1rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
        }
    </style>
    <style>
        /* Game Details Specific Styles */
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

        body {
            background-color: var(--darker-bg);
            color: var(--text-white);
            min-height: 100vh;
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

        /* Footer - Keep same as games.php */
        .footer {
            background: rgba(10, 10, 15, 0.95);
            border-top: 2px solid var(--primary-green);
            margin-top: 3rem;
            padding: 2rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .footer-content {
                grid-template-columns: 1fr;
            }
        }

        .footer-section h3 {
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            margin-bottom: 1rem;
            font-size: 0.8rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: var(--text-light-gray);
            text-decoration: none;
            font-family: 'VT323', monospace;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary-green);
        }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1rem;
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
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" width=\"400\" height=\"400\" viewBox=\"0 0 400 400\"%3E%3Cdefs%3E%3ClinearGradient id=\"grad\" x1=\"0%25\" y1=\"0%25\" x2=\"100%25\" y2=\"100%25\"%3E%3Cstop offset=\"0%25\" stop-color=\"%2300ff41\"/%3E%3Cstop offset=\"100%25\" stop-color=\"%23bd00ff\"/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width=\"400\" height=\"400\" fill=\"url(%23grad)\"/%3E%3Ctext x=\"50%25\" y=\"50%25\" font-family=\"Courier New\" font-size=\"24\" fill=\"white\" text-anchor=\"middle\" dominant-baseline=\"middle\"%3E%3Ctspan x=\"50%25\" dy=\"-20\"%3E<?php echo urlencode(substr($game['nom'], 0, 20)); ?>%3C/tspan%3E%3Ctspan x=\"50%25\" dy=\"30\"%3ELUDOLOGY VAULT%3C/tspan%3E%3C/text%3E%3C/svg%3E';">
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

            <!-- Game Description (Full width) -->
            <div class="game-description-section">
                <h2 class="section-title">SYNOPSIS ET DÉTAILS</h2>
                <div class="game-description">
                    <?php echo nl2br(htmlspecialchars($game['description'])); ?>
                    
                    <?php if (!empty($game['specifications'])): ?>
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                            <h3 style="color: var(--primary-green); font-family: 'Press Start 2P', cursive; font-size: 0.9rem; margin-bottom: 1rem;">
                                🔧 SPÉCIFICATIONS TECHNIQUES
                            </h3>
                            <div style="font-family: 'VT323', monospace; font-size: 1.2rem; line-height: 1.8;">
                                <?php echo nl2br(htmlspecialchars($game['specifications'])); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer (Same as games.php) -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>LUDOLOGY VAULT</h3>
                <p style="color: var(--text-light-gray); font-family: 'VT323', monospace; font-size: 1rem;">
                    Préserver l'histoire du jeu vidéo pour les générations futures.
                </p>
            </div>

            <div class="footer-section">
                <h3>NAVIGATION</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="games.php">Jeux</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="events.php">Événements</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>SUPPORT</h3>
                <ul class="footer-links">
                    <li><a href="reclamation.php">Réclamations</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Guide</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>INFORMATIONS</h3>
                <ul class="footer-links">
                    <li><a href="#">À propos</a></li>
                    <li><a href="#">Mentions légales</a></li>
                    <li><a href="#">Confidentialité</a></li>
                    <li><a href="#">Conditions</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 LUDOLOGY VAULT - Tous droits réservés</p>
            <p>Made with <span style="color: var(--accent-pink);">♥</span> for gamers worldwide</p>
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

        // Add to cart form handling
        document.addEventListener('DOMContentLoaded', function() {
            const cartForms = document.querySelectorAll('form[action="add_to_cart.php"]');
            
            cartForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Add loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<span>⏳</span> AJOUT EN COURS...';
                        submitBtn.disabled = true;
                        
                        // Restore button after 3 seconds if request fails
                        setTimeout(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }, 3000);
                    }
                });
            });
        });
    </script>
</body>
</html>
