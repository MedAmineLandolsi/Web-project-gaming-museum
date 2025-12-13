<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/JeuxController.php';
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

// Get all games and users
$gamesC = new JeuxController();
$commandeC = new CommandeController();
$userC = new UserController();

$games = $gamesC->listjeux();
$allUsers = $userC->getAllUsers();

$error = "";
$success = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate required fields
    if (empty($_POST['produit_id']) || empty($_POST['quantity']) || empty($_POST['total'])) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        try {
            // Create commande object
            $cmd = new Commande(
                $_POST['produit_id'],
                $_POST['total'],
                $_POST['quantity'],
                date("Y-m-d"),
                $_POST['user_id'] ?? null  // user_id can be null for anonymous orders
            );

            // Add commande
            $result = $commandeC->addCommande($cmd);
            
            if ($result) {
                $success = "Commande ajoutée avec succès!";
                // Clear form or redirect after success
                header("refresh:2;url=commande.php");
            } else {
                $error = "Erreur lors de l'ajout de la commande.";
            }
        } catch (Exception $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    }
}

$current_page = 'addcommande.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Commande - Ludology Vault</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .error-message {
            background: rgba(255, 0, 85, 0.2);
            border: 2px solid var(--danger-red);
            color: var(--danger-red);
            padding: 1rem;
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
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
        
        .price-info {
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--primary-green);
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 8px;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .price-row:last-child {
            border-bottom: none;
            font-weight: bold;
            color: var(--primary-green);
        }
        
        /* Logout form styling */
        .logout-form {
            display: inline;
        }
        
        .logout-btn {
            width: 100%;
            background: transparent;
            border: 2px solid var(--accent-pink);
            color: var(--accent-pink);
            padding: 0.8rem;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .logout-btn:hover {
            background: rgba(255, 0, 110, 0.1);
            box-shadow: 0 0 10px rgba(255, 0, 110, 0.3);
        }
        
        .user-select-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .user-email {
            font-size: 0.8rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
    </style>
    <script>
        // JavaScript for auto-calculating total price
        document.addEventListener('DOMContentLoaded', function() {
            const gameSelect = document.querySelector('select[name="produit_id"]');
            const quantityInput = document.querySelector('input[name="quantity"]');
            const totalInput = document.querySelector('input[name="total"]');
            const priceDisplay = document.getElementById('gamePrice');
            const unitPriceSpan = document.getElementById('unitPrice');
            const totalPriceSpan = document.getElementById('totalPrice');
            
            // Store game prices
            const gamePrices = {};
            <?php foreach ($games as $game): ?>
                gamePrices[<?= $game['id'] ?>] = <?= $game['prix'] ?>;
            <?php endforeach; ?>
            
            // Calculate total price
            function calculateTotal() {
                const gameId = gameSelect.value;
                const quantity = parseInt(quantityInput.value) || 1;
                
                if (gameId && gamePrices[gameId]) {
                    const unitPrice = gamePrices[gameId];
                    const totalPrice = unitPrice * quantity;
                    
                    // Update displays
                    priceDisplay.textContent = unitPrice.toFixed(2) + ' €';
                    unitPriceSpan.textContent = unitPrice.toFixed(2) + ' €';
                    totalPriceSpan.textContent = totalPrice.toFixed(2) + ' €';
                    
                    // Update total input field
                    totalInput.value = totalPrice.toFixed(2);
                } else {
                    priceDisplay.textContent = '0.00 €';
                    unitPriceSpan.textContent = '0.00 €';
                    totalPriceSpan.textContent = '0.00 €';
                    totalInput.value = '0.00';
                }
            }
            
            // Add event listeners
            if (gameSelect && quantityInput) {
                gameSelect.addEventListener('change', calculateTotal);
                quantityInput.addEventListener('input', calculateTotal);
                
                // Initial calculation
                calculateTotal();
            }
        });
    </script>
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
                <li class="nav-item <?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                    <a href="users.php">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">UTILISATEURS</span>
                    </a>
                </li>
                <li class="nav-item <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                    <a href="profile.php">
                        <span class="nav-icon">👤</span>
                        <span class="nav-text">PROFIL</span>
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
                <h1 class="page-title">◄ AJOUTER UNE COMMANDE ►</h1>
            </div>
            <div class="top-bar-right">
                <a href="commande.php" style="text-decoration: none;">
                    <button class="btn-view-site">← RETOUR AUX COMMANDES</button>
                </a>
            </div>
        </header>

        <!-- ADD ORDER FORM -->
        <section class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">◄ NOUVELLE COMMANDE ►</h3>
            </div>

            <div class="card-content">
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="success-message"><?= htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <table class="data-table vertical-form">
                        <tbody>
                            <tr>
                                <th>UTILISATEUR</th>
                                <td>
                                    <select name="user_id" class="search-input">
                                        <option value="">-- Aucun (Commande anonyme) --</option>
                                        <?php foreach ($allUsers as $userItem): ?>
                                            <option value="<?= $userItem['id'] ?>">
                                                <?= htmlspecialchars($userItem['username']) ?> 
                                                (<?= htmlspecialchars($userItem['email']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small style="color: var(--text-gray); font-size: 0.8rem;">
                                        Optionnel - Laissez vide pour une commande anonyme.
                                    </small>
                                </td>
                            </tr>

                            <tr>
                                <th>JEU *</th>
                                <td>
                                    <select name="produit_id" class="search-input" required>
                                        <option value="">-- Sélectionner un jeu --</option>
                                        <?php foreach ($games as $game): 
                                            $stockStatus = $game['stock'] > 0 ? 'En stock' : 'Rupture';
                                            $stockClass = $game['stock'] > 0 ? 'status-active' : 'status-pending';
                                        ?>
                                            <option value="<?= $game['id'] ?>" 
                                                    data-price="<?= $game['prix'] ?>"
                                                    data-stock="<?= $game['stock'] ?>">
                                                <?= htmlspecialchars($game['nom']) ?> 
                                                - <?= $game['prix'] ?> € 
                                                (<span class="<?= $stockClass ?>"><?= $stockStatus ?></span>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div style="margin-top: 0.5rem;">
                                        <small style="color: var(--text-gray);">
                                            Prix du jeu sélectionné: <span id="gamePrice" style="color: var(--primary-green);">0.00 €</span>
                                        </small>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <th>QUANTITÉ *</th>
                                <td>
                                    <input type="number" 
                                           name="quantity" 
                                           placeholder="Quantité" 
                                           class="search-input" 
                                           min="1" 
                                           value="1"
                                           required>
                                </td>
                            </tr>

                            <tr>
                                <th>TOTAL (€) *</th>
                                <td>
                                    <input type="number" 
                                           name="total" 
                                           placeholder="Total" 
                                           class="search-input" 
                                           step="0.01"
                                           min="0"
                                           required
                                           readonly>
                                    <small style="color: var(--text-gray); font-size: 0.8rem;">
                                        Ce champ est calculé automatiquement.
                                    </small>
                                </td>
                            </tr>

                            <tr>
                                <th>RÉCAPITULATIF</th>
                                <td>
                                    <div class="price-info">
                                        <div class="price-row">
                                            <span>Prix unitaire:</span>
                                            <span id="unitPrice" style="color: var(--primary-green);">0.00 €</span>
                                        </div>
                                        <div class="price-row">
                                            <span>Quantité:</span>
                                            <span id="quantityDisplay" style="color: var(--primary-green);">1</span>
                                        </div>
                                        <div class="price-row">
                                            <span>Total commande:</span>
                                            <span id="totalPrice" style="color: var(--primary-green); font-size: 1.2rem;">0.00 €</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" style="text-align: center; padding-top: 2rem;">
                                    <div class="action-grid">
                                        <button type="submit" class="action-btn action-primary">
                                            <span class="action-icon">💾</span>
                                            <span class="action-text">ENREGISTRER LA COMMANDE</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
                
                <section class="quick-actions">
                    <a href="commande.php" style="text-decoration: none;">
                        <div class="action-grid">
                            <button class="action-btn action-primary">
                                <span class="action-icon">🔙</span>
                                <span class="action-text">RETOUR À LA LISTE DES COMMANDES</span>
                            </button>
                        </div>
                    </a>
                </section>
            </div>
        </section>

        <!-- FORM TIPS -->
        <section class="dashboard-card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 class="card-title">◄ INFORMATIONS IMPORTANTES ►</h3>
            </div>
            <div class="card-content">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div>
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">👤 UTILISATEUR</h4>
                        <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                            Associez la commande à un utilisateur pour suivre son historique d'achats.
                        </p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">🎮 STOCK</h4>
                        <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                            Le stock est automatiquement déduit lors de l'ajout d'une commande.
                        </p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">💰 CALCUL AUTOMATIQUE</h4>
                        <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                            Le total est calculé automatiquement (prix × quantité).
                        </p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">📅 DATE</h4>
                        <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                            La date est automatiquement enregistrée à la date d'aujourd'hui.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <script>
        // Update quantity display
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.querySelector('input[name="quantity"]');
            const quantityDisplay = document.getElementById('quantityDisplay');
            
            if (quantityInput && quantityDisplay) {
                quantityInput.addEventListener('input', function() {
                    quantityDisplay.textContent = this.value || '1';
                });
            }
        });
    </script>
</body>
</html>