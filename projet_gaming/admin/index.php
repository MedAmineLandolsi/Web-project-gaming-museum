<?php
include_once '../config/database.php';
include_once '../models/Evenement.php';
include_once '../models/Participation.php';

$database = new Database();
$db = $database->getConnection();

$evenement = new Evenement($db);
$participation = new Participation($db);

// Read once to compute stats + recent list
$evenements = $evenement->read()->fetchAll(PDO::FETCH_ASSOC);
$participations = $participation->read()->fetchAll(PDO::FETCH_ASSOC);

$totalEvenements = count($evenements);
$totalParticipations = count($participations);

$upcomingEvents = 0;
foreach ($evenements as $ev) {
    $start = isset($ev['date_debut']) ? strtotime((string)$ev['date_debut']) : false;
    if ($start && $start > time()) {
        $upcomingEvents++;
    }
}

$todayParticipations = 0;
$todayTs = strtotime('today');
foreach ($participations as $p) {
    $ts = isset($p['date_inscription']) ? strtotime((string)$p['date_inscription']) : false;
    if ($ts && $ts >= $todayTs) {
        $todayParticipations++;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Administration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <!-- Reuse the ProjetWeb admin dashboard stylesheet to match the requested design -->
    <link rel="stylesheet" href="/projet-web/ProjetWeb/assets/css/admin-style.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <div class="logo-box">⚙</div>
                <div class="admin-title">
                    <h2>SYSTÈME ADMIN</h2>
                    <div class="admin-badge">PANEL DE CONTROLE</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item active">
                    <a href="index.php">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                        <span class="nav-count"><?php echo (int)$totalEvenements; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="evenements.php">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">ÉVÉNEMENTS</span>
                        <span class="nav-count"><?php echo (int)$totalEvenements; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="participations.php">
                        <span class="nav-icon">🧾</span>
                        <span class="nav-text">PARTICIPATIONS</span>
                        <span class="nav-count"><?php echo (int)$totalParticipations; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../index.php" style="color: #00FF41; font-weight: bold;">
                        <span class="nav-icon">🌐</span>
                        <span class="nav-text">FRONT OFFICE</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">AD</div>
                <div class="admin-info">
                    <div class="admin-name">ADMINISTRATEUR</div>
                    <div class="admin-role">GAMING EVENTS</div>
                </div>
            </div>
            <a href="../index.php" class="btn-logout" style="background: #00FF41; color: #000; border-color: #00FF41;">
                <span>←</span> RETOUR AU SITE
            </a>
            <a class="btn-logout" href="../logout.php" onclick="return confirm('Déconnexion ?')">
                <span>→</span> DÉCONNEXION
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="dashboard">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">Tableau de bord administrateur</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="RECHERCHER...">
                    <button class="search-btn" type="button">🔍</button>
                </div>
                <button class="notification-btn" type="button" aria-label="Notifications">
                    🔔
                    <span class="notif-badge"><?php echo (int)$todayParticipations; ?></span>
                </button>
                <a href="../index.php" class="btn-view-site">🌐 VOIR LE SITE</a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card stat-primary">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <div class="stat-label">ÉVÉNEMENTS TOTAUX</div>
                    <div class="stat-value"><?php echo (int)$totalEvenements; ?></div>
                    <div class="stat-change positive">+<?php echo (int)$totalEvenements; ?> au total</div>
                </div>
            </div>
            <div class="stat-card stat-secondary">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <div class="stat-label">À VENIR</div>
                    <div class="stat-value"><?php echo (int)$upcomingEvents; ?></div>
                    <div class="stat-change"><?php echo (int)$upcomingEvents; ?> événements à venir</div>
                </div>
            </div>
            <div class="stat-card stat-accent">
                <div class="stat-icon">🧾</div>
                <div class="stat-content">
                    <div class="stat-label">PARTICIPATIONS</div>
                    <div class="stat-value"><?php echo (int)$totalParticipations; ?></div>
                    <div class="stat-change positive"><?php echo (int)$totalParticipations; ?> inscriptions</div>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">📌</div>
                <div class="stat-content">
                    <div class="stat-label">AUJOURD'HUI</div>
                    <div class="stat-value"><?php echo (int)$todayParticipations; ?></div>
                    <div class="stat-change"><?php echo (int)$todayParticipations; ?> nouvelles inscriptions</div>
                </div>
            </div>
        </div>

        <!-- Recent Events Table -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">📋 Événements récents</h3>
            </div>
            <div class="card-content">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>NOM</th>
                            <th>JEU</th>
                            <th>DATE</th>
                            <th>LIEU</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($evenements, 0, 5) as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string)$event['nom']); ?></td>
                            <td><?php echo htmlspecialchars((string)$event['jeu']); ?></td>
                            <td><?php echo isset($event['date_debut']) ? date('d/m/Y H:i', strtotime((string)$event['date_debut'])) : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars((string)$event['lieu']); ?></td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a class="icon-btn edit" href="evenements/edit.php?id=<?php echo (int)$event['id_evenement']; ?>" title="Modifier">✏️</a>
                                    <a class="icon-btn delete" href="evenements/delete.php?id=<?php echo (int)$event['id_evenement']; ?>" onclick="return confirm('Êtes-vous sûr ?')" title="Supprimer">🗑️</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>