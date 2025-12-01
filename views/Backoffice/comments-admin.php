<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Commentaire.php';
include_once '../../models/Article.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$commentaireModel = new Commentaire($db);
$articleModel = new Article($db);

$commentaires = $commentaireModel->lireAvecArticles()->fetchAll(PDO::FETCH_ASSOC);
$articles = $articleModel->lire()->fetchAll(PDO::FETCH_ASSOC);

function formatDateTime($dateString) {
    try {
        $date = new DateTime($dateString);
        return $date->format('d/m/Y À H:i');
    } catch (Exception $e) {
        return 'DATE INCONNUE';
    }
}

$totalComments = count($commentaires);
$recentComments = 0;
$yesterday = new DateTime('yesterday');
foreach ($commentaires as $comment) {
    $commentDate = new DateTime($comment['Date_Commentaire']);
    if ($commentDate > $yesterday) {
        $recentComments++;
    }
}

$commenters = [];
foreach ($commentaires as $comment) {
    $author = $comment['Auteur'];
    $commenters[$author] = ($commenters[$author] ?? 0) + 1;
}
$topCommenter = $commenters ? array_keys($commenters, max($commenters))[0] : '-';

$totalLength = 0;
foreach ($commentaires as $comment) {
    $totalLength += strlen($comment['Contenu']);
}
$avgLength = $totalComments > 0 ? round($totalLength / $totalComments) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COMMENTAIRES - ADMIN</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-green: #00FF41;
            --secondary-purple: #BD00FF;
            --accent-pink: #FF006E;
            --warning-orange: #FF9500;
            --dark-bg: #0a0a0a;
            --darker-bg: #050505;
            --card-bg: #1a1a1a;
            --sidebar-bg: #0d0d0d;
            --text-white: #ffffff;
            --text-gray: #888888;
            --text-light-gray: #aaaaaa;
            --border-color: #333333;
            --success-green: #00FF41;
            --danger-red: #FF0055;
        }

        body {
            font-family: 'Press Start 2P', cursive;
            background-color: var(--dark-bg);
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 65, 0.02) 2px,
                    rgba(0, 255, 65, 0.02) 4px
                );
            color: var(--text-white);
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 300px;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--darker-bg) 100%);
            border-right: 2px solid var(--primary-green);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 5px 0 30px rgba(0, 255, 65, 0.2);
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 2px solid var(--primary-green);
        }

        .admin-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-box {
            width: 50px;
            height: 50px;
            border: 2px solid var(--primary-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.5rem;
            color: var(--primary-green);
            background-color: var(--darker-bg);
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.4);
        }

        .admin-title h2 {
            font-size: 0.8rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 0.3rem;
        }

        .admin-badge {
            font-size: 0.5rem;
            color: var(--secondary-purple);
            text-shadow: 0 0 5px var(--secondary-purple);
        }

        /* Sidebar Navigation */
        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 0;
        }

        .nav-list {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.6rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .nav-item a:hover {
            background-color: rgba(0, 255, 65, 0.05);
            color: var(--primary-green);
            border-left-color: var(--primary-green);
        }

        .nav-item.active a {
            background-color: rgba(0, 255, 65, 0.1);
            color: var(--primary-green);
            border-left-color: var(--primary-green);
            box-shadow: inset 0 0 20px rgba(0, 255, 65, 0.1);
        }

        .nav-icon {
            font-size: 1.2rem;
            filter: grayscale(1);
        }

        .nav-item.active .nav-icon,
        .nav-item:hover .nav-icon {
            filter: grayscale(0);
        }

        .nav-text {
            flex: 1;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 300px;
            padding: 2rem;
        }

        /* Top Bar */
        .top-bar {
            background: linear-gradient(135deg, var(--sidebar-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 30px rgba(0, 255, 65, 0.2);
        }

        .page-title {
            font-size: 1.2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        /* Buttons */
        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 0;
            cursor: pointer;
            font-size: 0.6rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            font-family: 'Press Start 2P', cursive;
            text-align: center;
            justify-content: center;
        }

        .btn-success {
            background: var(--primary-green);
            color: var(--dark-bg);
            border: 2px solid var(--primary-green);
        }

        .btn-success:hover {
            background: transparent;
            color: var(--primary-green);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
        }

        .btn-danger {
            background: var(--danger-red);
            color: var(--text-white);
            border: 2px solid var(--danger-red);
        }

        .btn-danger:hover {
            background: transparent;
            color: var(--danger-red);
            box-shadow: 0 0 20px rgba(255, 0, 85, 0.5);
        }

        .btn-sm {
            padding: 0.6rem 1rem;
            font-size: 0.5rem;
        }

        /* Cards */
        .card {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--border-color);
            padding: 2rem;
            position: relative;
            overflow: visible;
        }

        /* Stats Overview */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--border-color);
            padding: 1.5rem;
            display: flex;
            gap: 1rem;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05), rgba(189, 0, 255, 0.05));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 65, 0.3);
        }

        .stat-primary {
            border-color: var(--primary-green);
        }

        .stat-secondary {
            border-color: var(--secondary-purple);
        }

        .stat-accent {
            border-color: var(--accent-pink);
        }

        .stat-warning {
            border-color: var(--warning-orange);
        }

        .stat-icon {
            font-size: 2.5rem;
            filter: grayscale(1);
        }

        .stat-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .stat-label {
            font-size: 0.5rem;
            color: var(--text-gray);
        }

        .stat-value {
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .stat-secondary .stat-value {
            color: var(--secondary-purple);
            text-shadow: 0 0 10px var(--secondary-purple);
        }

        .stat-accent .stat-value {
            color: var(--accent-pink);
            text-shadow: 0 0 10px var(--accent-pink);
        }

        .stat-warning .stat-value {
            color: var(--warning-orange);
            text-shadow: 0 0 10px var(--warning-orange);
        }

        /* Comments List */
        .comments-list {
            space-y: 1rem;
        }

        .comment-item {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05) 0%, rgba(189, 0, 255, 0.02) 100%);
            padding: 2rem;
            border: 2px solid var(--border-color);
            margin-bottom: 1.5rem;
            transition: all 0.3s;
            border-left: 4px solid var(--primary-green);
        }

        .comment-item:hover {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.08) 0%, rgba(189, 0, 255, 0.04) 100%);
            transform: translateX(8px);
            box-shadow: 0 8px 24px rgba(0, 255, 65, 0.1);
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .comment-meta {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .comment-author {
            color: var(--primary-green);
            font-weight: bold;
            font-size: 0.8rem;
            font-family: 'Press Start 2P', cursive;
        }

        .comment-article {
            color: var(--secondary-purple);
            font-size: 0.7rem;
            font-weight: bold;
            font-family: 'Press Start 2P', cursive;
        }

        .comment-article a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .comment-article a:hover {
            color: var(--primary-green);
        }

        .comment-date {
            color: var(--text-gray);
            font-size: 0.6rem;
            font-family: 'Press Start 2P', cursive;
        }

        .comment-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .comment-content {
            color: var(--text-light-gray);
            line-height: 1.7;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            font-size: 1rem;
            font-family: 'VT323', monospace;
        }

        .no-comments {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-gray);
            font-family: 'Press Start 2P', cursive;
        }

        .no-comments h3 {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: var(--text-light-gray);
        }

        /* Filters */
        .filters {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            min-width: 180px;
            font-size: 0.8rem;
            font-family: 'VT323', monospace;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 320px;
            border: 2px solid var(--primary-green);
        }

        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(255, 255, 255, 0.05);
            border: none;
            color: var(--text-white);
            font-size: 0.8rem;
            font-family: 'VT323', monospace;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-green);
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .sidebar.open {
                transform: translateX(0);
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
            
            .stats-overview {
                grid-template-columns: 1fr;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--darker-bg);
            border-left: 1px solid var(--border-color);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--primary-green), var(--secondary-purple));
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #00cc33, #9900cc);
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <div class="logo-box">ADMIN</div>
                <div class="admin-title">
                    <h2>GAMING BLOG</h2>
                    <div class="admin-badge">PANEL v1.0</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item active">
                    <a href="comments-admin.php" class="nav-link">
                        <span class="nav-icon">💬</span>
                        <span class="nav-text">COMMENTAIRES</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="blog-admin.php" class="nav-link">
                        <span class="nav-icon">📝</span>
                        <span class="nav-text">ARTICLES</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link">
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
                    <div class="admin-name">ADMIN</div>
                    <div class="admin-role">SUPER USER</div>
                </div>
            </div>
            <a href="../Frontoffice/index.php" class="btn btn-success" style="width: 100%; margin-bottom: 1rem; text-align: center; display: block;">
                🌐 SITE PUBLIC
            </a>
            <a href="logout.php" class="btn btn-danger" style="width: 100%; text-align: center; display: block;">
                🚪 DÉCONNEXION
            </a>
        </div>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h1 class="page-title">GESTION DES COMMENTAIRES</h1>
        </div>

        <div class="stats-overview">
            <div class="stat-card stat-primary">
                <div class="stat-icon">📊</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $totalComments; ?></div>
                    <div class="stat-label">TOTAL COMMENTAIRES</div>
                </div>
            </div>
            <div class="stat-card stat-secondary">
                <div class="stat-icon">🕐</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $recentComments; ?></div>
                    <div class="stat-label">DERNIÈRES 24H</div>
                </div>
            </div>
            <div class="stat-card stat-accent">
                <div class="stat-icon">👑</div>
                <div class="stat-content">
                    <div class="stat-value" style="font-size: 1rem;"><?php echo $topCommenter; ?></div>
                    <div class="stat-label">TOP COMMENTATEUR</div>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">📏</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $avgLength; ?></div>
                    <div class="stat-label">LONGUEUR MOYENNE</div>
                </div>
            </div>
        </div>

        <div class="filters">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="searchInput" placeholder="RECHERCHER UN COMMENTAIRE...">
            </div>
            <select class="filter-select" id="articleFilter">
                <option value="">TOUS LES ARTICLES</option>
                <?php foreach ($articles as $article): ?>
                    <option value="<?php echo $article['Article_ID']; ?>">
                        <?php echo htmlspecialchars($article['Titre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select class="filter-select" id="dateFilter">
                <option value="">TOUTES LES DATES</option>
                <option value="today">AUJOURD'HUI</option>
                <option value="week">CETTE SEMAINE</option>
                <option value="month">CE MOIS</option>
            </select>
        </div>

        <div class="card">
            <div class="comments-list" id="commentsList">
                <?php if (count($commentaires) > 0): ?>
                    <?php foreach ($commentaires as $comment): ?>
                    <div class="comment-item" data-comment-id="<?php echo $comment['ID']; ?>">
                        <div class="comment-header">
                            <div class="comment-meta">
                                <div class="comment-author"><?php echo htmlspecialchars($comment['Auteur']); ?></div>
                                <div class="comment-article">
                                    SUR <a href="../frontoffice/blog-single.php?id=<?php echo $comment['Article_ID']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($comment['Titre'] ?? 'ARTICLE SUPPRIMÉ'); ?>
                                    </a>
                                </div>
                                <div class="comment-date"><?php echo formatDateTime($comment['Date_Commentaire']); ?></div>
                            </div>
                            <div class="comment-actions">
                                <a href="../frontoffice/blog-single.php?id=<?php echo $comment['Article_ID']; ?>" target="_blank" class="btn btn-success btn-sm">
                                    👁️ VOIR L'ARTICLE
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $comment['ID']; ?>)">
                                    🗑️ SUPPRIMER
                                </button>
                            </div>
                        </div>
                        <div class="comment-content">
                            <?php echo nl2br(htmlspecialchars($comment['Contenu'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-comments">
                        <h3>💬 AUCUN COMMENTAIRE À MODÉRER</h3>
                        <p>LES COMMENTAIRES DES VISITEURS APPARAÎTRONT ICI.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        function confirmDelete(commentId) {
            if (confirm('ÊTES-VOUS SÛR DE VOULOIR SUPPRIMER CE COMMENTAIRE ? CETTE ACTION EST IRRÉVERSIBLE.')) {
                window.location.href = 'delete-comment.php?id=' + commentId;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const articleFilter = document.getElementById('articleFilter');
            const dateFilter = document.getElementById('dateFilter');
            const commentItems = document.querySelectorAll('.comment-item');

            function filterComments() {
                const searchTerm = searchInput.value.toLowerCase();
                const articleValue = articleFilter.value;
                const dateValue = dateFilter.value;

                let hasVisibleComments = false;

                commentItems.forEach(item => {
                    const author = item.querySelector('.comment-author').textContent.toLowerCase();
                    const content = item.querySelector('.comment-content').textContent.toLowerCase();
                    const article = item.querySelector('.comment-article a').textContent.toLowerCase();
                    const date = item.querySelector('.comment-date').textContent;

                    const matchesSearch = author.includes(searchTerm) || content.includes(searchTerm);
                    const matchesArticle = !articleValue || item.getAttribute('data-comment-id').includes(articleValue);
                    const matchesDate = !dateValue || filterByDate(date, dateValue);

                    if (matchesSearch && matchesArticle && matchesDate) {
                        item.style.display = 'block';
                        hasVisibleComments = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                const noComments = document.querySelector('.no-comments');
                if (!hasVisibleComments && !noComments) {
                    const commentsList = document.getElementById('commentsList');
                    commentsList.innerHTML = `
                        <div class="no-comments">
                            <h3>🔍 AUCUN COMMENTAIRE NE CORRESPOND AUX CRITÈRES</h3>
                            <p>ESSAYEZ DE MODIFIER VOS FILTRES DE RECHERCHE.</p>
                        </div>
                    `;
                } else if (hasVisibleComments && noComments) {
                    location.reload();
                }
            }

            function filterByDate(dateString, filterType) {
                const now = new Date();
                const commentDate = parseFrenchDate(dateString);
                
                if (!commentDate) return true;

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

            function parseFrenchDate(dateString) {
                const parts = dateString.split(' ');
                if (parts.length >= 1) {
                    const datePart = parts[0];
                    const [day, month, year] = datePart.split('/');
                    return new Date(year, month - 1, day);
                }
                return null;
            }

            searchInput.addEventListener('input', filterComments);
            articleFilter.addEventListener('change', filterComments);
            dateFilter.addEventListener('change', filterComments);
        });
    </script>
</body>
</html>