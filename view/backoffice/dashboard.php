<?php
// Only ONE session_start() at the beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/JeuxController.php';
require_once '../../controller/CommandeController.php';

$controller = new UserController();

if (!$controller->isLoggedIn() || !$controller->isAdmin()) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$currentUser = $controller->viewProfile($_SESSION['user_id']);
$user = $currentUser['user'];

$allUsers = $controller->getAllUsers();
$gamesC = new JeuxController();
$commandeC = new CommandeController();

$gameCount = $gamesC->countGames();
$recentGames = $gamesC->listjeux(null, null, null);
$recentOrders = $commandeC->listCommandes();
$stats = $commandeC->getDashboardStats();

// Keep only first 5 games for display
$recentGames = array_slice($recentGames, 0, 5);
$recentOrders = array_slice($recentOrders, 0, 5);

$current_page = 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ludology Vault</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    
</head>
<body>
    <!-- SIDEBAR - Copy this to every page -->
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
            <a href="addgame.php">  <!-- CHANGED FROM games.php TO addgame.php -->
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
            <button class="btn-logout" onclick="logout(); return false;">
                <span>🚪</span> DÉCONNEXION
            </button>
        </div>
    </aside>

    <main class="main-content">
        <!-- TOP BAR - Copy this to every page -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title" id="pageTitle">◄ DASHBOARD PRINCIPAL ►</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <input type="text" placeholder="Rechercher..." class="search-input" id="searchInput">
                    <button class="search-btn">🔍</button>
                </div>
                <button class="btn-view-site" onclick="window.location.href='../frontoffice/index.php'">VOIR LE SITE →</button>
            </div>
        </header>

        <!-- PAGE CONTENT - This changes per page -->
        <section class="stats-overview">
            <div class="stat-card stat-primary">
                <div class="stat-icon">🎮</div>
                <div class="stat-content">
                    <span class="stat-label">TOTAL JEUX</span>
                    <span class="stat-value" data-target="<?= $gameCount ?>">0</span>
                    <span class="stat-change positive"><?= $gameCount ?> en stock</span>
                </div>
            </div>

            <div class="stat-card stat-secondary">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <span class="stat-label">UTILISATEURS</span>
                    <span class="stat-value" data-target="<?= count($allUsers) ?>">0</span>
                    <span class="stat-change positive"><?= count($allUsers) ?> inscrits</span>
                </div>
            </div>

            <div class="stat-card stat-accent">
                <div class="stat-icon">🛒</div>
                <div class="stat-content">
                    <span class="stat-label">COMMANDES</span>
                    <span class="stat-value" data-target="<?= $stats['total_orders'] ?? 0 ?>">0</span>
                    <span class="stat-change"><?= $stats['total_orders'] ?? 0 ?> total</span>
                </div>
            </div>

            <div class="stat-card stat-warning">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <span class="stat-label">REVENU</span>
                    <span class="stat-value" data-target="<?= $stats['total_revenue'] ?? 0 ?>">0</span>
                    <span class="stat-change"><?= $stats['total_revenue'] ?? 0 ?> €</span>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="quick-actions">
            <h2 class="section-title">◄ ACTIONS RAPIDES ►</h2>
            <div class="action-grid">
                <a href="addgame.php" class="action-btn action-primary">
                    <span class="action-icon">➕</span>
                    <span class="action-text">AJOUTER UN JEU</span>
                </a>
                <a href="addcommande.php" class="action-btn action-secondary">
                    <span class="action-icon">🛒</span>
                    <span class="action-text">AJOUTER COMMANDE</span>
                </a>
                <a href="games.php" class="action-btn action-accent">
                    <span class="action-icon">🎮</span>
                    <span class="action-text">GÉRER JEUX</span>
                </a>
                <a href="users.php" class="action-btn action-warning">
                    <span class="action-icon">👥</span>
                    <span class="action-text">GÉRER UTILISATEURS</span>
                </a>
            </div>
        </section>

        <!-- Main Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Recent Games -->
            <section class="dashboard-card recent-games">
                <div class="card-header">
                    <h3 class="card-title">◄ JEUX RÉCENTS ►</h3>
                    <a href="games.php"><button class="btn-view-all-small">VOIR TOUT</button></a>
                </div>
                <div class="card-content">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NOM</th>
                                <th>CATÉGORIE</th>
                                <th>PRIX</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentGames as $game): ?>
                            <tr>
                                <td>#<?= $game['id'] ?></td>
                                <td><?= htmlspecialchars($game['nom']) ?></td>
                                <td><?= htmlspecialchars($game['categorie']) ?></td>
                                <td><?= $game['prix'] ?> €</td>
                                <td>
                                    <a href="updategame.php?id=<?= $game['id'] ?>">
                                        <button class="icon-btn edit">✏️</button>
                                    </a>
                                    <a href="delete.php?type=game&id=<?= $game['id'] ?>" onclick="return confirm('Supprimer ce jeu?')">
                                        <button class="icon-btn delete">🗑️</button>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Recent Orders -->
            <section class="dashboard-card recent-orders">
                <div class="card-header">
                    <h3 class="card-title">◄ COMMANDES RÉCENTES ►</h3>
                    <a href="commande.php"><button class="btn-view-all-small">VOIR TOUT</button></a>
                </div>
                <div class="card-content">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>UTILISATEUR</th>
                                <th>MONTANT</th>
                                <th>DATE</th>
                                <th>STATUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= $order['username'] ?? 'Anonyme' ?></td>
                                <td><?= $order['Total'] ?> €</td>
                                <td><?= date('d/m/Y', strtotime($order['Date'])) ?></td>
                                <td><span class="status status-active"><?= $order['statut'] ?? 'En cours' ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <script src="notification.js"></script>
    <script src="admin-script.js"></script>
    <script>
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
    <script>
    // ULTIMATE NAVIGATION FIX - Add this to dashboard.php
    (function() {
        'use strict';
        
        console.log('=== ULTIMATE NAV FIX ACTIVATED ===');
        
        // Save original methods
        const originalPreventDefault = Event.prototype.preventDefault;
        const originalStopPropagation = Event.prototype.stopPropagation;
        const originalAddEventListener = EventTarget.prototype.addEventListener;
        
        // Override preventDefault to ignore it for navigation links
        Event.prototype.preventDefault = function() {
            const target = this.target;
            const isNavLink = target && 
                             target.tagName === 'A' && 
                             target.href && 
                             !target.href.includes('javascript:');
            
            if (isNavLink) {
                console.warn('BLOCKED preventDefault on navigation link:', target.href);
                return; // Don't prevent default!
            }
            
            return originalPreventDefault.call(this);
        };
        
        // Override addEventListener to intercept click handlers
        EventTarget.prototype.addEventListener = function(type, listener, options) {
            if (type === 'click' && 
                this.tagName === 'A' && 
                this.href && 
                !this.href.includes('javascript:')) {
                
                // Wrap the listener to ensure navigation works
                const safeListener = function(e) {
                    try {
                        if (listener) {
                            listener.call(this, e);
                        }
                    } catch (err) {
                        console.error('Error in click handler:', err);
                    }
                    
                    // Always navigate after the handler runs
                    if (this.href && this.href !== window.location.href) {
                        console.log('Navigating to:', this.href);
                        window.location.href = this.href;
                    }
                };
                
                return originalAddEventListener.call(this, type, safeListener, options);
            }
            
            return originalAddEventListener.call(this, type, listener, options);
        };
        
        // Make sure all existing links work
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a[href]:not([href*="javascript"])');
            links.forEach(link => {
                // Remove any existing click handlers
                const newLink = link.cloneNode(true);
                link.parentNode.replaceChild(newLink, link);
                
                // Add simple click handler
                newLink.onclick = function() {
                    return true; // Allow navigation
                };
            });
            
            console.log('Fixed', links.length, 'navigation links');
        });
        
        console.log('=== ULTIMATE NAV FIX READY ===');
    })();
</script>
    
</body>
</html>