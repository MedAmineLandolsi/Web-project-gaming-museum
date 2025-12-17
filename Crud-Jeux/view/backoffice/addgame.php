<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/JeuxController.php';

// Check authentication
$controller = new UserController();
if (!$controller->isLoggedIn() || !$controller->isAdmin()) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$currentUser = $controller->viewProfile($_SESSION['user_id']);
$user = $currentUser['user'];

// Get games
$gamesC = new JeuxController();
$search = $_GET['search'] ?? null;
$sortBy = $_GET['sort'] ?? null;
$order = $_GET['order'] ?? null;
$list = $gamesC->listjeux($search, $sortBy, $order);
$gameCount = $gamesC->countGames();

$current_page = 'addgame.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Jeux - Ludology Vault</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .game-image-cell {
            width: 80px;
            padding: 5px;
        }
        
        .game-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border: 2px solid var(--primary-green);
            border-radius: 8px;
        }
        
        .search-filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
        }
        
        .search-box-inline {
            flex: 1;
            display: flex;
            gap: 0.5rem;
        }
        
        .search-input-inline {
            flex: 1;
            padding: 0.8rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .search-btn-inline {
            padding: 0.8rem 1.5rem;
            background: var(--primary-green);
            border: none;
            color: var(--darker-bg);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
        }
        
        .filter-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .filter-btn {
            padding: 0.6rem 1rem;
            background: transparent;
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: rgba(0, 255, 65, 0.1);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
        }
        
        .add-game-btn {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            border: none;
            color: var(--darker-bg);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            padding: 0.8rem 1.5rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .add-game-btn:hover {
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.6);
            transform: translateY(-2px);
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
                        <span class="nav-count"><?= $gameCount ?></span>
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
                <h1 class="page-title">◄ GESTION DES JEUX (<?= $gameCount ?>) ►</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <form method="GET" action="" style="display: flex; gap: 0.5rem;">
                        <input type="text" 
                               name="search" 
                               placeholder="Rechercher..." 
                               class="search-input"
                               value="<?= htmlspecialchars($search ?? '') ?>">
                        <button type="submit" class="search-btn">🔍</button>
                    </form>
                </div>
                <a href="adddgame.php" class="add-game-btn">➕ AJOUTER UN JEU</a>
            </div>
        </header>

        <!-- SEARCH AND FILTER BAR -->
        <div class="search-filter-bar">
            <form method="GET" action="" class="search-box-inline">
                <input type="text" 
                       name="search" 
                       placeholder="Rechercher par nom, catégorie..." 
                       class="search-input-inline"
                       value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit" class="search-btn-inline">🔍 RECHERCHER</button>
            </form>
            
            <div class="filter-buttons">
                <span style="color: var(--text-gray); font-size: 0.8rem;">TRIER PAR:</span>
                <a href="?sort=prix&order=asc<?= $search ? '&search=' . urlencode($search) : '' ?>" 
                   class="filter-btn <?= ($sortBy=='prix' && $order=='asc')?'active':'' ?>">
                   PRIX ↑
                </a>
                <a href="?sort=prix&order=desc<?= $search ? '&search=' . urlencode($search) : '' ?>" 
                   class="filter-btn <?= ($sortBy=='prix' && $order=='desc')?'active':'' ?>">
                   PRIX ↓
                </a>
                <a href="?sort=nom&order=asc<?= $search ? '&search=' . urlencode($search) : '' ?>" 
                   class="filter-btn <?= ($sortBy=='nom' && $order=='asc')?'active':'' ?>">
                   NOM A-Z
                </a>
                <a href="addgame.php" class="filter-btn">TOUT EFFACER</a>
            </div>
        </div>

        <!-- QUICK ACTION -->
        <section class="quick-actions">
            <a href="adddgame.php" style="text-decoration: none;">
                <div class="action-grid">
                    <button class="action-btn action-primary">
                        <span class="action-icon">➕</span>
                        <span class="action-text">AJOUTER UN JEU</span>
                    </button>
                </div>
            </a>
        </section>

        <!-- GAMES TABLE -->
        <section class="dashboard-card recent-games">
            <div class="card-header">
                <h3 class="card-title">◄ COLLECTION DES JEUX ►</h3>
            </div>
            <div class="card-content">
                <?php if (empty($list)): ?>
                    <div style="text-align: center; padding: 3rem;">
                        <p style="color: var(--text-gray); font-size: 1.2rem;">Aucun jeu trouvé.</p>
                        <a href="adddgame.php" class="add-game-btn" style="margin-top: 1rem;">➕ AJOUTER VOTRE PREMIER JEU</a>
                    </div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NOM DU JEU</th>
                                <th>CATÉGORIE</th>
                                <th>PRIX</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list as $game): ?>
                            <tr>
                                <td><?= htmlspecialchars($game['id']); ?></td>
                                <td class="game-name">
                                    <span class="game-icon">🎮</span>
                                    <?= htmlspecialchars($game['nom']); ?>
                                </td>
                                <td>
                                    <span class="badge badge-console">
                                        <?= htmlspecialchars($game['categorie']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status status-active">
                                        <?= htmlspecialchars($game['prix']); ?> €
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="updategame.php?id=<?= $game['id']; ?>">
                                            <button class="icon-btn edit">✏️</button>
                                        </a>
                                        <a href="delete.php?id=<?= $game['id']; ?>"
                                           onclick="return confirm('Supprimer ce jeu ?');">
                                            <button type="button" class="icon-btn delete">🗑️</button>
                                        </a>
                                        <a href="../frontoffice/gamedetails.php?id=<?= $game['id'] ?>" target="_blank">
                                            <button class="icon-btn" style="background: var(--secondary-purple);">👁️</button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <!-- SIMPLE PAGINATION INFO -->
                    <div style="margin-top: 2rem; text-align: center; color: var(--text-gray);">
                        <?php if (count($list) >= 10): ?>
                        <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                            <span style="padding: 0.6rem 1rem;">
                                Affichage de <?= count($list) ?> jeux sur <?= $gameCount ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>