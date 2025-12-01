<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Article.php';
include_once '../../models/Commentaire.php';

// Vérifier l'authentification
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$articleModel = new Article($db);
$commentaireModel = new Commentaire($db);

// Récupérer tous les articles
$articles = $articleModel->lire()->fetchAll(PDO::FETCH_ASSOC);

// Fonctions helper
function getCategoryLabel($category) {
    $categories = [
        'review' => 'Test & Review',
        'news' => 'Actualité',
        'tutorial' => 'Tutoriel',
        'trends' => 'Tendances'
    ];
    return $categories[$category] ?? $category;
}

function getStatusLabel($status) {
    $statuses = [
        'published' => 'Public',
        'draft' => 'Brouillon',
        'pending' => 'En attente'
    ];
    return $statuses[$status] ?? 'En attente';
}

function formatDate($dateString) {
    try {
        $date = new DateTime($dateString);
        return $date->format('d/m/Y');
    } catch (Exception $e) {
        return 'Date inconnue';
    }
}

// Compter les articles par statut
$totalArticles = count($articles);
$publishedArticles = 0;
$draftArticles = 0;
$pendingArticles = 0;

// Préparer un tableau pour stocker le nombre de commentaires par article
$commentCounts = [];

foreach ($articles as $article) {
    switch ($article['Statut'] ?? 'pending') {
        case 'published':
            $publishedArticles++;
            break;
        case 'draft':
            $draftArticles++;
            break;
        case 'pending':
            $pendingArticles++;
            break;
    }
    
    // Compter les commentaires pour chaque article
    $articleId = $article['Article_ID'];
    $commentCounts[$articleId] = $commentaireModel->getCommentCountByArticle($articleId);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles Blog - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
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

        .btn-view-site {
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            border: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            text-decoration: none;
        }

        .btn-view-site:hover {
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.6);
            transform: translateY(-2px);
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
            position: relative;
            z-index: 10;
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

        .btn-primary {
            background: var(--secondary-purple);
            color: var(--dark-bg);
            border: 2px solid var(--secondary-purple);
        }

        .btn-primary:hover {
            background: transparent;
            color: var(--secondary-purple);
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.5);
        }

        .btn-info {
            background: var(--primary-green);
            color: var(--dark-bg);
            border: 2px solid var(--primary-green);
        }

        .btn-info:hover {
            background: transparent;
            color: var(--primary-green);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
        }

        .btn-warning {
            background: var(--warning-orange);
            color: var(--dark-bg);
            border: 2px solid var(--warning-orange);
        }

        .btn-warning:hover {
            background: transparent;
            color: var(--warning-orange);
            box-shadow: 0 0 20px rgba(255, 149, 0, 0.5);
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

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05), rgba(189, 0, 255, 0.05));
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .card:hover::before {
            opacity: 1;
        }

        /* Table */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            border: 2px solid var(--border-color);
            position: relative;
            z-index: 1;
        }

        .admin-table th,
        .admin-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            position: relative;
            z-index: 2;
        }

        .admin-table th {
            background: rgba(0, 255, 65, 0.1);
            color: var(--primary-green);
            font-weight: bold;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            border-right: 1px solid var(--border-color);
        }

        .admin-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* Badges */
        .badge {
            padding: 0.3rem 0.8rem;
            border-radius: 0;
            font-size: 0.6rem;
            font-weight: bold;
            font-family: 'Press Start 2P', cursive;
            display: inline-block;
        }

        .badge-category {
            background: rgba(189, 0, 255, 0.2);
            border: 1px solid var(--secondary-purple);
            color: var(--secondary-purple);
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 0;
            font-size: 0.6rem;
            font-weight: bold;
            font-family: 'Press Start 2P', cursive;
            display: inline-block;
        }

        .status-badge.published {
            background: rgba(0, 255, 65, 0.2);
            border: 1px solid var(--success-green);
            color: var(--success-green);
        }

        .status-badge.draft {
            background: rgba(255, 149, 0, 0.2);
            border: 1px solid var(--warning-orange);
            color: var(--warning-orange);
        }

        .status-badge.pending {
            background: rgba(0, 204, 255, 0.2);
            border: 1px solid var(--secondary-purple);
            color: var(--secondary-purple);
        }

        /* Actions */
        .actions-cell {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            position: relative;
            z-index: 100;
        }

        .actions-cell .btn {
            white-space: nowrap;
            min-width: auto;
            position: relative;
            z-index: 1000;
        }

        .title-cell {
            max-width: 250px;
        }

        .title-cell strong {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        /* Search and Filters */
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
            border: 2px solid var(--primary-green);
        }

        .search-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            background: var(--card-bg);
            border: none;
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        .search-input:focus {
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-green);
        }

        .filters {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 0.8rem;
            background: var(--card-bg);
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            min-width: 150px;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        /* Responsive */
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

            .stats-overview {
                grid-template-columns: repeat(2, 1fr);
            }

            .actions-cell {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .stats-overview {
                grid-template-columns: 1fr;
            }

            .admin-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                min-width: auto;
            }

            .admin-table {
                display: block;
                overflow-x: auto;
            }

            .actions-cell {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .actions-cell .btn {
                flex: 1;
                min-width: 120px;
            }
        }

        @media (max-width: 480px) {
            .actions-cell {
                flex-direction: column;
            }
            
            .actions-cell .btn {
                width: 100%;
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

        /* Assurer que les boutons sont cliquables */
        .btn, .btn *, a, a * {
            pointer-events: auto !important;
        }

        .actions-cell, .actions-cell * {
            pointer-events: auto !important;
        }

        .admin-table td, .admin-table th {
            pointer-events: auto !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
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
                <li class="nav-item">
                    <a href="comments-admin.php" class="nav-link">
                        <span class="nav-icon">💬</span>
                        <span class="nav-text">COMMENTAIRES</span>
                    </a>
                </li>
                <li class="nav-item active">
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

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <h1 class="page-title">GESTION DES ARTICLES</h1>
            <a href="blog-edit.php" class="btn btn-success">
                ➕ NOUVEL ARTICLE
            </a>
        </div>

        <!-- Statistiques -->
        <div class="stats-overview">
            <div class="stat-card stat-primary">
                <div class="stat-icon">📊</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $totalArticles; ?></div>
                    <div class="stat-label">TOTAL ARTICLES</div>
                </div>
            </div>
            <div class="stat-card stat-secondary">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $publishedArticles; ?></div>
                    <div class="stat-label">ARTICLES PUBLICS</div>
                </div>
            </div>
            <div class="stat-card stat-accent">
                <div class="stat-icon">📝</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $draftArticles; ?></div>
                    <div class="stat-label">BROUILLONS</div>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $pendingArticles; ?></div>
                    <div class="stat-label">EN ATTENTE</div>
                </div>
            </div>
        </div>

        <!-- Barre d'outils -->
        <div class="admin-toolbar">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="searchInput" placeholder="RECHERCHER UN ARTICLE...">
            </div>
            <div class="filters">
                <select class="filter-select" id="statusFilter">
                    <option value="">TOUS LES STATUTS</option>
                    <option value="published">PUBLIÉS</option>
                    <option value="draft">BROUILLONS</option>
                    <option value="pending">EN ATTENTE</option>
                </select>
                <select class="filter-select" id="categoryFilter">
                    <option value="">TOUTES LES CATÉGORIES</option>
                    <option value="review">TEST & REVIEW</option>
                    <option value="news">ACTUALITÉS</option>
                    <option value="tutorial">TUTORIELS</option>
                    <option value="trends">TENDANCES</option>
                </select>
            </div>
        </div>

        <!-- Tableau des Articles -->
        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>TITRE</th>
                        <th>CATÉGORIE</th>
                        <th>AUTEUR</th>
                        <th>DATE</th>
                        <th>STATUT</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="articlesTable">
                    <?php if (count($articles) > 0): ?>
                        <?php foreach ($articles as $article): ?>
                        <?php 
                        $categorie = isset($article['Categorie']) ? $article['Categorie'] : 'news';
                        $articleId = $article['Article_ID'];
                        $hasComments = ($commentCounts[$articleId] ?? 0) > 0;
                        ?>
                        <tr>
                            <td><?php echo $articleId; ?></td>
                            <td class="title-cell">
                                <strong><?php echo htmlspecialchars($article['Titre']); ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-category">
                                    <?php echo getCategoryLabel($categorie); ?>
                                </span>
                            </td>
                            <td>AUTEUR <?php echo $article['Auteur_ID']; ?></td>
                            <td><?php echo formatDate($article['Date_Publication']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $article['Statut'] ?? 'pending'; ?>">
                                    <?php echo getStatusLabel($article['Statut'] ?? 'pending'); ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <?php if (($article['Statut'] ?? 'pending') === 'published'): ?>
                                    <a href="../../views/frontoffice/blog-single.php?id=<?php echo $articleId; ?>" target="_blank" class="btn btn-info btn-sm">
                                        👁️ VOIR
                                    </a>
                                <?php endif; ?>
                                <!-- BOUTON COMMENTAIRES - AFFICHÉ SEULEMENT S'IL Y A DES COMMENTAIRES -->
                                <?php if ($hasComments): ?>
                                    <a href="comments-admin.php?article_id=<?php echo $articleId; ?>" class="btn btn-warning btn-sm">
                                        💬 COMMENTS (<?php echo $commentCounts[$articleId]; ?>)
                                    </a>
                                <?php endif; ?>
                                <a href="blog-edit.php?id=<?php echo $articleId; ?>" class="btn btn-primary btn-sm">
                                    ✏️ MODIFIER
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $articleId; ?>)">
                                    🗑️ SUPPRIMER
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-gray); font-family: 'Press Start 2P', cursive; font-size: 0.6rem;">
                                AUCUN ARTICLE TROUVÉ. <a href="blog-edit.php" style="color: var(--primary-green);">CRÉEZ VOTRE PREMIER ARTICLE</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function confirmDelete(articleId) {
            if (confirm('ÊTES-VOUS SÛR DE VOULOIR SUPPRIMER CET ARTICLE ? CETTE ACTION EST IRRÉVERSIBLE.')) {
                window.location.href = 'delete-article.php?id=' + articleId;
            }
        }

        // Filtrage côté client
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const categoryFilter = document.getElementById('categoryFilter');
            const tableBody = document.getElementById('articlesTable');
            const originalRows = tableBody.innerHTML;

            function filterArticles() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;
                const categoryValue = categoryFilter.value;

                const rows = tableBody.getElementsByTagName('tr');
                let hasVisibleRows = false;

                for (let row of rows) {
                    if (row.cells.length < 2) continue;

                    const title = row.cells[1].textContent.toLowerCase();
                    const category = row.cells[2].textContent.toLowerCase();
                    const status = row.cells[5].textContent.toLowerCase();

                    const matchesSearch = title.includes(searchTerm);
                    const matchesStatus = !statusValue || status.includes(statusValue);
                    const matchesCategory = !categoryValue || category.includes(categoryValue.toLowerCase());

                    if (matchesSearch && matchesStatus && matchesCategory) {
                        row.style.display = '';
                        hasVisibleRows = true;
                    } else {
                        row.style.display = 'none';
                    }
                }

                if (!hasVisibleRows) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-gray); font-family: 'Press Start 2P', cursive; font-size: 0.6rem;">
                                AUCUN ARTICLE NE CORRESPOND AUX CRITÈRES DE RECHERCHE.
                            </td>
                        </tr>
                    `;
                }
            }

            searchInput.addEventListener('input', filterArticles);
            statusFilter.addEventListener('change', filterArticles);
            categoryFilter.addEventListener('change', filterArticles);
        });
    </script>
</body>
</html>