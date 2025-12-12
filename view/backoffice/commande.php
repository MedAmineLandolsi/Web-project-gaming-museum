<?php
require "../../../Crud-Jeux/controller/JeuxController.php";
require "../../controller/CommandeController.php";
$controller = new CommandeController();
$list = $controller->listCommandes();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes Dashboard</title>
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
                    <a href="../../../Crud-Jeux/view/backoffice/dashboard.php">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../../../Crud-Jeux/view/backoffice/addgame.php">
                        <span class="nav-icon">🎮</span>
                        <span class="nav-text">JEUX</span>
                        <span class="nav-count"></span>

                    </a>
                </li>
                <li class="nav-item active">
                    <a href="commande.php">
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
                <h1 class="page-title">◄ COMMANDE DASHBOARD ►</h1>
            </div>
            
        </header>
         <section class="quick-actions">
            
            <a href="addcommande.php" style="text-decoration: none;"><div class="action-grid">
                <button class="action-btn action-primary">
                    <span class="action-icon">➕</span>
                    <span class="action-text">AJOUTER UNE COMMANDE</span>
                </button>
                
            </div></a>
        </section>
         <section class="dashboard-card recent-games">
                <div class="card-header">
                    <h3 class="card-title">◄ LES COMMANDES  ►</h3>
                    
                </div>
                <div class="card-content">
                    <table class="data-table">

    <thead>
        <tr>
            <th>ID</th>
            <th>Jeu</th>
            <th>Quantité</th>
            <th>Total (€)</th>
            <th>Date</th>
            <th>ACTIONS</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($list as $cmd) { ?>
        <tr>
            <td><?= $cmd['ID']; ?></td>

            <td class="game-name">
                <span class="game-icon">🎮</span>
                <?= htmlspecialchars($cmd['nom']); ?>
            </td>

            <td>
                <span class="badge badge-console">
                    <?= $cmd['quantity']; ?>
                </span>
            </td>

            <td>
                <span class="status status-active">
                    <?= $cmd['Total']; ?>
                </span>
            </td>
            <td>
                <span class="status status-active">
                <?= $cmd['Date']; ?>
                </span>
            </td>
            <td>
                <a href="updatecmd.php?id=<?= $cmd['ID']; ?>">
                    <button class="icon-btn edit">✏️</button>
                </a>

                <a href="delete.php?id=<?= $game['id']; ?>"
                   onclick="return confirm('Supprimer cette commande ?');">
                    <button type="button" class="icon-btn delete">🗑️</button>
                </a>
            </td>
        </tr>
    <?php 
        } 
    
    ?>
    </tbody>

</table>

                </div>
            </section>

    </main>


</body>
</html>
