<?php
// Calculer base URL dynamique si le fichier est inclus depuis différents chemins
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$baseUrl = preg_replace('#/view/frontoffice.*$#', '', $script);
if ($baseUrl === '' || $baseUrl === '/') {
    $baseUrl = '/projet';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $baseUrl; ?>/">
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
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/membres">
                        <i class="fas fa-user-friends"></i> Membres
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/publications">
                        <i class="fas fa-newspaper"></i> Publications
                    </a>
                </li>
            </ul>
            
            <div class="navbar-nav">
                <a href="<?php echo $baseUrl; ?>/admin" class="btn btn-primary">
                    <i class="fas fa-cog"></i> Administration
                </a>
            </div>
        </div>
    </div>
</nav>