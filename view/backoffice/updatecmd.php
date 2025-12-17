<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/CommandeController.php';
require_once '../../model/Commande.php';

// Check authentication
$controller = new UserController();
if (!$controller->isLoggedIn() || !$controller->isAdmin()) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$currentUser = $controller->viewProfile($_SESSION['user_id']);
$user = $currentUser['user'];

$error = "";
$success = "";
$commandeController = new CommandeController();

// 1 — Check if ID is provided
if (!isset($_GET['id'])) {
    die("ID de la commande non fourni !");
}

$id = $_GET['id'];

// 2 — Fetch order using controller method
$order = $commandeController->getCommandeById($id);

if (!$order) {
    die("Commande introuvable !");
}

// Create Commande object for display
$commandeObj = Commande::createFromArray($order);

// 3 — If form submitted → update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare order data
    $orderData = [
        'Produit_id' => $_POST['produit_id'],
        'Total' => $_POST['total'],
        'quantity' => $_POST['quantity'],
        'Date' => $_POST['date'],
        'user_id' => $_POST['user_id'],
        'order_ref' => $_POST['order_ref'],
        'statut' => $_POST['statut'],
        'shipping_name' => $_POST['shipping_name'],
        'shipping_email' => $_POST['shipping_email'],
        'shipping_phone' => $_POST['shipping_phone'],
        'shipping_address' => $_POST['shipping_address'],
        'shipping_city' => $_POST['shipping_city'],
        'shipping_zip' => $_POST['shipping_zip'],
        'shipping_country' => $_POST['shipping_country'],
        'billing_name' => $_POST['billing_name'],
        'billing_address' => $_POST['billing_address'],
        'billing_city' => $_POST['billing_city'],
        'billing_zip' => $_POST['billing_zip'],
        'billing_country' => $_POST['billing_country'],
        'payment_method' => $_POST['payment_method'],
        'notes' => $_POST['notes']
    ];

    $updatedOrder = Commande::createFromArray($orderData);
    
    // Validate before updating
    $validationErrors = $updatedOrder->validate();
    if (empty($validationErrors)) {
        $result = $commandeController->updateCommande($id, $orderData);
        if ($result['success']) {
            $success = "Commande mise à jour avec succès !";
            // Refresh the order data
            $order = $commandeController->getCommandeById($id);
            $commandeObj = Commande::createFromArray($order);
        } else {
            $error = $result['error'] ?? "Erreur lors de la mise à jour";
        }
    } else {
        $error = implode("<br>", $validationErrors);
    }
}

$current_page = 'updatecmd.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Commande - Ludology Vault</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .vertical-form th {
            width: 200px;
            text-align: left;
            padding: 1rem;
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
        }
        
        .vertical-form td {
            padding: 1rem;
        }
        
        .search-input, select.search-input {
            width: 100%;
            padding: 0.8rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        textarea.search-input {
            min-height: 100px;
            resize: vertical;
        }
        
        .success-message {
            background: rgba(0, 255, 65, 0.2);
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            padding: 1rem;
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
        }
        
        .error-message {
            background: rgba(255, 0, 85, 0.2);
            border: 2px solid var(--danger-red);
            color: var(--danger-red);
            padding: 1rem;
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-family: 'VT323', monospace;
            margin-right: 10px;
            margin-bottom: 5px;
        }
        
        .status-pending { background-color: rgba(255, 193, 7, 0.2); color: #ffc107; border: 1px solid #ffc107; }
        .status-processing { background-color: rgba(33, 150, 243, 0.2); color: #2196f3; border: 1px solid #2196f3; }
        .status-shipped { background-color: rgba(76, 175, 80, 0.2); color: #4caf50; border: 1px solid #4caf50; }
        .status-delivered { background-color: rgba(76, 175, 80, 0.4); color: #2e7d32; border: 1px solid #2e7d32; }
        .status-completed { background-color: rgba(156, 39, 176, 0.2); color: #9c27b0; border: 1px solid #9c27b0; }
        .status-cancelled { background-color: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--border-color);
        }
        
        .btn-update {
            flex: 1;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            border: none;
            color: var(--darker-bg);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-update:hover {
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.6);
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            flex: 1;
            padding: 1rem;
            background: transparent;
            border: 2px solid var(--accent-pink);
            color: var(--accent-pink);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-cancel:hover {
            background: rgba(255, 0, 110, 0.1);
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.3);
        }
        
        .two-column-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-section {
            background: rgba(0, 0, 0, 0.3);
            padding: 1.5rem;
            border-radius: 8px;
            border: 2px solid var(--border-color);
        }
        
        .form-section-title {
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .order-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .summary-item {
            text-align: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        
        .summary-label {
            color: var(--text-gray);
            font-size: 0.8rem;
            font-family: 'VT323', monospace;
            margin-bottom: 0.5rem;
        }
        
        .summary-value {
            color: var(--text-white);
            font-size: 1.2rem;
            font-family: 'Press Start 2P', cursive;
        }
        
        .status-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .status-option {
            flex: 1;
            min-width: 120px;
        }
        
        .status-option input[type="radio"] {
            display: none;
        }
        
        .status-option label {
            display: block;
            padding: 10px;
            text-align: center;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .status-option input[type="radio"]:checked + label {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.5);
            border-color: var(--primary-green);
        }
        
        .payment-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .info-bubble {
            display: inline-block;
            background: rgba(0, 255, 65, 0.1);
            border: 1px solid var(--primary-green);
            color: var(--primary-green);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-family: 'VT323', monospace;
            margin-top: 0.5rem;
        }
        
        .user-info {
            background: rgba(33, 150, 243, 0.1);
            border: 1px solid #2196f3;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .product-info {
            background: rgba(156, 39, 176, 0.1);
            border: 1px solid #9c27b0;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <div class="logo-box">[LOGO]</div>
                <div class="admin-title">
                    <h2>LUDOLOGY VAULT</h2>
                    <span class="admin-badge">ADMIN PANEL</span>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                    <a href="dashboard.php">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                    </a>
                </li>
                <li class="nav-item <?php echo in_array($current_page, ['addgame.php', 'adddgame.php', 'updategame.php']) ? 'active' : ''; ?>">
                    <a href="addgame.php">
                        <span class="nav-icon">🎮</span>
                        <span class="nav-text">JEUX</span>
                    </a>
                </li>
                <li class="nav-item <?php echo in_array($current_page, ['commande.php', 'addcommande.php', 'updatecmd.php']) ? 'active' : ''; ?>">
                    <a href="commande.php">
                        <span class="nav-icon">🛒</span>
                        <span class="nav-text">COMMANDES</span>
                    </a>
                </li>
                
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar" style="position: relative; border-radius: 50%;">
                    <?php if ($user['profile_picture_url'] && file_exists("../../uploads/" . $user['profile_picture_url'])): ?>
                        <img src="../../uploads/<?php echo htmlspecialchars($user['profile_picture_url']); ?>" alt="Admin" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                    <?php endif; ?>
                    <div class="admin-profile-indicator"></div>
                </div>
                <div class="admin-info">
                    <span class="admin-name"><?php echo htmlspecialchars($user['username']); ?></span>
                    <span class="admin-role">Super Admin</span>
                </div>
            </div>
            <form method="POST" action="../../controller/user_controller.php" class="logout-form">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="logout-btn" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter?');">
                    <span>🚪</span> DÉCONNEXION
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- TOP BAR -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">◄ MODIFIER LA COMMANDE ►</h1>
            </div>
            <div class="top-bar-right">
                <a href="commande.php" style="text-decoration: none;">
                    <button class="btn-view-site">← RETOUR AUX COMMANDES</button>
                </a>
            </div>
        </header>

        <!-- UPDATE FORM -->
        <section class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">◄ MODIFICATION COMMANDE #<?= $order['ID'] ?> ►</h3>
                <div style="display: flex; gap: 10px; margin-left: auto;">
                    <span class="status-badge status-<?= $commandeObj->getStatut() ?>">
                        <?= Commande::getStatusText($commandeObj->getStatut()) ?>
                    </span>
                    <span class="status-badge" style="background-color: rgba(33, 150, 243, 0.2); color: #2196f3; border: 1px solid #2196f3;">
                        <?= strtoupper($commandeObj->getPaymentMethod() ?? 'Non spécifié') ?>
                    </span>
                </div>
            </div>

            <div class="card-content">
                <?php if ($success): ?>
                    <div class="success-message">✔ <?= $success ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="error-message">⚠ <?= $error ?></div>
                <?php endif; ?>

                <!-- User & Product Info -->
                <div class="two-column-form">
                    <?php if ($order['username']): ?>
                    <div class="user-info">
                        <h4 style="color: #2196f3; margin-bottom: 0.5rem;">👤 CLIENT</h4>
                        <p style="margin: 0;">
                            <strong>Nom:</strong> <?= htmlspecialchars($order['username']) ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? 'N/A') ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($order['produit_nom']): ?>
                    <div class="product-info">
                        <h4 style="color: #9c27b0; margin-bottom: 0.5rem;">🎮 PRODUIT</h4>
                        <p style="margin: 0;">
                            <strong>Nom:</strong> <?= htmlspecialchars($order['produit_nom']) ?><br>
                            <strong>Prix unitaire:</strong> <?= number_format($order['produit_prix'] ?? 0, 2) ?> €
                        </p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="summary-item">
                        <div class="summary-label">RÉFÉRENCE</div>
                        <div class="summary-value"><?= $commandeObj->getOrderRef() ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">MONTANT TOTAL</div>
                        <div class="summary-value"><?= $commandeObj->getFormattedTotal() ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">QUANTITÉ</div>
                        <div class="summary-value"><?= $commandeObj->getQuantity() ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">DATE</div>
                        <div class="summary-value"><?= $commandeObj->getFormattedDate('d/m/Y') ?></div>
                    </div>
                </div>

                <form method="POST">
                    <div class="two-column-form">
                        <!-- Order Information -->
                        <div class="form-section">
                            <h4 class="form-section-title">📦 INFORMATIONS COMMANDE</h4>
                            <table class="data-table vertical-form">
                                <tbody>
                                    <tr>
                                        <th>PRODUIT ID *</th>
                                        <td>
                                            <input type="number" 
                                                   name="produit_id" 
                                                   value="<?= htmlspecialchars($commandeObj->getProduitId()) ?>" 
                                                   class="search-input" 
                                                   required
                                                   min="1">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>TOTAL (€) *</th>
                                        <td>
                                            <input type="number" 
                                                   step="0.01" 
                                                   name="total" 
                                                   value="<?= htmlspecialchars($commandeObj->getTotal()) ?>" 
                                                   class="search-input" 
                                                   required
                                                   min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>QUANTITÉ *</th>
                                        <td>
                                            <input type="number" 
                                                   name="quantity" 
                                                   value="<?= htmlspecialchars($commandeObj->getQuantity()) ?>" 
                                                   class="search-input" 
                                                   required
                                                   min="1">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>DATE *</th>
                                        <td>
                                            <input type="datetime-local" 
                                                   name="date" 
                                                   value="<?= date('Y-m-d\TH:i', strtotime($commandeObj->getDate())) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>USER ID</th>
                                        <td>
                                            <input type="number" 
                                                   name="user_id" 
                                                   value="<?= htmlspecialchars($commandeObj->getUserId()) ?>" 
                                                   class="search-input"
                                                   min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>RÉFÉRENCE *</th>
                                        <td>
                                            <input type="text" 
                                                   name="order_ref" 
                                                   value="<?= htmlspecialchars($commandeObj->getOrderRef()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Status & Payment -->
                        <div class="form-section">
                            <h4 class="form-section-title">📋 STATUT & PAIEMENT</h4>
                            <table class="data-table vertical-form">
                                <tbody>
                                    <tr>
                                        <th>STATUT</th>
                                        <td>
                                            <div class="status-options">
                                                <?php foreach (Commande::getAllStatuses() as $status => $label): ?>
                                                <div class="status-option">
                                                    <input type="radio" 
                                                           name="statut" 
                                                           value="<?= $status ?>" 
                                                           id="status_<?= $status ?>"
                                                           <?= $commandeObj->getStatut() == $status ? 'checked' : '' ?>>
                                                    <label for="status_<?= $status ?>" class="status-badge status-<?= $status ?>">
                                                        <?= $label ?>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>MÉTHODE PAIEMENT</th>
                                        <td>
                                            <div class="payment-options">
                                                <?php foreach (Commande::getAllPaymentMethods() as $method => $label): ?>
                                                <div class="status-option">
                                                    <input type="radio" 
                                                           name="payment_method" 
                                                           value="<?= $method ?>" 
                                                           id="payment_<?= $method ?>"
                                                           <?= $commandeObj->getPaymentMethod() == $method ? 'checked' : '' ?>>
                                                    <label for="payment_<?= $method ?>">
                                                        <?= $label ?>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>NOTES</th>
                                        <td>
                                            <textarea name="notes" 
                                                      class="search-input" 
                                                      rows="3"><?= htmlspecialchars($commandeObj->getNotes()) ?></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Shipping & Billing Information -->
                    <div class="two-column-form">
                        <!-- Shipping Information -->
                        <div class="form-section">
                            <h4 class="form-section-title">🚚 ADRESSE LIVRAISON</h4>
                            <table class="data-table vertical-form">
                                <tbody>
                                    <tr>
                                        <th>NOM *</th>
                                        <td>
                                            <input type="text" 
                                                   name="shipping_name" 
                                                   value="<?= htmlspecialchars($commandeObj->getShippingName()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>EMAIL</th>
                                        <td>
                                            <input type="email" 
                                                   name="shipping_email" 
                                                   value="<?= htmlspecialchars($commandeObj->getShippingEmail()) ?>" 
                                                   class="search-input">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>TÉLÉPHONE</th>
                                        <td>
                                            <input type="text" 
                                                   name="shipping_phone" 
                                                   value="<?= htmlspecialchars($commandeObj->getShippingPhone()) ?>" 
                                                   class="search-input">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>ADRESSE *</th>
                                        <td>
                                            <input type="text" 
                                                   name="shipping_address" 
                                                   value="<?= htmlspecialchars($commandeObj->getShippingAddress()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>VILLE *</th>
                                        <td>
                                            <input type="text" 
                                                   name="shipping_city" 
                                                   value="<?= htmlspecialchars($commandeObj->getShippingCity()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>CODE POSTAL *</th>
                                        <td>
                                            <input type="text" 
                                                   name="shipping_zip" 
                                                   value="<?= htmlspecialchars($commandeObj->getShippingZip()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>PAYS *</th>
                                        <td>
                                            <input type="text" 
                                                   name="shipping_country" 
                                                   value="<?= htmlspecialchars($commandeObj->getShippingCountry()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Billing Information -->
                        <div class="form-section">
                            <h4 class="form-section-title">🧾 ADRESSE FACTURATION</h4>
                            <table class="data-table vertical-form">
                                <tbody>
                                    <tr>
                                        <th>NOM *</th>
                                        <td>
                                            <input type="text" 
                                                   name="billing_name" 
                                                   value="<?= htmlspecialchars($commandeObj->getBillingName()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>ADRESSE *</th>
                                        <td>
                                            <input type="text" 
                                                   name="billing_address" 
                                                   value="<?= htmlspecialchars($commandeObj->getBillingAddress()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>VILLE *</th>
                                        <td>
                                            <input type="text" 
                                                   name="billing_city" 
                                                   value="<?= htmlspecialchars($commandeObj->getBillingCity()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>CODE POSTAL *</th>
                                        <td>
                                            <input type="text" 
                                                   name="billing_zip" 
                                                   value="<?= htmlspecialchars($commandeObj->getBillingZip()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>PAYS *</th>
                                        <td>
                                            <input type="text" 
                                                   name="billing_country" 
                                                   value="<?= htmlspecialchars($commandeObj->getBillingCountry()) ?>" 
                                                   class="search-input" 
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div class="form-actions">
                                                <button type="submit" class="btn-update">
                                                    <span style="font-size: 1.2rem; margin-right: 0.5rem;">💾</span>
                                                    METTRE À JOUR LA COMMANDE
                                                </button>
                                                <a href="commande.php">
                                                    <button type="button" class="btn-cancel">
                                                        <span style="font-size: 1.2rem; margin-right: 0.5rem;">←</span>
                                                        ANNULER
                                                    </button>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="dashboard-card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 class="card-title">◄ ACTIONS RAPIDES ►</h3>
            </div>
            <div class="card-content">
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="commande.php?action=view&id=<?= $id ?>" class="btn-view-all-small" style="text-decoration: none;">
                        👁️ VOIR DÉTAILS
                    </a>
                    <?php if ($commandeObj->canBeCancelled()): ?>
                    <a href="delete.php?type=commande&id=<?= $id ?>" 
                       onclick="return confirm('Annuler cette commande? Cette action est irréversible.')"
                       class="btn-view-all-small" 
                       style="text-decoration: none; background-color: var(--danger-red);">
                        ✖️ ANNULER COMMANDE
                    </a>
                    <?php endif; ?>
                    <a href="../../frontoffice/invoice.php?id=<?= $id ?>" target="_blank" class="btn-view-all-small" style="text-decoration: none;">
                        🧾 GÉNÉRER FACTURE
                    </a>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Auto-update status badge when selecting new status
        document.querySelectorAll('input[name="statut"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelector('.status-badge.status-<?= $commandeObj->getStatut() ?>').className = 
                    'status-badge status-' + this.value;
                document.querySelector('.status-badge.status-<?= $commandeObj->getStatut() ?>').textContent = 
                    this.nextElementSibling.textContent;
            });
        });
    </script>
</body>
</html>