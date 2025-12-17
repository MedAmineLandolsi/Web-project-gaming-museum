<?php
session_start();

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? null) !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include_once '../config/database.php';
include_once '../controllers/EvenementController.php';
include_once '../controllers/ParticipationController.php';

$database = new Database();
$db = $database->getConnection();

$evenementController = new EvenementController($db);
$participationController = new ParticipationController($db);

$evenements = $evenementController->index();
$participations = $participationController->index();

$totalEvents = count($evenements);
$totalParticipations = count($participations);

$totalParticipants = 0;
$upcomingEvents = 0;
$freeEvents = 0;

foreach ($evenements as $ev) {
    $eventId = (int)($ev['id_evenement'] ?? 0);
    if ($eventId > 0) {
        $totalParticipants += (int)$evenementController->countParticipants($eventId);
    }

    $start = isset($ev['date_debut']) ? strtotime((string)$ev['date_debut']) : false;
    if ($start && $start > time()) {
        $upcomingEvents++;
    }

    $price = (float)($ev['prix'] ?? 0);
    if ($price == 0.0) {
        $freeEvents++;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - Administration</title>
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
                        <span class="nav-count"><?php echo (int)$totalEvents; ?></span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="evenements.php">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">ÉVÉNEMENTS</span>
                        <span class="nav-count"><?php echo (int)$totalEvents; ?></span>
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

    <main class="main-content" id="dashboard">
        <div class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">Gestion des événements</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="RECHERCHER...">
                    <button class="search-btn" type="button">🔍</button>
                </div>
                <button class="notification-btn" type="button" aria-label="Notifications">
                    🔔
                    <span class="notif-badge"><?php echo (int)$upcomingEvents; ?></span>
                </button>
                <a href="../index.php" class="btn-view-site">🌐 VOIR LE SITE</a>
            </div>
        </div>

        <div class="stats-overview">
            <div class="stat-card stat-primary">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <div class="stat-label">ÉVÉNEMENTS TOTAUX</div>
                    <div class="stat-value"><?php echo (int)$totalEvents; ?></div>
                    <div class="stat-change positive">+<?php echo (int)$totalEvents; ?> au total</div>
                </div>
            </div>
            <div class="stat-card stat-secondary">
                <div class="stat-icon">🧾</div>
                <div class="stat-content">
                    <div class="stat-label">INSCRIPTIONS</div>
                    <div class="stat-value"><?php echo (int)$totalParticipants; ?></div>
                    <div class="stat-change"><?php echo (int)$totalParticipants; ?> participants</div>
                </div>
            </div>
            <div class="stat-card stat-accent">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <div class="stat-label">À VENIR</div>
                    <div class="stat-value"><?php echo (int)$upcomingEvents; ?></div>
                    <div class="stat-change"><?php echo (int)$upcomingEvents; ?> événements à venir</div>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">🆓</div>
                <div class="stat-content">
                    <div class="stat-label">GRATUITS</div>
                    <div class="stat-value"><?php echo (int)$freeEvents; ?></div>
                    <div class="stat-change"><?php echo (int)$freeEvents; ?> gratuits</div>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">📋 Liste des événements</h3>
                <a href="evenements/create.php" class="btn-view-site">➕ NOUVEL ÉVÉNEMENT</a>
            </div>
            <div class="card-content">
                <?php if (empty($evenements)): ?>
                    <div style="text-align: center; padding: 2rem; color: var(--text-gray);">
                        🎮 AUCUN ÉVÉNEMENT TROUVÉ
                        <div style="margin-top: 1rem; font-size: 0.8rem; font-family: 'VT323', monospace;">Créez votre premier événement pour commencer !</div>
                    </div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>NOM</th>
                                <th>JEU</th>
                                <th>ORGANISATEUR</th>
                                <th>DATE DÉBUT</th>
                                <th>DATE FIN</th>
                                <th>LIEU</th>
                                <th>PLACES</th>
                                <th>PRIX</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evenements as $ev):
                                $eventId = (int)($ev['id_evenement'] ?? 0);
                                $participationsCount = $eventId > 0 ? (int)$evenementController->countParticipants($eventId) : 0;

                                $placesMax = (int)($ev['places_max'] ?? 0);
                                $isFull = $placesMax > 0 && $participationsCount >= $placesMax;
                                $isWarning = $placesMax > 0 && !$isFull && $participationsCount >= (int)ceil($placesMax * 0.8);

                                $price = (float)($ev['prix'] ?? 0);
                                $isFree = $price == 0.0;
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars((string)($ev['nom'] ?? '')); ?></strong></td>
                                <td><?php echo htmlspecialchars((string)($ev['jeu'] ?? '')); ?></td>
                                <td>
                                    <span class="badge badge-console"><?php echo htmlspecialchars((string)($ev['organisateur_nom'] ?? 'Admin')); ?></span>
                                </td>
                                <td><?php echo isset($ev['date_debut']) ? date('d/m/Y H:i', strtotime((string)$ev['date_debut'])) : 'N/A'; ?></td>
                                <td><?php echo isset($ev['date_fin']) ? date('d/m/Y H:i', strtotime((string)$ev['date_fin'])) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars((string)($ev['lieu'] ?? '')); ?></td>
                                <td>
                                    <?php if ($isFull): ?>
                                        <span class="status" style="background-color: rgba(255, 0, 85, 0.2); border: 1px solid var(--danger-red); color: var(--danger-red);">
                                            <?php echo (int)$participationsCount; ?> / <?php echo (int)$placesMax; ?>
                                        </span>
                                    <?php elseif ($isWarning): ?>
                                        <span class="status status-pending">
                                            <?php echo (int)$participationsCount; ?> / <?php echo (int)$placesMax; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="status status-active">
                                            <?php echo (int)$participationsCount; ?> / <?php echo (int)$placesMax; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isFree): ?>
                                        <span class="badge badge-playstation">GRATUIT</span>
                                    <?php else: ?>
                                        <span class="badge badge-nintendo"><?php echo htmlspecialchars((string)$price); ?>€</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a class="icon-btn edit" href="evenements/edit.php?id=<?php echo (int)$eventId; ?>" title="Modifier">✏️</a>
                                        <a class="icon-btn delete" href="evenements/delete.php?id=<?php echo (int)$eventId; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')" title="Supprimer">🗑️</a>
                                        <a class="icon-btn edit" href="../views/front/participer.php?id=<?php echo (int)$eventId; ?>" target="_blank" title="Voir">👁️</a>
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
session_start();

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? null) !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include_once '../config/database.php';
include_once '../controllers/EvenementController.php';
include_once '../controllers/ParticipationController.php';

$database = new Database();
$db = $database->getConnection();

$evenementController = new EvenementController($db);
$participationController = new ParticipationController($db);

$evenements = $evenementController->index();
$participations = $participationController->index();

$totalEvents = count($evenements);
$totalParticipations = count($participations);

$totalParticipants = 0;
$upcomingEvents = 0;
$freeEvents = 0;

foreach ($evenements as $ev) {
    $eventId = (int)($ev['id_evenement'] ?? 0);
    if ($eventId > 0) {
        $totalParticipants += (int)$evenementController->countParticipants($eventId);
    }

    $start = isset($ev['date_debut']) ? strtotime((string)$ev['date_debut']) : false;
    if ($start && $start > time()) {
        $upcomingEvents++;
    }

    $price = (float)($ev['prix'] ?? 0);
    if ($price == 0.0) {
        $freeEvents++;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - Administration</title>
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
                        <span class="nav-count"><?php echo (int)$totalEvents; ?></span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="evenements.php">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">ÉVÉNEMENTS</span>
                        <span class="nav-count"><?php echo (int)$totalEvents; ?></span>
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

    <main class="main-content" id="dashboard">
        <div class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">Gestion des événements</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="RECHERCHER...">
                    <button class="search-btn" type="button">🔍</button>
                </div>
                <button class="notification-btn" type="button" aria-label="Notifications">
                    🔔
                    <span class="notif-badge"><?php echo (int)$upcomingEvents; ?></span>
                </button>
                <a href="../index.php" class="btn-view-site">🌐 VOIR LE SITE</a>
            </div>
        </div>

        <div class="stats-overview">
            <div class="stat-card stat-primary">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <div class="stat-label">ÉVÉNEMENTS TOTAUX</div>
                    <div class="stat-value"><?php echo (int)$totalEvents; ?></div>
                    <div class="stat-change positive">+<?php echo (int)$totalEvents; ?> au total</div>
                </div>
            </div>
            <div class="stat-card stat-secondary">
                <div class="stat-icon">🧾</div>
                <div class="stat-content">
                    <div class="stat-label">INSCRIPTIONS</div>
                    <div class="stat-value"><?php echo (int)$totalParticipants; ?></div>
                    <div class="stat-change"><?php echo (int)$totalParticipants; ?> participants</div>
                </div>
            </div>
            <div class="stat-card stat-accent">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <div class="stat-label">À VENIR</div>
                    <div class="stat-value"><?php echo (int)$upcomingEvents; ?></div>
                    <div class="stat-change"><?php echo (int)$upcomingEvents; ?> événements à venir</div>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">🆓</div>
                <div class="stat-content">
                    <div class="stat-label">GRATUITS</div>
                    <div class="stat-value"><?php echo (int)$freeEvents; ?></div>
                    <div class="stat-change"><?php echo (int)$freeEvents; ?> gratuits</div>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">📋 Liste des événements</h3>
                <a href="evenements/create.php" class="btn-view-site">➕ NOUVEL ÉVÉNEMENT</a>
            </div>
            <div class="card-content">
                <?php if (empty($evenements)): ?>
                    <div style="text-align: center; padding: 2rem; color: var(--text-gray);">
                        🎮 AUCUN ÉVÉNEMENT TROUVÉ
                        <div style="margin-top: 1rem; font-size: 0.8rem; font-family: 'VT323', monospace;">Créez votre premier événement pour commencer !</div>
                    </div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>NOM</th>
                                <th>JEU</th>
                                <th>ORGANISATEUR</th>
                                <th>DATE DÉBUT</th>
                                <th>DATE FIN</th>
                                <th>LIEU</th>
                                <th>PLACES</th>
                                <th>PRIX</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evenements as $ev):
                                $eventId = (int)($ev['id_evenement'] ?? 0);
                                $participationsCount = $eventId > 0 ? (int)$evenementController->countParticipants($eventId) : 0;

                                $placesMax = (int)($ev['places_max'] ?? 0);
                                $isFull = $placesMax > 0 && $participationsCount >= $placesMax;
                                $isWarning = $placesMax > 0 && !$isFull && $participationsCount >= (int)ceil($placesMax * 0.8);

                                $price = (float)($ev['prix'] ?? 0);
                                $isFree = $price == 0.0;
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars((string)($ev['nom'] ?? '')); ?></strong></td>
                                <td><?php echo htmlspecialchars((string)($ev['jeu'] ?? '')); ?></td>
                                <td>
                                    <span class="badge badge-console"><?php echo htmlspecialchars((string)($ev['organisateur_nom'] ?? 'Admin')); ?></span>
                                </td>
                                <td><?php echo isset($ev['date_debut']) ? date('d/m/Y H:i', strtotime((string)$ev['date_debut'])) : 'N/A'; ?></td>
                                <td><?php echo isset($ev['date_fin']) ? date('d/m/Y H:i', strtotime((string)$ev['date_fin'])) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars((string)($ev['lieu'] ?? '')); ?></td>
                                <td>
                                    <?php if ($isFull): ?>
                                        <span class="status" style="background-color: rgba(255, 0, 85, 0.2); border: 1px solid var(--danger-red); color: var(--danger-red);">
                                            <?php echo (int)$participationsCount; ?> / <?php echo (int)$placesMax; ?>
                                        </span>
                                    <?php elseif ($isWarning): ?>
                                        <span class="status status-pending">
                                            <?php echo (int)$participationsCount; ?> / <?php echo (int)$placesMax; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="status status-active">
                                            <?php echo (int)$participationsCount; ?> / <?php echo (int)$placesMax; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isFree): ?>
                                        <span class="badge badge-playstation">GRATUIT</span>
                                    <?php else: ?>
                                        <span class="badge badge-nintendo"><?php echo htmlspecialchars((string)$price); ?>€</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a class="icon-btn edit" href="evenements/edit.php?id=<?php echo (int)$eventId; ?>" title="Modifier">✏️</a>
                                        <a class="icon-btn delete" href="evenements/delete.php?id=<?php echo (int)$eventId; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')" title="Supprimer">🗑️</a>
                                        <a class="icon-btn edit" href="../views/front/participer.php?id=<?php echo (int)$eventId; ?>" target="_blank" title="Voir">👁️</a>
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
                        <span class="nav-icon">🧾</span>
                        <span class="nav-text">PARTICIPATIONS</span>
                        <span class="nav-count"><?php echo (int)count($participations); ?></span>
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
        <?php
        $totalEvents = count($evenements);
        $totalParticipants = 0;
        $upcomingEvents = 0;
        $freeEvents = 0;

        foreach ($evenements as $ev) {
            $participationsCount = $evenementController->show($ev['id_evenement'])->countParticipations();
            $totalParticipants += $participationsCount;

            if (isset($ev['date_debut']) && strtotime((string)$ev['date_debut']) > time()) {
                $upcomingEvents++;
            }

            if ((float)$ev['prix'] == 0.0) {
                $freeEvents++;
            }
        }
        ?>

        <div class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">Gestion des événements</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="RECHERCHER...">
                    <button class="search-btn" type="button">🔍</button>
                </div>
                <button class="notification-btn" type="button" aria-label="Notifications">
                    🔔
                    <span class="notif-badge"><?php echo (int)$upcomingEvents; ?></span>
                </button>
                <a href="../index.php" class="btn-view-site">🌐 VOIR LE SITE</a>
            </div>
        </div>

        <div class="stats-overview">
            <div class="stat-card stat-primary">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <div class="stat-label">ÉVÉNEMENTS TOTAUX</div>
                    <div class="stat-value"><?php echo (int)$totalEvents; ?></div>
                    <div class="stat-change positive">+<?php echo (int)$totalEvents; ?> au total</div>
                </div>
            </div>
            <div class="stat-card stat-secondary">
                <div class="stat-icon">🧾</div>
                <div class="stat-content">
                    <div class="stat-label">INSCRIPTIONS</div>
                    <div class="stat-value"><?php echo (int)$totalParticipants; ?></div>
                    <div class="stat-change"><?php echo (int)$totalParticipants; ?> participants</div>
                </div>
            </div>
            <div class="stat-card stat-accent">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <div class="stat-label">À VENIR</div>
                    <div class="stat-value"><?php echo (int)$upcomingEvents; ?></div>
                    <div class="stat-change"><?php echo (int)$upcomingEvents; ?> événements à venir</div>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">🆓</div>
                <div class="stat-content">
                    <div class="stat-label">GRATUITS</div>
                    <div class="stat-value"><?php echo (int)$freeEvents; ?></div>
                    <div class="stat-change"><?php echo (int)$freeEvents; ?> gratuits</div>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">📋 Liste des événements</h3>
                <a href="evenements/create.php" class="btn-view-site">➕ NOUVEL ÉVÉNEMENT</a>
            </div>
            <div class="card-content">
                <?php if (empty($evenements)): ?>
                    <div style="text-align: center; padding: 2rem; color: var(--text-gray);">
                        🎮 AUCUN ÉVÉNEMENT TROUVÉ
                        <div style="margin-top: 1rem; font-size: 0.8rem; font-family: 'VT323', monospace;">Créez votre premier événement pour commencer !</div>
                    </div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>NOM</th>
                                <th>JEU</th>
                                <th>ORGANISATEUR</th>
                                <th>DATE DÉBUT</th>
                                <th>DATE FIN</th>
                                <th>LIEU</th>
                                <th>PLACES</th>
                                <th>PRIX</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evenements as $ev):
                                $participationsCount = $evenementController->show($ev['id_evenement'])->countParticipations();
                                $placesMax = (int)($ev['places_max'] ?? 0);
                                $isFull = $placesMax > 0 && $participationsCount >= $placesMax;
                                $isWarning = $placesMax > 0 && !$isFull && $participationsCount >= (int)ceil($placesMax * 0.8);

                                $price = (float)($ev['prix'] ?? 0);
                                $isFree = $price == 0.0;
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars((string)$ev['nom']); ?></strong></td>
                                <td><?php echo htmlspecialchars((string)$ev['jeu']); ?></td>
                                <td>
                                    <span class="badge badge-console"><?php echo htmlspecialchars((string)($ev['organisateur_nom'] ?? 'Admin')); ?></span>
                                </td>
                                <td><?php echo isset($ev['date_debut']) ? date('d/m/Y H:i', strtotime((string)$ev['date_debut'])) : 'N/A'; ?></td>
                                <td><?php echo isset($ev['date_fin']) ? date('d/m/Y H:i', strtotime((string)$ev['date_fin'])) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars((string)$ev['lieu']); ?></td>
                                <td>
                                    <?php if ($isFull): ?>
                                        <span class="status" style="background-color: rgba(255, 0, 85, 0.2); border: 1px solid var(--danger-red); color: var(--danger-red);">
                                            <?php echo (int)$participationsCount; ?> / <?php echo (int)$placesMax; ?>
                                        </span>
                                    <?php elseif ($isWarning): ?>
                                        <span class="status status-pending">
                                            <?php echo (int)$participationsCount; ?> / <?php echo (int)$placesMax; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="status status-active">
                                            <?php echo (int)$participationsCount; ?> / <?php echo (int)$placesMax; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isFree): ?>
                                        <span class="badge badge-playstation">GRATUIT</span>
                                    <?php else: ?>
                                        <span class="badge badge-nintendo"><?php echo htmlspecialchars((string)$price); ?>€</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a class="icon-btn edit" href="evenements/edit.php?id=<?php echo (int)$ev['id_evenement']; ?>" title="Modifier">✏️</a>
                                        <a class="icon-btn delete" href="evenements/delete.php?id=<?php echo (int)$ev['id_evenement']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')" title="Supprimer">🗑️</a>
                                        <a class="icon-btn edit" href="../views/front/participer.php?id=<?php echo (int)$ev['id_evenement']; ?>" target="_blank" title="Voir">👁️</a>
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
                            <!-- AJOUTER : Colonne organisateur -->
                            <td><span class="organizer-name"><?php echo htmlspecialchars($evenement['organisateur_nom'] ?? 'Admin'); ?></span></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($evenement['date_debut'])); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($evenement['date_fin'])); ?></td>
                            <td><?php echo htmlspecialchars($evenement['lieu']); ?></td>
                            <td>
                                <span class="places-count <?php echo $placesClass; ?>">
                                    <?php echo $participationsCount; ?> / <?php echo $evenement['places_max']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="price">
                                    <?php echo $evenement['prix'] == 0 ? 'GRATUIT' : $evenement['prix'] . '€'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="evenements/edit.php?id=<?php echo $evenement['id_evenement']; ?>" class="btn-edit" title="Modifier">
                                    ✏️
                                </a>
                                <a href="evenements/delete.php?id=<?php echo $evenement['id_evenement']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')" title="Supprimer">
                                    🗑️
                                </a>
                                <a href="../views/front/participer.php?id=<?php echo $evenement['id_evenement']; ?>" class="btn-view" target="_blank" title="Voir l'événement">
                                    👁️
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once 'views/back/footer.php'; ?>
</body>
</html>