<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';

$controller = new UserController();

if (!$controller->isLoggedIn() || !$controller->isAdmin()) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$allUsers = $controller->getAllUsers();
$currentUser = $controller->viewProfile($_SESSION['user_id']);
$user = $currentUser['user'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ludology Vault</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .sidebar-footer {
            position: relative;
        }

        .admin-profile {
            cursor: pointer;
            transition: all 0.3s;
        }

        .admin-profile:hover {
            background-color: rgba(0, 255, 65, 0.1);
        }

        .admin-dropdown {
            position: absolute;
            bottom: calc(100% + 0.5rem);
            left: 0;
            right: 0;
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            box-shadow: 0 -10px 40px rgba(0, 255, 65, 0.4);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s;
            z-index: 1000;
            overflow: hidden;
        }

        .admin-dropdown::before {
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

        .sidebar-footer:hover .admin-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .admin-dropdown-header {
            padding: 1rem 1.5rem;
            border-bottom: 2px solid var(--primary-green);
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), transparent);
        }

        .admin-dropdown-title {
            font-size: 0.5rem;
            color: var(--primary-green);
            margin-bottom: 0.3rem;
        }

        .admin-dropdown-subtitle {
            font-size: 0.45rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 0.8rem;
        }

        .admin-dropdown-menu {
            list-style: none;
            padding: 0.5rem 0;
        }

        .admin-dropdown-item {
            margin: 0;
        }

        .admin-dropdown-link {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1.5rem;
            color: var(--text-light-gray);
            text-decoration: none;
            font-size: 0.5rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .admin-dropdown-link:hover {
            background: rgba(0, 255, 65, 0.1);
            color: var(--primary-green);
            border-left-color: var(--primary-green);
        }

        .admin-dropdown-link.profile {
            color: var(--secondary-purple);
        }

        .admin-dropdown-link.profile:hover {
            background: rgba(189, 0, 255, 0.1);
            color: var(--secondary-purple);
            border-left-color: var(--secondary-purple);
        }

        .admin-dropdown-link.logout {
            border-top: 1px solid var(--border-color);
            color: var(--accent-pink);
        }

        .admin-dropdown-link.logout:hover {
            background: rgba(255, 0, 110, 0.1);
            color: var(--accent-pink);
            border-left-color: var(--accent-pink);
        }

        .dropdown-icon-item {
            font-size: 0.9rem;
        }

        .admin-profile-indicator {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 8px;
            height: 8px;
            background: var(--primary-green);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--primary-green);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        /* Actions Dropdown Styles */
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

        /* Profile Section Styles */
        #profileSection {
            display: none;
        }

        .profile-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .profile-sidebar {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            padding: 2rem;
            height: fit-content;
        }

        .profile-avatar-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-avatar-large {
            width: 150px;
            height: 150px;
            margin: 0 auto 1rem;
            border: 3px solid var(--primary-green);
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--text-white);
            border-radius: 50%;
            overflow: hidden;
            position: relative;
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.4);
        }

        .profile-avatar-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-username {
            font-size: 1rem;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
            text-shadow: 0 0 10px var(--primary-green);
        }

        .profile-role-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(189, 0, 255, 0.2);
            border: 2px solid var(--secondary-purple);
            color: var(--secondary-purple);
            font-size: 0.5rem;
            margin-bottom: 2rem;
        }

        .profile-stats {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .profile-stat-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem;
            background: rgba(0, 255, 65, 0.05);
            border-left: 3px solid var(--primary-green);
        }

        .profile-stat-label {
            font-size: 0.5rem;
            color: var(--text-gray);
        }

        .profile-stat-value {
            font-size: 0.5rem;
            color: var(--primary-green);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        .profile-content {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            padding: 2rem;
        }

        .profile-section-title {
            font-size: 1rem;
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .profile-form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .profile-form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .profile-form-group.full-width {
            grid-column: 1 / -1;
        }

        .profile-form-label {
            font-size: 0.5rem;
            color: var(--primary-green);
        }

        .profile-form-input {
            padding: 0.8rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .profile-form-input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
        }

        .profile-form-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .profile-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--border-color);
        }

        .btn-profile-save {
            flex: 1;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            border: none;
            color: var(--darker-bg);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        }

        .btn-profile-save:hover {
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.6);
            transform: translateY(-2px);
        }

        .btn-profile-cancel {
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

        .btn-profile-cancel:hover {
            background: rgba(255, 0, 110, 0.1);
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.3);
        }

        @media (max-width: 1024px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .profile-form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                <li class="nav-item active">
                    <a href="#" onclick="showSection('dashboard')">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" onclick="showSection('users')">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">UTILISATEURS</span>
                        <span class="nav-count"><?php echo count($allUsers); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" onclick="showSection('profile')">
                        <span class="nav-icon">👤</span>
                        <span class="nav-text">MON PROFIL</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-dropdown">
                <div class="admin-dropdown-header">
                    <div class="admin-dropdown-title">ADMIN MENU</div>
                    <div class="admin-dropdown-subtitle"><?php echo htmlspecialchars($user['username']); ?></div>
                </div>
                <ul class="admin-dropdown-menu">
                    <li class="admin-dropdown-item">
                        <a href="../frontoffice/index.php" class="admin-dropdown-link">
                            <span class="dropdown-icon-item">🌐</span>
                            MAIN SITE
                        </a>
                    </li>
                    <li class="admin-dropdown-item">
                        <a href="#" class="admin-dropdown-link logout" onclick="logout(); return false;">
                            <span class="dropdown-icon-item">🚪</span>
                            DECONNEXION
                        </a>
                    </li>
                </ul>
            </div>

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
        </div>
    </aside>

    <main class="main-content">
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

        <!-- Dashboard Section -->
        <div id="dashboardSection">
            <section class="stats-overview">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">🎮</div>
                    <div class="stat-content">
                        <span class="stat-label">TOTAL JEUX</span>
                        <span class="stat-value" data-target="5247">0</span>
                        <span class="stat-change positive">+127 ce mois</span>
                    </div>
                    <div class="stat-graph">
                        <div class="mini-bars">
                            <span style="height: 40%"></span>
                            <span style="height: 60%"></span>
                            <span style="height: 45%"></span>
                            <span style="height: 80%"></span>
                            <span style="height: 100%"></span>
                        </div>
                    </div>
                </div>

                <div class="stat-card stat-secondary">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <span class="stat-label">VISITEURS ACTIFS</span>
                        <span class="stat-value" data-target="1247">0</span>
                        <span class="stat-change positive">+23% cette semaine</span>
                    </div>
                    <div class="stat-graph">
                        <div class="mini-bars">
                            <span style="height: 50%"></span>
                            <span style="height: 70%"></span>
                            <span style="height: 60%"></span>
                            <span style="height: 90%"></span>
                            <span style="height: 100%"></span>
                        </div>
                    </div>
                </div>

                <div class="stat-card stat-accent">
                    <div class="stat-icon">📅</div>
                    <div class="stat-content">
                        <span class="stat-label">EVENEMENTS</span>
                        <span class="stat-value" data-target="12">0</span>
                        <span class="stat-change">4 a venir</span>
                    </div>
                    <div class="stat-graph">
                        <div class="mini-bars">
                            <span style="height: 30%"></span>
                            <span style="height: 50%"></span>
                            <span style="height: 70%"></span>
                            <span style="height: 60%"></span>
                            <span style="height: 80%"></span>
                        </div>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon">📮</div>
                    <div class="stat-content">
                        <span class="stat-label">RECLAMATIONS</span>
                        <span class="stat-value" data-target="8">0</span>
                        <span class="stat-change negative">En attente</span>
                    </div>
                    <div class="stat-graph">
                        <div class="mini-bars">
                            <span style="height: 60%"></span>
                            <span style="height: 40%"></span>
                            <span style="height: 70%"></span>
                            <span style="height: 50%"></span>
                            <span style="height: 60%"></span>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Users Section -->
        <div id="usersSection" style="display: none;">
            <section class="dashboard-card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">◄ GESTION DES UTILISATEURS ►</h3>
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
    <tr data-user-id="<?php echo $userItem['id']; ?>">
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
                <!-- Trigger Button -->
                <button class="actions-dropdown-trigger">ACTIONS</button>
                
                <!-- Dropdown Menu -->
                <ul class="actions-dropdown-menu">
                    
                    <!-- OPTION 1: INSPECTER -->
                    <li class="actions-dropdown-item">
                        <a class="actions-dropdown-link" onclick="viewUserProfile(<?php echo $userItem['id']; ?>)">
                            <span class="action-icon">👁</span>
                            INSPECTER
                        </a>
                    </li>

                    <!-- OPTION 2: BANNIR (Only if not already banned) -->
                    <?php if ($userItem['status'] !== 'banned'): ?>
                    <li class="actions-dropdown-item">
                        <a class="actions-dropdown-link warning" onclick="banUser(<?php echo $userItem['id']; ?>, '<?php echo htmlspecialchars($userItem['username']); ?>')">
                            <span class="action-icon">🚫</span>
                            BANNIR
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- OPTION 3: SUPPRIMER -->
                    <li class="actions-dropdown-item">
                        <a class="actions-dropdown-link danger" onclick="deleteUser(<?php echo $userItem['id']; ?>, '<?php echo htmlspecialchars($userItem['username']); ?>')">
                            <span class="action-icon">🗑</span>
                            SUPPRIMER
                        </a>
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
        </div>

        <!-- Profile Section -->
        <div id="profileSection">
            <div class="profile-container">
                <div class="profile-sidebar">
                    <div class="profile-avatar-container">
                        <div class="profile-avatar-large">
                            <?php if ($user['profile_picture_url'] && file_exists("../../uploads/" . $user['profile_picture_url'])): ?>
                                <img src="../../uploads/<?php echo htmlspecialchars($user['profile_picture_url']); ?>" alt="Admin">
                            <?php else: ?>
                                <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                            <?php endif; ?>
                        </div>
                        <h3 class="profile-username"><?php echo htmlspecialchars($user['username']); ?></h3>
                        <span class="profile-role-badge">SUPER ADMIN</span>
                    </div>

                    <div class="profile-stats">
                        <div class="profile-stat-item">
                            <span class="profile-stat-label">USER ID:</span>
                            <span class="profile-stat-value">#<?php echo $user['id']; ?></span>
                        </div>
                        <div class="profile-stat-item">
                            <span class="profile-stat-label">ROLE:</span>
                            <span class="profile-stat-value"><?php echo strtoupper($user['role']); ?></span>
                        </div>
                        <div class="profile-stat-item">
                            <span class="profile-stat-label">STATUS:</span>
                            <span class="profile-stat-value"><?php echo strtoupper($user['status']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="profile-content">
                    <h3 class="profile-section-title">◄ MODIFIER MON PROFIL ►</h3>
                    
                    <form id="profileForm" onsubmit="updateProfile(event)">
                        <div class="profile-form-grid">
                            <div class="profile-form-group">
                                <label class="profile-form-label">USERNAME:</label>
                                <input type="text" class="profile-form-input" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">EMAIL:</label>
                                <input type="email" class="profile-form-input" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">PRENOM:</label>
                                <input type="text" class="profile-form-input" id="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">NOM:</label>
                                <input type="text" class="profile-form-input" id="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">TELEPHONE:</label>
                                <input type="tel" class="profile-form-input" id="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">DATE DE NAISSANCE:</label>
                                <input type="date" class="profile-form-input" id="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                            </div>

                            <div class="profile-form-group full-width">
                                <label class="profile-form-label">NOUVEAU MOT DE PASSE:</label>
                                <input type="password" class="profile-form-input" id="new_password" placeholder="Laisser vide pour ne pas changer">
                            </div>

                            <div class="profile-form-group full-width">
                                <label class="profile-form-label">CONFIRMER MOT DE PASSE:</label>
                                <input type="password" class="profile-form-input" id="confirm_password" placeholder="Confirmer le nouveau mot de passe">
                            </div>
                        </div>

                        <div class="profile-actions">
                            <button type="submit" class="btn-profile-save">SAUVEGARDER</button>
                            <button type="button" class="btn-profile-cancel" onclick="showSection('dashboard')">ANNULER</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <div id="userModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: linear-gradient(135deg, var(--card-bg), var(--darker-bg)); border: 3px solid var(--primary-green); padding: 2rem; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0, 255, 65, 0.4);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 2px solid var(--primary-green); padding-bottom: 1rem;">
                <h3 style="color: var(--primary-green); font-size: 1rem; text-shadow: 0 0 10px var(--primary-green);">◄ PROFIL UTILISATEUR ►</h3>
                <button onclick="closeUserModal()" style="background: transparent; border: 2px solid var(--accent-pink); color: var(--accent-pink); padding: 0.5rem 1rem; cursor: pointer; font-family: 'Press Start 2P', cursive; font-size: 0.6rem;">✖ FERMER</button>
            </div>
            <div id="userModalContent" style="font-family: 'VT323', monospace; font-size: 1.1rem; color: var(--text-light-gray);"></div>
        </div>
    </div>

    <div id="customNotification"></div>

<script src="notification.js"></script>
<script src="admin-script.js"></script>
<script>
    // ========================================
    // GESTION DES SECTIONS
    // ========================================
    function showSection(section) {
        document.getElementById('dashboardSection').style.display = section === 'dashboard' ? 'block' : 'none';
        document.getElementById('usersSection').style.display = section === 'users' ? 'block' : 'none';
        document.getElementById('profileSection').style.display = section === 'profile' ? 'block' : 'none';
        
        document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
        
        if (section === 'dashboard') {
            document.querySelector('.nav-item a[onclick*="dashboard"]').parentElement.classList.add('active');
            document.getElementById('pageTitle').textContent = '◄ DASHBOARD PRINCIPAL ►';
        } else if (section === 'users') {
            document.querySelector('.nav-item a[onclick*="users"]').parentElement.classList.add('active');
            document.getElementById('pageTitle').textContent = '◄ GESTION DES UTILISATEURS ►';
        } else if (section === 'profile') {
            document.querySelector('.nav-item a[onclick*="profile"]').parentElement.classList.add('active');
            document.getElementById('pageTitle').textContent = '◄ MON PROFIL ADMIN ►';
        }
    }

    // ========================================
// GESTION DES DROPDOWNS D'ACTIONS
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un gestionnaire de clic sur tous les boutons ACTIONS
    document.querySelectorAll('.actions-dropdown-trigger').forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Get parent cell
            const cell = trigger.closest('.actions-cell');
            const menu = cell.querySelector('.actions-dropdown-menu');
            
            // Fermer tous les autres dropdowns
            document.querySelectorAll('.actions-dropdown-menu').forEach(function(otherMenu) {
                if (otherMenu !== menu) {
                    otherMenu.style.opacity = '0';
                    otherMenu.style.visibility = 'hidden';
                    otherMenu.style.transform = 'translateY(-10px)';
                }
            });
            
            // Toggle le dropdown actuel
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

    // Fermer les dropdowns quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.actions-cell')) {
            document.querySelectorAll('.actions-dropdown-menu').forEach(function(menu) {
                menu.style.opacity = '0';
                menu.style.visibility = 'hidden';
                menu.style.transform = 'translateY(-10px)';
            });
        }
    });

    // Empêcher la fermeture quand on clique dans le dropdown
    document.querySelectorAll('.actions-dropdown-menu').forEach(function(menu) {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});

    // ========================================
    // MISE À JOUR DU PROFIL
    // ========================================
    function updateProfile(event) {
        event.preventDefault();
        
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (newPassword && newPassword !== confirmPassword) {
            showToast('Les mots de passe ne correspondent pas!', 'error');
            return;
        }
        
        showNotification(
            'Êtes-vous sûr de vouloir sauvegarder les modifications de votre profil?', 
            'warning', 
            true, 
            function() {
                const formData = new FormData();
                formData.append('action', 'updateProfile');
                formData.append('username', document.getElementById('username').value);
                formData.append('email', document.getElementById('email').value);
                formData.append('first_name', document.getElementById('first_name').value);
                formData.append('last_name', document.getElementById('last_name').value);
                formData.append('phone_number', document.getElementById('phone_number').value);
                formData.append('date_of_birth', document.getElementById('date_of_birth').value);
                
                if (newPassword) {
                    formData.append('password', newPassword);
                }
                
                fetch('../../controller/user_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('✓ Profil mis à jour avec succès!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('✗ ' + (data.message || 'Erreur lors de la mise à jour'), 'error');
                    }
                })
                .catch(error => {
                    showToast('✗ Erreur de connexion', 'error');
                    console.error('Error:', error);
                });
            }
        );
    }

    // ========================================
    // DÉCONNEXION
    // ========================================
    function logout() {
        showNotification('Êtes-vous sûr de vouloir vous déconnecter?', 'warning', true, function() {
            const formData = new FormData();
            formData.append('action', 'logout');
            
            fetch('../../controller/user_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showToast('Déconnexion réussie!', 'success');
                setTimeout(() => {
                    window.location.href = '../frontoffice/login.php';
                }, 1500);
            })
            .catch(error => {
                window.location.href = '../frontoffice/login.php';
            });
        });
    }

    // ========================================
    // VOIR PROFIL UTILISATEUR
    // ========================================
    // ========================================
    // 1. INSPECTER (VIEW PROFILE)
    // ========================================
    function viewUserProfile(userId) {
        // Show loading toast
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
                // Generate French Content
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

    // ========================================
    // 2. BANNIR (BAN USER)
    // ========================================
    function banUser(userId, username) {
        showNotification(
            `CONFIRMATION : Voulez-vous vraiment BANNIR l'utilisateur "${username}" ?\nIl ne pourra plus se connecter.`, 
            'warning', 
            true, 
            function() {
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
                        // Refresh page after 1.5s to show new status
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
        );
    }

    // ========================================
    // 3. SUPPRIMER (DELETE USER)
    // ========================================
    function deleteUser(userId, username) {
        showNotification(
            `DANGER : Êtes-vous sûr de vouloir SUPPRIMER "${username}" ?\nCette action est IRRÉVERSIBLE !`, 
            'error', 
            true, 
            function() {
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
                        // Remove row from table immediately for visual feedback
                        const row = document.querySelector(`tr[data-user-id="${userId}"]`);
                        if(row) row.style.display = 'none';
                        
                        // Reload to ensure sync
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
        );
    }

    // ========================================
    // RECHERCHE DANS LE TABLEAU
    // ========================================
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#usersTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // ========================================
    // TEST DU SYSTÈME DE NOTIFICATION
    // ========================================
    console.log('✓ Scripts chargés avec succès');
    console.log('✓ Système de notification:', typeof showNotification !== 'undefined' ? 'OK' : 'ERREUR');
    console.log('✓ Dropdowns d\'actions:', document.querySelectorAll('.actions-dropdown-trigger').length + ' boutons trouvés');
</script>
</body>
</html>