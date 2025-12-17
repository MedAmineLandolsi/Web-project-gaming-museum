
<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/CommandeController.php';

// Initialize controllers
$userController = new UserController();
$commandeC = new CommandeController();

// Check if user is logged in
$isLoggedIn = $userController->isLoggedIn();

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$result = $userController->viewProfile($_SESSION['user_id']);
$user = $result['user'];
$username = $user['username'];
$profilePicture = $user['profile_picture_url'] ?? '';
$role = $user['role'];

// Check if we have order success data
if (!isset($_SESSION['order_success'])) {
    header('Location: my-orders.php');
    exit;
}

$orderData = $_SESSION['order_success'];
$orderIds = $orderData['order_ids'] ?? [];
$message = $orderData['message'] ?? 'Commande confirmée avec succès!';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ludology Vault - Commande Confirmée</title>
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
        .success-container {
            max-width: 800px;
            margin: 100px auto 60px;
            padding: 0 20px;
        }
        
        .success-card {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));
            border: 2px solid var(--primary-green);
            border-radius: 12px;
            padding: 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }
        
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-purple), var(--accent-pink));
            animation: slideIn 1s ease-out;
        }
        
        .success-icon {
            font-size: 4rem;
            margin-bottom: 2rem;
            animation: bounce 2s infinite;
            display: inline-block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { width: 0; }
            to { width: 100%; }
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .success-title {
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
            animation: glow 2s infinite alternate;
        }
        
        @keyframes glow {
            from { text-shadow: 0 0 10px var(--primary-green); }
            to { text-shadow: 0 0 20px var(--primary-green), 0 0 30px var(--secondary-purple); }
        }
        
        .success-message {
            font-size: 1.2rem;
            color: var(--text-gray);
            margin-bottom: 2rem;
            font-family: 'VT323', monospace;
            animation: fadeIn 1s ease-out 0.3s both;
        }
        
        .order-details {
            background: rgba(0, 255, 65, 0.05);
            border: 1px solid var(--primary-green);
            border-radius: 8px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: left;
            animation: fadeIn 1s ease-out 0.6s both;
        }
        
        .details-title {
            font-size: 1rem;
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            font-family: 'Press Start 2P', cursive;
            text-align: center;
        }
        
        .order-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 255, 65, 0.2);
            transition: all 0.3s;
        }
        
        .order-item:hover {
            background: rgba(0, 255, 65, 0.05);
            transform: translateX(5px);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-ref {
            color: var(--text-white);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
        }
        
        .order-game {
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .next-steps {
            background: rgba(189, 0, 255, 0.05);
            border: 1px solid var(--secondary-purple);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem 0;
            animation: fadeIn 1s ease-out 0.9s both;
        }
        
        .steps-title {
            font-size: 1rem;
            color: var(--secondary-purple);
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
            text-align: center;
        }
        
        .steps-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .step-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 0;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            opacity: 0;
            animation: slideInItem 0.5s ease-out forwards;
        }
        
        .step-item:nth-child(1) { animation-delay: 1.2s; }
        .step-item:nth-child(2) { animation-delay: 1.4s; }
        .step-item:nth-child(3) { animation-delay: 1.6s; }
        .step-item:nth-child(4) { animation-delay: 1.8s; }
        
        @keyframes slideInItem {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .step-icon {
            color: var(--secondary-purple);
            font-size: 1.2rem;
            min-width: 30px;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
            animation: fadeIn 1s ease-out 2s both;
        }
        
        .action-btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 4px;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            color: white;
        }
        
        .btn-secondary {
            background: transparent;
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 255, 65, 0.3);
        }
        
        /* Confetti */
        .confetti-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
        }
        
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            opacity: 0;
        }
        
        /* Order tracking */
        .tracking-bar {
            background: rgba(0, 255, 65, 0.1);
            border-radius: 20px;
            padding: 1rem;
            margin: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .tracking-progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-purple));
            border-radius: 20px;
            width: 25%; /* Start at 25% (order placed) */
            transition: width 2s ease-out;
        }
        
        .tracking-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }
        
        .tracking-step {
            text-align: center;
            flex: 1;
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            background: var(--darker-bg);
            border: 2px solid var(--primary-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
            color: var(--primary-green);
        }
        
        .step-label {
            font-family: 'VT323', monospace;
            font-size: 0.8rem;
            color: var(--text-gray);
        }
        
        .step-active .step-number {
            background: var(--primary-green);
            color: var(--darker-bg);
        }
    </style>
</head>
<body>
    <!-- Background particles -->
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

    <!-- Confetti container -->
    <div class="confetti-container" id="confettiContainer"></div>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <div class="logo-container">
                    <div class="logo-placeholder">🎮</div>
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
                    <li><a href="my-orders.php">MES COMMANDES</a></li>
                </ul>
            </div>
            
            <div class="nav-right">
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
                
                <a href="cart.php" style="text-decoration: none; margin-left: 1rem;">
                    <button class="btn-auth">
                        <span class="btn-icon">🛒</span> 
                    </button>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="success-container">
        <div class="success-card">
            <div class="success-icon">🎉</div>
            
            <h1 class="success-title">COMMANDE CONFIRMÉE!</h1>
            
            <p class="success-message">
                <?php echo htmlspecialchars($message); ?>
            </p>
            
            <!-- Order Tracking -->
            <div class="tracking-bar">
                <div class="tracking-progress" id="trackingProgress"></div>
                <div class="tracking-steps">
                    <div class="tracking-step step-active">
                        <div class="step-number">1</div>
                        <div class="step-label">COMMANDÉ</div>
                    </div>
                    <div class="tracking-step">
                        <div class="step-number">2</div>
                        <div class="step-label">EN PRÉPARATION</div>
                    </div>
                    <div class="tracking-step">
                        <div class="step-number">3</div>
                        <div class="step-label">EXPÉDIÉ</div>
                    </div>
                    <div class="tracking-step">
                        <div class="step-number">4</div>
                        <div class="step-label">LIVRÉ</div>
                    </div>
                </div>
            </div>
            
            <!-- Order Details -->
            <?php if (!empty($orderIds)): ?>
                <div class="order-details">
                    <h3 class="details-title">📋 DÉTAILS DE VOTRE COMMANDE</h3>
                    
                    <ul class="order-list">
                        <?php foreach ($orderIds as $order): ?>
                            <li class="order-item">
                                <div>
                                    <span class="order-ref">#<?php echo htmlspecialchars($order['ref']); ?></span>
                                    <span class="order-game"> - <?php echo htmlspecialchars($order['game']); ?></span>
                                </div>
                                <div>
                                    <span class="order-status-badge" style="background: var(--primary-green); color: white; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.5rem; font-family: 'Press Start 2P', cursive;">
                                        CONFIRMÉ
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div style="text-align: center; margin-top: 1rem; font-family: 'VT323', monospace; color: var(--text-gray);">
                        <p>📧 Un email de confirmation vous a été envoyé</p>
                        <p>📱 Vous pouvez suivre votre commande dans "Mes Commandes"</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Next Steps -->
            <div class="next-steps">
                <h3 class="steps-title">PROCHAINES ÉTAPES</h3>
                
                <ul class="steps-list">
                    <li class="step-item">
                        <span class="step-icon">📧</span>
                        <span>Vous recevrez un email de confirmation sous peu</span>
                    </li>
                    <li class="step-item">
                        <span class="step-icon">📞</span>
                        <span>Notre équipe vous contactera pour confirmer la livraison</span>
                    </li>
                    <li class="step-item">
                        <span class="step-icon">🚚</span>
                        <span>Livraison estimée: 3-5 jours ouvrables</span>
                    </li>
                    <li class="step-item">
                        <span class="step-icon">📱</span>
                        <span>Suivez votre commande dans votre espace personnel</span>
                    </li>
                </ul>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="my-orders.php" class="action-btn btn-primary">
                    <span>Voir mes commandes</span>
                    <span>→</span>
                </a>
                
                <a href="games.php" class="action-btn btn-secondary">
                    <span>Continuer mes achats</span>
                    <span>🎮</span>
                </a>
                
                <button class="action-btn btn-secondary" onclick="downloadInvoice()">
                    <span>Télécharger la facture</span>
                    <span>📄</span>
                </button>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div style="text-align: center; margin-top: 2rem; font-family: 'VT323', monospace; color: var(--text-gray);">
            <p>🛡️ <strong>Garantie Satisfait ou Remboursé</strong> - 30 jours pour changer d'avis</p>
            <p>📞 <strong>Service Client</strong> - Disponible du lundi au vendredi, 9h-18h</p>
            <p>🎁 <strong>Cadeau Bonus</strong> - Votre code promotionnel a été envoyé par email!</p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-content">
                <div class="footer-section footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-placeholder">🎮</div>
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
                        <li><a href="profile.php">► Mon Profil</a></li>
                        <li><a href="my-orders.php">► Mes Commandes</a></li>
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

    <script>
        // User dropdown functionality
        document.querySelector('.user-profile-btn').addEventListener('click', function(e) {
            e.stopPropagation();
            document.querySelector('.user-dropdown').classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-menu')) {
                document.querySelector('.user-dropdown').classList.remove('show');
            }
        });
        
        // Logout functionality
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
        
        // Create confetti
        function createConfetti() {
            const colors = ['#00ff41', '#bd00ff', '#ff006e', '#ffcc00', '#00ffcc', '#ff00ff', '#00ffff'];
            const container = document.getElementById('confettiContainer');
            
            for (let i = 0; i < 150; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = confetti.style.width;
                confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                
                // Animation
                const duration = Math.random() * 3 + 2;
                confetti.style.animation = `
                    confettiFall ${duration}s linear forwards,
                    confettiRotate ${duration}s linear forwards
                `;
                
                container.appendChild(confetti);
                
                // Remove after animation
                setTimeout(() => {
                    confetti.remove();
                }, duration * 1000);
            }
        }
        
        // Add CSS for confetti animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes confettiFall {
                0% {
                    opacity: 1;
                    transform: translateY(-100px) rotate(0deg);
                }
                100% {
                    opacity: 0;
                    transform: translateY(100vh) rotate(720deg);
                }
            }
            
            @keyframes confettiRotate {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            .user-dropdown.show {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }
        `;
        document.head.appendChild(style);
        
        // Animate tracking progress
        function animateTracking() {
            const progressBar = document.getElementById('trackingProgress');
            const steps = document.querySelectorAll('.tracking-step');
            
            // Start at 25% (step 1)
            setTimeout(() => {
                progressBar.style.width = '25%';
                steps[0].classList.add('step-active');
            }, 500);
            
            // Animate to 50% (step 2)
            setTimeout(() => {
                progressBar.style.width = '50%';
                steps[1].classList.add('step-active');
            }, 2000);
            
            // Animate to 75% (step 3)
            setTimeout(() => {
                progressBar.style.width = '75%';
                steps[2].classList.add('step-active');
            }, 3500);
            
            // Animate to 100% (step 4)
            setTimeout(() => {
                progressBar.style.width = '100%';
                steps[3].classList.add('step-active');
            }, 5000);
        }
        
        // Download invoice
        function downloadInvoice() {
            // Create a simple invoice in a new window
            const invoiceWindow = window.open('', '_blank');
            invoiceWindow.document.write(`
                <html>
                <head>
                    <title>Facture Ludology Vault</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .logo { font-size: 24px; font-weight: bold; color: #00ff41; }
                        .invoice-details { margin-bottom: 20px; }
                        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        .table th { background-color: #f2f2f2; }
                        .total { text-align: right; font-weight: bold; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div class="logo">LUDOLOGY VAULT</div>
                        <h2>FACTURE</h2>
                        <p>Date: ${new Date().toLocaleDateString('fr-FR')}</p>
                    </div>
                    
                    <div class="invoice-details">
                        <p><strong>Client:</strong> <?php echo htmlspecialchars($username); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                        <p><strong>Numéro de commande:</strong> 
                            <?php 
                            if (!empty($orderIds)) {
                                echo htmlspecialchars($orderIds[0]['ref']);
                            }
                            ?>
                        </p>
                    </div>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Description</th>
                                <th>Quantité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderIds as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['ref']); ?></td>
                                <td><?php echo htmlspecialchars($order['game']); ?></td>
                                <td>1</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="total">
                        <p>Merci pour votre commande !</p>
                        <p>Cette facture est disponible dans votre espace client.</p>
                    </div>
                    
                    <script>
                        window.onload = function() {
                            window.print();
                        }
                    </script>
                </body>
                </html>
            `);
            invoiceWindow.document.close();
        }
        
        // Auto-remove success message from session after display
        function clearSessionSuccess() {
            // You could make an AJAX call here to clear the session variable
            // For now, we'll just note that it should be cleared
            console.log('Order success displayed - session should be cleared on next page load');
        }
        
        // Initialize animations on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Start confetti
            createConfetti();
            
            // Animate tracking bar
            animateTracking();
            
            // Auto-clear session after 5 seconds
            setTimeout(clearSessionSuccess, 5000);
            
            // Add some interactive elements
            const orderItems = document.querySelectorAll('.order-item');
            orderItems.forEach(item => {
                item.addEventListener('click', function() {
                    this.style.backgroundColor = 'rgba(0, 255, 65, 0.1)';
                    setTimeout(() => {
                        this.style.backgroundColor = '';
                    }, 300);
                });
            });
            
            // Play success sound (optional)
            // const audio = new Audio('success-sound.mp3');
            // audio.volume = 0.3;
            // audio.play().catch(e => console.log('Audio play failed:', e));
        });
        
        // Countdown timer for delivery estimate
        function startDeliveryCountdown() {
            const now = new Date();
            const deliveryDate = new Date(now);
            deliveryDate.setDate(deliveryDate.getDate() + 5); // 5 days from now
            
            const options = { weekday: 'long', day: 'numeric', month: 'long' };
            const formattedDate = deliveryDate.toLocaleDateString('fr-FR', options);
            
            // Update delivery estimate in the DOM
            const deliveryElement = document.querySelector('.step-item:nth-child(3) span:last-child');
            if (deliveryElement) {
                deliveryElement.textContent = `Livraison estimée: ${formattedDate}`;
            }
        }
        
        // Start countdown
        startDeliveryCountdown();
    </script>
</body>
</html>
<?php
// Clear the success message from session after displaying
unset($_SESSION['order_success']);
?>