<!DOCTYPE html>
<html lang="fr">
<?php
// Dev helper: avoid stale PHP opcache when editing files locally.
if (function_exists('opcache_invalidate')) {
    @opcache_invalidate(__FILE__, true);
}

// BASE_URL est défini par /index.php (front controller racine)
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Home of the whole workspace (ex: /projet-web/). BASE_URL here is usually /projet-web/projet
// so we remove the trailing /projet to get the workspace root.
$rootBase = $baseUrl;
if ($rootBase !== '' && preg_match('#/projet$#', $rootBase)) {
    $rootBase = substr($rootBase, 0, -strlen('/projet'));
}
$homeUrl = ($rootBase === '') ? '/' : ($rootBase . '/');

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$displayName = trim((string)($_SESSION['user_prenom'] ?? '') . ' ' . (string)($_SESSION['user_nom'] ?? ''));
if ($displayName === '') {
    $displayName = (string)($_SESSION['username'] ?? 'Utilisateur');
}
$nameParts = preg_split('/\s+/', trim($displayName));
$firstName = $nameParts[0] ?? $displayName;
$lastName = '';
if (count($nameParts) > 1) {
    $lastName = implode(' ', array_slice($nameParts, 1));
}

// Pour matcher le design demandé (ex: "Imprina"), afficher le username dans le header.
$headerUsername = trim((string)($_SESSION['username'] ?? ''));
if ($headerUsername !== '') {
    $firstName = $headerUsername;
}
$avatarUrl = (string)($_SESSION['user_avatar'] ?? '');
if ($avatarUrl === '') {
    // Fallback if the bridge session hasn't set user_avatar but gaming_museum did set profile_picture
    $pp = (string)($_SESSION['profile_picture'] ?? '');
    if ($pp !== '') {
        $avatarUrl = ($rootBase === '')
            ? ('/gaming_museum/uploads/' . $pp)
            : ($rootBase . '/gaming_museum/uploads/' . $pp);
    }
}

// Normaliser l'URL avatar (certaines sessions peuvent stocker uniquement le nom de fichier
// ou un chemin /uploads/ sans le préfixe /gaming_museum).
if ($avatarUrl !== '') {
    // Si c'est juste un nom de fichier
    if (strpos($avatarUrl, '://') === false && $avatarUrl[0] !== '/' && strpos($avatarUrl, 'uploads/') === false) {
        $avatarUrl = ($rootBase === '')
            ? ('/gaming_museum/uploads/' . $avatarUrl)
            : ($rootBase . '/gaming_museum/uploads/' . $avatarUrl);
    }

    // Si c'est /uploads/... (sans gaming_museum), corriger
    if (strpos($avatarUrl, '/gaming_museum/uploads/') === false && strpos($avatarUrl, '/uploads/') !== false) {
        // Remplacer seulement la première occurrence
        $avatarUrl = preg_replace('#/uploads/#', '/gaming_museum/uploads/', $avatarUrl, 1);
        // Si le chemin n'est pas absolu, le rendre absolu avec rootBase
        if (strpos($avatarUrl, '://') === false && $avatarUrl[0] !== '/') {
            $avatarUrl = ($rootBase === '') ? ('/' . ltrim($avatarUrl, '/')) : ($rootBase . '/' . ltrim($avatarUrl, '/'));
        }
    }
}
$initials = strtoupper(substr((string)($_SESSION['user_prenom'] ?? 'U'), 0, 1) . substr((string)($_SESSION['user_nom'] ?? 'S'), 0, 1));
$profileUrl = ($rootBase === '')
    ? '/gaming_museum/view/frontoffice/profile.php'
    : ($rootBase . '/gaming_museum/view/frontoffice/profile.php');

$projetAdminDashboardUrl = $baseUrl . '/admin';

$gmAdminDashboardUrl = ($rootBase === '')
    ? '/gaming_museum/view/backoffice/dashboard.php'
    : ($rootBase . '/gaming_museum/view/backoffice/dashboard.php');
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Projet Communautaire'; ?></title>
    <!-- layout.php ver: <?php echo (string) @filemtime(__FILE__); ?> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <?php
    // Calculer les URLs d'assets et ajouter un cache-buster basé sur la date de modification
    $cssPath = __DIR__ . '/style.css';
    $cssVer = file_exists($cssPath) ? filemtime($cssPath) : time();
    $cssUrl = $baseUrl . '/view/frontoffice/style.css';
    $jsUrl = $baseUrl . '/view/frontoffice/script.js';
    ?>
    <!-- DEBUG: BASE_URL=<?php echo $baseUrl; ?> CSS_URL=<?php echo $cssUrl . '?v=' . $cssVer; ?> -->
    <link href="<?php echo $cssUrl . '?v=' . $cssVer; ?>" rel="stylesheet">
    <?php // Inline CSS fallback removed (it duplicated styles and caused conflicts). ?>
</head>
<body>
    <!-- Particules d'arrière-plan -->
    <div class="particles">
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <div class="particle"></div>
        <?php endfor; ?>
    </div>
    
    <!-- Header avec style rétro -->
    <header class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <div class="logo-container">
                        <div class="logo-placeholder">
                        <img src="<?php echo $baseUrl; ?>/logo.png" alt="Logo" style="height:40px;">
                    </div>
                    <div class="site-title">COMMUNAUTÉ</div>
                </div>
            </div>
            
            <div class="nav-center">
                <ul class="nav-menu">
                    <li><a href="<?php echo htmlspecialchars($homeUrl); ?>" class="<?php echo (strpos($_SERVER['REQUEST_URI'], $baseUrl . '/') === 0 && ($_SERVER['REQUEST_URI'] == $baseUrl . '/' || $_SERVER['REQUEST_URI'] == $baseUrl)) ? 'active' : ''; ?>">
                        ACCUEIL
                    </a></li>
                    <li><a href="<?php echo $baseUrl; ?>/communautes" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'communautes') !== false ? 'active' : ''; ?>">
                        COMMUNAUTÉS
                    </a></li>
                    <li><a href="<?php echo $baseUrl; ?>/publications" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'publications') !== false ? 'active' : ''; ?>">
                        PUBLICATIONS
                    </a></li>
                </ul>
            </div>
            
            <div class="nav-right">
                <div class="nav-search" id="projetGlobalSearchWrap">
                    <input
                        type="search"
                        id="projetGlobalSearch"
                        class="nav-search-input"
                        placeholder="RECHERCHER..."
                        autocomplete="off"
                    >
                </div>
                <div class="user-info-nav">
                    <?php if ($isLoggedIn): ?>
                        <div class="user-menu-nav">
                            <button class="user-profile-btn-nav" type="button">
                                <?php if ($avatarUrl !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar" class="user-avatar-nav">
                                <?php else: ?>
                                    <div class="avatar-nav"><?php echo htmlspecialchars($initials); ?></div>
                                <?php endif; ?>
                                <span class="user-name-nav">
                                    <span class="name-first"><?php echo htmlspecialchars($firstName); ?></span>
                                </span>
                                <span class="dropdown-caret">▼</span>
                            </button>

                            <div class="user-dropdown-nav">
                                <a class="user-dropdown-link" href="<?php echo htmlspecialchars($profileUrl); ?>">MON PROFIL</a>
                                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                    <a class="user-dropdown-link" href="<?php echo htmlspecialchars($gmAdminDashboardUrl); ?>">ADMIN DASHBOARD</a>
                                <?php endif; ?>
                                <a class="user-dropdown-link logout" href="<?php echo $baseUrl; ?>/logout">DÉCONNEXION</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="<?php echo htmlspecialchars($projetAdminDashboardUrl); ?>" class="btn-auth admin-btn-nav">
                        <i class="fas fa-cog btn-icon"></i>
                        ADMIN
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo $baseUrl; ?>/communautes/create" class="btn-auth create-btn-nav">
                        <i class="fas fa-plus btn-icon"></i>
                        CRÉER
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content py-4">
        <div class="container py-4">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <div class="mt-4">
                <?php echo $content; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-content">
                <div class="footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-placeholder">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>COMMUNAUTÉ</h3>
                    </div>
                    <p class="footer-tagline">
                        Plateforme de partage et d'échange entre passionnés. 
                        Rejoignez nos communautés et partagez vos centres d'intérêt.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-icon">
                            <i class="fab fa-discord"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-links-section">
                    <h4 class="footer-title">NAVIGATION</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $baseUrl; ?>/">Accueil</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/communautes">Communautés</a></li>
                        
                        <li><a href="<?php echo $baseUrl; ?>/publications">Publications</a></li>
                    </ul>
                </div>
                
                <div class="footer-links-section">
                    <h4 class="footer-title">COMPTE</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $baseUrl; ?>/profil">Mon Profil</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/parametres">Paramètres</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/communautes/create">Créer une communauté</a></li>
                    </ul>
                </div>
                
                <div class="footer-info">
                    <h4 class="footer-title">CONTACT</h4>
                    <div class="info-item">
                        <i class="fas fa-envelope info-icon"></i>
                        <div class="info-content">
                            <strong>Email</strong><br>
                            contact@projet.com
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone info-icon"></i>
                        <div class="info-content">
                            <strong>Téléphone</strong><br>
                            +33 1 23 45 67 89
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pixel-divider"></div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p class="copyright">
                    &copy; 2024 Projet Communautaire. Tous droits réservés.
                </p>
                <p class="copyright">
                    Connecté
                </p>
                <div class="footer-bottom-links">
                    <a href="#">Confidentialité</a>
                    <span>|</span>
                    <a href="#">Conditions</a>
                    <span>|</span>
                    <a href="#">Support</a>
                </div>
                <p class="made-with">
                    Made with <span class="heart">❤</span> by the Community
                </p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- AI Help Widget Mount (front) -->
    <div id="ai-help-widget" data-context="front"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php
    $jsPathLocal = __DIR__ . '/script.js';
    $jsVer = file_exists($jsPathLocal) ? filemtime($jsPathLocal) : time();
    $aiJsPathLocal = dirname(__DIR__) . '/shared/ai-features.js';
    $aiJsVer = file_exists($aiJsPathLocal) ? filemtime($aiJsPathLocal) : time();
    ?>
    <script src="<?php echo $baseUrl; ?>/view/frontoffice/script.js?v=<?php echo $jsVer; ?>"></script>
    <script>
        window.__PROJET_BASE_URL = <?php echo json_encode($baseUrl, JSON_UNESCAPED_SLASHES); ?>;
    </script>
    <script src="<?php echo $baseUrl; ?>/view/shared/ai-features.js?v=<?php echo $aiJsVer; ?>"></script>
    
    <script>
    // DÉSACTIVER COMPLÈTEMENT LA VALIDATION HTML5
    document.addEventListener('DOMContentLoaded', function() {
        const allForms = document.querySelectorAll('form');
        allForms.forEach(form => {
            form.setAttribute('novalidate', 'novalidate');
            form.noValidate = true;
        });
        
        document.addEventListener('invalid', function(e) {
            e.preventDefault();
        }, true);
        
        const requiredFields = document.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.removeAttribute('required');
        });
        
        const emailFields = document.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            field.setAttribute('type', 'text');
        });
        
        const urlFields = document.querySelectorAll('input[type="url"]');
        urlFields.forEach(field => {
            field.setAttribute('type', 'text');
        });
    });
    </script>
</body>
</html>