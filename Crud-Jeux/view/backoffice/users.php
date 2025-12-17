<?php
// Only ONE session_start() at the beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config.php';
require_once '../../controller/user_controller.php';

$controller = new UserController();

if (!$controller->isLoggedIn() || !$controller->isAdmin()) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$currentUser = $controller->viewProfile($_SESSION['user_id']);
$user = $currentUser['user'];
$allUsers = $controller->getAllUsers();

$current_page = 'users.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Admin Dashboard</title>
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

        /* User Modal Styles */
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

        .search-box {
            margin-bottom: 2rem;
        }

        .search-input {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
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
                <li class="nav-item">
                    <a href="commande.php">
                        <span class="nav-icon">🛒</span>
                        <span class="nav-text">COMMANDES</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="users.php">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">UTILISATEURS</span>
                        <span class="nav-count"><?php echo count($allUsers); ?></span>
                    </a>
                </li>
                <li class="nav-item">
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
        <!-- TOP BAR -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">◄ GESTION DES UTILISATEURS ►</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <input type="text" placeholder="Rechercher..." class="search-input" id="globalSearch">
                    <button class="search-btn">🔍</button>
                </div>
                <button class="btn-view-site" onclick="window.location.href='../frontoffice/index.php'">VOIR LE SITE →</button>
            </div>
        </header>

        <!-- USERS TABLE -->
        <section class="dashboard-card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 class="card-title">◄ LISTE DES UTILISATEURS ►</h3>
                <div class="card-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Rechercher un utilisateur..." class="search-input" id="userSearch">
                    </div>
                </div>
            </div>
            <div class="card-content">
                <table class="data-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOM D'UTILISATEUR</th>
                            <th>EMAIL</th>
                            <th>NOM COMPLET</th>
                            <th>ROLE</th>
                            <th>STATUT</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allUsers as $userItem): ?>
                        <tr data-user-id="<?php echo $userItem['id']; ?>" data-username="<?php echo htmlspecialchars(strtolower($userItem['username'])); ?>">
                            <td>#<?php echo $userItem['id']; ?></td>
                            <td class="game-name">
                                <span class="game-icon">👤</span>
                                <?php echo htmlspecialchars($userItem['username']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($userItem['email']); ?></td>
                            <td>
                                <?php 
                                $fullName = trim($userItem['first_name'] . ' ' . $userItem['last_name']);
                                echo $fullName ? htmlspecialchars($fullName) : '-';
                                ?>
                            </td>
                            <td>
                                <?php if ($userItem['role'] === 'admin'): ?>
                                    <span class="badge badge-nintendo">ADMIN</span>
                                <?php else: ?>
                                    <span class="badge badge-console">USER</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($userItem['status'] === 'active'): ?>
                                    <span class="status status-active">ACTIF</span>
                                <?php elseif ($userItem['status'] === 'banned'): ?>
                                    <span class="status status-pending">BANNI</span>
                                <?php else: ?>
                                    <span class="status" style="background-color: rgba(255, 255, 255, 0.2); border: 1px solid var(--text-gray); color: var(--text-gray);">INACTIF</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions-cell">
                                <?php if ($userItem['id'] !== $_SESSION['user_id']): ?>
                                    <button class="actions-dropdown-trigger">ACTIONS</button>
                                    
                                    <ul class="actions-dropdown-menu">
                                        <li class="actions-dropdown-item">
                                            <button class="actions-dropdown-link" onclick="viewUserProfile(<?php echo $userItem['id']; ?>)">
                                                <span class="action-icon">👁</span>
                                                INSPECTER
                                            </button>
                                        </li>

                                        <?php if ($userItem['status'] !== 'banned'): ?>
                                        <li class="actions-dropdown-item">
                                            <button class="actions-dropdown-link warning" onclick="banUser(<?php echo $userItem['id']; ?>, '<?php echo htmlspecialchars($userItem['username']); ?>')">
                                                <span class="action-icon">🚫</span>
                                                BANNIR
                                            </button>
                                        </li>
                                        <?php endif; ?>

                                        <li class="actions-dropdown-item">
                                            <button class="actions-dropdown-link danger" onclick="deleteUser(<?php echo $userItem['id']; ?>, '<?php echo htmlspecialchars($userItem['username']); ?>')">
                                                <span class="action-icon">🗑</span>
                                                SUPPRIMER
                                            </button>
                                        </li>
                                    </ul>
                                <?php else: ?>
                                    <span style="color: var(--text-gray); font-size: 0.5rem;">VOUS</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- User Modal -->
    <div id="userModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">◄ PROFIL UTILISATEUR ►</h3>
                <button class="modal-close" onclick="closeUserModal()">✖ FERMER</button>
            </div>
            <div id="userModalContent" style="font-family: 'VT323', monospace; font-size: 1.1rem; color: var(--text-light-gray);"></div>
        </div>
    </div>

    <script src="notification.js"></script>
    <script src="admin-script.js"></script>
    <script>
        // Search functionality
        document.getElementById('userSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            
            rows.forEach(row => {
                const username = row.getAttribute('data-username');
                const text = row.textContent.toLowerCase();
                row.style.display = (text.includes(searchTerm) || username.includes(searchTerm)) ? '' : 'none';
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

        // User functions
        function viewUserProfile(userId) {
            showToast('Chargement des données...', 'info');

            const formData = new FormData();
            formData.append('action', 'viewProfile');
            formData.append('user_id', userId);

            fetch('../../controller/user_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.user;
                    const content = `
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div style="grid-column: 1 / -1; text-align: center; margin-bottom: 1rem;">
                                <div style="width: 80px; height: 80px; background: var(--primary-green); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #000;">
                                    ${user.profile_picture_url ? '<img src="../../uploads/'+user.profile_picture_url+'" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">' : user.username.substring(0,2).toUpperCase()}
                                </div>
                            </div>
                            
                            <div>
                                <strong style="color: var(--primary-green);">ID UTILISATEUR:</strong><br>
                                #${user.id}
                            </div>
                            <div>
                                <strong style="color: var(--primary-green);">PSEUDO:</strong><br>
                                ${user.username}
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <strong style="color: var(--primary-green);">EMAIL:</strong><br>
                                ${user.email}
                            </div>
                            <div>
                                <strong style="color: var(--primary-green);">RÔLE:</strong><br>
                                ${user.role === 'admin' ? '<span style="color:var(--warning-orange)">ADMINISTRATEUR</span>' : 'UTILISATEUR'}
                            </div>
                            <div>
                                <strong style="color: var(--primary-green);">STATUT:</strong><br>
                                ${user.status === 'active' ? '<span style="color:var(--primary-green)">ACTIF</span>' : '<span style="color:var(--danger-red)">'+user.status.toUpperCase()+'</span>'}
                            </div>
                            <div>
                                <strong style="color: var(--primary-green);">PRÉNOM:</strong><br>
                                ${user.first_name || '-'}
                            </div>
                            <div>
                                <strong style="color: var(--primary-green);">NOM:</strong><br>
                                ${user.last_name || '-'}
                            </div>
                            <div>
                                <strong style="color: var(--primary-green);">TÉLÉPHONE:</strong><br>
                                ${user.phone_number || '-'}
                            </div>
                            <div>
                                <strong style="color: var(--primary-green);">DATE NAISSANCE:</strong><br>
                                ${user.date_of_birth || '-'}
                            </div>
                        </div>
                    `;
                    document.getElementById('userModalContent').innerHTML = content;
                    document.getElementById('userModal').style.display = 'flex';
                } else {
                    showToast('Erreur: Impossible de charger le profil', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                showToast('Erreur de communication avec le serveur', 'error');
            });
        }

        function banUser(userId, username) {
            if (confirm(`CONFIRMATION : Voulez-vous vraiment BANNIR l'utilisateur "${username}" ?\nIl ne pourra plus se connecter.`)) {
                const formData = new FormData();
                formData.append('action', 'banUser');
                formData.append('user_id', userId);
                
                fetch('../../controller/user_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('✓ Utilisateur banni avec succès', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('✗ ' + (data.message || 'Erreur lors du bannissement'), 'error');
                    }
                })
                .catch(error => {
                    showToast('✗ Erreur de connexion', 'error');
                    console.error('Error:', error);
                });
            }
        }

        function deleteUser(userId, username) {
            if (confirm(`DANGER : Êtes-vous sûr de vouloir SUPPRIMER "${username}" ?\nCette action est IRRÉVERSIBLE !`)) {
                const formData = new FormData();
                formData.append('action', 'deleteUser');
                formData.append('user_id', userId);
                
                fetch('../../controller/user_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('✓ Utilisateur supprimé définitivement', 'success');
                        const row = document.querySelector(`tr[data-user-id="${userId}"]`);
                        if(row) row.style.display = 'none';
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('✗ ' + (data.message || 'Erreur lors de la suppression'), 'error');
                    }
                })
                .catch(error => {
                    showToast('✗ Erreur de connexion', 'error');
                    console.error('Error:', error);
                });
            }
        }

        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
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
    <!-- Load existing scripts -->
<script src="notification.js"></script>
<script src="admin-script.js"></script>

<!-- Load the navigation fix LAST -->
<script src="nav-fix.js"></script>

<!-- If nav-fix.js doesn't exist yet, add this inline fix -->
<script>
    // EMERGENCY NAVIGATION FIX for users.php
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Fixing navigation in users.php');
        
        // Make all sidebar links work
        document.querySelectorAll('.sidebar a').forEach(link => {
            // Remove any problematic event listeners
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            
            // Add clean click handler
            newLink.addEventListener('click', function(e) {
                console.log('Navigating to:', this.href);
                // Allow normal navigation
            });
        });
        
        // Also fix any other navigation links
        document.querySelectorAll('a[href]').forEach(link => {
            link.style.pointerEvents = 'auto';
            link.style.cursor = 'pointer';
        });
    });
</script>
</body>
</html>