
<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/JeuxController.php';
require_once '../../controller/CommandeController.php';

// Initialize controllers
$userController = new UserController();
$jeuxController = new JeuxController();
$commandeController = new CommandeController();

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

// Check if user came from successful checkout
if (!isset($_SESSION['checkout_success']) || !$_SESSION['checkout_success']) {
    header('Location: cart.php');
    exit();
}

// Get order references from session
$orderRefs = $_SESSION['order_refs'] ?? [];

// Clear checkout success flag
unset($_SESSION['checkout_success']);
unset($_SESSION['order_refs']);

// Get user's recent orders
$userOrders = [];
if ($isLoggedIn) {
    $userOrders = $commandeController->getUserCommandes($_SESSION['user_id']);
    // Get only the most recent orders (last 5)
    $recentOrders = array_slice($userOrders, 0, 5);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Commande - LUDOLOGY VAULT</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    
    <style>
        /* User menu styles (copied from cart.php) */
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
        
        /* Confirmation page styles */
        .main-content {
            max-width: 1200px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        .confirmation-container {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05), rgba(189, 0, 255, 0.05));
            border: 2px solid var(--primary-green);
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            margin-bottom: 3rem;
        }

        .confirmation-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 10px,
                    rgba(0, 255, 65, 0.02) 10px,
                    rgba(0, 255, 65, 0.02) 20px
                );
            pointer-events: none;
        }

        .success-icon {
            font-size: 5rem;
            color: var(--primary-green);
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                text-shadow: 0 0 20px var(--primary-green);
            }
            50% {
                transform: scale(1.1);
                text-shadow: 0 0 40px var(--primary-green);
            }
        }

        .confirmation-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 2rem;
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            text-shadow: 0 0 10px var(--primary-green);
        }

        .confirmation-subtitle {
            font-family: 'VT323', monospace;
            font-size: 1.5rem;
            color: var(--text-white);
            margin-bottom: 2rem;
        }

        .order-refs {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem auto;
            max-width: 600px;
        }

        .order-ref {
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            color: var(--secondary-purple);
            margin: 0.5rem 0;
            padding: 0.5rem;
            background: rgba(189, 0, 255, 0.1);
            border-radius: 4px;
        }

        .confirmation-message {
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            color: var(--text-light-gray);
            line-height: 1.8;
            margin-bottom: 3rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .action-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 3rem;
        }

        .btn-primary {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            border: none;
            border-radius: 8px;
            color: var(--darker-bg);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 255, 65, 0.4);
        }

        .btn-secondary {
            padding: 1rem 2rem;
            background: transparent;
            border: 2px solid var(--primary-green);
            border-radius: 8px;
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
        }

        .btn-secondary:hover {
            background: rgba(0, 255, 65, 0.1);
            transform: translateY(-2px);
        }

        /* Recent orders section */
        .recent-orders {
            margin-top: 4rem;
        }

        .section-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 1.2rem;
            color: var(--primary-green);
            margin-bottom: 2rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .section-title::before,
        .section-title::after {
            content: '◄◄◄';
            color: var(--secondary-purple);
            font-size: 0.8rem;
        }

        .section-title::after {
            content: '►►►';
        }

        .orders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .order-card {
            background: rgba(26, 26, 46, 0.8);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .order-card:hover {
            border-color: var(--primary-green);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 255, 65, 0.2);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .order-ref-small {
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            color: var(--secondary-purple);
        }

        .order-date {
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
            color: var(--text-gray);
        }

        .order-details {
            margin-bottom: 1rem;
        }

        .order-game {
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
            color: var(--text-white);
            margin-bottom: 0.5rem;
        }

        .order-meta {
            display: flex;
            justify-content: space-between;
            font-family: 'VT323', monospace;
            font-size: 1rem;
            color: var(--text-light-gray);
        }

        .order-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
            text-transform: uppercase;
        }

        .status-pending { background: rgba(255, 158, 0, 0.1); color: var(--warning-orange); border: 1px solid var(--warning-orange); }
        .status-processing { background: rgba(0, 150, 255, 0.1); color: #0096ff; border: 1px solid #0096ff; }
        .status-shipped { background: rgba(0, 255, 65, 0.1); color: var(--primary-green); border: 1px solid var(--primary-green); }
        .status-delivered { background: rgba(189, 0, 255, 0.1); color: var(--secondary-purple); border: 1px solid var(--secondary-purple); }
        .status-completed { background: rgba(0, 200, 83, 0.1); color: #00c853; border: 1px solid #00c853; }
        .status-cancelled { background: rgba(255, 0, 110, 0.1); color: var(--accent-pink); border: 1px solid var(--accent-pink); }

        .no-orders {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .no-orders a {
            color: var(--primary-green);
            text-decoration: none;
        }

        .no-orders a:hover {
            text-decoration: underline;
        }

        /* Order steps */
        .order-steps {
            display: flex;
            justify-content: space-between;
            max-width: 800px;
            margin: 3rem auto;
            position: relative;
        }

        .order-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 50px;
            right: 50px;
            height: 2px;
            background: var(--border-color);
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--darker-bg);
            border: 2px solid var(--primary-green);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        .step.active .step-circle {
            background: var(--primary-green);
            color: var(--darker-bg);
            box-shadow: 0 0 20px var(--primary-green);
        }

        .step-label {
            font-family: 'VT323', monospace;
            font-size: 1rem;
            color: var(--text-light-gray);
            text-align: center;
        }

        .step.active .step-label {
            color: var(--primary-green);
            font-weight: bold;
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

    <main class="main-content">
        <!-- Order Confirmation -->
        <div class="confirmation-container">
            <div class="success-icon">✅</div>
            
            <h1 class="confirmation-title">COMMANDE CONFIRMÉE !</h1>
            <p class="confirmation-subtitle">Merci pour votre achat, <?php echo htmlspecialchars($username); ?> !</p>
            
            <?php if (!empty($orderRefs)): ?>
                <div class="order-refs">
                    <h3 style="font-family: 'Press Start 2P', cursive; font-size: 0.7rem; color: var(--text-white); margin-bottom: 1rem;">
                        VOS RÉFÉRENCES DE COMMANDE :
                    </h3>
                    <?php foreach ($orderRefs as $ref): ?>
                        <div class="order-ref"><?php echo htmlspecialchars($ref); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <p class="confirmation-message">
                Votre commande a été enregistrée avec succès. Vous recevrez un email de confirmation dans les prochaines minutes.<br>
                Vous pouvez suivre l'état de votre commande dans la section "Mes Commandes" de votre profil.
            </p>
            
            <!-- Order Progress Steps -->
            <div class="order-steps">
                <div class="step active">
                    <div class="step-circle">1</div>
                    <span class="step-label">Commande<br>Confirmée</span>
                </div>
                <div class="step">
                    <div class="step-circle">2</div>
                    <span class="step-label">En<br>Préparation</span>
                </div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <span class="step-label">Expédiée</span>
                </div>
                <div class="step">
                    <div class="step-circle">4</div>
                    <span class="step-label">Livrée</span>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="my-orders.php" class="btn-primary">
                    <span>📋</span> VOIR MES COMMANDES
                </a>
                <a href="games.php" class="btn-secondary">
                    <span>🎮</span> CONTINUER LES ACHATS
                </a>
            </div>
        </div>
        
        <!-- Recent Orders Section -->
        <?php if ($isLoggedIn && !empty($recentOrders)): ?>
            <section class="recent-orders">
                <h2 class="section-title">VOS COMMANDES RÉCENTES</h2>
                
                <div class="orders-grid">
                    <?php foreach ($recentOrders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-ref-small"><?php echo htmlspecialchars($order['order_ref']); ?></span>
                                <span class="order-date"><?php echo date('d/m/Y', strtotime($order['Date'])); ?></span>
                            </div>
                            
                            <div class="order-details">
                                <div class="order-game">
                                    <?php echo htmlspecialchars($order['produit_nom'] ?? 'Jeu'); ?>
                                </div>
                                <div class="order-meta">
                                    <span>Quantité: <?php echo $order['quantity']; ?></span>
                                    <span><?php echo number_format($order['Total'], 2); ?> €</span>
                                </div>
                            </div>
                            
                            <?php 
                            $statusClass = 'status-pending';
                            switch ($order['statut']) {
                                case 'processing': $statusClass = 'status-processing'; break;
                                case 'shipped': $statusClass = 'status-shipped'; break;
                                case 'delivered': $statusClass = 'status-delivered'; break;
                                case 'completed': $statusClass = 'status-completed'; break;
                                case 'cancelled': $statusClass = 'status-cancelled'; break;
                            }
                            ?>
                            <div class="order-status <?php echo $statusClass; ?>">
                                <?php 
                                $statusText = [
                                    'pending' => 'En attente',
                                    'processing' => 'En traitement',
                                    'shipped' => 'Expédiée',
                                    'delivered' => 'Livrée',
                                    'completed' => 'Terminée',
                                    'cancelled' => 'Annulée'
                                ];
                                echo $statusText[$order['statut']] ?? $order['statut'];
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="my-orders.php" class="btn-secondary">
                        <span>📜</span> VOIR TOUTES MES COMMANDES
                    </a>
                </div>
            </section>
        <?php elseif ($isLoggedIn): ?>
            <section class="recent-orders">
                <h2 class="section-title">VOS COMMANDES</h2>
                <div class="no-orders">
                    <p>Vous n'avez pas encore d'autres commandes.</p>
                    <p><a href="games.php">→ Découvrez notre collection de jeux ←</a></p>
                </div>
            </section>
        <?php endif; ?>
    </main>

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
                    <h3 class="footer-title">SUPPORT</h3>
                    <ul class="footer-links">
                        <li><a href="reclamation.php">► Support & Réclamations</a></li>
                        <li><a href="#">► FAQ</a></li>
                        <li><a href="#">► Guide d'utilisation</a></li>
                        <li><a href="#">► Politique de retour</a></li>
                        <li><a href="#">► Contact</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">INFORMATIONS</h3>
                    <div class="footer-info">
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
        
        // Animate order steps
        document.addEventListener('DOMContentLoaded', function() {
            const steps = document.querySelectorAll('.step');
            let currentStep = 0;
            
            function updateSteps() {
                steps.forEach((step, index) => {
                    if (index <= currentStep) {
                        step.classList.add('active');
                    } else {
                        step.classList.remove('active');
                    }
                });
            }
            
            // Auto-progress through steps
            setInterval(() => {
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    updateSteps();
                }
            }, 2000);
            
            updateSteps();
        });
    </script>
</body>
</html>
