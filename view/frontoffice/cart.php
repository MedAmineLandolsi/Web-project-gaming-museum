
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

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_to_cart':
            if (isset($_POST['game_id'])) {
                $gameId = (int)$_POST['game_id'];
                $quantity = (int)($_POST['quantity'] ?? 1);
                
                // Check if game exists and has stock
                $game = $gamesC->getGameById($gameId);
                
                if ($game && $game['stock'] > 0) {
                    // Check if game already in cart
                    $found = false;
                    foreach ($_SESSION['cart'] as &$item) {
                        if ($item['game_id'] == $gameId) {
                            $item['quantity'] += $quantity;
                            $item['quantity'] = min($item['quantity'], $game['stock']);
                            $found = true;
                            break;
                        }
                    }
                    
                    if (!$found) {
                        $_SESSION['cart'][] = [
                            'game_id' => $gameId,
                            'quantity' => min($quantity, $game['stock']),
                            'price' => $game['prix'],
                            'name' => $game['nom'],
                            'image' => $game['image'] ?? null
                        ];
                    }
                    
                    $_SESSION['cart_success'] = "Jeu ajouté au panier avec succès!";
                } else {
                    $_SESSION['cart_error'] = "Ce jeu n'est plus disponible en stock.";
                }
            }
            break;
            
        case 'update_quantity':
            if (isset($_POST['index']) && isset($_POST['quantity'])) {
                $index = (int)$_POST['index'];
                $quantity = (int)$_POST['quantity'];
                
                if (isset($_SESSION['cart'][$index])) {
                    // Check stock
                    $game = $gamesC->getGameById($_SESSION['cart'][$index]['game_id']);
                    if ($game && $quantity <= $game['stock'] && $quantity > 0) {
                        $_SESSION['cart'][$index]['quantity'] = $quantity;
                    }
                }
            }
            break;
            
        case 'remove_from_cart':
            if (isset($_POST['index'])) {
                $index = (int)$_POST['index'];
                if (isset($_SESSION['cart'][$index])) {
                    array_splice($_SESSION['cart'], $index, 1);
                }
            }
            break;
            
        case 'clear_cart':
            $_SESSION['cart'] = [];
            break;
            
        case 'checkout':
            if ($isLoggedIn && !empty($_SESSION['cart'])) {
                // Process checkout
                header('Location: checkout.php');
                exit;
            } elseif (!$isLoggedIn) {
                $_SESSION['cart_error'] = "Veuillez vous connecter pour finaliser la commande.";
                header('Location: login.php?redirect=cart.php');
                exit;
            } else {
                $_SESSION['cart_error'] = "Votre panier est vide.";
            }
            break;
    }
    
    header('Location: cart.php');
    exit;
}

// Calculate cart totals
$cartItems = $_SESSION['cart'] ?? [];
$subtotal = 0;
$taxRate = 0.20; // 20% TVA
$shipping = count($cartItems) > 0 ? 5.00 : 0; // 5€ shipping

foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$tax = $subtotal * $taxRate;
$total = $subtotal + $tax + $shipping;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ludology Vault - Panier</title>
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
        .cart-container {
            max-width: 1200px;
            margin: 100px auto 60px;
            padding: 0 20px;
        }
        
        .cart-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .cart-title {
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
        }
        
        .cart-subtitle {
            font-size: 1.2rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
        
        /* Cart Layout */
        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
        }
        
        @media (max-width: 900px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }
        }
        
        /* Cart Items */
        .cart-items {
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            align-items: center;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-image {
            width: 100px;
            height: 100px;
            border-radius: 6px;
            overflow: hidden;
            border: 2px solid var(--primary-green);
        }
        
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-item-details {
            flex: 1;
        }
        
        .cart-item-name {
            font-size: 1.1rem;
            color: var(--text-white);
            margin-bottom: 0.5rem;
            font-family: 'Press Start 2P', cursive;
        }
        
        .cart-item-price {
            font-size: 1rem;
            color: var(--primary-green);
            font-family: 'VT323', monospace;
        }
        
        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            background: var(--primary-green);
            border: none;
            color: var(--darker-bg);
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            padding: 0.5rem;
            background: var(--darker-bg);
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .remove-btn {
            background: var(--danger-red);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
        }
        
        /* Cart Summary */
        .cart-summary {
            background: rgba(189, 0, 255, 0.05);
            border: 2px solid var(--secondary-purple);
            border-radius: 8px;
            padding: 1.5rem;
            position: sticky;
            top: 100px;
        }
        
        .summary-title {
            font-size: 1rem;
            color: var(--secondary-purple);
            margin-bottom: 1.5rem;
            font-family: 'Press Start 2P', cursive;
            text-align: center;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(189, 0, 255, 0.2);
        }
        
        .summary-label {
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .summary-value {
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .summary-total {
            border-top: 2px solid var(--secondary-purple);
            margin-top: 1rem;
            padding-top: 1rem;
            font-weight: bold;
        }
        
        .summary-total .summary-label {
            color: var(--secondary-purple);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
        }
        
        .summary-total .summary-value {
            color: var(--secondary-purple);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
        }
        
        .checkout-btn {
            width: 100%;
            margin-top: 2rem;
            padding: 1rem;
            background: linear-gradient(135deg, var(--secondary-purple), var(--accent-pink));
            border: none;
            color: white;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(189, 0, 255, 0.3);
        }
        
        .checkout-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .empty-cart {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 8px;
        }
        
        .empty-cart-title {
            font-size: 1.5rem;
            color: var(--text-gray);
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
        }
        
        .empty-cart-message {
            color: var(--text-light-gray);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--border-color);
        }
        
        .continue-shopping {
            text-decoration: none;
        }
        
        .clear-cart-btn {
            background: var(--danger-red);
            border: 2px solid var(--danger-red);
            color: white;
            padding: 0.8rem 1.5rem;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            border-radius: 4px;
        }
        
        /* Messages */
        .cart-message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            text-align: center;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .cart-success {
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
        }
        
        .cart-error {
            background: rgba(255, 0, 110, 0.1);
            border: 2px solid var(--danger-red);
            color: var(--danger-red);
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
                                <li class="dropdown-menu-item">
                                    <a href="../../../gaming_museum/view/frontoffice/index.php" class="dropdown-menu-link">
                                        <span class="dropdown-icon-left">🎮</span>
                                        gaming museum
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
    <main class="cart-container">
        <header class="cart-header">
            <h1 class="cart-title">🛒 PANIER D'ACHAT</h1>
            <p class="cart-subtitle">Votre collection en attente</p>
        </header>
        
        <?php if (isset($_SESSION['cart_success'])): ?>
            <div class="cart-message cart-success">
                <?php echo htmlspecialchars($_SESSION['cart_success']); unset($_SESSION['cart_success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['cart_error'])): ?>
            <div class="cart-message cart-error">
                <?php echo htmlspecialchars($_SESSION['cart_error']); unset($_SESSION['cart_error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <h2 class="empty-cart-title">VOTRE PANIER EST VIDE</h2>
                <p class="empty-cart-message">Commencez votre collection en ajoutant des jeux à votre panier!</p>
                <a href="games.php" class="continue-shopping">
                    <button class="btn-auth">
                        <span class="btn-icon">🎮</span> EXPLORER LA COLLECTION
                    </button>
                </a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <!-- Cart Items -->
                <div class="cart-items">
                    <?php foreach ($cartItems as $index => $item): ?>
                        <?php $game = $gamesC->getGameById($item['game_id']); ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <?php if ($item['image'] && file_exists("../../uploads/" . $item['image'])): ?>
                                    <img src="../../uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple)); display: flex; align-items: center; justify-content: center; color: white; font-family: 'VT323', monospace;">
                                        <?php echo substr(htmlspecialchars($item['name']), 0, 3); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="cart-item-details">
                                <h3 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="cart-item-price"><?php echo number_format($item['price'], 2); ?> €</div>
                                <div style="color: var(--text-gray); font-family: 'VT323', monospace; font-size: 0.9rem;">
                                    Stock disponible: <?php echo $game['stock']; ?>
                                </div>
                            </div>
                            
                            <div class="cart-item-controls">
                                <form method="POST" action="cart.php" class="quantity-control">
                                    <input type="hidden" name="action" value="update_quantity">
                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                    
                                    <button type="submit" name="quantity" value="<?php echo max(1, $item['quantity'] - 1); ?>" 
                                            class="quantity-btn">-</button>
                                    
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="<?php echo $game['stock']; ?>" 
                                           class="quantity-input" onchange="this.form.submit()">
                                           
                                    <button type="submit" name="quantity" value="<?php echo min($game['stock'], $item['quantity'] + 1); ?>" 
                                            class="quantity-btn">+</button>
                                </form>
                                
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="action" value="remove_from_cart">
                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                    <button type="submit" class="remove-btn">SUPPRIMER</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="cart-actions">
                        <a href="games.php" class="continue-shopping">
                            <button class="btn-auth">
                                <span class="btn-icon">←</span> CONTINUER LES ACHATS
                            </button>
                        </a>
                        
                        <form method="POST" action="cart.php" onsubmit="return confirm('Vider tout le panier?')">
                            <input type="hidden" name="action" value="clear_cart">
                            <button type="submit" class="clear-cart-btn">VIDER LE PANIER</button>
                        </form>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="cart-summary">
                    <h3 class="summary-title">RÉSUMÉ DE LA COMMANDE</h3>
                    
                    <div class="summary-row">
                        <span class="summary-label">Sous-total</span>
                        <span class="summary-value"><?php echo number_format($subtotal, 2); ?> €</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Frais de livraison</span>
                        <span class="summary-value"><?php echo number_format($shipping, 2); ?> €</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">TVA (20%)</span>
                        <span class="summary-value"><?php echo number_format($tax, 2); ?> €</span>
                    </div>
                    
                    <div class="summary-row summary-total">
                        <span class="summary-label">TOTAL</span>
                        <span class="summary-value"><?php echo number_format($total, 2); ?> €</span>
                    </div>
                    
                    <?php if ($isLoggedIn): ?>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="action" value="checkout">
                            <button type="submit" class="checkout-btn">PASSER LA COMMANDE →</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php?redirect=cart.php">
                            <button class="checkout-btn">SE CONNECTER POUR COMMANDER</button>
                        </a>
                    <?php endif; ?>
                    
                    <div style="text-align: center; margin-top: 1rem; font-family: 'VT323', monospace; color: var(--text-gray); font-size: 0.9rem;">
                        ✅ Paiement sécurisé<br>
                        🔄 Retour sous 30 jours<br>
                        🚚 Livraison sous 3-5 jours
                    </div>
                </div>
            </div>
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

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <span>▲</span>
    </button>

    <script src="script.js"></script>
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
    </script>
</body>
</html>
