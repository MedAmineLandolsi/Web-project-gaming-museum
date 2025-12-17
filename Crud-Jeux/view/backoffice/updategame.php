<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/JeuxController.php';
require_once '../../model/Jeux.php';

// Check authentication
$controller = new UserController();
if (!$controller->isLoggedIn() || !$controller->isAdmin()) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$currentUser = $controller->viewProfile($_SESSION['user_id']);
$user = $currentUser['user'];

$error = "";
$gameController = new JeuxController();

// 1 — Check if ID is provided
if (!isset($_GET['id'])) {
    die("ID du jeu non fourni !");
}

$id = $_GET['id'];

// 2 — Fetch game
$game = $gameController->getGameById($id);

if (!$game) {
    die("Jeu introuvable !");
}

// 3 — If form submitted → update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle image upload if new image provided
    $imageName = $game['image'] ?? null; // Keep existing image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = "../../uploads/";
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Delete old image if exists
        if (!empty($game['image']) && file_exists("../../uploads/" . $game['image'])) {
            unlink("../../uploads/" . $game['image']);
        }
        
        $imageName = uniqid() . "_" . basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;
        
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }
    
    // Create updated game object with image
    $updatedGame = new Jeux(
        $id,
        $_POST['nom'],
        $_POST['description'],
        $_POST['prix'],
        $_POST['stock'], // Include stock in update
        $_POST['categorie'],
        $imageName // Include image
    );

    $gameController->updategame($updatedGame, $id);

    header("Location: updategame.php?id=$id&success=1");
    exit();
}

$current_page = 'updategame.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Jeu - Ludology Vault</title>
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
        
        .game-image-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid var(--primary-green);
            border-radius: 8px;
            margin-top: 0.5rem;
        }
        
        .image-container {
            text-align: center;
            margin: 1rem 0;
        }
        
        .stock-indicator {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-family: 'VT323', monospace;
            margin-left: 1rem;
        }
        
        .stock-high {
            background-color: rgba(0, 255, 65, 0.2);
            color: var(--primary-green);
            border: 1px solid var(--primary-green);
        }
        
        .stock-medium {
            background-color: rgba(255, 215, 0, 0.2);
            color: var(--warning-orange);
            border: 1px solid var(--warning-orange);
        }
        
        .stock-low {
            background-color: rgba(255, 0, 85, 0.2);
            color: var(--danger-red);
            border: 1px solid var(--danger-red);
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
                <h1 class="page-title">◄ MODIFIER LE JEU ►</h1>
            </div>
            <div class="top-bar-right">
                <a href="addgame.php" style="text-decoration: none;">
                    <button class="btn-view-site">← RETOUR AUX JEUX</button>
                </a>
            </div>
        </header>

        <!-- UPDATE FORM -->
        <section class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">◄ MODIFICATION JEU #<?= $id ?> ►</h3>
            </div>

            <div class="card-content">
                <?php if (!empty($_GET['success'])): ?>
                    <div class="success-message">✔ Jeu mis à jour avec succès !</div>
                <?php endif; ?>

                <!-- Current Game Image -->
                <?php if (!empty($game['image']) && file_exists("../../uploads/" . $game['image'])): ?>
                <div class="image-container">
                    <p style="color: var(--text-gray); margin-bottom: 0.5rem;">Image actuelle:</p>
                    <img src="../../uploads/<?= htmlspecialchars($game['image']) ?>" 
                         alt="<?= htmlspecialchars($game['nom']) ?>" 
                         class="game-image-preview">
                </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <table class="data-table vertical-form">
                        <tbody>
                            <tr>
                                <th>NOM *</th>
                                <td>
                                    <input type="text" 
                                           name="nom" 
                                           value="<?= htmlspecialchars($game['nom']) ?>" 
                                           class="search-input" 
                                           required>
                                </td>
                            </tr>

                            <tr>
                                <th>DESCRIPTION *</th>
                                <td>
                                    <textarea name="description" 
                                              class="search-input" 
                                              required><?= htmlspecialchars($game['description']) ?></textarea>
                                </td>
                            </tr>

                            <tr>
                                <th>PRIX (€) *</th>
                                <td>
                                    <input type="number" 
                                           step="0.01" 
                                           name="prix" 
                                           value="<?= htmlspecialchars($game['prix']) ?>" 
                                           class="search-input" 
                                           required
                                           min="0">
                                </td>
                            </tr>

                            <tr>
                                <th>STOCK *</th>
                                <td>
                                    <input type="number" 
                                           name="stock" 
                                           value="<?= htmlspecialchars($game['stock']) ?>" 
                                           class="search-input" 
                                           required
                                           min="0">
                                    <?php 
                                    $stock = intval($game['stock']);
                                    $stockClass = 'stock-high';
                                    if ($stock < 10) $stockClass = 'stock-low';
                                    elseif ($stock < 20) $stockClass = 'stock-medium';
                                    ?>
                                    <span class="stock-indicator <?= $stockClass ?>">
                                        <?= $stock ?> unités
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th>CATÉGORIE *</th>
                                <td>
                                    <input type="text" 
                                           name="categorie" 
                                           value="<?= htmlspecialchars($game['categorie']) ?>" 
                                           class="search-input" 
                                           required
                                           placeholder="ex: Action, RPG, Sport">
                                </td>
                            </tr>

                            <tr>
                                <th>IMAGE</th>
                                <td>
                                    <input type="file" 
                                           name="image" 
                                           class="search-input"
                                           accept="image/*">
                                    <small style="color: var(--text-gray); font-size: 0.8rem;">
                                        Laisser vide pour conserver l'image actuelle.<br>
                                        Formats: JPG, PNG, GIF. Taille max: 2MB.
                                    </small>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <div class="form-actions">
                                        <button type="submit" class="btn-update">
                                            <span style="font-size: 1.2rem; margin-right: 0.5rem;">💾</span>
                                            METTRE À JOUR LE JEU
                                        </button>
                                        <a href="addgame.php">
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
            </div>
        </section>

        <!-- GAME INFO -->
        <section class="dashboard-card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 class="card-title">◄ INFORMATIONS DU JEU ►</h3>
            </div>
            <div class="card-content">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div>
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">📊 IDENTIFICATION</h4>
                        <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                            <strong>ID:</strong> #<?= $id ?><br>
                            <strong>Nom:</strong> <?= htmlspecialchars($game['nom']) ?><br>
                            <strong>Créé le:</strong> <?= isset($game['created_at']) ? date('d/m/Y', strtotime($game['created_at'])) : 'Date inconnue' ?>
                        </p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">💰 VALEUR</h4>
                        <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                            <strong>Prix unitaire:</strong> <?= number_format($game['prix'], 2) ?> €<br>
                            <strong>Valeur du stock:</strong> <?= number_format($game['prix'] * $game['stock'], 2) ?> €<br>
                            <strong>Catégorie:</strong> <?= htmlspecialchars($game['categorie']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>