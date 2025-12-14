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

$current_page = 'profile.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Admin Dashboard</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
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

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--text-gray);
            cursor: pointer;
            font-size: 0.8rem;
        }

        .password-container {
            position: relative;
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
                <li class="nav-item">
                    <a href="users.php">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">UTILISATEURS</span>
                    </a>
                </li>
                <li class="nav-item active">
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
                <h1 class="page-title">◄ MON PROFIL ADMIN ►</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <input type="text" placeholder="Rechercher..." class="search-input">
                    <button class="search-btn">🔍</button>
                </div>
                <button class="btn-view-site" onclick="window.location.href='../frontoffice/index.php'">VOIR LE SITE →</button>
            </div>
        </header>

        <!-- PROFILE CONTENT -->
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
                    <div class="profile-stat-item">
                        <span class="profile-stat-label">INSCRIT DEPUIS:</span>
                        <span class="profile-stat-value"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <div class="profile-content">
                <h3 class="profile-section-title">◄ MODIFIER MON PROFIL ►</h3>
                
                <form id="profileForm" onsubmit="updateProfile(event); return false;">
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
                            <div class="password-container">
                                <input type="password" class="profile-form-input" id="new_password" placeholder="Laisser vide pour ne pas changer">
                                <button type="button" class="password-toggle" onclick="togglePassword('new_password')">👁️</button>
                            </div>
                        </div>

                        <div class="profile-form-group full-width">
                            <label class="profile-form-label">CONFIRMER MOT DE PASSE:</label>
                            <div class="password-container">
                                <input type="password" class="profile-form-input" id="confirm_password" placeholder="Confirmer le nouveau mot de passe">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">👁️</button>
                            </div>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button type="submit" class="btn-profile-save">SAUVEGARDER</button>
                        <button type="button" class="btn-profile-cancel" onclick="window.location.href='dashboard.php'">ANNULER</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="notification.js"></script>
    <script src="admin-script.js"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggle = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = '🔒';
            } else {
                input.type = 'password';
                toggle.textContent = '👁️';
            }
        }

        function updateProfile(event) {
            event.preventDefault();
            
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword && newPassword !== confirmPassword) {
                alert('Les mots de passe ne correspondent pas!');
                return;
            }
            
            if (confirm('Êtes-vous sûr de vouloir sauvegarder les modifications de votre profil?')) {
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
                        alert('✓ Profil mis à jour avec succès!');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        alert('✗ ' + (data.message || 'Erreur lors de la mise à jour'));
                    }
                })
                .catch(error => {
                    alert('✗ Erreur de connexion');
                    console.error('Error:', error);
                });
            }
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
    // EMERGENCY NAVIGATION FIX for profile.php
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Fixing navigation in profile.php');
        
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
    });
</script>
</body>
</html>