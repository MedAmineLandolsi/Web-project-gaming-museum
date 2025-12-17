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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST["nom"], $_POST["description"], $_POST["prix"], $_POST["stock"], $_POST["categorie"]) &&
        !empty($_POST["nom"]) &&
        !empty($_POST["description"]) &&
        !empty($_POST["prix"]) &&
        !empty($_POST["stock"]) &&
        !empty($_POST["categorie"])
    ) {
        
        // Handle image upload
        $imageName = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = "../../uploads/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $imageName = uniqid() . "_" . basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $imageName;

            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        }

        // Create game object
        $game = new jeux(
            null,
            $_POST['nom'],
            $_POST['description'],
            floatval($_POST['prix']),
            intval($_POST['stock']),
            $_POST['categorie'],
            $imageName
        );

        // Add game
        $gameController->addgame($game);

        header('Location: addgame.php');
        exit;
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}

$current_page = 'adddgame.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Jeu - Ludology Vault</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .input-error {
            border: 2px solid red !important;
            background: #ffe6e6;
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
        
        .search-input {
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
                <h1 class="page-title">◄ AJOUTER UN JEU ►</h1>
            </div>
            <div class="top-bar-right">
                <a href="addgame.php" style="text-decoration: none;">
                    <button class="btn-view-site">← RETOUR AUX JEUX</button>
                </a>
            </div>
        </header>

        <!-- ADD GAME FORM -->
        <section class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">◄ NOUVEAU JEU ►</h3>
            </div>

            <div class="card-content">
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <table class="data-table vertical-form">
                        <tbody>
                            <tr>
                                <th>NOM DU JEU *</th>
                                <td>
                                    <input type="text" 
                                           name="nom" 
                                           placeholder="Nom du jeu" 
                                           class="search-input"
                                           required>
                                </td>
                            </tr>

                            <tr>
                                <th>DESCRIPTION *</th>
                                <td>
                                    <textarea name="description" 
                                              placeholder="Description du jeu..." 
                                              class="search-input"
                                              required></textarea>
                                </td>
                            </tr>

                            <tr>
                                <th>PRIX (€) *</th>
                                <td>
                                    <input type="number" 
                                           step="0.01" 
                                           min="0" 
                                           name="prix" 
                                           placeholder="Prix (ex: 49.99)" 
                                           class="search-input"
                                           required>
                                </td>
                            </tr>

                            <tr>
                                <th>STOCK *</th>
                                <td>
                                    <input type="number" 
                                           min="0" 
                                           name="stock" 
                                           placeholder="Quantité en stock" 
                                           class="search-input"
                                           required>
                                </td>
                            </tr>

                            <tr>
                                <th>CATÉGORIE *</th>
                                <td>
                                    <input type="text" 
                                           name="categorie" 
                                           placeholder="Catégorie (ex: Action, RPG, Sport)" 
                                           class="search-input"
                                           required>
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
                                        Formats acceptés: JPG, PNG, GIF. Taille max: 2MB.
                                    </small>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" style="text-align: center; padding-top: 2rem;">
                                    <div class="action-grid">
                                        <button type="submit" class="action-btn action-primary">
                                            <span class="action-icon">💾</span>
                                            <span class="action-text">ENREGISTRER LE JEU</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
                
                <section class="quick-actions">
                    <a href="addgame.php" style="text-decoration: none;">
                        <div class="action-grid">
                            <button class="action-btn action-primary">
                                <span class="action-icon">🔙</span>
                                <span class="action-text">RETOUR À LA LISTE DES JEUX</span>
                            </button>
                        </div>
                    </a>
                </section>
            </div>
        </section>

        <!-- FORM TIPS -->
        <section class="dashboard-card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 class="card-title">◄ CONSEILS POUR L'AJOUT ►</h3>
            </div>
            <div class="card-content">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div>
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">📝 DESCRIPTION</h4>
                        <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                            Soyez descriptif! Incluez le gameplay, l'histoire, les fonctionnalités spéciales.
                        </p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">💰 PRIX</h4>
                        <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                            Utilisez le format décimal (ex: 59.99). Le prix doit être compétitif.
                        </p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">📦 STOCK</h4>
                        <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                            Mettez à jour régulièrement. Les jeux en rupture de stock sont cachés du magasin.
                        </p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">🖼️ IMAGE</h4>
                        <p style="color: var(--text-light-gray); font-size: 0.9rem;">
                            Utilisez une image de bonne qualité (16:9 recommandé). C'est la première chose que les clients voient!
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>