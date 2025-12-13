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

$commandeC = new CommandeController();
$jeuxC = new JeuxController();

// Fetch commande by ID
if (!isset($_GET['id'])) {
    die("ID commande manquant.");
}

$cmd = $commandeC->getCommande($_GET['id']);

if (!$cmd) {
    die("Commande introuvable.");
}

// Fetch all games to select Produit_id
$games = $jeuxC->listjeux();

// Also fetch users for user selection
$userC = new UserController();
$allUsers = $userC->getAllUsers();

// Update form submit
if (isset($_POST['update'])) {
    $data = [
        'ID'         => $_POST['ID'],
        'Produit_id' => $_POST['Produit_id'],
        'quantity'   => $_POST['quantity'],
        'Total'      => $_POST['Total'],
        'Date'       => $_POST['Date'],
        'user_id'    => $_POST['user_id'] ?? null
    ];

    $commandeC->updateCommande($data);

    header("Location: commande.php");
    exit;
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
        
        .input {
            width: 100%;
            padding: 0.8rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        select.input {
            width: 100%;
            padding: 0.8rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
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
        
        .game-info {
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--primary-green);
            padding: 1rem;
            margin-top: 0.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
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
                <h1 class="page-title">◄ MODIFIER LA COMMANDE #<?= $cmd['ID'] ?> ►</h1>
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
                <h3 class="card-title">◄ MODIFICATION COMMANDE ►</h3>
            </div>

            <div class="card-content">
                <form method="POST">
                    <table class="data-table vertical-form">
                        <tbody>
                            <tr>
                                <th>ID COMMANDE</th>
                                <td>
                                    <input type="hidden" name="ID" value="<?= $cmd['ID']; ?>">
                                    <span style="color: var(--primary-green); font-family: 'VT323', monospace; font-size: 1.5rem;">
                                        #<?= $cmd['ID']; ?>
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th>UTILISATEUR</th>
                                <td>
                                    <select name="user_id" class="input">
                                        <option value="">-- Aucun (Commande anonyme) --</option>
                                        <?php foreach ($allUsers as $userItem): ?>
                                            <option value="<?= $userItem['id'] ?>"
                                                <?= (isset($cmd['user_id']) && $cmd['user_id'] == $userItem['id']) ? "selected" : "" ?>>
                                                <?= htmlspecialchars($userItem['username']) ?> 
                                                (<?= htmlspecialchars($userItem['email']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <th>JEU</th>
                                <td>
                                    <select name="Produit_id" required class="input">
                                        <option value="">-- Sélectionner un jeu --</option>
                                        <?php foreach ($games as $g): 
                                            $currentGame = ($g['id'] == $cmd['Produit_id']);
                                        ?>
                                            <option value="<?= $g['id']; ?>" 
                                                <?= $currentGame ? "selected" : "" ?>>
                                                <?= htmlspecialchars($g['nom']); ?> - <?= $g['prix']; ?> €
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <?php 
                                    // Show current game info if available
                                    $currentGame = null;
                                    foreach ($games as $g) {
                                        if ($g['id'] == $cmd['Produit_id']) {
                                            $currentGame = $g;
                                            break;
                                        }
                                    }
                                    if ($currentGame): ?>
                                    <div class="game-info">
                                        <strong>Jeu actuel:</strong> <?= htmlspecialchars($currentGame['nom']) ?><br>
                                        <strong>Prix:</strong> <?= $currentGame['prix'] ?> €<br>
                                        <strong>Stock:</strong> <?= $currentGame['stock'] ?> unités<br>
                                        <strong>Catégorie:</strong> <?= $currentGame['categorie'] ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <tr>
                                <th>QUANTITÉ</th>
                                <td>
                                    <input type="number" 
                                           name="quantity" 
                                           class="input" 
                                           value="<?= $cmd['quantity']; ?>" 
                                           min="1" 
                                           required>
                                </td>
                            </tr>

                            <tr>
                                <th>TOTAL (€)</th>
                                <td>
                                    <input type="number" 
                                           step="0.01"
                                           name="Total" 
                                           class="input" 
                                           value="<?= number_format($cmd['Total'], 2); ?>" 
                                           min="0" 
                                           required>
                                </td>
                            </tr>

                            <tr>
                                <th>DATE</th>
                                <td>
                                    <input type="date" 
                                           name="Date" 
                                           class="input"
                                           value="<?= $cmd['Date']; ?>" 
                                           required>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <div class="form-actions">
                                        <button type="submit" name="update" class="btn-update">
                                            <span style="font-size: 1.2rem; margin-right: 0.5rem;">💾</span>
                                            METTRE À JOUR
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
                </form>

                <!-- COMMANDE INFO -->
                <section class="dashboard-card" style="margin-top: 2rem;">
                    <div class="card-header">
                        <h3 class="card-title">◄ INFORMATIONS COMMANDE ►</h3>
                    </div>
                    <div class="card-content">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div>
                                <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">📊 STATUT</h4>
                                <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                                    <?php 
                                    $status = $cmd['statut'] ?? 'pending';
                                    $statusText = 'En attente';
                                    $statusColor = 'var(--warning-orange)';
                                    
                                    if ($status == 'completed') {
                                        $statusText = 'Complétée';
                                        $statusColor = 'var(--primary-green)';
                                    } elseif ($status == 'cancelled') {
                                        $statusText = 'Annulée';
                                        $statusColor = 'var(--danger-red)';
                                    }
                                    ?>
                                    <span style="color: <?= $statusColor ?>; font-weight: bold;"><?= $statusText ?></span>
                                </p>
                            </div>
                            <div>
                                <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">💰 VALEUR</h4>
                                <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                                    <span style="color: var(--primary-green); font-size: 1.2rem;">
                                        <?= number_format($cmd['Total'], 2) ?> €
                                    </span><br>
                                    (<?= $cmd['quantity'] ?> × <?= number_format($cmd['Total'] / $cmd['quantity'], 2) ?> €)
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </main>
</body>
</html>