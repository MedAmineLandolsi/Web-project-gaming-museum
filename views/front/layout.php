<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Projet Communautaire'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/projet/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="/projet/" class="logo">
                    <i class="fas fa-users me-2"></i>Communauté
                </a>
                
                <div class="mobile-menu" id="mobileMenu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                
                <ul class="nav-links" id="navLinks">
                    <li><a href="/projet/" class="<?php echo ($_SERVER['REQUEST_URI'] == '/projet/' || $_SERVER['REQUEST_URI'] == '/projet') ? 'active' : ''; ?>">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a></li>
                    <li><a href="/projet/communautes" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'communautes') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-users me-1"></i>Communautés
                    </a></li>
                    <li><a href="/projet/membres" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'membres') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-user-friends me-1"></i>Membres
                    </a></li>
                    <li><a href="/projet/publications" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'publications') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-newspaper me-1"></i>Publications
                    </a></li>
                    
                    <li class="user-info">
                        <?php if (isset($_SESSION['user_avatar'])): ?>
                            <img src="<?php echo $_SESSION['user_avatar']; ?>" alt="Avatar" class="user-avatar">
                        <?php else: ?>
                            <div class="avatar avatar-small">
                                <?php echo strtoupper(substr($_SESSION['user_prenom'] ?? 'U', 0, 1) . substr($_SESSION['user_nom'] ?? 'S', 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <span><?php echo ($_SESSION['user_prenom'] ?? 'Utilisateur') . ' ' . ($_SESSION['user_nom'] ?? ''); ?></span>
                    </li>
                    
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <li><a href="/projet/admin" class="admin-btn">
                        <i class="fas fa-cog me-1"></i>Administration
                    </a></li>
                    <?php endif; ?>
                    
                    <li><a href="/projet/communautes/create" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Créer
                    </a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php echo $content; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Projet Communautaire</h4>
                    <p>Plateforme de partage et d'échange entre passionnés. Rejoignez nos communautés et partagez vos centres d'intérêt.</p>
                </div>
                <div class="footer-section">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="/projet/">Accueil</a></li>
                        <li><a href="/projet/communautes">Communautés</a></li>
                        <li><a href="/projet/membres">Membres</a></li>
                        <li><a href="/projet/publications">Publications</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <ul>
                        <li><i class="fas fa-envelope me-2"></i>contact@projet.com</li>
                        <li><i class="fas fa-phone me-2"></i>+33 1 23 45 67 89</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Projet Communautaire. Tous droits réservés.</p>
                <p class="text-muted">Connecté en tant que <?php echo ($_SESSION['user_prenom'] ?? 'Utilisateur') . ' ' . ($_SESSION['user_nom'] ?? ''); ?></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Menu mobile
        document.getElementById('mobileMenu').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('active');
        });

        // Fermer le menu en cliquant à l'extérieur
        document.addEventListener('click', function(event) {
            const navLinks = document.getElementById('navLinks');
            const mobileMenu = document.getElementById('mobileMenu');
            if (!navLinks.contains(event.target) && !mobileMenu.contains(event.target)) {
                navLinks.classList.remove('active');
            }
        });
    </script>
</body>
</html>