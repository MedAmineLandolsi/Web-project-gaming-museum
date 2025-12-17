<?php
// BASE_URL est défini par /index.php (front controller racine)
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

$rootBase = $baseUrl;
if ($rootBase !== '' && preg_match('#/projet$#', $rootBase)) {
    $rootBase = substr($rootBase, 0, -strlen('/projet'));
}
$homeUrl = ($rootBase === '') ? '/' : ($rootBase . '/');

$projetAdminDashboardUrl = $baseUrl . '/admin';
?>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo htmlspecialchars($homeUrl); ?>">
            <div class="logo-container">
                <img src="<?php echo $baseUrl; ?>/logo.png" alt="Logo" style="height:40px;">
            </div>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/communautes">
                        <i class="fas fa-users"></i> Communautés
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/publications">
                        <i class="fas fa-newspaper"></i> Publications
                    </a>
                </li>
            </ul>
            
            <div class="navbar-nav">
                <a href="<?php echo htmlspecialchars($projetAdminDashboardUrl); ?>" class="btn btn-primary">
                    <i class="fas fa-cog"></i> Administration
                </a>
            </div>
        </div>
    </div>
</nav>