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

// Récupérer les statistiques
$articles = $articleModel->lire()->fetchAll(PDO::FETCH_ASSOC);
$commentaires = $commentaireModel->lire()->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'] ?? '';
    
    if ($section === 'general') {
        // Sauvegarde des paramètres généraux
        $site_name = htmlspecialchars($_POST['site_name'] ?? '');
        $site_slogan = htmlspecialchars($_POST['site_slogan'] ?? '');
        $contact_email = htmlspecialchars($_POST['contact_email'] ?? '');
        $contact_phone = htmlspecialchars($_POST['contact_phone'] ?? '');
        $site_description = htmlspecialchars($_POST['site_description'] ?? '');
        $site_keywords = htmlspecialchars($_POST['site_keywords'] ?? '');
        
        $success_message = '✅ PARAMÈTRES GÉNÉRAUX SAUVEGARDÉS AVEC SUCCÈS !';
        
    } elseif ($section === 'content') {
        // Sauvegarde des paramètres de contenu
        $auto_moderate = isset($_POST['auto_moderate']) ? 1 : 0;
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $comment_limit = intval($_POST['comment_limit'] ?? 100);
        $max_image_size = intval($_POST['max_image_size'] ?? 5);
        
        $success_message = '✅ PARAMÈTRES DE CONTENU SAUVEGARDÉS AVEC SUCCÈS !';
    }
}

// Calcul des statistiques système
$totalArticles = count($articles);
$totalComments = count($commentaires);
$storageUsed = $totalArticles * 2 + $totalComments * 0.5; // Estimation en KB
$lastUpdate = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PARAMÈTRES - ADMIN</title>
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

        /* Tabs */
        .settings-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2.5rem;
            border-bottom: 2px solid var(--primary-green);
            flex-wrap: wrap;
        }

        .tab-btn {
            background: none;
            border: none;
            color: var(--text-gray);
            padding: 1rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
            font-size: 0.6rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Press Start 2P', cursive;
        }

        .tab-btn.active {
            color: var(--primary-green);
            border-bottom-color: var(--primary-green);
            background: rgba(0, 255, 65, 0.05);
        }

        .tab-btn:hover {
            color: var(--text-white);
            background: rgba(0, 255, 65, 0.1);
        }

        /* Cards */
        .card {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--border-color);
            padding: 2rem;
            position: relative;
            overflow: visible;
            margin-bottom: 2rem;
        }

        .card-title {
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            font-size: 1rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            color: var(--primary-green);
            font-weight: bold;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Press Start 2P', cursive;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            font-size: 1rem;
            font-family: 'VT323', monospace;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
        }

        select.form-control {
            cursor: pointer;
        }

        .form-help {
            color: var(--text-gray);
            font-size: 0.5rem;
            margin-top: 0.5rem;
            display: block;
            font-family: 'Press Start 2P', cursive;
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

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-white);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* Toggle Switch */
        .toggle-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border-color);
        }

        .toggle-label {
            color: var(--text-white);
            font-weight: bold;
            flex: 1;
            font-size: 0.6rem;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.1);
            transition: .4s;
            border-radius: 0;
            border: 2px solid var(--border-color);
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 2px;
            bottom: 2px;
            background-color: var(--text-white);
            transition: .4s;
            border-radius: 0;
        }

        input:checked + .toggle-slider {
            background-color: var(--primary-green);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        /* Danger Zone */
        .danger-zone {
            border: 2px solid var(--danger-red);
            background: linear-gradient(135deg, rgba(255, 0, 85, 0.05) 0%, rgba(189, 0, 255, 0.02) 100%);
        }

        .danger-zone .card-title {
            color: var(--danger-red);
        }

        .danger-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* System Info */
        .system-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .info-item {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1) 0%, rgba(189, 0, 255, 0.05) 100%);
            padding: 1.5rem;
            border: 2px solid var(--border-color);
            text-align: center;
        }

        .info-label {
            color: var(--text-gray);
            font-size: 0.5rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            font-family: 'Press Start 2P', cursive;
        }

        .info-value {
            color: var(--text-white);
            font-weight: bold;
            font-size: 1rem;
            font-family: 'Press Start 2P', cursive;
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Success Message */
        .success-message {
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--success-green);
            color: var(--success-green);
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: bold;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
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

            .form-row {
                grid-template-columns: 1fr;
            }

            .settings-tabs {
                flex-direction: column;
            }

            .danger-actions {
                flex-direction: column;
            }

            .system-info {
                grid-template-columns: repeat(2, 1fr);
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
                <li class="nav-item">
                    <a href="blog-admin.php" class="nav-link">
                        <span class="nav-icon">📝</span>
                        <span class="nav-text">ARTICLES</span>
                    </a>
                </li>
                <li class="nav-item active">
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
        <!-- Header -->
        <div class="top-bar">
            <h1 class="page-title">PARAMÈTRES DU SITE</h1>
        </div>

        <!-- Message de succès PHP -->
        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="settings-tabs">
            <button class="tab-btn active" onclick="showTab('general')">⚙️ GÉNÉRAL</button>
            <button class="tab-btn" onclick="showTab('content')">📝 CONTENU</button>
            <button class="tab-btn" onclick="showTab('system')">🔧 SYSTÈME</button>
            <button class="tab-btn" onclick="showTab('danger')">⚠️ ZONE DE DANGER</button>
        </div>

        <!-- Tab Contents -->
        <div id="general" class="tab-content active">
            <!-- Paramètres Généraux -->
            <div class="card">
                <h2 class="card-title">⚙️ PARAMÈTRES GÉNÉRAUX</h2>
                
                <form method="POST" action="settings.php">
                    <input type="hidden" name="section" value="general">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">NOM DU SITE *</label>
                            <input type="text" class="form-control" name="site_name" id="site_name"
                                   placeholder="NOM DE VOTRE SITE" required
                                   value="BLOG GAMING">
                        </div>
                        <div class="form-group">
                            <label class="form-label">SLOGAN</label>
                            <input type="text" class="form-control" name="site_slogan" id="site_slogan"
                                   placeholder="SLOGAN DU SITE"
                                   value="VOTRE DESTINATION GAMING ULTIME">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">EMAIL DE CONTACT *</label>
                            <input type="email" class="form-control" name="contact_email" id="contact_email"
                                   placeholder="EMAIL@EXEMPLE.COM" required
                                   value="CONTACT@BLOG-GAMING.FR">
                        </div>
                        <div class="form-group">
                            <label class="form-label">TÉLÉPHONE</label>
                            <input type="text" class="form-control" name="contact_phone" id="contact_phone"
                                   placeholder="+33 1 23 45 67 89"
                                   value="+33 1 23 45 67 89">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">DESCRIPTION DU SITE</label>
                        <textarea class="form-control" name="site_description" id="site_description" rows="4" placeholder="DESCRIPTION DE VOTRE SITE">DÉCOUVREZ L'UNIVERS FASCINANT DU JEU VIDÉO À TRAVERS NOS ARTICLES, TESTS ET ANALYSES</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">MOTS-CLÉS (SEO)</label>
                        <input type="text" class="form-control" name="site_keywords" id="site_keywords"
                               placeholder="MOTS-CLÉS, SÉPARÉS, PAR, DES, VIRGULES"
                               value="GAMING, JEUX VIDÉO, BLOG, ACTUALITÉS, TESTS, REVIEWS">
                        <div class="form-help">SÉPARÉS PAR DES VIRGULES</div>
                    </div>

                    <button type="submit" class="btn btn-success" id="saveGeneralBtn">
                        💾 SAUVEGARDER LES PARAMÈTRES
                    </button>
                </form>
            </div>
        </div>

        <div id="content" class="tab-content">
            <!-- Paramètres du Contenu -->
            <div class="card">
                <h2 class="card-title">📝 GESTION DU CONTENU</h2>
                
                <form method="POST" action="settings.php">
                    <input type="hidden" name="section" value="content">
                    
                    <div class="toggle-group">
                        <span class="toggle-label">COMMENTAIRES AUTOMODÉRÉS</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="auto_moderate" id="auto_moderate" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="toggle-group">
                        <span class="toggle-label">NOTIFICATIONS PAR EMAIL</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="email_notifications" id="email_notifications" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">LIMITE DE COMMENTAIRES PAR ARTICLE *</label>
                        <input type="number" class="form-control" name="comment_limit" id="comment_limit"
                               placeholder="100" min="10" max="1000" required
                               value="100">
                        <div class="form-help">ENTRE 10 ET 1000 COMMENTAIRES</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">TAILLE MAXIMUM DES IMAGES (MB) *</label>
                        <input type="number" class="form-control" name="max_image_size" id="max_image_size"
                               placeholder="5" min="1" max="20" required
                               value="5">
                        <div class="form-help">ENTRE 1 ET 20 MB</div>
                    </div>

                    <button type="submit" class="btn btn-success" id="saveContentBtn">
                        💾 SAUVEGARDER LES PARAMÈTRES
                    </button>
                </form>
            </div>
        </div>

        <div id="system" class="tab-content">
            <!-- Informations Système -->
            <div class="card">
                <h2 class="card-title">🔧 INFORMATIONS SYSTÈME</h2>
                
                <div class="system-info">
                    <div class="info-item">
                        <div class="info-label">VERSION</div>
                        <div class="info-value">1.0.0</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">DERNIÈRE MISE À JOUR</div>
                        <div class="info-value"><?php echo $lastUpdate; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">ARTICLES</div>
                        <div class="info-value"><?php echo $totalArticles; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">COMMENTAIRES</div>
                        <div class="info-value"><?php echo $totalComments; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">UTILISATEURS</div>
                        <div class="info-value">1</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">ESPACE UTILISÉ</div>
                        <div class="info-value"><?php echo round($storageUsed, 1); ?> KB</div>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="button" class="btn btn-secondary" onclick="clearCache()">
                        🗑️ VIDER LE CACHE
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="runBackup()">
                        💾 CRÉER UNE SAUVEGARDE
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="refreshStats()">
                        🔄 ACTUALISER LES STATISTIQUES
                    </button>
                </div>
            </div>
        </div>

        <div id="danger" class="tab-content">
            <!-- Zone de Danger -->
            <div class="card danger-zone">
                <h2 class="card-title">⚠️ ZONE DE DANGER</h2>
                <p style="color: var(--text-gray); margin-bottom: 2rem; font-size: 0.6rem;">
                    CES ACTIONS SONT IRRÉVERSIBLES. UTILISEZ AVEC EXTRÊME PRUDENCE.
                </p>

                <div class="danger-actions">
                    <a href="reset-site.php" class="btn btn-danger" onclick="return confirm('ÊTES-VOUS SÛR ? CETTE ACTION EST IRRÉVERSIBLE !')">
                        🗑️ RÉINITIALISER LE SITE
                    </a>
                    <a href="delete-all-articles.php" class="btn btn-danger" onclick="return confirm('SUPPRIMER TOUS LES ARTICLES ? ACTION IRRÉVERSIBLE !')">
                        📝 SUPPRIMER TOUS LES ARTICLES
                    </a>
                    <a href="delete-all-comments.php" class="btn btn-danger" onclick="return confirm('SUPPRIMER TOUS LES COMMENTAIRES ? ACTION IRRÉVERSIBLE !')">
                        💬 SUPPRIMER TOUS LES COMMENTAIRES
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Gestion des onglets
        function showTab(tabName) {
            // Cacher tous les contenus de tab
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Désactiver tous les boutons de tab
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Afficher la tab sélectionnée
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        // Fonctions système
        function clearCache() {
            if (confirm('VIDER LE CACHE ? LES DONNÉES TEMPORAIRES SERONT SUPPRIMÉES.')) {
                alert('CACHE VIDÉ AVEC SUCCÈS!');
                location.reload();
            }
        }

        function runBackup() {
            alert('SAUVEGARDE CRÉÉE AVEC SUCCÈS!');
        }

        function refreshStats() {
            location.reload();
        }

        // Validation côté client
        document.addEventListener('DOMContentLoaded', function() {
            // Validation des formulaires
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    
                    // Validation des champs requis
                    const requiredFields = form.querySelectorAll('[required]');
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.style.borderColor = 'var(--danger-red)';
                        } else {
                            field.style.borderColor = 'var(--primary-green)';
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        alert('VEUILLEZ REMPLIR TOUS LES CHAMPS OBLIGATOIRES.');
                    }
                });
            });
        });
    </script>
</body>
</html>