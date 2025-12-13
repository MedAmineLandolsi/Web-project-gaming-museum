<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';

// Check if user is logged in
$userController = new UserController();
$isLoggedIn = $userController->isLoggedIn();

if (!$isLoggedIn) {
    header('Location: ../../../Crud-Jeux/view/frontoffice/login.php');
    exit();
}

// Get user info for the user menu
if ($isLoggedIn) {
    $result = $userController->viewProfile($_SESSION['user_id']);
    $user = $result['user'];
    $username = $user['username'];
    $profilePicture = $user['profile_picture_url'] ?? '';
    $role = $user['role'];
}

// Handle quantity update
if (isset($_POST['update'])) {
    $id  = $_POST['id'];
    $qty = max(1, intval($_POST['quantity']));
    $_SESSION['cart'][$id]['quantity'] = $qty;
}

// Handle remove
if (isset($_POST['remove'])) {
    $id = $_POST['id'];
    unset($_SESSION['cart'][$id]);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre Panier - Ludology Vault</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        /* ===== PAGE PANIER ===== */
        .cart-page {
            max-width: 1100px;
            margin: 120px auto 60px;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* État panier vide */
        .cart-empty {
            margin-top: 20px;
        }

        .cart-empty-card {
            border-radius: 16px;
            padding: 30px 22px;
            border: 1px dashed rgba(0, 255, 170, 0.6);
            background: radial-gradient(circle at top, rgba(0, 255, 170, 0.12), #050508);
            text-align: center;
            box-shadow: 0 0 18px rgba(0, 255, 170, 0.28);
            font-family: "VT323", monospace;
        }

        .cart-empty-card .empty-title {
            font-size: 20px;
            margin-bottom: 6px;
        }

        .cart-empty-card .empty-subtitle {
            font-size: 16px;
            color: #c7ffe8;
            margin-bottom: 18px;
        }

        /* Conteneur panier */
        .cart-section {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* Carte contenant le tableau */
        .cart-table-card {
            border-radius: 16px;
            padding: 18px 18px 20px;
            border: 1px solid rgba(0, 255, 170, 0.45);
            background: #050508;
            box-shadow: 0 0 22px rgba(0, 255, 170, 0.3);
            overflow-x: auto;
        }

        /* Tableau */
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            font-family: "VT323", monospace;
            font-size: 16px;
        }

        .cart-table th,
        .cart-table td {
            padding: 10px 8px;
            text-align: left;
        }

        .cart-table thead tr {
            background: linear-gradient(90deg, #111827, #020617);
        }

        .cart-table th {
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(0, 255, 170, 0.5);
        }

        .cart-table tbody tr {
            border-bottom: 1px solid rgba(0, 255, 170, 0.18);
        }

        .cart-table tbody tr:last-child {
            border-bottom: none;
        }

        .cart-table tbody tr:hover {
            background: rgba(0, 255, 170, 0.04);
        }

        .cart-game-name {
            font-weight: 600;
        }

        /* Ligne total */
        .cart-total-row {
            background: radial-gradient(circle at top left, rgba(0, 255, 170, 0.18), #020617);
        }

        .cart-total-label {
            text-align: right;
            font-size: 15px;
        }

        .cart-total-value {
            font-size: 18px;
        }

        /* Formulaires inline */
        .cart-inline-form {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Input quantité */
        .cart-qty-input {
            width: 64px;
            padding: 4px 6px;
            border-radius: 6px;
            border: 1px solid rgba(0, 255, 170, 0.6);
            background: #020617;
            color: #e5fff8;
            font-family: "VT323", monospace;
            font-size: 15px;
        }

        /* Boutons icônes (update / remove) */
        .icon-button {
            border: 1px solid rgba(0, 255, 170, 0.7);
            background: transparent;
            border-radius: 6px;
            padding: 3px 9px;
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
            font-family: "Press Start 2P", system-ui, sans-serif;
            transition: transform 0.1s ease, box-shadow 0.1s ease, background 0.1s ease;
        }

        .icon-button-update {
            border-color: #0aff9d;
        }

        .icon-button-remove {
            border-color: #ff006f;
            color: #ff5b9d;
        }

        .icon-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 0 10px rgba(0, 255, 170, 0.5);
            background: rgba(0, 255, 170, 0.08);
        }

        .icon-button-remove:hover {
            box-shadow: 0 0 10px rgba(255, 0, 111, 0.6);
            background: rgba(255, 0, 111, 0.08);
        }

        /* Boutons bas de page */
        .cart-footer-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: flex-end;
            align-items: center;
        }

        .btn-auth.btn-primary {
            background: linear-gradient(135deg, #ff00e6, #ff0095);
            border-color: #ff66f0;
        }

        .btn-auth.btn-secondary {
            background: transparent;
            border-color: #0aff9d;
        }

        .btn-auth.btn-secondary:hover {
            background: rgba(10, 255, 157, 0.12);
        }

        /* User Menu Styles */
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

        /* Email input styling */
        .email-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .email-input {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid rgba(0, 255, 170, 0.6);
            background: #020617;
            color: #e5fff8;
            font-family: "VT323", monospace;
            font-size: 16px;
            min-width: 250px;
        }

        .email-label {
            color: #c7ffe8;
            font-family: 'VT323', monospace;
            font-size: 16px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .cart-footer-actions {
                justify-content: flex-start;
                flex-direction: column;
                align-items: stretch;
            }
            
            .email-input-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .email-input {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Background particles -->
    <div class="particles">
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- NAVBAR -->
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
                    <li><a href="../../../Crud-Jeux/view/frontoffice/index.php">HOME</a></li>
                    <li><a href="../../../Crud-Jeux/view/frontoffice/games.php">JEUX</a></li>
                    <li><a href="blog.html">BLOG</a></li>
                    <li><a href="events.html">EVENTS</a></li>
                    <li><a href="reclamation.html">RÉCLAMATION</a></li>
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
                                <div class="dropdown-header-title">VOTRE PANIER</div>
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
                    <a href="../../../Crud-Jeux/view/frontoffice/login.php" style="text-decoration: none;">
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

    <main class="main-content cart-page">
        <!-- Header style détails -->
        <header class="page-header">
            <div class="breadcrumb">PANIER / <span>VOS JEUX</span></div>
            <h1 class="page-title">🛒 Votre Panier</h1>
            <p class="page-subtitle">Révisez votre sélection avant de lancer la partie.</p>
        </header>

        <?php if (empty($_SESSION['cart'])): ?>

            <!-- État panier vide -->
            <section class="cart-empty">
                <div class="cart-empty-card">
                    <p class="empty-title">Votre panier est vide.</p>
                    <p class="empty-subtitle">Ajoutez quelques jeux et revenez ici pour finaliser votre combo.</p>
                    <a href="../../../Crud-Jeux/view/frontoffice/games.php" style="text-decoration: none;">
                        <button class="btn-auth btn-primary">
                            <span class="btn-icon">◀</span> Retour aux jeux
                        </button>
                    </a>
                </div>
            </section>

        <?php else: ?>

            <!-- Panier rempli -->
            <section class="cart-section">
                <div class="cart-table-card">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Jeu</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $grandTotal = 0;

                        foreach ($_SESSION['cart'] as $item):
                            $total = $item['prix'] * $item['quantity'];
                            $grandTotal += $total;
                        ?>
                            <tr>
                                <td class="cart-game-name"><?= htmlspecialchars($item['nom']); ?></td>
                                <td><?= number_format($item['prix'], 2); ?> €</td>

                                <td>
                                    <form method="POST" class="cart-inline-form">
                                        <input type="hidden" name="id" value="<?= $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?= $item['quantity']; ?>" min="1" class="cart-qty-input">
                                        <button type="submit" name="update" class="icon-button icon-button-update" title="Mettre à jour">
                                            ✔
                                        </button>
                                    </form>
                                </td>

                                <td><?= number_format($total, 2); ?> €</td>

                                <td>
                                    <form method="POST" class="cart-inline-form">
                                        <input type="hidden" name="id" value="<?= $item['id']; ?>">
                                        <button type="submit" name="remove" class="icon-button icon-button-remove" title="Supprimer">
                                            🗑
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                            <tr class="cart-total-row">
                                <td colspan="3" class="cart-total-label"><strong>Total Général :</strong></td>
                                <td colspan="2" class="cart-total-value"><strong><?= number_format($grandTotal, 2); ?> €</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="cart-footer-actions">
                    <div class="email-input-group">
                        <label for="clientEmail" class="email-label">Email de confirmation:</label>
                        <form action="checkout.php" method="POST" style="display: flex; gap: 10px;">
                            <input type="email" 
                                   id="clientEmail" 
                                   name="clientEmail" 
                                   required 
                                   placeholder="votre-email@example.com"
                                   class="email-input">
                            <button class="btn-auth btn-primary" type="submit">
                                <span class="btn-icon">▶</span> Procéder au paiement
                            </button>
                        </form>
                    </div>
                    
                    <a href="../../../Crud-Jeux/view/frontoffice/games.php" style="text-decoration: none;">
                        <button class="btn-auth btn-secondary">
                            <span class="btn-icon">◀</span> Continuer vos achats
                        </button>
                    </a>
                </div>
            </section>

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
    </script>
</body>
</html>