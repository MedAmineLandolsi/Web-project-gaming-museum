<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modération Commentaires - Admin</title>
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

        .comments-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .comment-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid var(--primary);
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .comment-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .comment-meta {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .comment-author {
            color: var(--primary);
            font-weight: bold;
            font-size: 1.1rem;
        }

        .comment-article {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .comment-article a {
            color: inherit;
            text-decoration: none;
        }

        .comment-article a:hover {
            text-decoration: underline;
        }

        .comment-date {
            color: var(--gray);
            font-size: 0.8rem;
        }

        .comment-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .comment-content {
            color: var(--light);
            line-height: 1.6;
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .no-comments {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }

        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
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

        .search-box {
            position: relative;
            flex: 1;
            min-width: 300px;
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

        .error-message {
            color: var(--danger);
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .form-control.error {
            border-color: var(--danger);
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
            
            .comment-header {
                flex-direction: column;
            }
            
            .comment-actions {
                width: 100%;
                justify-content: flex-start;
            }
            
            .filters {
                flex-direction: column;
            }
            
            .search-box {
                min-width: auto;
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
                    <a href="comments-admin.php" class="nav-link active">
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
            <h1 class="page-title">Modération des Commentaires</h1>
            <div class="comment-actions">
                <button class="btn btn-success" onclick="approveAll()">
                    ✅ Tout Approuver
                </button>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="totalComments">0</div>
                <div class="stat-label">Total Commentaires</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="recentComments">0</div>
                <div class="stat-label">Dernières 24h</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="topCommenter">-</div>
                <div class="stat-label">Top Commentateur</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="avgLength">0</div>
                <div class="stat-label">Longueur Moyenne</div>
            </div>
        </div>

        <!-- Filtres et Recherche -->
        <div class="filters">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="searchInput" placeholder="Rechercher un commentaire...">
            </div>
            <select class="filter-select" id="articleFilter">
                <option value="">Tous les articles</option>
            </select>
            <select class="filter-select" id="dateFilter">
                <option value="">Toutes les dates</option>
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
            </select>
        </div>

        <!-- Liste des Commentaires -->
        <div class="card">
            <div class="comments-list" id="commentsList">
                <!-- Les commentaires seront chargés dynamiquement ici -->
            </div>
        </div>
    </main>

    <script src="../assets/js/state-manager.js"></script>
    <script>
        const FormValidator = {
            validateText: function(value, minLength = 1) {
                return value && value.trim().length >= minLength;
            },

            validateEmail: function(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            },

            validateNumber: function(number, min = null, max = null) {
                const num = parseFloat(number);
                if (isNaN(num)) return false;
                if (min !== null && num < min) return false;
                if (max !== null && num > max) return false;
                return true;
            },

            showError: function(input, message) {
                let errorElement = input.parentNode.querySelector('.error-message');
                if (!errorElement) {
                    errorElement = document.createElement('div');
                    errorElement.className = 'error-message';
                    input.parentNode.appendChild(errorElement);
                }
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                input.classList.add('error');
            },

            hideError: function(input) {
                const errorElement = input.parentNode.querySelector('.error-message');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
                input.classList.remove('error');
            }
        };

        function loadCommentsAdmin() {
            updateStats();
            loadCommentsList();
            initFilters();
            setupValidation();
        }

        function setupValidation() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0 && !FormValidator.validateText(this.value, 2)) {
                    FormValidator.showError(this, 'La recherche doit contenir au moins 2 caractères');
                } else {
                    FormValidator.hideError(this);
                    filterComments();
                }
            });
        }

        function updateStats() {
            const comments = AppState.state.comments;
            const articles = AppState.state.articles;
            
            document.getElementById('totalComments').textContent = comments.length;
            
            const last24h = new Date();
            last24h.setDate(last24h.getDate() - 1);
            const recentComments = comments.filter(comment => 
                new Date(comment.created_at) > last24h
            );
            document.getElementById('recentComments').textContent = recentComments.length;
            
            const commenters = {};
            comments.forEach(comment => {
                commenters[comment.author] = (commenters[comment.author] || 0) + 1;
            });
            const topCommenter = Object.keys(commenters).reduce((a, b) => 
                commenters[a] > commenters[b] ? a : b, ''
            );
            document.getElementById('topCommenter').textContent = topCommenter || '-';
            
            const avgLength = comments.length > 0 ? 
                Math.round(comments.reduce((sum, comment) => sum + comment.content.length, 0) / comments.length) : 0;
            document.getElementById('avgLength').textContent = avgLength + ' chars';
        }

        function loadCommentsList() {
            const comments = AppState.state.comments;
            const articles = AppState.state.articles;
            const commentsList = document.getElementById('commentsList');
            
            if (comments.length === 0) {
                commentsList.innerHTML = `
                    <div class="no-comments">
                        <h3>Aucun commentaire à modérer</h3>
                        <p>Les commentaires des visiteurs apparaîtront ici.</p>
                    </div>
                `;
                return;
            }

            comments.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            commentsList.innerHTML = comments.map(comment => {
                const article = articles.find(a => a.id === comment.article_id);
                const articleTitle = article ? article.title : 'Article supprimé';
                const articleLink = article ? `../frontoffice/blog-single.php?id=${article.id}` : '#';
                
                return `
                    <div class="comment-item" data-comment-id="${comment.id}">
                        <div class="comment-header">
                            <div class="comment-meta">
                                <div class="comment-author">${comment.author}</div>
                                <div class="comment-article">
                                    sur <a href="${articleLink}" target="_blank">${articleTitle}</a>
                                </div>
                                <div class="comment-date">${formatDateTime(comment.created_at)}</div>
                            </div>
                            <div class="comment-actions">
                                <button class="btn btn-success btn-sm" onclick="viewArticle(${comment.article_id})">
                                    👁️ Voir l'article
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteComment(${comment.id})">
                                    🗑️ Supprimer
                                </button>
                            </div>
                        </div>
                        <div class="comment-content">
                            ${comment.content}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function initFilters() {
            const articles = AppState.state.articles;
            const articleFilter = document.getElementById('articleFilter');
            
            articleFilter.innerHTML = '<option value="">Tous les articles</option>' +
                articles.map(article => 
                    `<option value="${article.id}">${article.title}</option>`
                ).join('');
            
            document.getElementById('articleFilter').addEventListener('change', filterComments);
            document.getElementById('dateFilter').addEventListener('change', filterComments);
        }

        function filterComments() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const articleId = document.getElementById('articleFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;
            
            const comments = AppState.state.comments;
            const filteredComments = comments.filter(comment => {
                const matchesSearch = !searchTerm || 
                    comment.author.toLowerCase().includes(searchTerm) ||
                    comment.content.toLowerCase().includes(searchTerm);
                
                const matchesArticle = !articleId || comment.article_id === parseInt(articleId);
                
                const matchesDate = !dateFilter || filterByDate(comment.created_at, dateFilter);
                
                return matchesSearch && matchesArticle && matchesDate;
            });
            
            displayFilteredComments(filteredComments);
        }

        function filterByDate(dateString, filterType) {
            const commentDate = new Date(dateString);
            const now = new Date();
            
            switch(filterType) {
                case 'today':
                    return commentDate.toDateString() === now.toDateString();
                case 'week':
                    const weekAgo = new Date(now);
                    weekAgo.setDate(weekAgo.getDate() - 7);
                    return commentDate > weekAgo;
                case 'month':
                    const monthAgo = new Date(now);
                    monthAgo.setMonth(monthAgo.getMonth() - 1);
                    return commentDate > monthAgo;
                default:
                    return true;
            }
        }

        function displayFilteredComments(comments) {
            const commentsList = document.getElementById('commentsList');
            const articles = AppState.state.articles;
            
            if (comments.length === 0) {
                commentsList.innerHTML = `
                    <div class="no-comments">
                        <h3>Aucun commentaire ne correspond aux critères</h3>
                        <p>Essayez de modifier vos filtres de recherche.</p>
                    </div>
                `;
                return;
            }

            commentsList.innerHTML = comments.map(comment => {
                const article = articles.find(a => a.id === comment.article_id);
                const articleTitle = article ? article.title : 'Article supprimé';
                const articleLink = article ? `../frontoffice/blog-single.php?id=${article.id}` : '#';
                
                return `
                    <div class="comment-item" data-comment-id="${comment.id}">
                        <div class="comment-header">
                            <div class="comment-meta">
                                <div class="comment-author">${comment.author}</div>
                                <div class="comment-article">
                                    sur <a href="${articleLink}" target="_blank">${articleTitle}</a>
                                </div>
                                <div class="comment-date">${formatDateTime(comment.created_at)}</div>
                            </div>
                            <div class="comment-actions">
                                <button class="btn btn-success btn-sm" onclick="viewArticle(${comment.article_id})">
                                    👁️ Voir l'article
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteComment(${comment.id})">
                                    🗑️ Supprimer
                                </button>
                            </div>
                        </div>
                        <div class="comment-content">
                            ${comment.content}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function viewArticle(articleId) {
            window.open(`../frontoffice/blog-single.php?id=${articleId}`, '_blank');
        }

        function deleteComment(commentId) {
            if (validateDeleteAction()) {
                AppState.deleteComment(commentId);
                loadCommentsAdmin();
                showNotification('Commentaire supprimé avec succès!', 'success');
            }
        }

        function validateDeleteAction() {
            const confirmed = confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ? Cette action est irréversible.');
            if (!confirmed) {
                showNotification('Suppression annulée', 'warning');
                return false;
            }
            return true;
        }

        function approveAll() {
            if (validateApproveAll()) {
                showNotification('Tous les commentaires ont été approuvés!', 'success');
            }
        }

        function validateApproveAll() {
            const confirmed = confirm('Approuver tous les commentaires affichés ?');
            if (!confirmed) {
                showNotification('Action annulée', 'warning');
                return false;
            }
            return true;
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
                background: ${type === 'success' ? 'var(--success)' : type === 'warning' ? 'var(--warning)' : 'var(--danger)'};
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
            loadCommentsAdmin();
        }

        AppState.on('commentsUpdated', loadCommentsAdmin);
    </script>
</body>
</html>