<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo $title ?? 'Projet'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/projet/assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h4>
                    <i class="fas fa-cogs"></i> Administration
                </h4>
            </div>
            
            <div class="admin-user-info">
                <div class="admin-user-avatar">
                    <?php echo strtoupper(substr($_SESSION['user_prenom'] ?? 'A', 0, 1) . substr($_SESSION['user_nom'] ?? 'D', 0, 1)); ?>
                </div>
                <div class="admin-user-name">
                    <?php echo ($_SESSION['user_prenom'] ?? 'Admin') . ' ' . ($_SESSION['user_nom'] ?? ''); ?>
                </div>
            </div>
            
            <div class="admin-nav">
                <ul>
                    <li>
                        <a href="/projet/admin" class="<?php echo ($_SERVER['REQUEST_URI'] == '/projet/admin') ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/projet/admin/membres" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/membres') !== false ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i>
                            Membres
                        </a>
                    </li>
                    <li>
                        <a href="/projet/admin/communautes" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/communautes') !== false ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i>
                            Communautés
                        </a>
                    </li>
                    <li>
                        <a href="/projet/admin/publications" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/publications') !== false ? 'active' : ''; ?>">
                            <i class="fas fa-newspaper"></i>
                            Publications
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="back-to-site">
                <a href="/projet/">
                    <i class="fas fa-arrow-left"></i>
                    Retour au site
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1><?php echo $title ?? 'Tableau de bord'; ?></h1>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php echo $content; ?>
        </main>
    </div>

    <!-- Mobile Menu Toggle (visible seulement sur mobile) -->
    <button class="mobile-menu-toggle d-md-none" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Menu mobile pour l'admin
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('active');
        });

        // Fermer le menu en cliquant à l'extérieur
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.admin-sidebar');
            const toggle = document.getElementById('mobileMenuToggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !toggle.contains(event.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>