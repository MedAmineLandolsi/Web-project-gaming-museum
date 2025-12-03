<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo $title ?? 'Projet'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link href="/projet/assets/css/admin.css?v=20241125" rel="stylesheet">
</head>
<body>
    <?php
    $sidebarStatsDefaults = [
        'dashboard' => 0,
        'membres' => 0,
        'communautes' => 0,
        'publications' => 0,
    ];
    $sidebarStats = isset($sidebarStats) && is_array($sidebarStats)
        ? array_merge($sidebarStatsDefaults, $sidebarStats)
        : $sidebarStatsDefaults;
    ?>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
            <div class="admin-logo">
    <div class="logo-box">
        <a href="/projet/" class="logo-link">
            <img src="/projet/assets/images/logo-lv.png" alt="Logo" class="admin-logo-img">
        </a>
    </div>
    <div class="admin-title">
        <h2>SYSTÈME ADMIN</h2>
        <div class="admin-badge">PANEL DE CONTRÔLE</div>
    </div>
</div>
            </div>
            
            <div class="admin-profile">
                <div class="admin-avatar">
                    <?php echo strtoupper(substr($_SESSION['user_prenom'] ?? 'A', 0, 1) . substr($_SESSION['user_nom'] ?? 'D', 0, 1)); ?>
                </div>
                <div class="admin-info">
                    <div class="admin-name"><?php echo ($_SESSION['user_prenom'] ?? 'Admin') . ' ' . ($_SESSION['user_nom'] ?? ''); ?></div>
                    <div class="admin-role">ADMINISTRATEUR</div>
                </div>
            </div>
            
            <div class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item <?php echo ($_SERVER['REQUEST_URI'] == '/projet/admin') ? 'active' : ''; ?>">
                        <a href="/projet/admin">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <span class="nav-text">DASHBOARD</span>
                            <span class="nav-count"><?php echo number_format($sidebarStats['dashboard']); ?></span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/membres') !== false ? 'active' : ''; ?>">
                        <a href="/projet/admin/membres">
                            <i class="fas fa-users nav-icon"></i>
                            <span class="nav-text">MEMBRES</span>
                            <span class="nav-count"><?php echo number_format($sidebarStats['membres']); ?></span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/communautes') !== false ? 'active' : ''; ?>">
                        <a href="/projet/admin/communautes">
                            <i class="fas fa-users nav-icon"></i>
                            <span class="nav-text">COMMUNAUTÉS</span>
                            <span class="nav-count"><?php echo number_format($sidebarStats['communautes']); ?></span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/publications') !== false ? 'active' : ''; ?>">
                        <a href="/projet/admin/publications">
                            <i class="fas fa-newspaper nav-icon"></i>
                            <span class="nav-text">PUBLICATIONS</span>
                            <span class="nav-count"><?php echo number_format($sidebarStats['publications']); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <a href="/projet/" class="btn-view-site">
                    <i class="fas fa-arrow-left me-2"></i>
                    RETOUR AU SITE
                </a>
                <form action="/projet/logout" method="POST" class="mt-2">
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        DÉCONNEXION
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="admin-main py-4">
            <div class="container-fluid">
                <div class="top-bar mb-4">
                    <div class="top-bar-left d-flex align-items-center gap-3">
                        <button class="menu-toggle" id="menuToggle">
                            <span></span><span></span><span></span>
                        </button>
                        <h1 class="page-title fs-2 fw-bold mb-0"> <?php echo $title ?? 'TABLEAU DE BORD'; ?> </h1>
                    </div>
                    <div class="top-bar-right d-flex align-items-center gap-3 ms-4">
                        <div class="search-box mb-0">
                            <input type="text" class="search-input" placeholder="RECHERCHER...">
                            <button class="search-btn"><i class="fas fa-search"></i></button>
                        </div>
                        <button class="notification-btn"><i class="fas fa-bell"></i><span class="notif-badge">3</span></button>
                    </div>
                </div>
            
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $_SESSION['success_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php echo $content; ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/projet/assets/js/admin-script.js?v=20241125"></script>
</body>
</html>