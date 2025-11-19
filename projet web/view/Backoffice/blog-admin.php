<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Articles - Admin</title>
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

        .btn-success:hover {
            background: #26c46a;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--dark);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--light);
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

        .badge-category {
            background: rgba(0, 204, 255, 0.2);
            color: var(--secondary);
            border: 1px solid var(--secondary);
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-badge.published {
            background: rgba(46, 213, 115, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .status-badge.draft {
            background: rgba(255, 165, 2, 0.2);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .actions-cell {
            display: flex;
            gap: 0.5rem;
        }

        .title-cell {
            max-width: 250px;
        }

        .title-cell strong {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .comment-count {
            background: var(--secondary);
            color: var(--dark);
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: bold;
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

        .error-message {
            color: var(--danger);
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .search-input.error {
            border-color: var(--danger);
            box-shadow: 0 0 0 2px rgba(255, 71, 87, 0.2);
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

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
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
                    <a href="blog-admin.php" class="nav-link active">
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
                    <a href="users-admin.php" class="nav-link">
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
            <h1 class="page-title">Gestion des Articles</h1>
            <a href="blog-edit.php" class="btn btn-success">
                <i>+</i> Nouvel Article
            </a>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="totalArticles">0</div>
                <div class="stat-label">Total Articles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="publishedArticles">0</div>
                <div class="stat-label">Articles Publiés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="draftArticles">0</div>
                <div class="stat-label">Brouillons</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalComments">0</div>
                <div class="stat-label">Commentaires Total</div>
            </div>
        </div>

        <!-- Barre d'outils -->
        <div class="admin-toolbar">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="searchInput" placeholder="Rechercher un article...">
                <div class="error-message" id="searchError"></div>
            </div>
            <div class="filters">
                <select class="filter-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="published">Publiés</option>
                    <option value="draft">Brouillons</option>
                </select>
                <select class="filter-select" id="categoryFilter">
                    <option value="">Toutes les catégories</option>
                    <option value="review">Tests</option>
                    <option value="news">Actualités</option>
                    <option value="tutorial">Tutoriels</option>
                    <option value="trends">Tendances</option>
                </select>
            </div>
        </div>

        <!-- Tableau des Articles -->
        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Date</th>
                        <th>Commentaires</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="articlesTable">
                    <!-- Les articles seront chargés dynamiquement ici -->
                </tbody>
            </table>
        </div>
    </main>

    <script src="../assets/js/state-manager.js"></script>
    <script>
        const FormValidator = {
            validateSearch: function(value, minLength = 2) {
                if (value && value.length < minLength) {
                    return { isValid: false, message: `La recherche doit contenir au moins ${minLength} caractères` };
                }
                return { isValid: true };
            },

            showError: function(input, message) {
                const errorElement = document.getElementById(input.id + 'Error');
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                    input.classList.add('error');
                }
            },

            hideError: function(input) {
                const errorElement = document.getElementById(input.id + 'Error');
                if (errorElement) {
                    errorElement.style.display = 'none';
                    input.classList.remove('error');
                }
            }
        };

        function loadBlogAdmin() {
            updateStats();
            loadArticlesTable();
            initSearchAndFilters();
            setupValidation();
        }

        function setupValidation() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    const validation = FormValidator.validateSearch(this.value, 2);
                    if (!validation.isValid) {
                        FormValidator.showError(this, validation.message);
                    } else {
                        FormValidator.hideError(this);
                        filterArticles();
                    }
                } else {
                    FormValidator.hideError(this);
                    filterArticles();
                }
            });
        }

        function updateStats() {
            const articles = AppState.getAllArticles();
            const comments = AppState.state.comments;
            
            document.getElementById('totalArticles').textContent = articles.length;
            document.getElementById('publishedArticles').textContent = 
                articles.filter(a => a.status === 'published').length;
            document.getElementById('draftArticles').textContent = 
                articles.filter(a => a.status === 'draft').length;
            document.getElementById('totalComments').textContent = comments.length;
        }

        function loadArticlesTable() {
            const articles = AppState.getAllArticles();
            const tableBody = document.getElementById('articlesTable');
            
            if (articles.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--gray);">
                            Aucun article trouvé. <a href="blog-edit.php" style="color: var(--primary);">Créez votre premier article</a>.
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = articles.map(article => `
                <tr>
                    <td>${article.id}</td>
                    <td class="title-cell">
                        <strong>${article.title}</strong>
                    </td>
                    <td>
                        <span class="badge badge-category">${getCategoryLabel(article.category)}</span>
                    </td>
                    <td>${formatDate(article.created_at)}</td>
                    <td>
                        <span class="comment-count">${getCommentCount(article.id)}</span>
                    </td>
                    <td>
                        <span class="status-badge ${article.status}">
                            ${article.status === 'published' ? 'Publié' : 'Brouillon'}
                        </span>
                    </td>
                    <td class="actions-cell">
                        <a href="blog-edit.php?id=${article.id}" class="btn btn-primary btn-sm">
                            ✏️ Modifier
                        </a>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(${article.id})">
                            🗑️ Supprimer
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function getCommentCount(articleId) {
            const comments = AppState.state.comments;
            return comments.filter(comment => comment.article_id === articleId).length;
        }

        function initSearchAndFilters() {
            const statusFilter = document.getElementById('statusFilter');
            const categoryFilter = document.getElementById('categoryFilter');

            function filterArticles() {
                const searchInput = document.getElementById('searchInput');
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;
                const categoryValue = categoryFilter.value;

                if (searchTerm && searchTerm.length < 2) {
                    return;
                }

                const articles = AppState.getAllArticles();
                const filteredArticles = articles.filter(article => {
                    const matchesSearch = !searchTerm || 
                                        article.title.toLowerCase().includes(searchTerm) ||
                                        article.content.toLowerCase().includes(searchTerm);
                    const matchesStatus = !statusValue || article.status === statusValue;
                    const matchesCategory = !categoryValue || article.category === categoryValue;

                    return matchesSearch && matchesStatus && matchesCategory;
                });

                displayFilteredArticles(filteredArticles);
            }

            statusFilter.addEventListener('change', filterArticles);
            categoryFilter.addEventListener('change', filterArticles);
        }

        function displayFilteredArticles(articles) {
            const tableBody = document.getElementById('articlesTable');
            
            if (articles.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--gray);">
                            Aucun article ne correspond aux critères de recherche.
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = articles.map(article => `
                <tr>
                    <td>${article.id}</td>
                    <td class="title-cell">
                        <strong>${article.title}</strong>
                    </td>
                    <td>
                        <span class="badge badge-category">${getCategoryLabel(article.category)}</span>
                    </td>
                    <td>${formatDate(article.created_at)}</td>
                    <td>
                        <span class="comment-count">${getCommentCount(article.id)}</span>
                    </td>
                    <td>
                        <span class="status-badge ${article.status}">
                            ${article.status === 'published' ? 'Publié' : 'Brouillon'}
                        </span>
                    </td>
                    <td class="actions-cell">
                        <a href="blog-edit.php?id=${article.id}" class="btn btn-primary btn-sm">
                            ✏️ Modifier
                        </a>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(${article.id})">
                            🗑️ Supprimer
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function confirmDelete(articleId) {
            if (validateDeleteAction()) {
                AppState.deleteArticle(articleId);
                loadBlogAdmin();
                showNotification('Article supprimé avec succès!', 'success');
            }
        }

        function validateDeleteAction() {
            const confirmed = confirm('Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.');
            if (!confirmed) {
                showNotification('Suppression annulée', 'warning');
                return false;
            }
            return true;
        }

        function getCategoryLabel(category) {
            const categories = {
                'review': 'Test & Review',
                'news': 'Actualité',
                'tutorial': 'Tutoriel',
                'trends': 'Tendances',
                'interview': 'Interview'
            };
            return categories[category] || category;
        }

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('fr-FR', options);
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
                background: ${type === 'success' ? 'var(--success)' : type === 'warning' ? 'var(--warning)' : 'var(--danger)'};
                color: var(--dark);
                border-radius: 8px;
                font-weight: bold;
                z-index: 10000;
                animation: slideIn 0.3s ease-out;
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
            loadBlogAdmin();
        }

        AppState.on('articlesUpdated', loadBlogAdmin);
    </script>
</body>
</html>