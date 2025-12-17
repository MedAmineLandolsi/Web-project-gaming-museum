<?php
session_start();

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? null) !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include_once '../config/database.php';
include_once '../controllers/ParticipationController.php';
include_once '../controllers/EvenementController.php';

$database = new Database();
$db = $database->getConnection();

$participationController = new ParticipationController($db);
$evenementController = new EvenementController($db);

$participations = $participationController->index();
$evenements = $evenementController->index();

$totalParticipations = count($participations);
$totalEvenements = count($evenements);

$connectedUsers = 0;
$guestUsers = 0;
$todayParticipations = 0;
$todayTs = strtotime('today');

foreach ($participations as $p) {
    if (isset($p['User_ID']) && !empty($p['User_ID'])) {
        $connectedUsers++;
    } else {
        $guestUsers++;
    }

    $inscriptionDate = isset($p['date_inscription']) ? strtotime((string)$p['date_inscription']) : 0;
    if ($inscriptionDate && $inscriptionDate >= $todayTs) {
        $todayParticipations++;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participations - Administration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
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
                <li class="nav-item">
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
                <li class="nav-item active">
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

    <main class="main-content" id="dashboard">
        <div class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">Gestion des participations</h1>
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

        <div class="stats-overview">
            <div class="stat-card stat-secondary">
                <div class="stat-icon">🧾</div>
                <div class="stat-content">
                    <div class="stat-label">INSCRIPTIONS TOTALES</div>
                    <div class="stat-value"><?php echo (int)$totalParticipations; ?></div>
                    <div class="stat-change"><?php echo (int)$totalParticipations; ?> enregistrements</div>
                </div>
            </div>
            <div class="stat-card stat-primary">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-label">UTILISATEURS CONNECTÉS</div>
                    <div class="stat-value"><?php echo (int)$connectedUsers; ?></div>
                    <div class="stat-change positive">+<?php echo (int)$connectedUsers; ?></div>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">👤</div>
                <div class="stat-content">
                    <div class="stat-label">INVITÉS</div>
                    <div class="stat-value"><?php echo (int)$guestUsers; ?></div>
                    <div class="stat-change"><?php echo (int)$guestUsers; ?> invités</div>
                </div>
            </div>
            <div class="stat-card stat-accent">
                <div class="stat-icon">🕒</div>
                <div class="stat-content">
                    <div class="stat-label">AUJOURD'HUI</div>
                    <div class="stat-value"><?php echo (int)$todayParticipations; ?></div>
                    <div class="stat-change"><?php echo (int)$todayParticipations; ?> aujourd'hui</div>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">👥 Inscriptions aux événements</h3>
                <div class="badge badge-console">TOTAL: <?php echo (int)$totalParticipations; ?></div>
            </div>
            <div class="card-content">
                <?php if (empty($participations)): ?>
                    <div style="text-align: center; padding: 2rem; color: var(--text-gray);">
                        👥 AUCUNE INSCRIPTION TROUVÉE
                        <div style="margin-top: 1rem; font-size: 0.8rem; font-family: 'VT323', monospace;">Les inscriptions aux événements apparaîtront ici</div>
                    </div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ÉVÉNEMENT</th>
                                <th>PARTICIPANT</th>
                                <th>UTILISATEUR</th>
                                <th>EMAIL</th>
                                <th>TÉLÉPHONE</th>
                                <th>DATE INSCRIPTION</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participations as $p):
                                $isConnected = isset($p['User_ID']) && !empty($p['User_ID']);
                                $eventId = (int)($p['id_evenement'] ?? 0);
                            ?>
                            <tr>
                                <td><strong>#<?php echo (int)($p['id_participation'] ?? 0); ?></strong></td>
                                <td>
                                    <span class="badge badge-arcade"><?php echo htmlspecialchars((string)($p['evenement_nom'] ?? '')); ?></span>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:0.25rem;">
                                        <span style="font-family: 'VT323', monospace; font-size: 1.2rem; color: var(--text-white);">
                                            <?php echo htmlspecialchars((string)($p['nom_participant'] ?? '')); ?>
                                        </span>
                                        <?php if ($isConnected): ?>
                                            <span class="status status-active">Utilisateur connecté</span>
                                        <?php else: ?>
                                            <span class="status status-pending">Invité</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($isConnected): ?>
                                        <div style="display:flex; flex-direction:column; gap:0.25rem;">
                                            <span class="badge badge-console"><?php echo htmlspecialchars((string)($p['user_nom'] ?? 'Utilisateur')); ?></span>
                                            <span class="badge badge-nintendo">ID: <?php echo (int)$p['User_ID']; ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--text-gray);">Non connecté</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars((string)($p['email'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($p['telephone'] ?? '')); ?></td>
                                <td><?php echo isset($p['date_inscription']) ? date('d/m/Y H:i', strtotime((string)$p['date_inscription'])) : 'N/A'; ?></td>
                                <td>
                                    <div style="display:flex; gap:0.5rem; flex-wrap: wrap;">
                                        <?php if ($eventId > 0): ?>
                                            <a class="icon-btn edit" href="../views/front/participer.php?id=<?php echo (int)$eventId; ?>" target="_blank" title="Voir l'événement">👁️</a>
                                        <?php endif; ?>
                                        <a class="icon-btn delete" href="participations/delete.php?id=<?php echo (int)($p['id_participation'] ?? 0); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?')" title="Supprimer">🗑️</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
<?php __halt_compiler(); ?>
    <?php
    $totalParticipations = count($participations);
    $connectedUsers = 0;
    $guestUsers = 0;
    $todayParticipations = 0;

    foreach ($participations as $p) {
        if (isset($p['User_ID']) && !empty($p['User_ID'])) {
            $connectedUsers++;
        } else {
            $guestUsers++;
        }

        $inscriptionDate = isset($p['date_inscription']) ? strtotime((string)$p['date_inscription']) : 0;
        if ($inscriptionDate >= strtotime('today')) {
            $todayParticipations++;
        }
    }
    ?>

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
                <li class="nav-item">
                    <a href="index.php">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                        <span class="nav-count"><?php echo (int)$totalParticipations; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="evenements.php">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">ÉVÉNEMENTS</span>
                        <span class="nav-count"><?php echo (int)count($evenements); ?></span>
                    </a>
                </li>
                <li class="nav-item active">
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

    <main class="main-content" id="dashboard">
        <div class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">Gestion des participations</h1>
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

        <div class="stats-overview">
            <div class="stat-card stat-secondary">
                <div class="stat-icon">🧾</div>
                <div class="stat-content">
                    <div class="stat-label">INSCRIPTIONS TOTALES</div>
                    <div class="stat-value"><?php echo (int)$totalParticipations; ?></div>
                    <div class="stat-change"><?php echo (int)$totalParticipations; ?> enregistrements</div>
                </div>
            </div>
            <div class="stat-card stat-primary">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-label">UTILISATEURS CONNECTÉS</div>
                    <div class="stat-value"><?php echo (int)$connectedUsers; ?></div>
                    <div class="stat-change positive">+<?php echo (int)$connectedUsers; ?></div>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">👤</div>
                <div class="stat-content">
                    <div class="stat-label">INVITÉS</div>
                    <div class="stat-value"><?php echo (int)$guestUsers; ?></div>
                    <div class="stat-change"><?php echo (int)$guestUsers; ?> invités</div>
                </div>
            </div>
            <div class="stat-card stat-accent">
                <div class="stat-icon">🕒</div>
                <div class="stat-content">
                    <div class="stat-label">AUJOURD'HUI</div>
                    <div class="stat-value"><?php echo (int)$todayParticipations; ?></div>
                    <div class="stat-change"><?php echo (int)$todayParticipations; ?> aujourd'hui</div>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">👥 Inscriptions aux événements</h3>
                <div class="badge badge-console">TOTAL: <?php echo (int)$totalParticipations; ?></div>
            </div>
            <div class="card-content">
                <?php if (empty($participations)): ?>
                    <div style="text-align: center; padding: 2rem; color: var(--text-gray);">
                        👥 AUCUNE INSCRIPTION TROUVÉE
                        <div style="margin-top: 1rem; font-size: 0.8rem; font-family: 'VT323', monospace;">Les inscriptions aux événements apparaîtront ici</div>
                    </div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ÉVÉNEMENT</th>
                                <th>PARTICIPANT</th>
                                <th>UTILISATEUR</th>
                                <th>EMAIL</th>
                                <th>TÉLÉPHONE</th>
                                <th>DATE INSCRIPTION</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participations as $p):
                                $isConnected = isset($p['User_ID']) && !empty($p['User_ID']);
                            ?>
                            <tr>
                                <td><strong>#<?php echo (int)$p['id_participation']; ?></strong></td>
                                <td>
                                    <span class="badge badge-arcade"><?php echo htmlspecialchars((string)($p['evenement_nom'] ?? '')); ?></span>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:0.25rem;">
                                        <span style="font-family: 'VT323', monospace; font-size: 1.2rem; color: var(--text-white);">
                                            <?php echo htmlspecialchars((string)($p['nom_participant'] ?? '')); ?>
                                        </span>
                                        <?php if ($isConnected): ?>
                                            <span class="status status-active">Utilisateur connecté</span>
                                        <?php else: ?>
                                            <span class="status status-pending">Invité</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($isConnected): ?>
                                        <div style="display:flex; flex-direction:column; gap:0.25rem;">
                                            <span class="badge badge-console"><?php echo htmlspecialchars((string)($p['user_nom'] ?? 'Utilisateur')); ?></span>
                                            <span class="badge badge-nintendo">ID: <?php echo (int)$p['User_ID']; ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--text-gray);">Non connecté</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars((string)($p['email'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($p['telephone'] ?? '')); ?></td>
                                <td><?php echo isset($p['date_inscription']) ? date('d/m/Y H:i', strtotime((string)$p['date_inscription'])) : 'N/A'; ?></td>
                                <td>
                                    <div style="display:flex; gap:0.5rem; flex-wrap: wrap;">
                                        <?php if (isset($p['id_evenement'])): ?>
                                            <a class="icon-btn edit" href="../views/front/participer.php?id=<?php echo (int)$p['id_evenement']; ?>" target="_blank" title="Voir l'événement">👁️</a>
                                        <?php endif; ?>
                                        <a class="icon-btn delete" href="participations/delete.php?id=<?php echo (int)$p['id_participation']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?')" title="Supprimer">🗑️</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>