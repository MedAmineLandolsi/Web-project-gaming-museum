<?php
// view/backoffice/addcommande.php
require_once '../../controller/user_controller.php';
require_once '../../controller/CommandeController.php';
require_once '../../controller/JeuxController.php';

$controller = new UserController();

if (!$controller->isLoggedIn() || !$controller->isAdmin()) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$currentUser = $controller->viewProfile($_SESSION['user_id']);
$user = $currentUser['user'];

$commandeController = new CommandeController();
$jeuxController = new JeuxController();

// Get games and statuses
$games = $jeuxController->listjeux();
$statuses = $commandeController->getStatuses();

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'Produit_id' => $_POST['Produit_id'],
        'quantity' => $_POST['quantity'],
        'Total' => $_POST['Total'] ?? null,
        'user_id' => !empty($_POST['user_id']) ? $_POST['user_id'] : null,
        'statut' => $_POST['statut'],
        'Date' => $_POST['Date'],
        'shipping_name' => $_POST['shipping_name'] ?? null,
        'shipping_email' => $_POST['shipping_email'] ?? null,
        'shipping_phone' => $_POST['shipping_phone'] ?? null,
        'shipping_address' => $_POST['shipping_address'] ?? null,
        'shipping_city' => $_POST['shipping_city'] ?? null,
        'shipping_zip' => $_POST['shipping_zip'] ?? null,
        'shipping_country' => $_POST['shipping_country'] ?? null,
        'billing_name' => $_POST['billing_name'] ?? null,
        'billing_address' => $_POST['billing_address'] ?? null,
        'billing_city' => $_POST['billing_city'] ?? null,
        'billing_zip' => $_POST['billing_zip'] ?? null,
        'billing_country' => $_POST['billing_country'] ?? null,
        'payment_method' => $_POST['payment_method'] ?? null,
        'notes' => $_POST['notes'] ?? null
    ];
    
    $result = $commandeController->addCommande($data);
    
    if ($result['success']) {
        header('Location: commande.php?success=Commande créée avec succès! Référence: ' . $result['order_ref']);
        exit();
    } else {
        $message = $result['error'];
        $messageType = 'error';
    }
}

$current_page = 'addcommande.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Commande - Admin Dashboard</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .actions-cell {
            position: relative;
        }

        .actions-dropdown-trigger {
            background: transparent;
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .actions-dropdown-trigger:hover {
            background: rgba(0, 255, 65, 0.1);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
        }

        .actions-dropdown-trigger::after {
            content: '▼';
            font-size: 0.4rem;
        }

        .actions-dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s;
            z-index: 100;
            box-shadow: 0 10px 30px rgba(0, 255, 65, 0.3);
        }

        .actions-cell:hover .actions-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .actions-dropdown-menu::before {
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

        .actions-dropdown-item {
            list-style: none;
        }

        .actions-dropdown-link {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            color: var(--text-light-gray);
            text-decoration: none;
            font-size: 0.5rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            font-family: 'Press Start 2P', cursive;
        }

        .actions-dropdown-link:hover {
            background: rgba(0, 255, 65, 0.1);
            color: var(--primary-green);
            border-left-color: var(--primary-green);
        }

        .actions-dropdown-link.danger:hover {
            background: rgba(255, 0, 85, 0.1);
            color: var(--danger-red);
            border-left-color: var(--danger-red);
        }

        .actions-dropdown-link.warning:hover {
            background: rgba(255, 149, 0, 0.1);
            color: var(--warning-orange);
            border-left-color: var(--warning-orange);
        }

        .action-icon {
            font-size: 0.6rem;
            font-family: 'Press Start 2P', cursive;
        }

        /* Form styles */
        .form-container {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--border-color);
            padding: 2rem;
            border-radius: 10px;
            margin-top: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            text-transform: uppercase;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-hint {
            font-size: 0.8rem;
            color: var(--text-gray);
            margin-top: 0.5rem;
            font-family: 'VT323', monospace;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--border-color);
        }

        .btn-submit {
            background: var(--primary-green);
            color: #000;
            border: none;
            padding: 1rem 2rem;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background: #00ff41;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
        }

        .btn-cancel {
            background: transparent;
            border: 2px solid var(--danger-red);
            color: var(--danger-red);
            padding: 1rem 2rem;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: rgba(255, 0, 85, 0.1);
        }

        /* Section titles */
        .section-title {
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.8rem;
        }

        /* Alert messages */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-family: 'VT323', monospace;
            font-size: 1rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            border-color: #2ecc71;
            color: #2ecc71;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            border-color: #e74c3c;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <div class="logo-box">🎮</div>
                <div class="admin-title">
                    <h2>LUDOLOGY VAULT</h2>
                    <span class="admin-badge">ADMIN PANEL</span>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="dashboard.php">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="addgame.php">
                        <span class="nav-icon">🎮</span>
                        <span class="nav-text">JEUX</span>
                    </a>
                </li>
                <li class="nav-item active">
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
            <button class="btn-logout" onclick="logout(); return false;">
                <span>🚪</span> DÉCONNEXION
            </button>
        </div>
    </aside>

    <main class="main-content">
        <!-- TOP BAR -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">◄ AJOUTER UNE COMMANDE ►</h1>
            </div>
            <div class="top-bar-right">
                <a href="commande.php">
                    <button class="btn-view-site" style="background: transparent; border: 2px solid var(--primary-green); color: var(--primary-green);">
                        ← RETOUR AUX COMMANDES
                    </button>
                </a>
            </div>
        </header>

        <!-- Add Commande Form -->
        <div class="form-container">
            <h2 class="section-title">◄ INFORMATIONS DE LA COMMANDE ►</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo $messageType == 'success' ? '✅' : '❌'; ?>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-grid">
                    <!-- Product Selection -->
                    <div class="form-group">
                        <label for="Produit_id" class="form-label">🎮 PRODUIT *</label>
                        <select id="Produit_id" name="Produit_id" class="form-select" required onchange="updatePrice()">
                            <option value="">-- Sélectionner un jeu --</option>
                            <?php foreach ($games as $game): ?>
                                <option value="<?php echo $game['id']; ?>" 
                                        data-price="<?php echo $game['prix']; ?>"
                                        data-stock="<?php echo $game['stock']; ?>">
                                    <?php echo htmlspecialchars($game['nom']); ?> - <?php echo $game['prix']; ?> € (Stock: <?php echo $game['stock']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="stock-info" class="form-hint"></div>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group">
                        <label for="quantity" class="form-label">📦 QUANTITÉ *</label>
                        <input type="number" id="quantity" name="quantity" class="form-input" min="1" value="1" required onchange="calculateTotal()">
                    </div>

                    <!-- Price -->
                    <div class="form-group">
                        <label for="unit_price" class="form-label">💰 PRIX UNITAIRE</label>
                        <input type="number" id="unit_price" class="form-input" step="0.01" readonly>
                    </div>

                    <!-- Total -->
                    <div class="form-group">
                        <label for="Total" class="form-label">💵 TOTAL</label>
                        <input type="number" id="Total" name="Total" class="form-input" step="0.01" required readonly>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="statut" class="form-label">📊 STATUT *</label>
                        <select id="statut" name="statut" class="form-select" required>
                            <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo $key == 'pending' ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Date -->
                    <div class="form-group">
                        <label for="Date" class="form-label">📅 DATE *</label>
                        <input type="date" id="Date" name="Date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <!-- User ID -->
                    <div class="form-group">
                        <label for="user_id" class="form-label">👤 ID UTILISATEUR (Optionnel)</label>
                        <input type="number" id="user_id" name="user_id" class="form-input" placeholder="ID de l'utilisateur">
                    </div>
                </div>

                <!-- Shipping Information -->
                <h3 class="section-title" style="margin-top: 2rem;">🚚 INFORMATIONS DE LIVRAISON</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="shipping_name" class="form-label">Nom</label>
                        <input type="text" id="shipping_name" name="shipping_name" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="shipping_email" class="form-label">Email</label>
                        <input type="email" id="shipping_email" name="shipping_email" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="shipping_phone" class="form-label">Téléphone</label>
                        <input type="text" id="shipping_phone" name="shipping_phone" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="shipping_address" class="form-label">Adresse</label>
                        <textarea id="shipping_address" name="shipping_address" class="form-textarea" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="shipping_city" class="form-label">Ville</label>
                        <input type="text" id="shipping_city" name="shipping_city" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="shipping_zip" class="form-label">Code Postal</label>
                        <input type="text" id="shipping_zip" name="shipping_zip" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="shipping_country" class="form-label">Pays</label>
                        <input type="text" id="shipping_country" name="shipping_country" class="form-input">
                    </div>
                </div>

                <!-- Billing Information -->
                <h3 class="section-title" style="margin-top: 2rem;">🧾 INFORMATIONS DE FACTURATION</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="billing_name" class="form-label">Nom</label>
                        <input type="text" id="billing_name" name="billing_name" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="billing_address" class="form-label">Adresse</label>
                        <textarea id="billing_address" name="billing_address" class="form-textarea" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="billing_city" class="form-label">Ville</label>
                        <input type="text" id="billing_city" name="billing_city" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="billing_zip" class="form-label">Code Postal</label>
                        <input type="text" id="billing_zip" name="billing_zip" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="billing_country" class="form-label">Pays</label>
                        <input type="text" id="billing_country" name="billing_country" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="payment_method" class="form-label">Méthode de Paiement</label>
                        <select id="payment_method" name="payment_method" class="form-select">
                            <option value="">-- Sélectionner --</option>
                            <option value="credit_card">Carte de crédit</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank_transfer">Virement bancaire</option>
                            <option value="cash">Espèces</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label for="notes" class="form-label">📝 NOTES (Optionnel)</label>
                    <textarea id="notes" name="notes" class="form-textarea" rows="3" placeholder="Notes internes..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">✅ CRÉER LA COMMANDE</button>
                    <button type="button" class="btn-cancel" onclick="window.location.href='commande.php'">❌ ANNULER</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function updatePrice() {
            const productSelect = document.getElementById('Produit_id');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = selectedOption.dataset.price;
            const stock = selectedOption.dataset.stock;
            
            document.getElementById('unit_price').value = price || '';
            if (stock) {
                document.getElementById('stock-info').innerHTML = `Stock disponible: ${stock}`;
            } else {
                document.getElementById('stock-info').innerHTML = '';
            }
            
            calculateTotal();
        }

        function calculateTotal() {
            const price = parseFloat(document.getElementById('unit_price').value) || 0;
            const quantity = parseInt(document.getElementById('quantity').value) || 0;
            const total = price * quantity;
            
            document.getElementById('Total').value = total.toFixed(2);
        }

        // Copy shipping to billing
        function copyShippingToBilling() {
            document.getElementById('billing_name').value = document.getElementById('shipping_name').value;
            document.getElementById('billing_address').value = document.getElementById('shipping_address').value;
            document.getElementById('billing_city').value = document.getElementById('shipping_city').value;
            document.getElementById('billing_zip').value = document.getElementById('shipping_zip').value;
            document.getElementById('billing_country').value = document.getElementById('shipping_country').value;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updatePrice();
            
            // Add copy button
            const copyBtn = document.createElement('button');
            copyBtn.type = 'button';
            copyBtn.className = 'btn-submit';
            copyBtn.style.marginTop = '1rem';
            copyBtn.style.background = 'var(--secondary-color)';
            copyBtn.innerHTML = '📋 Copier livraison vers facturation';
            copyBtn.onclick = copyShippingToBilling;
            
            const shippingTitle = document.querySelector('h3.section-title:nth-of-type(2)');
            shippingTitle.parentNode.insertBefore(copyBtn, shippingTitle.nextSibling);
        });

        function logout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter?')) {
                const formData = new FormData();
                formData.append('action', 'logout');
                
                fetch('../../controller/user_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    window.location.href = '../frontoffice/login.php';
                })
                .catch(error => {
                    window.location.href = '../frontoffice/login.php';
                });
            }
        }
    </script>
</body>
</html>