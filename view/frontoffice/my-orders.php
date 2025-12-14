
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
    header('Location: login.php?redirect=my-orders.php');
    exit;
}

$result = $userController->viewProfile($_SESSION['user_id']);
$user = $result['user'];
$username = $user['username'];
$profilePicture = $user['profile_picture_url'] ?? '';
$role = $user['role'];

// Get user orders
$userOrders = $commandeC->getUserCommandes($_SESSION['user_id']);

// Get user order stats
$userOrderStats = $commandeC->getUserOrderCount($_SESSION['user_id']);

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'cancel_order' && isset($_POST['order_id'])) {
        $orderId = (int)$_POST['order_id'];
        $order = $commandeC->getCommandeById($orderId);
        
        if ($order && $order['user_id'] == $_SESSION['user_id']) {
            $result = $commandeC->updateCommande($orderId, [
                'statut' => 'cancelled'
            ]);
            
            if ($result['success']) {
                $_SESSION['order_message'] = "Commande annulée avec succès!";
            } else {
                $_SESSION['order_error'] = "Erreur lors de l'annulation de la commande.";
            }
        }
        
        header('Location: my-orders.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ludology Vault - Mes Commandes</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
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
            font-family: 'Press Start 2P', cursive;
        }
        
        .orders-subtitle {
            font-size: 1.2rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
        
        /* Order Stats */
        .order-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));
            border: 2px solid var(--primary-green);
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 255, 65, 0.3);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .stat-number {
            font-size: 1.5rem;
            color: var(--primary-green);
            display: block;
            margin-bottom: 0.5rem;
            font-family: 'VT323', monospace;
        }
        
        .stat-label {
            font-size: 0.6rem;
            color: var(--text-gray);
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
        }
        
        /* Orders Table */
        .orders-table-container {
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .table-header {
            display: grid;
            grid-template-columns: 100px 1fr 120px 120px 120px 120px;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 2px solid var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            color: var(--primary-green);
        }
        
        .order-row {
            display: grid;
            grid-template-columns: 100px 1fr 120px 120px 120px 120px;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            align-items: center;
        }
        
        .order-row:last-child {
            border-bottom: none;
        }
        
        .order-id {
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
        }
        
        .order-game {
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .order-date {
            font-family: 'VT323', monospace;
            color: var(--text-gray);
        }
        
        .order-total {
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-weight: bold;
        }
        
        .order-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
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
            font-weight: bold;
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
        
        .order-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.4rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .view-btn {
            background: var(--primary-green);
            color: var(--darker-bg);
        }
        
        .cancel-btn {
            background: var(--danger-red);
            color: white;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
        }
        
        .empty-orders {
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 8px;
        }
        
        .empty-title {
            font-size: 1.5rem;
            color: var(--text-gray);
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
        }
        
        .empty-message {
            color: var(--text-light-gray);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        /* Messages */
        .order-message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            text-align: center;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .order-success {
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
        }
        
        .order-error {
            background: rgba(255, 0, 110, 0.1);
            border: 2px solid var(--danger-red);
            color: var(--danger-red);
        }
        
        /* Responsive */
        @media (max-width: 900px) {
            .table-header,
            .order-row {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .table-header {
                display: none;
            }
            
            .order-cell {
                display: flex;
                justify-content: space-between;
            }
            
            .order-cell::before {
                content: attr(data-label);
                font-family: 'Press Start 2P', cursive;
                font-size: 0.5rem;
                color: var(--primary-green);
            }
        }
    </style>
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
                    <li><a href="my-orders.php" class="active">MES COMMANDES</a></li>
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
    <main class="orders-container">
        <header class="orders-header">
            <h1 class="orders-title">📋 MES COMMANDES</h1>
            <p class="orders-subtitle">Historique de vos achats chez Ludology Vault</p>
        </header>
        
        <?php if (isset($_SESSION['order_message'])): ?>
            <div class="order-message order-success">
                <?php echo htmlspecialchars($_SESSION['order_message']); unset($_SESSION['order_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['order_error'])): ?>
            <div class="order-message order-error">
                <?php echo htmlspecialchars($_SESSION['order_error']); unset($_SESSION['order_error']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Order Stats -->
        <div class="order-stats">
            <div class="stat-card">
                <span class="stat-icon">📦</span>
                <span class="stat-number"><?php echo $userOrderStats['order_count'] ?? 0; ?></span>
                <span class="stat-label">COMMANDES</span>
            </div>
            
            <div class="stat-card">
                <span class="stat-icon">💰</span>
                <span class="stat-number"><?php echo number_format($userOrderStats['total_spent'] ?? 0, 2); ?>€</span>
                <span class="stat-label">TOTAL DÉPENSÉ</span>
            </div>
            
            <div class="stat-card">
                <span class="stat-icon">🎮</span>
                <span class="stat-number"><?php echo $userOrderStats['games_count'] ?? 0; ?></span>
                <span class="stat-label">JEUX ACHETÉS</span>
            </div>
            
            <div class="stat-card">
                <span class="stat-icon">⭐</span>
                <span class="stat-number">
                    <?php 
                    if (($userOrderStats['order_count'] ?? 0) >= 10) {
                        echo 'VIP';
                    } elseif (($userOrderStats['order_count'] ?? 0) >= 5) {
                        echo 'Régulier';
                    } else {
                        echo 'Membre';
                    }
                    ?>
                </span>
                <span class="stat-label">STATUT</span>
            </div>
        </div>
        
        <!-- Orders Table -->
        <div class="orders-table-container">
            <?php if (!empty($userOrders)): ?>
                <div class="table-header">
                    <div>N° COMMANDE</div>
                    <div>JEU</div>
                    <div>DATE</div>
                    <div>QUANTITÉ</div>
                    <div>TOTAL</div>
                    <div>STATUT</div>
                    <div>ACTIONS</div>
                </div>
                
                <?php foreach ($userOrders as $order): ?>
                    <div class="order-row">
                        <div class="order-cell" data-label="N° COMMANDE">
                            <span class="order-id">#<?php echo str_pad($order['ID'], 4, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        
                        <div class="order-cell" data-label="JEU">
                            <span class="order-game">
                                <?php 
                                $game = $gamesC->getGameById($order['Produit_id']);
                                echo htmlspecialchars($game['nom'] ?? 'Produit inconnu'); 
                                ?>
                            </span>
                        </div>
                        
                        <div class="order-cell" data-label="DATE">
                            <span class="order-date"><?php echo date('d/m/Y', strtotime($order['Date'])); ?></span>
                        </div>
                        
                        <div class="order-cell" data-label="QUANTITÉ">
                            <span class="order-quantity"><?php echo $order['quantity']; ?></span>
                        </div>
                        
                        <div class="order-cell" data-label="TOTAL">
                            <span class="order-total"><?php echo number_format($order['Total'], 2); ?> €</span>
                        </div>
                        
                        <div class="order-cell" data-label="STATUT">
                            <?php
                            $statusClass = 'status-' . $order['statut'];
                            $statusText = $commandeC->getStatuses()[$order['statut']] ?? $order['statut'];
                            ?>
                            <span class="order-status <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($statusText); ?>
                            </span>
                        </div>
                        
                        <div class="order-cell" data-label="ACTIONS">
                            <div class="order-actions">
                                <button class="action-btn view-btn" 
                                        onclick="viewOrderDetails(<?php echo $order['ID']; ?>)">
                                    DÉTAILS
                                </button>
                                
                                <?php if (in_array($order['statut'], ['pending', 'processing'])): ?>
                                    <form method="POST" action="my-orders.php" 
                                          onsubmit="return confirm('Annuler cette commande?')"
                                          style="display: inline;">
                                        <input type="hidden" name="action" value="cancel_order">
                                        <input type="hidden" name="order_id" value="<?php echo $order['ID']; ?>">
                                        <button type="submit" class="action-btn cancel-btn">
                                            ANNULER
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-orders">
                    <h2 class="empty-title">AUCUNE COMMANDE TROUVÉE</h2>
                    <p class="empty-message">Vous n'avez pas encore passé de commande. Explorez notre collection pour commencer!</p>
                    <a href="games.php">
                        <button class="btn-auth">
                            <span class="btn-icon">🎮</span> EXPLORER LA COLLECTION
                        </button>
                    </a>
                </div>
            <?php endif; ?>
        </div>
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

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="orderDetailsContent"></div>
        </div>
    </div>

    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }
        
        .modal-content {
            background: var(--darker-bg);
            border: 2px solid var(--primary-green);
            border-radius: 8px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }
        
        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            color: var(--accent-pink);
            cursor: pointer;
        }
        
        .order-details {
            font-family: 'VT323', monospace;
        }
        
        .order-details h3 {
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0, 255, 65, 0.2);
        }
        
        .detail-label {
            color: var(--text-gray);
        }
        
        .detail-value {
            color: var(--text-white);
        }
    </style>

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
        
        function viewOrderDetails(orderId) {
            fetch(`../../controller/CommandeController.php?action=getDetails&id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const order = data.commande;
                        const user = data.user_info;
                        const produit = data.produit_info;
                        
                        const modalContent = document.getElementById('orderDetailsContent');
                        modalContent.innerHTML = `
                            <div class="order-details">
                                <h3>📋 DÉTAILS DE LA COMMANDE #${String(order.ID).padStart(4, '0')}</h3>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Référence:</span>
                                    <span class="detail-value">${order.order_ref}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Date:</span>
                                    <span class="detail-value">${new Date(order.Date).toLocaleDateString('fr-FR')}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Jeu:</span>
                                    <span class="detail-value">${produit.nom || 'N/A'}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Quantité:</span>
                                    <span class="detail-value">${order.quantity}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Prix unitaire:</span>
                                    <span class="detail-value">${produit.prix ? produit.prix.toFixed(2) + ' €' : 'N/A'}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Total:</span>
                                    <span class="detail-value">${parseFloat(order.Total).toFixed(2)} €</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Statut:</span>
                                    <span class="detail-value">${getStatusText(order.statut)}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Méthode de paiement:</span>
                                    <span class="detail-value">${order.payment_method || 'N/A'}</span>
                                </div>
                                
                                <h3 style="margin-top: 2rem;">🚚 LIVRAISON</h3>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Nom:</span>
                                    <span class="detail-value">${order.shipping_name || 'N/A'}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Adresse:</span>
                                    <span class="detail-value">${order.shipping_address || 'N/A'}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Ville:</span>
                                    <span class="detail-value">${order.shipping_city || 'N/A'}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Code postal:</span>
                                    <span class="detail-value">${order.shipping_zip || 'N/A'}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Téléphone:</span>
                                    <span class="detail-value">${order.shipping_phone || 'N/A'}</span>
                                </div>
                                
                                ${order.notes ? `
                                    <h3 style="margin-top: 2rem;">📝 NOTES</h3>
                                    <p style="color: var(--text-gray);">${order.notes}</p>
                                ` : ''}
                            </div>
                        `;
                        
                        document.getElementById('orderModal').style.display = 'flex';
                    }
                });
        }
        
        function getStatusText(status) {
            const statuses = {
                'pending': '🟡 En attente',
                'processing': '🔄 En traitement',
                'shipped': '🚚 Expédié',
                'delivered': '✅ Livré',
                'completed': '🏁 Terminé',
                'cancelled': '❌ Annulé'
            };
            return statuses[status] || status;
        }
        
        // Close modal
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('orderModal').style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
