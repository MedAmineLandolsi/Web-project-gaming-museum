
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

if (!$isLoggedIn) {
    header('Location: login.php?redirect=order-details.php');
    exit;
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header('Location: my-orders.php');
    exit;
}

$orderId = (int)$_GET['id'];
$order = $commandeC->getCommandeById($orderId);

// Check if order exists and belongs to user
if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header('Location: my-orders.php');
    exit;
}

$result = $userController->viewProfile($_SESSION['user_id']);
$user = $result['user'];
$username = $user['username'];
$profilePicture = $user['profile_picture_url'] ?? '';
$role = $user['role'];

// Get game details
$game = $gamesC->getGameById($order['Produit_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ludology Vault - Détails de la Commande</title>
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
        .order-details-container {
            max-width: 1000px;
            margin: 100px auto 60px;
            padding: 0 20px;
        }
        
        .order-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .order-title {
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
        }
        
        .order-subtitle {
            font-size: 1.2rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
        
        /* Order Info Cards */
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .info-card {
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .info-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-green);
        }
        
        .info-card-icon {
            font-size: 1.5rem;
            color: var(--primary-green);
        }
        
        .info-card-title {
            font-size: 0.8rem;
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(0, 255, 65, 0.1);
        }
        
        .info-label {
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .info-value {
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            text-align: right;
            max-width: 60%;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: rgba(255, 165, 0, 0.2);
            border: 1px solid orange;
            color: orange;
        }
        
        .status-processing {
            background: rgba(0, 100, 255, 0.2);
            border: 1px solid blue;
            color: blue;
        }
        
        .status-shipped {
            background: rgba(0, 255, 65, 0.2);
            border: 1px solid var(--primary-green);
            color: var(--primary-green);
        }
        
        .status-delivered {
            background: rgba(0, 255, 65, 0.3);
            border: 1px solid #00cc33;
            color: #00cc33;
        }
        
        .status-completed {
            background: rgba(189, 0, 255, 0.2);
            border: 1px solid var(--secondary-purple);
            color: var(--secondary-purple);
        }
        
        .status-cancelled {
            background: rgba(255, 0, 110, 0.2);
            border: 1px solid var(--danger-red);
            color: var(--danger-red);
        }
        
        /* Product Details */
        .product-details {
            background: rgba(189, 0, 255, 0.05);
            border: 2px solid var(--secondary-purple);
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .product-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .product-image {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--secondary-purple);
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .product-info h3 {
            font-family: 'Press Start 2P', cursive;
            font-size: 1rem;
            color: var(--text-white);
            margin-bottom: 0.5rem;
        }
        
        .product-category {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: rgba(189, 0, 255, 0.1);
            border: 1px solid var(--secondary-purple);
            border-radius: 4px;
            font-family: 'VT323', monospace;
            color: var(--text-gray);
            font-size: 0.9rem;
        }
        
        .product-price {
            font-family: 'Press Start 2P', cursive;
            font-size: 1.2rem;
            color: var(--secondary-purple);
            margin-top: 0.5rem;
        }
        
        /* Timeline */
        .timeline {
            position: relative;
            padding: 2rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 2rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--primary-green);
        }
        
        .timeline-item {
            position: relative;
            padding-left: 3rem;
            margin-bottom: 2rem;
        }
        
        .timeline-item:last-child {
            margin-bottom: 0;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 1.5rem;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-green);
            border: 2px solid var(--darker-bg);
            transform: translateX(-50%);
        }
        
        .timeline-item.active::before {
            background: var(--secondary-purple);
            box-shadow: 0 0 10px var(--secondary-purple);
        }
        
        .timeline-date {
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
        }
        
        .timeline-content {
            font-family: 'VT323', monospace;
            font-size: 1rem;
            color: var(--text-white);
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 3rem;
            flex-wrap: wrap;
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
        
        .btn-danger {
            background: var(--danger-red);
            border: 2px solid var(--danger-red);
            color: white;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 255, 65, 0.3);
        }
        
        /* Print Styles */
        @media print {
            .navbar, .footer, .action-buttons {
                display: none !important;
            }
            
            .order-details-container {
                margin: 0;
                padding: 20px;
            }
            
            .info-card, .product-details, .timeline {
                border: 1px solid #000;
                box-shadow: none;
            }
        }
        
        /* Modal for tracking updates */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: var(--darker-bg);
            border: 2px solid var(--primary-green);
            border-radius: 8px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-green);
        }
        
        .modal-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 1rem;
            color: var(--primary-green);
        }
        
        .close-modal {
            background: none;
            border: none;
            color: var(--accent-pink);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
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
    <main class="order-details-container">
        <header class="order-header">
            <h1 class="order-title">📋 DÉTAILS DE LA COMMANDE</h1>
            <p class="order-subtitle">Suivez l'avancement de votre commande #<?php echo str_pad($order['ID'], 4, '0', STR_PAD_LEFT); ?></p>
        </header>
        
        <!-- Order Information Grid -->
        <div class="order-info-grid">
            <!-- Order Details -->
            <div class="info-card">
                <div class="info-card-header">
                    <span class="info-card-icon">📦</span>
                    <h2 class="info-card-title">INFORMATIONS COMMANDE</h2>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Numéro de commande:</span>
                    <span class="info-value">#<?php echo str_pad($order['ID'], 4, '0', STR_PAD_LEFT); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Référence:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['order_ref']); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Date de commande:</span>
                    <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($order['Date'])); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Statut:</span>
                    <span class="info-value">
                        <?php
                        $statusClass = 'status-' . $order['statut'];
                        $statuses = $commandeC->getStatuses();
                        $statusText = $statuses[$order['statut']] ?? $order['statut'];
                        ?>
                        <span class="status-badge <?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars($statusText); ?>
                        </span>
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Méthode de paiement:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['payment_method'] ?? 'Non spécifiée'); ?></span>
                </div>
            </div>
            
            <!-- Shipping Information -->
            <div class="info-card">
                <div class="info-card-header">
                    <span class="info-card-icon">🚚</span>
                    <h2 class="info-card-title">LIVRAISON</h2>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Nom:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['shipping_name'] ?? 'Non spécifié'); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['shipping_email'] ?? 'Non spécifié'); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Téléphone:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['shipping_phone'] ?? 'Non spécifié'); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Adresse:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['shipping_address'] ?? 'Non spécifiée'); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Ville:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['shipping_city'] ?? 'Non spécifiée'); ?>, <?php echo htmlspecialchars($order['shipping_zip'] ?? ''); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Pays:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['shipping_country'] ?? 'Non spécifié'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="product-details">
            <div class="product-header">
                <div class="product-image">
                    <?php if ($game['image'] && file_exists("../../uploads/" . $game['image'])): ?>
                        <img src="../../uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['nom']); ?>">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple)); display: flex; align-items: center; justify-content: center; color: white; font-family: 'VT323', monospace;">
                            <?php echo substr(htmlspecialchars($game['nom']), 0, 3); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($game['nom']); ?></h3>
                    <div class="product-category"><?php echo htmlspecialchars($game['categorie']); ?></div>
                    <div class="product-price"><?php echo number_format($game['prix'], 2); ?> €</div>
                </div>
            </div>
            
            <div class="info-item">
                <span class="info-label">Quantité:</span>
                <span class="info-value"><?php echo $order['quantity']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Prix unitaire:</span>
                <span class="info-value"><?php echo number_format($game['prix'], 2); ?> €</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Sous-total:</span>
                <span class="info-value"><?php echo number_format($game['prix'] * $order['quantity'], 2); ?> €</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Frais de livraison:</span>
                <span class="info-value">5.00 €</span>
            </div>
            
            <div class="info-item" style="border-top: 2px solid var(--secondary-purple); padding-top: 1rem; margin-top: 1rem;">
                <span class="info-label" style="font-family: 'Press Start 2P', cursive; font-size: 0.8rem; color: var(--secondary-purple);">TOTAL:</span>
                <span class="info-value" style="font-family: 'Press Start 2P', cursive; font-size: 0.8rem; color: var(--secondary-purple);"><?php echo number_format($order['Total'], 2); ?> €</span>
            </div>
        </div>
        
        <!-- Order Timeline -->
        <div class="timeline">
            <div class="info-card-header" style="border: none; padding: 0; margin: 0 0 2rem 0;">
                <span class="info-card-icon">⏱️</span>
                <h2 class="info-card-title">SUIVI DE COMMANDE</h2>
            </div>
            
            <div class="timeline-item active">
                <div class="timeline-date"><?php echo date('d/m/Y H:i', strtotime($order['Date'])); ?></div>
                <div class="timeline-content">Commande passée</div>
            </div>
            
            <?php if ($order['statut'] === 'processing' || $order['statut'] === 'shipped' || $order['statut'] === 'delivered' || $order['statut'] === 'completed'): ?>
            <div class="timeline-item active">
                <div class="timeline-date"><?php echo date('d/m/Y H:i', strtotime($order['Date']) + 3600); ?></div>
                <div class="timeline-content">Commande en préparation</div>
            </div>
            <?php endif; ?>
            
            <?php if ($order['statut'] === 'shipped' || $order['statut'] === 'delivered' || $order['statut'] === 'completed'): ?>
            <div class="timeline-item <?php echo in_array($order['statut'], ['shipped', 'delivered', 'completed']) ? 'active' : ''; ?>">
                <div class="timeline-date"><?php echo date('d/m/Y H:i', strtotime($order['Date']) + 86400); ?></div>
                <div class="timeline-content">Commande expédiée</div>
            </div>
            <?php endif; ?>
            
            <?php if ($order['statut'] === 'delivered' || $order['statut'] === 'completed'): ?>
            <div class="timeline-item <?php echo in_array($order['statut'], ['delivered', 'completed']) ? 'active' : ''; ?>">
                <div class="timeline-date"><?php echo date('d/m/Y H:i', strtotime($order['Date']) + 172800); ?></div>
                <div class="timeline-content">Commande livrée</div>
            </div>
            <?php endif; ?>
            
            <?php if ($order['statut'] === 'completed'): ?>
            <div class="timeline-item active">
                <div class="timeline-date"><?php echo date('d/m/Y H:i', strtotime($order['Date']) + 259200); ?></div>
                <div class="timeline-content">Commande terminée</div>
            </div>
            <?php endif; ?>
            
            <?php if ($order['statut'] === 'cancelled'): ?>
            <div class="timeline-item">
                <div class="timeline-date"><?php echo date('d/m/Y H:i', strtotime($order['Date']) + 3600); ?></div>
                <div class="timeline-content" style="color: var(--danger-red);">Commande annulée</div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="my-orders.php" class="action-btn btn-secondary">
                <span>← Retour aux commandes</span>
            </a>
            
            <button class="action-btn btn-primary" onclick="window.print()">
                <span>📄 Imprimer la facture</span>
            </button>
            
            <button class="action-btn btn-primary" onclick="downloadInvoice()">
                <span>💾 Télécharger PDF</span>
            </button>
            
            <?php if (in_array($order['statut'], ['pending', 'processing'])): ?>
                <button class="action-btn btn-danger" onclick="cancelOrder()">
                    <span>❌ Annuler la commande</span>
                </button>
            <?php endif; ?>
            
            <?php if ($order['statut'] === 'delivered'): ?>
                <button class="action-btn btn-primary" onclick="openReviewModal()">
                    <span>⭐ Noter la commande</span>
                </button>
            <?php endif; ?>
        </div>
    </main>

    <!-- Review Modal -->
    <div class="modal" id="reviewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">⭐ NOTER VOTRE COMMANDE</h3>
                <button class="close-modal" onclick="closeReviewModal()">×</button>
            </div>
            
            <div id="reviewContent">
                <!-- Review form will be loaded here -->
            </div>
        </div>
    </div>

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
        
        // Download invoice as PDF
        function downloadInvoice() {
            // Create invoice HTML
            const invoiceHTML = `
                <html>
                <head>
                    <title>Facture #<?php echo $order['order_ref']; ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 30px; max-width: 800px; margin: 0 auto; }
                        .header { text-align: center; margin-bottom: 40px; }
                        .logo { font-size: 28px; font-weight: bold; color: #00ff41; margin-bottom: 10px; }
                        .company-info { margin-bottom: 30px; }
                        .invoice-details { margin-bottom: 30px; }
                        .table { width: 100%; border-collapse: collapse; margin: 30px 0; }
                        .table th { background: #f5f5f5; padding: 10px; text-align: left; }
                        .table td { padding: 10px; border-bottom: 1px solid #ddd; }
                        .totals { text-align: right; margin-top: 30px; }
                        .total-row { margin: 10px 0; }
                        .grand-total { font-size: 18px; font-weight: bold; color: #333; }
                        .footer { margin-top: 50px; padding-top: 20px; border-top: 2px solid #333; text-align: center; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div class="logo">LUDOLOGY VAULT</div>
                        <h1>FACTURE</h1>
                        <p>Date: <?php echo date('d/m/Y', strtotime($order['Date'])); ?></p>
                    </div>
                    
                    <div class="company-info">
                        <p><strong>Ludology Vault SARL</strong></p>
                        <p>Tunis, Tunisia</p>
                        <p>contact@ludologyvault.tn</p>
                        <p>+216 XX XXX XXX</p>
                    </div>
                    
                    <div class="invoice-details">
                        <p><strong>Client:</strong> <?php echo htmlspecialchars($order['shipping_name']); ?></p>
                        <p><strong>Adresse:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?>, <?php echo htmlspecialchars($order['shipping_city']); ?> <?php echo htmlspecialchars($order['shipping_zip']); ?></p>
                        <p><strong>Numéro de commande:</strong> <?php echo htmlspecialchars($order['order_ref']); ?></p>
                        <p><strong>Date de commande:</strong> <?php echo date('d/m/Y H:i', strtotime($order['Date'])); ?></p>
                    </div>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($game['nom']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td><?php echo number_format($game['prix'], 2); ?> €</td>
                                <td><?php echo number_format($game['prix'] * $order['quantity'], 2); ?> €</td>
                            </tr>
                            <tr>
                                <td colspan="3">Frais de livraison</td>
                                <td>5.00 €</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="totals">
                        <div class="total-row">
                            <span>Sous-total: <?php echo number_format($game['prix'] * $order['quantity'], 2); ?> €</span>
                        </div>
                        <div class="total-row">
                            <span>Frais de livraison: 5.00 €</span>
                        </div>
                        <div class="total-row grand-total">
                            <span>TOTAL: <?php echo number_format($order['Total'], 2); ?> €</span>
                        </div>
                    </div>
                    
                    <div class="footer">
                        <p>Merci pour votre commande !</p>
                        <p>Cette facture est disponible dans votre espace client à tout moment.</p>
                    </div>
                </body>
                </html>
            `;
            
            // Open in new window for printing/download
            const win = window.open('', '_blank');
            win.document.write(invoiceHTML);
            win.document.close();
            win.focus();
            
            // Auto-print after a short delay
            setTimeout(() => {
                win.print();
            }, 500);
        }
        
        // Cancel order
        function cancelOrder() {
            if (confirm('Êtes-vous sûr de vouloir annuler cette commande?')) {
                const formData = new FormData();
                formData.append('action', 'cancel_order');
                formData.append('order_id', <?php echo $order['ID']; ?>);
                
                fetch('my-orders.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        alert('Commande annulée avec succès!');
                        window.location.reload();
                    } else {
                        alert('Erreur lors de l\'annulation de la commande.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de l\'annulation de la commande.');
                });
            }
        }
        
        // Review modal functions
        function openReviewModal() {
            const modal = document.getElementById('reviewModal');
            const content = document.getElementById('reviewContent');
            
            content.innerHTML = `
                <div style="font-family: 'VT323', monospace; font-size: 1rem;">
                    <p>Donnez votre avis sur cette commande :</p>
                    
                    <div style="margin: 1.5rem 0;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-gray);">Note:</label>
                        <div class="star-rating">
                            ${[1,2,3,4,5].map(i => `
                                <span class="star" data-rating="${i}" style="font-size: 2rem; cursor: pointer; color: var(--text-gray); margin-right: 0.5rem;">☆</span>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div style="margin: 1.5rem 0;">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-gray);">Commentaire:</label>
                        <textarea id="reviewComment" rows="4" style="width: 100%; padding: 0.8rem; background: var(--darker-bg); border: 2px solid var(--primary-green); color: var(--text-white); border-radius: 4px; font-family: 'VT323', monospace;"></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button onclick="submitReview()" style="padding: 0.8rem 1.5rem; background: var(--primary-green); border: none; color: var(--darker-bg); font-family: 'Press Start 2P', cursive; font-size: 0.6rem; cursor: pointer; border-radius: 4px;">
                            ENVOYER L'AVIS
                        </button>
                        <button onclick="closeReviewModal()" style="padding: 0.8rem 1.5rem; background: transparent; border: 2px solid var(--accent-pink); color: var(--accent-pink); font-family: 'Press Start 2P', cursive; font-size: 0.6rem; cursor: pointer; border-radius: 4px;">
                            ANNULER
                        </button>
                    </div>
                </div>
            `;
            
            modal.style.display = 'flex';
            
            // Initialize star rating
            const stars = content.querySelectorAll('.star');
            let selectedRating = 0;
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    selectedRating = parseInt(this.dataset.rating);
                    
                    // Update stars display
                    stars.forEach((s, i) => {
                        s.textContent = i < selectedRating ? '★' : '☆';
                        s.style.color = i < selectedRating ? '#ffcc00' : 'var(--text-gray)';
                    });
                });
                
                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.dataset.rating);
                    stars.forEach((s, i) => {
                        s.style.color = i < rating ? '#ffcc00' : 'var(--text-gray)';
                    });
                });
                
                star.addEventListener('mouseout', function() {
                    stars.forEach((s, i) => {
                        s.style.color = i < selectedRating ? '#ffcc00' : 'var(--text-gray)';
                    });
                });
            });
        }
        
        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
        }
        
        function submitReview() {
            const stars = document.querySelectorAll('.star');
            const comment = document.getElementById('reviewComment').value;
            let rating = 0;
            
            stars.forEach((star, i) => {
                if (star.textContent === '★') {
                    rating = i + 1;
                }
            });
            
            if (rating === 0) {
                alert('Veuillez sélectionner une note.');
                return;
            }
            
            // Here you would normally send the review to the server
            // For now, we'll just show a success message
            alert(`Merci pour votre avis! Note: ${rating}/5\n\n"${comment}"`);
            closeReviewModal();
        }
        
        // Add CSS for modal display
        const style = document.createElement('style');
        style.textContent = `
            .user-dropdown.show {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }
            
            .modal {
                display: none;
            }
        `;
        document.head.appendChild(style);
        
        // Auto-update timeline animation
        document.addEventListener('DOMContentLoaded', function() {
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    item.style.transition = 'all 0.5s ease-out';
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, index * 300);
            });
        });
    </script>
</body>
</html>