<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo $title ?? 'Projet'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
    <?php $adminCssVersion = @filemtime(__DIR__ . '/admin.css') ?: time(); ?>
    <link href="<?php echo $baseUrl; ?>/view/backoffice/admin.css?v=<?php echo $adminCssVersion; ?>" rel="stylesheet">
    <script src="<?php echo $baseUrl; ?>/view/backoffice/admin-script.js"></script>
</head>
<body>
    <?php
    $sidebarStatsDefaults = [
        'dashboard' => 0,
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
                        <img src="<?php echo $baseUrl; ?>/logo.png" alt="Logo" style="height:40px;">
                    </div>
                    <div class="admin-title">
                        <h2>SYSTÈME ADMIN</h2>
                        <div class="admin-badge">PANEL DE CONTRÔLE</div>
                    </div>
                </div>
            </div>
            
            <?php
            // Profil admin: aligné au style gaming_museum (avatar rond + indicateur)
            $rootBase = $baseUrl;
            if ($rootBase !== '' && preg_match('#/projet$#', $rootBase)) {
                $rootBase = substr($rootBase, 0, -strlen('/projet'));
            }

            $adminUsername = trim((string)($_SESSION['username'] ?? ''));
            if ($adminUsername === '') {
                $adminUsername = trim((string)($_SESSION['prenom'] ?? 'Admin'));
            }
            if ($adminUsername === '') {
                $adminUsername = 'Admin';
            }

            $adminAvatarUrl = (string)($_SESSION['user_avatar'] ?? '');
            if ($adminAvatarUrl === '') {
                $pp = (string)($_SESSION['profile_picture_url'] ?? '');
                if ($pp !== '') {
                    $adminAvatarUrl = ($rootBase === '')
                        ? ('/gaming_museum/uploads/' . $pp)
                        : ($rootBase . '/gaming_museum/uploads/' . $pp);
                }
            }

            // Normaliser URL avatar si besoin
            if ($adminAvatarUrl !== '') {
                if (strpos($adminAvatarUrl, '://') === false && $adminAvatarUrl[0] !== '/' && strpos($adminAvatarUrl, 'uploads/') === false) {
                    $adminAvatarUrl = ($rootBase === '')
                        ? ('/gaming_museum/uploads/' . $adminAvatarUrl)
                        : ($rootBase . '/gaming_museum/uploads/' . $adminAvatarUrl);
                }
                if (strpos($adminAvatarUrl, '/gaming_museum/uploads/') === false && strpos($adminAvatarUrl, '/uploads/') !== false) {
                    $adminAvatarUrl = preg_replace('#/uploads/#', '/gaming_museum/uploads/', $adminAvatarUrl, 1);
                    if (strpos($adminAvatarUrl, '://') === false && $adminAvatarUrl[0] !== '/') {
                        $adminAvatarUrl = ($rootBase === '') ? ('/' . ltrim($adminAvatarUrl, '/')) : ($rootBase . '/' . ltrim($adminAvatarUrl, '/'));
                    }
                }
            }

            $adminInitials = strtoupper(substr($adminUsername, 0, 2));
            ?>
            <div class="admin-profile">
                <div class="admin-avatar">
                    <?php if ($adminAvatarUrl !== ''): ?>
                        <img src="<?php echo htmlspecialchars($adminAvatarUrl); ?>" alt="Admin" class="admin-avatar-img">
                    <?php else: ?>
                        <?php echo htmlspecialchars($adminInitials); ?>
                    <?php endif; ?>
                    <div class="admin-profile-indicator"></div>
                </div>
                <div class="admin-info">
                    <div class="admin-name"><?php echo htmlspecialchars($adminUsername); ?></div>
                    <div class="admin-role">Super Admin</div>
                </div>
            </div>
            
            <div class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item <?php echo (($_SERVER['REQUEST_URI'] ?? '') == $baseUrl . '/admin') ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl; ?>/admin">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <span class="nav-text">DASHBOARD</span>
                            <span class="nav-count"><?php echo number_format($sidebarStats['dashboard']); ?></span>
                        </a>
                    </li>

                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/communautes') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl; ?>/admin/communautes">
                            <i class="fas fa-users nav-icon"></i>
                            <span class="nav-text">COMMUNAUTÉS</span>
                            <span class="nav-count"><?php echo number_format($sidebarStats['communautes']); ?></span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/publications') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl; ?>/admin/publications">
                            <i class="fas fa-newspaper nav-icon"></i>
                            <span class="nav-text">PUBLICATIONS</span>
                            <span class="nav-count"><?php echo number_format($sidebarStats['publications']); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <a href="<?php echo $baseUrl; ?>/" class="btn-view-site">
                    <i class="fas fa-arrow-left me-2"></i>
                    RETOUR AU SITE
                </a>
                <form action="<?php echo $baseUrl; ?>/logout" method="POST" class="mt-2">
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

    <!-- AI Help Widget Mount (admin) -->
    <div id="ai-help-widget" data-context="admin"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php
        $aiJsPathLocal = dirname(__DIR__) . '/shared/ai-features.js';
        $aiJsVer = file_exists($aiJsPathLocal) ? filemtime($aiJsPathLocal) : time();
    ?>
    <script>
        window.__PROJET_BASE_URL = <?php echo json_encode($baseUrl, JSON_UNESCAPED_SLASHES); ?>;
    </script>
    <script src="<?php echo $baseUrl; ?>/view/shared/ai-features.js?v=<?php echo $aiJsVer; ?>"></script>
</body>
</html>