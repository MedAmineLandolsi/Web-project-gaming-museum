<?php
include '../../controller/JeuxController.php';
$gamesC = new JeuxController();
$list = $gamesC->listjeux();
$gameCount = $gamesC->countGames();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Game</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
</head>
<body>
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
                <li class="nav-item">
                    <a href="dashboard.php">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="addgame.php">
                        <span class="nav-icon">🎮</span>
                        <span class="nav-text">JEUX</span>
                        <span class="nav-count"><?= $gameCount ?></span>

                    </a>
                </li>
                <li class="nav-item">
                    <a href="../../../Crud-Commande/view/backoffice/commande.php">
                        <span class="nav-icon">🛒</span>
                        <span class="nav-text">COMMANDES</span>
                        <span class="nav-count">287</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#events">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">ÉVÉNEMENTS</span>
                        <span class="nav-count">12</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#blog">
                        <span class="nav-icon">📝</span>
                        <span class="nav-text">BLOG</span>
                        <span class="nav-count">45</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#users">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">UTILISATEURS</span>
                        <span class="nav-count">1.2K</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#reclamations">
                        <span class="nav-icon">📮</span>
                        <span class="nav-text">RÉCLAMATIONS</span>
                        <span class="nav-count alert">8</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#analytics">
                        <span class="nav-icon">📈</span>
                        <span class="nav-text">ANALYTICS</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#settings">
                        <span class="nav-icon">⚙️</span>
                        <span class="nav-text">PARAMÈTRES</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">AD</div>
                <div class="admin-info">
                    <span class="admin-name">Admin User</span>
                    <span class="admin-role">Super Admin</span>
                </div>
            </div>
            <button class="btn-logout">
                <span>🚪</span> DÉCONNEXION
            </button>
        </div>
    </aside>
    <main class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">◄ GAMES DASHBOARD ►</h1>
            </div>
            
        </header>
         <section class="quick-actions">
            
            <a href="adddgame.php" style="text-decoration: none;"><div class="action-grid">
                <button class="action-btn action-primary">
                    <span class="action-icon">➕</span>
                    <span class="action-text">AJOUTER UN JEU</span>
                </button>
                
            </div></a>
        </section>
         <section class="dashboard-card recent-games">
                <div class="card-header">
                    <h3 class="card-title">◄ COLLECTION DES JEUX  ►</h3>
                    
                </div>
                <div class="card-content">
                    <table class="data-table">

    <thead>
        <tr>
            <th>ID</th>
            <th>NOM DU JEU</th>
            <th>CATEGORIE</th>
            <th>PRIX</th>
            <th>ACTIONS</th>
        </tr>
    </thead>

    <tbody>
    <?php if (!empty($list)) { 
            foreach ($list as $game) { 
    ?>  
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
                    <?= htmlspecialchars($game['prix']); ?>
                </span>
            </td>

            <td>
                <a href="updategame.php?id=<?= $game['id']; ?>">
                    <button class="icon-btn edit">✏️</button>
                </a>

                <a href="delete.php?id=<?= $game['id']; ?>"
                   onclick="return confirm('Supprimer ce jeu ?');">
                    <button type="button" class="icon-btn delete">🗑️</button>
                </a>
            </td>
        </tr>
    <?php 
        } 
    } 
    ?>
    </tbody>

</table>

                </div>
            </section>

    </main>


</body>
</html>