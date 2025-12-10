<!DOCTYPE html>
<html lang="fr">
<?php
// Calculer dynamiquement la base URL pour que les liens fonctionnent
// Calculer la base URL à partir du chemin de la requête.
// On prend le premier segment de l'URL (ex: /projet/...) afin
// que le site fonctionne correctement quand Apache réécrit vers
// la racine `index.php` ou quand on appelle directement
// `view/frontoffice/index.php`.
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$parts = array_values(array_filter(explode('/', $requestPath)));
$baseUrl = isset($parts[0]) ? '/' . $parts[0] : '';
// Si la base n'est pas trouvée, essayer SCRIPT_NAME, puis tomber
// en dernier recours sur '/projet' pour les environnements locaux.
if ($baseUrl === '') {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $s_parts = array_values(array_filter(explode('/', $script)));
    $baseUrl = isset($s_parts[0]) ? '/' . $s_parts[0] : '/projet';
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Projet Communautaire'; ?></title>
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
    <?php
    // Fallback inline CSS: si le fichier existe sur le disque mais ne peut
    // pas être chargé par le navigateur (problème de rewrite ou headers),
    // injecter son contenu inline pour garantir le rendu.
    if (file_exists($cssPath)) {
        $cssContent = file_get_contents($cssPath);
        echo "\n    <style>\n" . $cssContent . "\n    </style>\n";
    }
    ?>
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
                    <li><a href="<?php echo $baseUrl; ?>/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], $baseUrl . '/') === 0 && ($_SERVER['REQUEST_URI'] == $baseUrl . '/' || $_SERVER['REQUEST_URI'] == $baseUrl)) ? 'active' : ''; ?>">
                        ACCUEIL
                    </a></li>
                    <li><a href="<?php echo $baseUrl; ?>/communautes" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'communautes') !== false ? 'active' : ''; ?>">
                        COMMUNAUTÉS
                    </a></li>
                    <li><a href="<?php echo $baseUrl; ?>/membres" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'membres') !== false ? 'active' : ''; ?>">
                        MEMBRES
                    </a></li>
                    <li><a href="<?php echo $baseUrl; ?>/publications" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'publications') !== false ? 'active' : ''; ?>">
                        PUBLICATIONS
                    </a></li>
                </ul>
            </div>
            
            <div class="nav-right">
                <div class="user-info-nav">
                    <?php if (isset($_SESSION['user_avatar'])): ?>
                        <img src="<?php echo $_SESSION['user_avatar']; ?>" alt="Avatar" class="user-avatar-nav">
                    <?php else: ?>
                        <div class="avatar-nav">
                            <?php echo strtoupper(substr($_SESSION['user_prenom'] ?? 'U', 0, 1) . substr($_SESSION['user_nom'] ?? 'S', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <span class="user-name-nav">
                        <?php echo ($_SESSION['user_prenom'] ?? 'Utilisateur') . ' ' . ($_SESSION['user_nom'] ?? ''); ?>
                    </span>
                    
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="<?php echo $baseUrl; ?>/admin" class="btn-auth admin-btn-nav">
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
                        <li><a href="<?php echo $baseUrl; ?>/membres">Membres</a></li>
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
                    Connecté en tant que <?php echo ($_SESSION['user_prenom'] ?? 'Utilisateur') . ' ' . ($_SESSION['user_nom'] ?? ''); ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php
    $jsPathLocal = __DIR__ . '/script.js';
    $jsVer = file_exists($jsPathLocal) ? filemtime($jsPathLocal) : time();
    ?>
    <script src="<?php echo $baseUrl; ?>/view/frontoffice/script.js?v=<?php echo $jsVer; ?>"></script>
    
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