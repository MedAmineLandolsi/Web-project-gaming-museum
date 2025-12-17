
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

// Check if user is logged in for checkout
if (!$isLoggedIn) {
    header('Location: login.php?redirect=checkout');
    exit();
}

// Get cart items from session
$cartItems = $_SESSION['cart'] ?? [];
$subtotal = 0;

// Calculate total and prepare cart items for order
$preparedCartItems = [];
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $preparedCartItems[] = [
        'game_id' => $item['game_id'],
        'name' => $item['name'],
        'price' => $item['price'],
        'quantity' => $item['quantity']
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required shipping fields
        $requiredFields = [
            'shipping_name' => 'Nom complet',
            'shipping_email' => 'Email',
            'shipping_address' => 'Adresse',
            'shipping_city' => 'Ville',
            'shipping_zip' => 'Code postal',
            'payment_method' => 'Méthode de paiement'
        ];
        
        $errors = [];
        $shippingData = [];
        
        foreach ($requiredFields as $field => $label) {
            if (empty($_POST[$field])) {
                $errors[] = "Le champ $label est requis.";
            } else {
                $shippingData[$field] = htmlspecialchars($_POST[$field]);
            }
        }
        
        // Get optional fields
        $optionalFields = ['shipping_phone', 'shipping_notes'];
        foreach ($optionalFields as $field) {
            $shippingData[$field] = $_POST[$field] ?? null;
        }
        
        // Get billing info if provided, otherwise use shipping info
        $billingFields = [
            'billing_name' => 'shipping_name',
            'billing_address' => 'shipping_address',
            'billing_city' => 'shipping_city',
            'billing_zip' => 'shipping_zip',
            'billing_country' => 'shipping_country'
        ];
        
        foreach ($billingFields as $billingField => $shippingField) {
            $shippingData[$billingField] = $_POST[$billingField] ?? $_POST[$shippingField] ?? null;
        }
        
        // Check if cart is not empty
        if (empty($cartItems)) {
            $errors[] = "Votre panier est vide.";
        }
        
        // If no errors, process the order
        if (empty($errors)) {
            // Prepare shipping data for CommandeController
            $shippingInfo = [
                'name' => $shippingData['shipping_name'],
                'email' => $shippingData['shipping_email'],
                'phone' => $shippingData['shipping_phone'],
                'address' => $shippingData['shipping_address'],
                'city' => $shippingData['shipping_city'],
                'zip' => $shippingData['shipping_zip'],
                'country' => $shippingData['shipping_country'] ?? 'Tunisia'
            ];
            
            $paymentMethod = $shippingData['payment_method'];
            
            // Use the correct method from CommandeController
            $result = $commandeController->createOrderFromCart(
                $_SESSION['user_id'],
                $preparedCartItems,
                $shippingInfo,
                $paymentMethod
            );
            
            if ($result['success']) {
                // Clear cart from session
                $_SESSION['cart'] = [];
                
                // Redirect to confirmation page
                $_SESSION['checkout_success'] = true;
                $_SESSION['order_refs'] = array_column($result['order_ids'], 'ref');
                header('Location: order_confirmation.php');
                exit();
            } else {
                $errorMessage = $result['error'] ?? 'Une erreur est survenue lors de la commande.';
            }
        } else {
            $errorMessage = implode('<br>', $errors);
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - LUDOLOGY VAULT</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    
    <!-- User Menu Styles (same as cart.php) -->
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
        
        /* Checkout specific styles */
        .main-content {
            max-width: 1200px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 3rem;
        }

        @media (max-width: 900px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }

        .checkout-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 1.5rem;
            color: var(--primary-green);
            margin-bottom: 2rem;
            text-align: center;
        }

        .section-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 0.9rem;
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

        .form-section {
            background: rgba(26, 26, 46, 0.8);
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-light-gray);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .form-label.required::after {
            content: ' *';
            color: var(--accent-pink);
        }

        .form-input {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
        }

        .form-input.error {
            border-color: var(--accent-pink);
        }

        .error-message {
            color: var(--accent-pink);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .payment-method {
            position: relative;
        }

        .payment-method input[type="radio"] {
            display: none;
        }

        .payment-method label {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method label:hover {
            background: rgba(0, 255, 65, 0.1);
            border-color: var(--primary-green);
        }

        .payment-method input[type="radio"]:checked + label {
            background: rgba(0, 255, 65, 0.15);
            border-color: var(--primary-green);
            color: var(--primary-green);
        }

        /* Order Summary */
        .order-summary {
            background: rgba(26, 26, 46, 0.8);
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            position: sticky;
            top: 120px;
            height: fit-content;
        }

        .summary-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 1rem;
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .summary-items {
            margin-bottom: 2rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-total {
            font-family: 'Press Start 2P', cursive;
            font-size: 1.2rem;
            color: var(--secondary-purple);
            padding: 1rem 0;
            border-top: 2px solid var(--border-color);
            margin-top: 1rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            border: none;
            border-radius: 8px;
            color: var(--darker-bg);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1.5rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

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

        .alert-error {
            background: rgba(255, 0, 110, 0.1);
            border-color: var(--accent-pink);
            color: var(--accent-pink);
        }

        .alert-success {
            background: rgba(0, 255, 65, 0.1);
            border-color: var(--primary-green);
            color: var(--primary-green);
        }

        .empty-cart {
            text-align: center;
            padding: 3rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .empty-cart a {
            color: var(--primary-green);
            text-decoration: none;
        }

        .empty-cart a:hover {
            text-decoration: underline;
        }
        
        .same-as-shipping {
            margin-top: 1rem;
            font-family: 'VT323', monospace;
            font-size: 1rem;
            color: var(--text-light-gray);
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
                    <div class="logo-placeholder">🎮</div>
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

    <main class="main-content">
        <h1 class="checkout-title">CHECKOUT - FINALISATION DE COMMANDE</h1>

        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <p>Votre panier est vide.</p>
                <p><a href="games.php">← Retourner à la boutique</a></p>
            </div>
        <?php else: ?>
            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-error">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="checkout.php" class="checkout-container">
                <!-- Left Column: Shipping and Payment Info -->
                <div class="checkout-left">
                    <!-- Shipping Information -->
                    <div class="form-section">
                        <h2 class="section-title">INFORMATIONS DE LIVRAISON</h2>
                        
                        <div class="form-group">
                            <label for="shipping_name" class="form-label required">Nom complet</label>
                            <input type="text" 
                                   id="shipping_name" 
                                   name="shipping_name" 
                                   class="form-input"
                                   value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="shipping_email" class="form-label required">Email</label>
                            <input type="email" 
                                   id="shipping_email" 
                                   name="shipping_email" 
                                   class="form-input"
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="shipping_address" class="form-label required">Adresse</label>
                            <input type="text" 
                                   id="shipping_address" 
                                   name="shipping_address" 
                                   class="form-input"
                                   placeholder="Numéro, rue, appartement..."
                                   required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="shipping_city" class="form-label required">Ville</label>
                                <input type="text" 
                                       id="shipping_city" 
                                       name="shipping_city" 
                                       class="form-input"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="shipping_zip" class="form-label required">Code postal</label>
                                <input type="text" 
                                       id="shipping_zip" 
                                       name="shipping_zip" 
                                       class="form-input"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="shipping_phone" class="form-label">Téléphone</label>
                            <input type="tel" 
                                   id="shipping_phone" 
                                   name="shipping_phone" 
                                   class="form-input"
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="shipping_country" class="form-label">Pays</label>
                            <input type="text" 
                                   id="shipping_country" 
                                   name="shipping_country" 
                                   class="form-input"
                                   value="Tunisia">
                        </div>

                        <div class="form-group">
                            <label for="shipping_notes" class="form-label">Notes de livraison (optionnel)</label>
                            <textarea id="shipping_notes" 
                                      name="shipping_notes" 
                                      class="form-input" 
                                      rows="3"
                                      placeholder="Instructions spéciales, code d'accès, etc."></textarea>
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="form-section">
                        <h2 class="section-title">INFORMATIONS DE FACTURATION</h2>
                        
                        <div class="same-as-shipping">
                            <input type="checkbox" id="same_as_shipping" checked>
                            <label for="same_as_shipping">Identique aux informations de livraison</label>
                        </div>
                        
                        <div id="billing-fields" style="display: none;">
                            <div class="form-group">
                                <label for="billing_name" class="form-label">Nom pour la facturation</label>
                                <input type="text" 
                                       id="billing_name" 
                                       name="billing_name" 
                                       class="form-input"
                                       value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="billing_address" class="form-label">Adresse de facturation</label>
                                <input type="text" 
                                       id="billing_address" 
                                       name="billing_address" 
                                       class="form-input">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="billing_city" class="form-label">Ville</label>
                                    <input type="text" 
                                           id="billing_city" 
                                           name="billing_city" 
                                           class="form-input">
                                </div>

                                <div class="form-group">
                                    <label for="billing_zip" class="form-label">Code postal</label>
                                    <input type="text" 
                                           id="billing_zip" 
                                           name="billing_zip" 
                                           class="form-input">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="billing_country" class="form-label">Pays</label>
                                <input type="text" 
                                       id="billing_country" 
                                       name="billing_country" 
                                       class="form-input"
                                       value="Tunisia">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="form-section">
                        <h2 class="section-title">INFORMATIONS DE PAIEMENT</h2>
                        
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="payment_cash" name="payment_method" value="cash" checked required>
                                <label for="payment_cash">
                                    <span>💰</span> Paiement à la livraison
                                </label>
                            </div>
                            
                            <div class="payment-method">
                                <input type="radio" id="payment_card" name="payment_method" value="card" required>
                                <label for="payment_card">
                                    <span>💳</span> Carte bancaire
                                </label>
                            </div>
                            
                            <div class="payment-method">
                                <input type="radio" id="payment_transfer" name="payment_method" value="transfer" required>
                                <label for="payment_transfer">
                                    <span>🤝</span> Virement bancaire
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="order-summary">
                    <h2 class="summary-title">RÉSUMÉ DE LA COMMANDE</h2>
                    
                    <div class="summary-items">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="summary-item">
                            <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                            <span><?php echo number_format($item['price'] * $item['quantity'], 2); ?> €</span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-total">
                        <span>TOTAL:</span>
                        <span><?php echo number_format($subtotal, 2); ?> €</span>
                    </div>

                    <button type="submit" class="submit-btn">
                        FINALISER LA COMMANDE
                    </button>

                    <p style="text-align: center; margin-top: 1rem; color: var(--text-gray); font-size: 0.8rem;">
                        En finalisant votre commande, vous acceptez nos 
                        <a href="#" style="color: var(--primary-green);">conditions générales de vente</a>.
                    </p>
                </div>
            </form>
        <?php endif; ?>
    </main>

    <script>
        // Toggle billing fields
        document.addEventListener('DOMContentLoaded', function() {
            const sameAsShippingCheckbox = document.getElementById('same_as_shipping');
            const billingFields = document.getElementById('billing-fields');
            
            sameAsShippingCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    billingFields.style.display = 'none';
                } else {
                    billingFields.style.display = 'block';
                }
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
        });
    </script>
</body>
</html>
