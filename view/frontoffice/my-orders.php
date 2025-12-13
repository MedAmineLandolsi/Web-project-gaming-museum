<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/JeuxController.php';
require_once '../../controller/CommandeController.php';

// Initialize controllers
$userController = new UserController();
$commandeC = new CommandeController();
$jeuxC = new JeuxController();

// Check if user is logged in
$isLoggedIn = $userController->isLoggedIn();
if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

// Get user info
$result = $userController->viewProfile($_SESSION['user_id']);
$user = $result['user'];
$username = $user['username'];
$profilePicture = $user['profile_picture_url'] ?? '';
$role = $user['role'];

// Get user's orders
$userOrders = $commandeC->getUserCommandes($_SESSION['user_id']);
$orderStats = $commandeC->getUserOrderCount($_SESSION['user_id']);

// Get detailed order information for each order
$detailedOrders = [];
foreach ($userOrders as $order) {
    $orderDetails = $commandeC->getOrderDetails($order['ID']);
    if ($orderDetails) {
        $detailedOrders[] = $orderDetails;
    }
}

// Check if there are any orders
$hasOrders = !empty($detailedOrders);

// Calculate total games purchased
$totalGames = 0;
$totalSpent = 0;
foreach ($detailedOrders as $order) {
    $totalSpent += $order['Total'] ?? 0;
    if (isset($order['items'])) {
        foreach ($order['items'] as $item) {
            $totalGames += $item['quantity'] ?? 1;
        }
    } else {
        $totalGames += $order['quantity'] ?? 1;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes - Ludology Vault</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        /* User menu styles - keep as is */
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
        
        /* Orders Page Styles */
        .orders-container {
            max-width: 1200px;
            margin: 100px auto 60px;
            padding: 0 20px;
        }
        
        .orders-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .orders-title {
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 1rem;
        }
        
        .orders-subtitle {
            font-size: 1rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
        
        /* Orders Stats */
        .orders-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card-order {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));
            border: 2px solid var(--primary-green);
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-card-order:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 255, 65, 0.3);
        }
        
        .stat-icon-order {
            font-size: 2rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .stat-number-order {
            font-size: 2rem;
            color: var(--primary-green);
            display: block;
            margin-bottom: 0.5rem;
            font-family: 'VT323', monospace;
        }
        
        .stat-label-order {
            font-size: 0.6rem;
            color: var(--text-gray);
            font-family: 'Press Start 2P', cursive;
        }
        
        /* Orders List */
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .order-card {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .order-card:hover {
            border-color: var(--primary-green);
            box-shadow: 0 5px 20px rgba(0, 255, 65, 0.2);
        }
        
        .order-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05), transparent);
            border-bottom: 2px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .order-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .order-ref {
            font-size: 0.9rem;
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
        }
        
        .order-date {
            font-size: 0.8rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
        
        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.5rem;
            font-family: 'Press Start 2P', cursive;
        }
        
        .status-pending {
            background: rgba(255, 149, 0, 0.2);
            border: 1px solid var(--warning-orange);
            color: var(--warning-orange);
        }
        
        .status-processing {
            background: rgba(0, 255, 65, 0.2);
            border: 1px solid var(--primary-green);
            color: var(--primary-green);
        }
        
        .status-completed {
            background: rgba(189, 0, 255, 0.2);
            border: 1px solid var(--secondary-purple);
            color: var(--secondary-purple);
        }
        
        .status-cancelled {
            background: rgba(255, 0, 85, 0.2);
            border: 1px solid var(--danger-red);
            color: var(--danger-red);
        }
        
        .status-shipped {
            background: rgba(0, 123, 255, 0.2);
            border: 1px solid #007bff;
            color: #007bff;
        }
        
        .status-delivered {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
        }
        
        .order-content {
            padding: 1.5rem;
        }
        
        .order-items {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0, 255, 65, 0.05);
            border-radius: 4px;
            border: 1px solid rgba(0, 255, 170, 0.2);
        }
        
        .item-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 4px;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--darker-bg);
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-details h4 {
            font-size: 0.9rem;
            color: var(--text-white);
            margin-bottom: 0.3rem;
        }
        
        .item-details p {
            font-size: 0.8rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            margin-bottom: 0.2rem;
        }
        
        .item-quantity {
            color: var(--primary-green);
            font-weight: bold;
        }
        
        .item-price {
            font-size: 0.9rem;
            color: var(--primary-green);
            font-family: 'VT323', monospace;
            text-align: right;
            min-width: 100px;
        }
        
        .item-total {
            font-size: 1rem;
            color: var(--primary-green);
            font-family: 'VT323', monospace;
            font-weight: bold;
            min-width: 100px;
            text-align: right;
        }
        
        .shipping-info {
            background: rgba(0, 255, 170, 0.05);
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            border-left: 3px solid var(--primary-green);
        }
        
        .shipping-info h4 {
            color: var(--primary-green);
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
            font-family: 'Press Start 2P', cursive;
        }
        
        .shipping-details {
            font-size: 0.8rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            line-height: 1.4;
        }
        
        .order-total {
            text-align: right;
            padding-top: 1.5rem;
            border-top: 2px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-total-label {
            font-size: 0.9rem;
            color: var(--text-gray);
        }
        
        .order-total-amount {
            font-size: 1.5rem;
            color: var(--primary-green);
            font-family: 'VT323', monospace;
            font-weight: bold;
        }
        
        /* Order meta info */
        .order-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--border-color);
            font-size: 0.8rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
        
        .payment-method {
            color: var(--primary-green);
        }
        
        /* Empty State */
        .orders-empty {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05), rgba(189, 0, 255, 0.05));
            border: 2px dashed var(--primary-green);
            border-radius: 8px;
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .empty-title {
            font-size: 1.5rem;
            color: var(--text-gray);
            margin-bottom: 1rem;
        }
        
        .empty-subtitle {
            font-size: 1rem;
            color: var(--text-light-gray);
            margin-bottom: 2rem;
            font-family: 'VT323', monospace;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .orders-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-info {
                width: 100%;
            }
            
            .order-item {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }
            
            .item-info {
                width: 100%;
            }
            
            .item-price, .item-total {
                text-align: left;
                min-width: auto;
            }
            
            .order-total {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .order-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .orders-stats {
                grid-template-columns: 1fr;
            }
            
            .item-info {
                flex-direction: column;
                align-items: flex-start;
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
                    <li><a href="my-orders.php" class="active">MES COMMANDES</a></li>
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
                                <div class="dropdown-header-title">MES COMMANDES</div>
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
                                        DÉCONNEXION
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

    <!-- Orders Container -->
    <main class="orders-container">
        <!-- Orders Header -->
        <header class="orders-header">
            <h1 class="orders-title">◄◄◄ MES COMMANDES ►►►</h1>
            <p class="orders-subtitle">Historique complet de vos achats sur Ludology Vault</p>
        </header>

        <!-- Orders Stats -->
        <div class="orders-stats">
            <div class="stat-card-order">
                <span class="stat-icon-order">🛒</span>
                <span class="stat-number-order"><?= $orderStats['order_count'] ?? 0 ?></span>
                <span class="stat-label-order">COMMANDES TOTALES</span>
            </div>
            
            <div class="stat-card-order">
                <span class="stat-icon-order">💰</span>
                <span class="stat-number-order"><?= number_format($totalSpent, 2) ?>€</span>
                <span class="stat-label-order">TOTAL DÉPENSÉ</span>
            </div>
            
            <div class="stat-card-order">
                <span class="stat-icon-order">⭐</span>
                <span class="stat-number-order">
                    <?php 
                    if (($orderStats['order_count'] ?? 0) >= 10) {
                        echo 'VIP';
                    } elseif (($orderStats['order_count'] ?? 0) >= 5) {
                        echo 'Régulier';
                    } else {
                        echo 'Membre';
                    }
                    ?>
                </span>
                <span class="stat-label-order">STATUT CLIENT</span>
            </div>
            
            <div class="stat-card-order">
                <span class="stat-icon-order">🎮</span>
                <span class="stat-number-order"><?= $totalGames ?></span>
                <span class="stat-label-order">JEUX ACHETÉS</span>
            </div>
        </div>

        <!-- Orders List -->
        <?php if ($hasOrders): ?>
            <div class="orders-list">
                <?php foreach ($detailedOrders as $order): 
                    // Determine status class
                    $statusClass = 'status-pending';
                    if (!empty($order['statut'])) {
                        switch ($order['statut']) {
                            case 'processing': $statusClass = 'status-processing'; break;
                            case 'completed': $statusClass = 'status-completed'; break;
                            case 'cancelled': $statusClass = 'status-cancelled'; break;
                            case 'shipped': $statusClass = 'status-shipped'; break;
                            case 'delivered': $statusClass = 'status-delivered'; break;
                            default: $statusClass = 'status-pending';
                        }
                    }
                    
                    // Get status display text
                    $statusText = strtoupper($order['statut'] ?? 'EN ATTENTE');
                    if ($statusText === 'PENDING') $statusText = 'EN ATTENTE';
                    if ($statusText === 'PROCESSING') $statusText = 'EN TRAITEMENT';
                    if ($statusText === 'COMPLETED') $statusText = 'TERMINÉE';
                    if ($statusText === 'CANCELLED') $statusText = 'ANNULÉE';
                    if ($statusText === 'SHIPPED') $statusText = 'EXPÉDIÉE';
                    if ($statusText === 'DELIVERED') $statusText = 'LIVRÉE';
                ?>
                    <div class="order-card">
                        <!-- Order Header -->
                        <div class="order-header">
                            <div class="order-info">
                                <?php if (!empty($order['order_ref'])): ?>
                                    <div class="order-ref">
                                        COMMANDE #<?= htmlspecialchars($order['order_ref']) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="order-ref">
                                        COMMANDE #<?= $order['ID'] ?>
                                    </div>
                                <?php endif; ?>
                                <div class="order-date">
                                    <?= date('d/m/Y H:i', strtotime($order['Date'])) ?>
                                </div>
                            </div>
                            <div class="order-status <?= $statusClass ?>">
                                <?= $statusText ?>
                            </div>
                        </div>

                        <!-- Order Content -->
                        <div class="order-content">
                            <div class="order-items">
                                <?php if (isset($order['items']) && !empty($order['items'])): ?>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div class="order-item">
                                            <div class="item-info">
                                                <div class="item-image">
                                                    <?php if (!empty($item['game_image'])): ?>
                                                        <img src="../../uploads/<?= htmlspecialchars($item['game_image']) ?>" alt="<?= htmlspecialchars($item['game_name'] ?? 'Game') ?>">
                                                    <?php else: ?>
                                                        🎮
                                                    <?php endif; ?>
                                                </div>
                                                <div class="item-details">
                                                    <h4><?= htmlspecialchars($item['game_name'] ?? 'Jeu inconnu') ?></h4>
                                                    <p><?= htmlspecialchars($item['game_category'] ?? 'Catégorie inconnue') ?></p>
                                                    <p class="item-quantity">Quantité: <?= $item['quantity'] ?? 1 ?></p>
                                                </div>
                                            </div>
                                            <div class="item-price">
                                                <?= number_format($item['prix'] ?? 0, 2) ?> €
                                            </div>
                                            <div class="item-total">
                                                <?= number_format($item['total'] ?? 0, 2) ?> €
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Fallback for single item orders -->
                                    <div class="order-item">
                                        <div class="item-info">
                                            <div class="item-image">
                                                <?php if (!empty($order['game_image'])): ?>
                                                    <img src="../../uploads/<?= htmlspecialchars($order['game_image']) ?>" alt="<?= htmlspecialchars($order['game_name'] ?? 'Game') ?>">
                                                <?php else: ?>
                                                    🎮
                                                <?php endif; ?>
                                            </div>
                                            <div class="item-details">
                                                <h4><?= htmlspecialchars($order['game_name'] ?? 'Jeu inconnu') ?></h4>
                                                <p><?= htmlspecialchars($order['game_category'] ?? 'Catégorie inconnue') ?></p>
                                                <p class="item-quantity">Quantité: <?= $order['quantity'] ?? 1 ?></p>
                                            </div>
                                        </div>
                                        <div class="item-price">
                                            <?= number_format($order['unit_price'] ?? 0, 2) ?> €
                                        </div>
                                        <div class="item-total">
                                            <?= number_format($order['Total'] ?? 0, 2) ?> €
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Shipping Info -->
                            <?php if (!empty($order['shipping_name'])): ?>
                            <div class="shipping-info">
                                <h4>📦 ADRESSE DE LIVRAISON</h4>
                                <div class="shipping-details">
                                    <strong><?= htmlspecialchars($order['shipping_name']) ?></strong><br>
                                    <?= htmlspecialchars($order['shipping_address'] ?? '') ?><br>
                                    <?= htmlspecialchars($order['shipping_zip'] ?? '') ?> <?= htmlspecialchars($order['shipping_city'] ?? '') ?><br>
                                    <?= htmlspecialchars($order['shipping_country'] ?? '') ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Order Total -->
                            <div class="order-total">
                                <div class="order-total-label">
                                    <strong>TOTAL DE LA COMMANDE:</strong>
                                </div>
                                <div class="order-total-amount">
                                    <?= number_format($order['Total'] ?? 0, 2) ?> €
                                </div>
                            </div>
                            
                            <!-- Order Meta Info -->
                            <div class="order-meta">
                                <?php if (!empty($order['payment_method'])): ?>
                                    <div class="payment-method">
                                        Paiement: <?= strtoupper($order['payment_method']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="order-id">
                                    ID: <?= $order['ID'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="orders-empty">
                <div class="empty-icon">🛒</div>
                <h2 class="empty-title">Aucune commande trouvée</h2>
                <p class="empty-subtitle">
                    Vous n'avez pas encore passé de commande. Parcourez notre collection de jeux et faites votre premier achat!
                </p>
                <a href="games.php" style="text-decoration: none;">
                    <button class="btn-auth btn-primary" style="padding: 1rem 2rem; font-size: 1rem;">
                        <span class="btn-icon">🎮</span> PARCOURIR LES JEUX
                    </button>
                </a>
            </div>
        <?php endif; ?>
    </main>

    <script>
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
        
        // Add hover effect to order cards
        document.addEventListener('DOMContentLoaded', function() {
            const orderCards = document.querySelectorAll('.order-card');
            orderCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>