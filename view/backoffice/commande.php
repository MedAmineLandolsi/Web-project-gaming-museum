<?php
// view/backoffice/commande.php
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

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : null;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'ID';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Get all commandes
$commandes = $commandeController->listCommandes($search, $sortBy, $order);
$statuses = $commandeController->getStatuses();

$current_page = 'commande.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes - Admin Dashboard</title>
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

        /* Status badges */
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.4rem;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            font-family: 'Press Start 2P', cursive;
        }

        .status-pending { background: rgba(255, 209, 102, 0.2); color: #ffd166; border: 1px solid #ffd166; }
        .status-processing { background: rgba(17, 138, 178, 0.2); color: #118ab2; border: 1px solid #118ab2; }
        .status-shipped { background: rgba(6, 214, 160, 0.2); color: #06d6a0; border: 1px solid #06d6a0; }
        .status-delivered { background: rgba(7, 59, 76, 0.2); color: #073b4c; border: 1px solid #073b4c; }
        .status-completed { background: rgba(106, 153, 78, 0.2); color: #6a994e; border: 1px solid #6a994e; }
        .status-cancelled { background: rgba(239, 71, 111, 0.2); color: #ef476f; border: 1px solid #ef476f; }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--border-color);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            display: block;
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-green);
            font-family: 'VT323', monospace;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
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

        /* Quick filters */
        .filter-buttons {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-light-gray);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.4rem;
            transition: all 0.3s;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary-green);
            color: #000;
            border-color: var(--primary-green);
        }

        /* Order modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 3px solid var(--primary-green);
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 255, 65, 0.4);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--primary-green);
            padding-bottom: 1rem;
        }

        .modal-title {
            color: var(--primary-green);
            font-size: 1rem;
            text-shadow: 0 0 10px var(--primary-green);
        }

        .modal-close {
            background: transparent;
            border: 2px solid var(--accent-pink);
            color: var(--accent-pink);
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
        }

        /* Order details */
        .order-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .detail-label {
            font-size: 0.6rem;
            color: var(--text-gray);
            margin-bottom: 0.3rem;
            text-transform: uppercase;
        }

        .detail-value {
            font-size: 0.8rem;
            color: var(--text-light-gray);
            font-family: 'VT323', monospace;
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
                        <span class="nav-count"><?php echo count($commandes); ?></span>
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
                <h1 class="page-title">◄ GESTION DES COMMANDES ►</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <form method="GET" action="" style="display: flex;">
                        <input type="text" name="search" placeholder="Rechercher commande..." class="search-input" value="<?php echo htmlspecialchars($search ?? ''); ?>" style="border-right: none; border-top-right-radius: 0; border-bottom-right-radius: 0;">
                        <button type="submit" class="search-btn">🔍</button>
                    </form>
                </div>
                <a href="addcommande.php">
                    <button class="btn-view-site" style="background: var(--primary-green); color: #000;">➕ AJOUTER COMMANDE</button>
                </a>
            </div>
        </header>

        <!-- Stats Overview -->
        <?php 
        $stats = $commandeController->getDashboardStats();
        $statusCount = [];
        foreach ($commandes as $cmd) {
            $status = $cmd['statut'];
            $statusCount[$status] = ($statusCount[$status] ?? 0) + 1;
        }
        ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
                <div class="stat-label">Commandes Totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value"><?php echo number_format($stats['total_revenue'], 0); ?>€</div>
                <div class="stat-label">Revenu Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-value"><?php echo $stats['pending_orders']; ?></div>
                <div class="stat-label">En Attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-value"><?php echo number_format($stats['recent_revenue'], 0); ?>€</div>
                <div class="stat-label">30 Derniers Jours</div>
            </div>
        </div>

        <!-- Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Filter Buttons -->
        <div class="filter-buttons">
            <button class="filter-btn <?php echo empty($_GET['statut']) ? 'active' : ''; ?>" onclick="window.location.href='commande.php'">
                TOUTES (<?php echo count($commandes); ?>)
            </button>
            <?php foreach ($statuses as $key => $label): ?>
                <?php if (isset($statusCount[$key])): ?>
                <button class="filter-btn <?php echo (isset($_GET['statut']) && $_GET['statut'] == $key) ? 'active' : ''; ?>" 
                        onclick="window.location.href='commande.php?statut=<?php echo $key; ?>'">
                    <?php echo $label; ?> (<?php echo $statusCount[$key]; ?>)
                </button>
                <?php endif; ?>
            <?php endforeach; ?>
            <button class="filter-btn" onclick="window.location.href='commande.php?sort=Total&order=DESC'">
                🏆 PLUS GROS MONTANTS
            </button>
        </div>

        <!-- COMMANDES TABLE -->
        <section class="dashboard-card" style="margin-top: 1rem;">
            <div class="card-header">
                <h3 class="card-title">◄ LISTE DES COMMANDES ►</h3>
                <div class="card-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Rechercher une commande..." class="search-input" id="commandeSearch">
                    </div>
                </div>
            </div>
            <div class="card-content">
                <table class="data-table" id="commandesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>RÉFÉRENCE</th>
                            <th>PRODUIT</th>
                            <th>CLIENT</th>
                            <th>QUANTITÉ</th>
                            <th>MONTANT</th>
                            <th>DATE</th>
                            <th>STATUT</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Filter by status if specified
                        $filteredCommandes = $commandes;
                        if (isset($_GET['statut']) && !empty($_GET['statut'])) {
                            $filteredCommandes = array_filter($commandes, function($cmd) {
                                return $cmd['statut'] == $_GET['statut'];
                            });
                        }
                        ?>
                        
                        <?php if (empty($filteredCommandes)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-gray);">
                                    🚫 Aucune commande trouvée
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filteredCommandes as $commande): ?>
                            <tr data-order-id="<?php echo $commande['ID']; ?>" data-order-ref="<?php echo htmlspecialchars(strtolower($commande['order_ref'] ?? '')); ?>">
                                <td>#<?php echo $commande['ID']; ?></td>
                                <td>
                                    <span style="font-family: 'VT323', monospace; color: var(--primary-green);">
                                        <?php echo htmlspecialchars($commande['order_ref'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td class="game-name">
                                    <span class="game-icon">🎮</span>
                                    <?php echo htmlspecialchars($commande['produit_nom'] ?? 'Produit inconnu'); ?>
                                </td>
                                <td><?php echo htmlspecialchars($commande['username'] ?? 'Anonyme'); ?></td>
                                <td><?php echo $commande['quantity']; ?></td>
                                <td>
                                    <strong style="color: var(--primary-green);">
                                        <?php echo number_format($commande['Total'], 2); ?>€
                                    </strong>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($commande['Date'])); ?></td>
                                <td>
                                    <?php 
                                    $statusClass = 'status-' . $commande['statut'];
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($statuses[$commande['statut']] ?? $commande['statut']); ?>
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <button class="actions-dropdown-trigger">ACTIONS</button>
                                    
                                    <ul class="actions-dropdown-menu">
                                        <li class="actions-dropdown-item">
                                            <button class="actions-dropdown-link" onclick="viewOrderDetails(<?php echo $commande['ID']; ?>)">
                                                <span class="action-icon">👁</span>
                                                VOIR DÉTAILS
                                            </button>
                                        </li>
                                        <li class="actions-dropdown-item">
                                            <button class="actions-dropdown-link" onclick="window.location.href='updatecmd.php?id=<?php echo $commande['ID']; ?>'">
                                                <span class="action-icon">✏</span>
                                                MODIFIER
                                            </button>
                                        </li>
                                        <li class="actions-dropdown-item">
                                            <button class="actions-dropdown-link danger" onclick="deleteOrder(<?php echo $commande['ID']; ?>, '<?php echo htmlspecialchars($commande['order_ref'] ?? 'N/A'); ?>')">
                                                <span class="action-icon">🗑</span>
                                                SUPPRIMER
                                            </button>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Order Modal -->
    <div id="orderModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">◄ DÉTAILS DE LA COMMANDE ►</h3>
                <button class="modal-close" onclick="closeOrderModal()">✖ FERMER</button>
            </div>
            <div id="orderModalContent" style="font-family: 'VT323', monospace; font-size: 1.1rem; color: var(--text-light-gray);"></div>
        </div>
    </div>

    <script src="notification.js"></script>
    <script src="admin-script.js"></script>
    <script>
        // Search functionality
        document.getElementById('commandeSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#commandesTable tbody tr');
            
            rows.forEach(row => {
                const orderRef = row.getAttribute('data-order-ref');
                const text = row.textContent.toLowerCase();
                row.style.display = (text.includes(searchTerm) || orderRef.includes(searchTerm)) ? '' : 'none';
            });
        });

        // Dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.actions-dropdown-trigger').forEach(function(trigger) {
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    const cell = trigger.closest('.actions-cell');
                    const menu = cell.querySelector('.actions-dropdown-menu');
                    
                    document.querySelectorAll('.actions-dropdown-menu').forEach(function(otherMenu) {
                        if (otherMenu !== menu) {
                            otherMenu.style.opacity = '0';
                            otherMenu.style.visibility = 'hidden';
                            otherMenu.style.transform = 'translateY(-10px)';
                        }
                    });
                    
                    if (menu) {
                        const isVisible = menu.style.visibility === 'visible';
                        if (isVisible) {
                            menu.style.opacity = '0';
                            menu.style.visibility = 'hidden';
                            menu.style.transform = 'translateY(-10px)';
                        } else {
                            menu.style.opacity = '1';
                            menu.style.visibility = 'visible';
                            menu.style.transform = 'translateY(0)';
                        }
                    }
                });
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.actions-cell')) {
                    document.querySelectorAll('.actions-dropdown-menu').forEach(function(menu) {
                        menu.style.opacity = '0';
                        menu.style.visibility = 'hidden';
                        menu.style.transform = 'translateY(-10px)';
                    });
                }
            });

            document.querySelectorAll('.actions-dropdown-menu').forEach(function(menu) {
                menu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
        });

        // Order functions
        function viewOrderDetails(orderId) {
            showToast('Chargement des données...', 'info');

            fetch(`../../controller/CommandeController.php?action=getDetails&id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const commande = data.commande;
                        const userInfo = data.user_info || {};
                        const produitInfo = data.produit_info || {};
                        
                        const content = `
                            <div class="order-details-grid">
                                <div class="detail-item">
                                    <div class="detail-label">RÉFÉRENCE</div>
                                    <div class="detail-value">${commande.order_ref}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">DATE</div>
                                    <div class="detail-value">${new Date(commande.Date).toLocaleDateString('fr-FR')}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">STATUT</div>
                                    <div class="detail-value">
                                        <span class="status-badge status-${commande.statut}" style="font-size: 0.6rem;">
                                            ${commande.statut.toUpperCase()}
                                        </span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">MONTANT TOTAL</div>
                                    <div class="detail-value" style="color: var(--primary-green); font-size: 1.2rem;">
                                        ${commande.Total}€
                                    </div>
                                </div>
                            </div>

                            <div style="margin: 1.5rem 0; padding: 1rem; background: rgba(0, 255, 65, 0.05); border-radius: 8px;">
                                <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">👤 CLIENT</h4>
                                <div>${userInfo.username || 'Anonyme'} (${userInfo.email || 'Email non disponible'})</div>
                            </div>

                            <div style="margin: 1.5rem 0; padding: 1rem; background: rgba(0, 255, 65, 0.05); border-radius: 8px;">
                                <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">🎮 PRODUIT</h4>
                                <div>${produitInfo.nom || 'Produit inconnu'} - ${commande.quantity} x ${produitInfo.prix || 0}€</div>
                            </div>

                            ${commande.shipping_name ? `
                            <div style="margin: 1.5rem 0; padding: 1rem; background: rgba(0, 255, 65, 0.05); border-radius: 8px;">
                                <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">🚚 LIVRAISON</h4>
                                <div>${commande.shipping_name}<br>
                                ${commande.shipping_address}<br>
                                ${commande.shipping_city} ${commande.shipping_zip}<br>
                                ${commande.shipping_country}<br>
                                📞 ${commande.shipping_phone}<br>
                                ✉ ${commande.shipping_email}</div>
                            </div>` : ''}

                            ${commande.notes ? `
                            <div style="margin: 1.5rem 0; padding: 1rem; background: rgba(0, 255, 65, 0.05); border-radius: 8px;">
                                <h4 style="color: var(--primary-green); margin-bottom: 0.5rem;">📝 NOTES</h4>
                                <div>${commande.notes}</div>
                            </div>` : ''}
                        `;
                        
                        document.getElementById('orderModalContent').innerHTML = content;
                        document.getElementById('orderModal').style.display = 'flex';
                    } else {
                        showToast('Erreur: Impossible de charger les détails', 'error');
                    }
                })
                .catch(error => {
                    console.error(error);
                    showToast('Erreur de communication avec le serveur', 'error');
                });
        }

        function deleteOrder(orderId, orderRef) {
            if (confirm(`DANGER : Êtes-vous sûr de vouloir SUPPRIMER la commande "${orderRef}" ?\nCette action est IRRÉVERSIBLE !`)) {
                fetch(`../../controller/CommandeController.php?action=delete&id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('✓ Commande supprimée avec succès', 'success');
                        const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                        if (row) row.remove();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('✗ ' + (data.error || 'Erreur lors de la suppression'), 'error');
                    }
                })
                .catch(error => {
                    showToast('✗ Erreur de connexion', 'error');
                    console.error('Error:', error);
                });
            }
        }

        function closeOrderModal() {
            document.getElementById('orderModal').style.display = 'none';
        }

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