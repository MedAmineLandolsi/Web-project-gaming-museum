<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Article.php';

// Vérifier l'authentification
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$articleModel = new Article($db);

$currentArticle = null;
$pageTitle = 'NOUVEL ARTICLE';
$submitButtonText = 'CRÉER L\'ARTICLE';

// Si mode édition, charger l'article
if (isset($_GET['id'])) {
    $articleId = $_GET['id'];
    // Utiliser la nouvelle méthode lireUnComplet pour avoir les infos auteur
    $articleData = $articleModel->lireUnComplet($articleId);
    
    if ($articleData) {
        $currentArticle = [
            'Article_ID' => $articleData['Article_ID'],
            'Titre' => $articleData['Titre'],
            'Contenu' => $articleData['Contenu'],
            'Categorie' => $articleData['Categorie'],
            'Auteur_ID' => $articleData['Auteur_ID'],
            'Statut' => $articleData['Statut'] ?? 'pending',
            'Date_Publication' => $articleData['Date_Publication'],
            'created_at' => $articleData['created_at'],
            'updated_at' => $articleData['updated_at'],
            'auteur_nom' => $articleData['auteur_nom'] ?? '',
            'auteur_username' => $articleData['auteur_username'] ?? ''
        ];
        $pageTitle = 'MODIFIER L\'ARTICLE';
        $submitButtonText = 'METTRE À JOUR';
    }
}

// Récupérer la liste des utilisateurs depuis la base de données
$users = [];
$author_options = '<option value="">-- SÉLECTIONNEZ UN AUTEUR --</option>';
try {
    $query = "SELECT id, username, first_name, last_name, email, role FROM users ORDER BY username ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Construire les options pour le select
    foreach ($users as $user) {
        $selected = ($currentArticle && $currentArticle['Auteur_ID'] == $user['id']) ? 'selected' : '';
        $display_name = !empty(trim($user['first_name'] . ' ' . $user['last_name'])) 
            ? htmlspecialchars($user['first_name'] . ' ' . $user['last_name'])
            : htmlspecialchars($user['username']);
        
        $author_options .= sprintf(
            '<option value="%d" %s>%s (@%s) - %s</option>',
            $user['id'],
            $selected,
            $display_name,
            htmlspecialchars($user['username']),
            htmlspecialchars($user['role'])
        );
    }
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des utilisateurs: " . $e->getMessage());
    $error_message = "Impossible de charger la liste des utilisateurs. Erreur: " . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleModel->Titre = htmlspecialchars($_POST['title']);
    $articleModel->Contenu = htmlspecialchars($_POST['content']);
    $articleModel->Categorie = $_POST['category'];
    $articleModel->Auteur_ID = (int)$_POST['author']; // Cast en entier
    $articleModel->Statut = $_POST['status'];
    
    // Vérifier que l'auteur existe
    if ($articleModel->Auteur_ID <= 0) {
        $error_message = 'ERREUR : L\'ID DE L\'AUTEUR DOIT ÊTRE UN NOMBRE POSITIF.';
    } elseif (!empty($users)) {
        // Vérifier que l'auteur existe dans la liste
        $author_exists = false;
        foreach ($users as $user) {
            if ($user['id'] == $articleModel->Auteur_ID) {
                $author_exists = true;
                break;
            }
        }
        
        if (!$author_exists) {
            $error_message = 'ERREUR : L\'AUTEUR SÉLECTIONNÉ N\'EXISTE PAS DANS LA BASE DE DONNÉES.';
        }
    }
    
    // Si pas d'erreur, continuer avec la création/mise à jour
    if (!isset($error_message)) {
        if (isset($_POST['article_id'])) {
            // Mode édition
            $articleModel->Article_ID = $_POST['article_id'];
            try {
                if ($articleModel->mettreAJour()) {
                    $_SESSION['success_message'] = 'ARTICLE MIS À JOUR AVEC SUCCÈS!';
                    header('Location: blog-admin.php');
                    exit();
                } else {
                    $error_message = 'ERREUR LORS DE LA MISE À JOUR.';
                }
            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }
        } else {
            // Mode création
            $articleModel->Date_Publication = date('Y-m-d H:i:s');
            try {
                if ($articleModel->creer()) {
                    $_SESSION['success_message'] = 'ARTICLE CRÉÉ AVEC SUCCÈS!';
                    header('Location: blog-admin.php');
                    exit();
                } else {
                    $error_message = 'ERREUR LORS DE LA CRÉATION.';
                }
            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }
        }
    }
}

// Messages
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? $error_message ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - ADMIN</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        /* Votre CSS existant reste inchangé */
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

        /* Cards */
        .card {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--border-color);
            padding: 2rem;
            position: relative;
            overflow: visible;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 2rem;
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
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2300FF41' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 12px;
            padding-right: 2.5rem;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 400px;
            line-height: 1.7;
        }

        .form-help {
            color: var(--text-gray);
            font-size: 0.5rem;
            margin-top: 0.5rem;
            display: block;
            font-family: 'Press Start 2P', cursive;
        }
        
        .author-info {
            background: rgba(0, 255, 65, 0.1);
            border: 1px solid var(--primary-green);
            padding: 1rem;
            margin-top: 0.5rem;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }
        
        .author-info span {
            color: var(--secondary-purple);
            font-weight: bold;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid var(--primary-green);
        }

        .notification {
            padding: 1.25rem 1.5rem;
            border-radius: 0;
            margin-bottom: 2rem;
            font-weight: bold;
            border: 2px solid;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
        }

        .notification.success {
            background: rgba(0, 255, 65, 0.1);
            color: var(--success-green);
            border-color: var(--success-green);
        }

        .notification.error {
            background: rgba(255, 0, 85, 0.1);
            color: var(--danger-red);
            border-color: var(--danger-red);
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

            .form-actions {
                flex-direction: column;
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
            <a href="../../index.php" class="btn btn-success" style="width: 100%; margin-bottom: 1rem; text-align: center; display: block;">
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
            <h1 class="page-title"><?php echo $pageTitle; ?></h1>
            <a href="blog-admin.php" class="btn btn-danger">
                ← RETOUR
            </a>
        </div>

        <!-- Messages -->
        <?php if ($success_message): ?>
            <div class="notification success">
                ✅ <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="notification error">
                ❌ <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'édition -->
        <div class="card">
            <form method="POST" id="articleForm">
                <?php if ($currentArticle): ?>
                    <input type="hidden" name="article_id" value="<?php echo $currentArticle['Article_ID']; ?>">
                    
                    <!-- Infos supplémentaires en mode édition -->
                    <div style="margin-bottom: 2rem; padding: 1rem; background: rgba(0, 255, 65, 0.05); border: 1px solid var(--primary-green);">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-family: 'VT323', monospace; font-size: 1rem;">
                            <div><strong>ID:</strong> <?php echo $currentArticle['Article_ID']; ?></div>
                            <div><strong>Date création:</strong> <?php echo date('d/m/Y H:i', strtotime($currentArticle['created_at'])); ?></div>
                            <div><strong>Dernière modif:</strong> <?php echo date('d/m/Y H:i', strtotime($currentArticle['updated_at'])); ?></div>
                            <?php if (!empty($currentArticle['auteur_nom'])): ?>
                                <div><strong>Auteur actuel:</strong> <?php echo htmlspecialchars($currentArticle['auteur_nom']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="articleTitle" class="form-label">TITRE DE L'ARTICLE *</label>
                    <input type="text" class="form-control" id="articleTitle" name="title" required
                           placeholder="ENTREZ LE TITRE DE L'ARTICLE"
                           value="<?php echo htmlspecialchars($currentArticle['Titre'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="articleContent" class="form-label">CONTENU DE L'ARTICLE *</label>
                    <textarea class="form-control" id="articleContent" name="content" required
                              placeholder="RÉDIGEZ VOTRE ARTICLE ICI..."><?php echo htmlspecialchars($currentArticle['Contenu'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="articleCategory" class="form-label">CATÉGORIE *</label>
                    <select class="form-control" id="articleCategory" name="category" required>
                        <option value="news" <?php echo ($currentArticle['Categorie'] ?? '') === 'news' ? 'selected' : ''; ?>>ACTUALITÉS</option>
                        <option value="review" <?php echo ($currentArticle['Categorie'] ?? '') === 'review' ? 'selected' : ''; ?>>TEST & REVIEW</option>
                        <option value="tutorial" <?php echo ($currentArticle['Categorie'] ?? '') === 'tutorial' ? 'selected' : ''; ?>>TUTORIELS</option>
                        <option value="trends" <?php echo ($currentArticle['Categorie'] ?? '') === 'trends' ? 'selected' : ''; ?>>TENDANCES</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="articleAuthor" class="form-label">AUTEUR *</label>
                    <?php if (empty($users)): ?>
                        <div class="notification error">
                            ❌ IMPOSSIBLE DE CHARGER LA LISTE DES UTILISATEURS. VEUILLEZ CRÉER DES UTILISATEURS D'ABORD.
                        </div>
                        <input type="hidden" name="author" value="0">
                    <?php else: ?>
                        <select class="form-control" id="articleAuthor" name="author" required>
                            <?php echo $author_options; ?>
                        </select>
                        <div class="form-help">
                            SÉLECTIONNEZ UN AUTEUR EXISTANT DANS LA BASE DE DONNÉES
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($currentArticle && !empty($currentArticle['auteur_nom'])): ?>
                        <div class="author-info">
                            Auteur actuel : <span><?php echo htmlspecialchars($currentArticle['auteur_nom']); ?></span>
                            <?php if (!empty($currentArticle['auteur_username'])): ?>
                                (@<?php echo htmlspecialchars($currentArticle['auteur_username']); ?>)
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="articleStatus" class="form-label">STATUT *</label>
                    <select class="form-control" id="articleStatus" name="status" required>
                        <option value="published" <?php echo ($currentArticle['Statut'] ?? '') === 'published' ? 'selected' : ''; ?>>PUBLIÉ</option>
                        <option value="draft" <?php echo ($currentArticle['Statut'] ?? '') === 'draft' ? 'selected' : ''; ?>>BROUILLON</option>
                        <option value="pending" <?php echo ($currentArticle['Statut'] ?? '') === 'pending' ? 'selected' : ''; ?>>EN ATTENTE</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success" <?php echo empty($users) ? 'disabled' : ''; ?>>
                        <?php echo $submitButtonText; ?>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="window.location.href='blog-admin.php'">
                        ANNULER
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Validation du formulaire
        document.getElementById('articleForm').addEventListener('submit', function(e) {
            const title = document.getElementById('articleTitle').value.trim();
            const content = document.getElementById('articleContent').value.trim();
            const author = document.getElementById('articleAuthor') ? document.getElementById('articleAuthor').value : '0';
            const category = document.getElementById('articleCategory').value;
            const status = document.getElementById('articleStatus').value;

            if (!title || !content || !category || !status) {
                e.preventDefault();
                alert('VEUILLEZ REMPLIR TOUS LES CHAMPS OBLIGATOIRES.');
                return false;
            }

            if (title.length < 5) {
                e.preventDefault();
                alert('LE TITRE DOIT CONTENIR AU MOINS 5 CARACTÈRES.');
                return false;
            }

            if (content.length < 50) {
                e.preventDefault();
                alert('LE CONTENU DOIT CONTENIR AU MOINS 50 CARACTÈRES.');
                return false;
            }

            // Vérifier qu'un auteur est sélectionné
            if (author === "" || author === "0") {
                e.preventDefault();
                alert('VEUILLEZ SÉLECTIONNER UN AUTEUR VALIDE.');
                return false;
            }

            return true;
        });

        // Compteur de caractères pour le contenu
        const contentTextarea = document.getElementById('articleContent');
        if (contentTextarea) {
            contentTextarea.addEventListener('input', function() {
                const charCount = this.value.length;
                const counter = document.getElementById('charCount') || (function() {
                    const counter = document.createElement('div');
                    counter.id = 'charCount';
                    counter.style.color = 'var(--text-gray)';
                    counter.style.fontSize = '0.5rem';
                    counter.style.marginTop = '0.5rem';
                    counter.style.fontFamily = "'Press Start 2P', cursive";
                    this.parentNode.appendChild(counter);
                    return counter;
                })();
                
                counter.textContent = `Caractères: ${charCount}`;
                
                if (charCount < 50) {
                    this.style.borderColor = 'var(--danger-red)';
                    counter.style.color = 'var(--danger-red)';
                } else if (charCount < 200) {
                    this.style.borderColor = 'var(--warning-orange)';
                    counter.style.color = 'var(--warning-orange)';
                } else {
                    this.style.borderColor = 'var(--primary-green)';
                    counter.style.color = 'var(--primary-green)';
                }
            });
            
            // Déclencher l'événement initial
            contentTextarea.dispatchEvent(new Event('input'));
        }
    </script>
</body>
</html>