<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary: #00ff88;
            --secondary: #00ccff;
            --dark: #1a1a2e;
            --darker: #16213e;
            --light: #ffffff;
            --gray: #cccccc;
            --danger: #ff4757;
            --warning: #ffa502;
            --success: #2ed573;
            --card-bg: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, var(--dark) 0%, var(--darker) 100%);
            color: var(--light);
            display: flex;
            min-height: 100vh;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            background: var(--dark);
            padding: 2rem 1rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .admin-logo {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            color: var(--gray);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(0, 255, 136, 0.1);
            color: var(--primary);
            border-left: 4px solid var(--primary);
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .page-title {
            font-size: 2rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-success {
            background: var(--success);
            color: var(--dark);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--dark);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--light);
        }

        .btn-warning {
            background: var(--warning);
            color: var(--dark);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            border: 1px solid var(--border);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .admin-table th,
        .admin-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .admin-table th {
            background: rgba(0, 255, 136, 0.1);
            color: var(--primary);
            font-weight: bold;
        }

        .admin-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .badge {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .badge-admin {
            background: rgba(255, 71, 87, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .badge-user {
            background: rgba(0, 204, 255, 0.2);
            color: var(--secondary);
            border: 1px solid var(--secondary);
        }

        .badge-moderator {
            background: rgba(255, 165, 2, 0.2);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-badge.active {
            background: rgba(46, 213, 115, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .status-badge.inactive {
            background: rgba(255, 255, 255, 0.1);
            color: var(--gray);
            border: 1px solid var(--gray);
        }

        .status-badge.banned {
            background: rgba(255, 71, 87, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .actions-cell {
            display: flex;
            gap: 0.5rem;
        }

        .admin-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--light);
            font-size: 1rem;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .filters {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--light);
            min-width: 150px;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            font-weight: bold;
            font-size: 1rem;
        }

        .error-message {
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: none;
        }

        .search-box.error .search-input {
            border-color: #ff6b6b;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .admin-table {
                display: block;
                overflow-x: auto;
            }
            
            .admin-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                min-width: auto;
            }
            
            .actions-cell {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="admin-logo">⚙️ Admin Gaming</div>
        <nav>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i>📊</i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="blog-admin.php" class="nav-link">
                        <i>📝</i> Articles Blog
                    </a>
                </li>
                <li class="nav-item">
                    <a href="comments-admin.php" class="nav-link">
                        <i>💬</i> Commentaires
                    </a>
                </li>
                <li class="nav-item">
                    <a href="games-admin.php" class="nav-link">
                        <i>🎮</i> Jeux
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users-admin.php" class="nav-link active">
                        <i>👥</i> Utilisateurs
                    </a>
                </li>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link">
                        <i>⚙️</i> Paramètres
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../index.php" class="nav-link">
                        <i>🏠</i> Site Public
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" onclick="logout()" class="nav-link">
                        <i>🚪</i> Déconnexion
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <div class="admin-header">
            <h1 class="page-title">Gestion des Utilisateurs</h1>
            <a href="user-edit.php" class="btn btn-success">
                <i>+</i> Nouvel Utilisateur
            </a>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="totalUsers">0</div>
                <div class="stat-label">Total Utilisateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="activeUsers">0</div>
                <div class="stat-label">Utilisateurs Actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="adminUsers">0</div>
                <div class="stat-label">Administrateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="newUsers">0</div>
                <div class="stat-label">Nouveaux (7j)</div>
            </div>
        </div>

        <!-- Barre d'outils -->
        <div class="admin-toolbar">
            <div class="search-box" id="searchBox">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="searchInput" placeholder="Rechercher un utilisateur...">
                <div class="error-message" id="searchError">Le terme de recherche doit contenir au moins 2 caractères</div>
            </div>
            <div class="filters">
                <select class="filter-select" id="roleFilter">
                    <option value="">Tous les rôles</option>
                    <option value="admin">Administrateur</option>
                    <option value="moderator">Modérateur</option>
                    <option value="user">Utilisateur</option>
                </select>
                <select class="filter-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                    <option value="banned">Banni</option>
                </select>
            </div>
        </div>

        <!-- Tableau des Utilisateurs -->
        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Inscription</th>
                        <th>Dernière Connexion</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <!-- Les utilisateurs seront chargés dynamiquement ici -->
                </tbody>
            </table>
        </div>
    </main>

    <script src="../assets/js/state-manager.js"></script>
    <script>
        const sampleUsers = [
            {
                id: 1,
                username: "admin",
                email: "admin@musee-gaming.fr",
                role: "admin",
                status: "active",
                created_at: "2024-01-15",
                last_login: "2024-03-20 14:30:00",
                avatar: "A"
            },
            {
                id: 2,
                username: "sarah_editor",
                email: "sarah@musee-gaming.fr",
                role: "moderator",
                status: "active",
                created_at: "2024-02-10",
                last_login: "2024-03-19 09:15:00",
                avatar: "S"
            },
            {
                id: 3,
                username: "GamerPro92",
                email: "gamerpro92@email.com",
                role: "user",
                status: "active",
                created_at: "2024-03-01",
                last_login: "2024-03-20 16:45:00",
                avatar: "G"
            },
            {
                id: 4,
                username: "TechLover",
                email: "tech.lover@email.com",
                role: "user",
                status: "active",
                created_at: "2024-02-28",
                last_login: "2024-03-18 11:20:00",
                avatar: "T"
            },
            {
                id: 5,
                username: "RPG_Fan",
                email: "rpgfan@email.com",
                role: "user",
                status: "inactive",
                created_at: "2024-01-20",
                last_login: "2024-02-15 08:30:00",
                avatar: "R"
            },
            {
                id: 6,
                username: "TrollMaster",
                email: "troll@email.com",
                role: "user",
                status: "banned",
                created_at: "2024-03-05",
                last_login: "2024-03-10 22:10:00",
                avatar: "T"
            }
        ];

        function loadUsersAdmin() {
            if (!AppState.state.users) {
                AppState.state.users = sampleUsers;
                AppState.saveState();
            }

            updateStats();
            loadUsersTable();
            initSearchAndFilters();
        }

        function updateStats() {
            const users = AppState.state.users;
            
            document.getElementById('totalUsers').textContent = users.length;
            document.getElementById('activeUsers').textContent = 
                users.filter(user => user.status === 'active').length;
            document.getElementById('adminUsers').textContent = 
                users.filter(user => user.role === 'admin').length;
            
            const weekAgo = new Date();
            weekAgo.setDate(weekAgo.getDate() - 7);
            const newUsers = users.filter(user => new Date(user.created_at) > weekAgo);
            document.getElementById('newUsers').textContent = newUsers.length;
        }

        function loadUsersTable() {
            const users = AppState.state.users;
            const tableBody = document.getElementById('usersTable');
            
            if (users.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--gray);">
                            Aucun utilisateur trouvé.
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = users.map(user => `
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="user-avatar">${user.avatar}</div>
                            <div>
                                <strong>${user.username}</strong>
                            </div>
                        </div>
                    </td>
                    <td>${user.email}</td>
                    <td>
                        <span class="badge badge-${user.role}">
                            ${getRoleLabel(user.role)}
                        </span>
                    </td>
                    <td>${formatDate(user.created_at)}</td>
                    <td>${formatDateTime(user.last_login)}</td>
                    <td>
                        <span class="status-badge ${user.status}">
                            ${getStatusLabel(user.status)}
                        </span>
                    </td>
                    <td class="actions-cell">
                        <button class="btn btn-primary btn-sm" onclick="editUser(${user.id})">
                            ✏️ Modifier
                        </button>
                        ${user.role !== 'admin' ? `
                            <button class="btn btn-${user.status === 'banned' ? 'success' : 'warning'} btn-sm" 
                                    onclick="toggleUserStatus(${user.id})">
                                ${user.status === 'banned' ? '🔓 Débannir' : '🚫 Bannir'}
                            </button>
                        ` : ''}
                        ${user.role !== 'admin' ? `
                            <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">
                                🗑️ Supprimer
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `).join('');
        }

        function validateSearch() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput.value.trim();
            const searchBox = document.getElementById('searchBox');
            const searchError = document.getElementById('searchError');

            searchBox.classList.remove('error');
            searchError.style.display = 'none';

            if (searchTerm.length > 0 && searchTerm.length < 2) {
                searchBox.classList.add('error');
                searchError.style.display = 'block';
                return false;
            }

            return true;
        }

        function initSearchAndFilters() {
            const searchInput = document.getElementById('searchInput');
            const roleFilter = document.getElementById('roleFilter');
            const statusFilter = document.getElementById('statusFilter');

            function filterUsers() {
                if (!validateSearch()) {
                    return;
                }

                const searchTerm = searchInput.value.toLowerCase();
                const roleValue = roleFilter.value;
                const statusValue = statusFilter.value;

                const users = AppState.state.users;
                const filteredUsers = users.filter(user => {
                    const matchesSearch = !searchTerm || 
                                        user.username.toLowerCase().includes(searchTerm) ||
                                        user.email.toLowerCase().includes(searchTerm);
                    const matchesRole = !roleValue || user.role === roleValue;
                    const matchesStatus = !statusValue || user.status === statusValue;

                    return matchesSearch && matchesRole && matchesStatus;
                });

                displayFilteredUsers(filteredUsers);
            }

            searchInput.addEventListener('input', filterUsers);
            roleFilter.addEventListener('change', filterUsers);
            statusFilter.addEventListener('change', filterUsers);

            searchInput.addEventListener('input', function() {
                const searchBox = document.getElementById('searchBox');
                const searchError = document.getElementById('searchError');
                
                if (this.value.trim().length >= 2 || this.value.trim().length === 0) {
                    searchBox.classList.remove('error');
                    searchError.style.display = 'none';
                }
            });
        }

        function displayFilteredUsers(users) {
            const tableBody = document.getElementById('usersTable');
            
            if (users.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--gray);">
                            Aucun utilisateur ne correspond aux critères de recherche.
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = users.map(user => `
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="user-avatar">${user.avatar}</div>
                            <div>
                                <strong>${user.username}</strong>
                            </div>
                        </div>
                    </td>
                    <td>${user.email}</td>
                    <td>
                        <span class="badge badge-${user.role}">
                            ${getRoleLabel(user.role)}
                        </span>
                    </td>
                    <td>${formatDate(user.created_at)}</td>
                    <td>${formatDateTime(user.last_login)}</td>
                    <td>
                        <span class="status-badge ${user.status}">
                            ${getStatusLabel(user.status)}
                        </span>
                    </td>
                    <td class="actions-cell">
                        <button class="btn btn-primary btn-sm" onclick="editUser(${user.id})">
                            ✏️ Modifier
                        </button>
                        ${user.role !== 'admin' ? `
                            <button class="btn btn-${user.status === 'banned' ? 'success' : 'warning'} btn-sm" 
                                    onclick="toggleUserStatus(${user.id})">
                                ${user.status === 'banned' ? '🔓 Débannir' : '🚫 Bannir'}
                            </button>
                        ` : ''}
                        ${user.role !== 'admin' ? `
                            <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">
                                🗑️ Supprimer
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `).join('');
        }

        function editUser(userId) {
            if (!userId || isNaN(userId)) {
                showNotification('ID utilisateur invalide', 'danger');
                return;
            }

            const user = AppState.state.users.find(u => u.id === userId);
            if (!user) {
                showNotification('Utilisateur non trouvé', 'danger');
                return;
            }

            window.location.href = `user-edit.php?id=${userId}`;
        }

        function toggleUserStatus(userId) {
            if (!userId || isNaN(userId)) {
                showNotification('ID utilisateur invalide', 'danger');
                return;
            }

            const users = AppState.state.users;
            const user = users.find(u => u.id === userId);
            
            if (user) {
                if (user.role === 'admin') {
                    showNotification('Impossible de modifier le statut d\'un administrateur', 'warning');
                    return;
                }

                if (user.status === 'banned') {
                    user.status = 'active';
                    showNotification('Utilisateur débanni avec succès!', 'success');
                } else {
                    user.status = 'banned';
                    showNotification('Utilisateur banni avec succès!', 'warning');
                }
                AppState.saveState();
                loadUsersAdmin();
            } else {
                showNotification('Utilisateur non trouvé', 'danger');
            }
        }

        function deleteUser(userId) {
            if (!userId || isNaN(userId)) {
                showNotification('ID utilisateur invalide', 'danger');
                return;
            }

            const user = AppState.state.users.find(u => u.id === userId);
            if (!user) {
                showNotification('Utilisateur non trouvé', 'danger');
                return;
            }

            if (user.role === 'admin') {
                showNotification('Impossible de supprimer un administrateur', 'warning');
                return;
            }

            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
                AppState.state.users = AppState.state.users.filter(user => user.id !== userId);
                AppState.saveState();
                loadUsersAdmin();
                showNotification('Utilisateur supprimé avec succès!', 'success');
            }
        }

        function getRoleLabel(role) {
            const roles = {
                'admin': 'Administrateur',
                'moderator': 'Modérateur',
                'user': 'Utilisateur'
            };
            return roles[role] || role;
        }

        function getStatusLabel(status) {
            const statuses = {
                'active': 'Actif',
                'inactive': 'Inactif',
                'banned': 'Banni'
            };
            return statuses[status] || status;
        }

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('fr-FR', options);
        }

        function formatDateTime(dateTimeString) {
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateTimeString).toLocaleDateString('fr-FR', options);
        }

        function logout() {
            AppState.logout();
            window.location.href = 'login.php';
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 2rem;
                background: ${type === 'success' ? 'var(--success)' : 
                            type === 'warning' ? 'var(--warning)' : 
                            type === 'danger' ? 'var(--danger)' : 'var(--primary)'};
                color: var(--dark);
                border-radius: 8px;
                font-weight: bold;
                z-index: 10000;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        if (!AppState.isAdminUser()) {
            window.location.href = 'login.php';
        } else {
            loadUsersAdmin();
        }
    </script>
</body>
</html>