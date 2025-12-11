<?php
include '../../controller/JeuxController.php';
$gamesC = new JeuxController();
$list = $gamesC->listjeux();
$gameCount = $gamesC->countGames();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ludology Vault</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <div class="logo-box">[LOGO]</div>
                <div class="admin-title">
                    <h2>LUDOLOGY VAULT</h2>
                    <span class="admin-badge">ADMIN PANEL</span>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item active">
                    <a href="dashboard.php">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="addgame.php">
                        <span class="nav-icon">🎮</span>
                        <span class="nav-text">JEUX</span>
                        <span class="nav-count"><?= $gameCount ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../../crud-commande/view/backoffice/commande.php">
                        <span class="nav-icon">🛒</span>
                        <span class="nav-text">COMMANDES</span>
                        <span class="nav-count">287</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#events">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">ÉVÉNEMENTS</span>
                        <span class="nav-count">12</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#blog">
                        <span class="nav-icon">📝</span>
                        <span class="nav-text">BLOG</span>
                        <span class="nav-count">45</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#users">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">UTILISATEURS</span>
                        <span class="nav-count">1.2K</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#reclamations">
                        <span class="nav-icon">📮</span>
                        <span class="nav-text">RÉCLAMATIONS</span>
                        <span class="nav-count alert">8</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#analytics">
                        <span class="nav-icon">📈</span>
                        <span class="nav-text">ANALYTICS</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#settings">
                        <span class="nav-icon">⚙️</span>
                        <span class="nav-text">PARAMÈTRES</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">AD</div>
                <div class="admin-info">
                    <span class="admin-name">Admin User</span>
                    <span class="admin-role">Super Admin</span>
                </div>
            </div>
            <button class="btn-logout">
                <span>🚪</span> DÉCONNEXION
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">◄ DASHBOARD PRINCIPAL ►</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <input type="text" placeholder="Rechercher..." class="search-input">
                    <button class="search-btn">🔍</button>
                </div>
                <button class="notification-btn">
                    <span class="notif-icon">🔔</span>
                    <span class="notif-badge">5</span>
                </button>
                <a href="../frontoffice/index.php" style="text-decoration: none;"><button class="btn-view-site">VOIR LE SITE →</button></a>
            </div>
        </header>

        <!-- Stats Overview -->
        <section class="stats-overview">
            <div class="stat-card stat-primary">
                <div class="stat-icon">🎮</div>
                <div class="stat-content">
                    <span class="stat-label">TOTAL JEUX</span>
                    <span class="stat-value" data-target="<?= $gameCount ?>">0</span>
                    <span class="stat-change positive"><?= $gameCount ?> ce mois</span>
                </div>
                <div class="stat-graph">
                    <div class="mini-bars">
                        <span style="height: 40%"></span>
                        <span style="height: 60%"></span>
                        <span style="height: 45%"></span>
                        <span style="height: 80%"></span>
                        <span style="height: 100%"></span>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-secondary">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <span class="stat-label">VISITEURS ACTIFS</span>
                    <span class="stat-value" data-target="1247">0</span>
                    <span class="stat-change positive">+23% cette semaine</span>
                </div>
                <div class="stat-graph">
                    <div class="mini-bars">
                        <span style="height: 50%"></span>
                        <span style="height: 70%"></span>
                        <span style="height: 60%"></span>
                        <span style="height: 90%"></span>
                        <span style="height: 100%"></span>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-accent">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <span class="stat-label">ÉVÉNEMENTS</span>
                    <span class="stat-value" data-target="12">0</span>
                    <span class="stat-change">4 à venir</span>
                </div>
                <div class="stat-graph">
                    <div class="mini-bars">
                        <span style="height: 30%"></span>
                        <span style="height: 50%"></span>
                        <span style="height: 70%"></span>
                        <span style="height: 60%"></span>
                        <span style="height: 80%"></span>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-warning">
                <div class="stat-icon">📮</div>
                <div class="stat-content">
                    <span class="stat-label">RÉCLAMATIONS</span>
                    <span class="stat-value" data-target="8">0</span>
                    <span class="stat-change negative">En attente</span>
                </div>
                <div class="stat-graph">
                    <div class="mini-bars">
                        <span style="height: 60%"></span>
                        <span style="height: 40%"></span>
                        <span style="height: 70%"></span>
                        <span style="height: 50%"></span>
                        <span style="height: 60%"></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="quick-actions">
            <h2 class="section-title">◄ ACTIONS RAPIDES ►</h2>
            <div class="action-grid">
                <button class="action-btn action-primary">
                    <span class="action-icon"><a href="addgame.php" style="text-decoration: none;">➕</a></span>
                    <span class="action-text"><a href="addgame.php" style="text-decoration: none;">AJOUTER UN JEU</a></span>
                </button>
                <button class="action-btn action-secondary">
                    <span class="action-icon">📅</span>
                    <span class="action-text">CRÉER ÉVÉNEMENT</span>
                </button>
                <button class="action-btn action-accent">
                    <span class="action-icon">✍️</span>
                    <span class="action-text">NOUVEL ARTICLE</span>
                </button>
                <button class="action-btn action-warning">
                    <span class="action-icon">👤</span>
                    <span class="action-text">AJOUTER ADMIN</span>
                </button>
            </div>
        </section>

        <!-- Main Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Recent Games -->
            <section class="dashboard-card recent-games">
                <div class="card-header">
                    <h3 class="card-title">◄ COLLECTION DES JEUX  ►</h3>
                    <a href="addgame.php"><button class="btn-view-all-small">VOIR TOUT</button></a>
                    
                </div>
                <div class="card-content">
                    <table class="data-table">

    <thead>
        <tr>
            <th>ID</th>
            <th>NOM DU JEU</th>
            <th>CATEGORIE</th>
            <th>PRIX</th>
            <th>ACTIONS</th>
        </tr>
    </thead>

    <tbody>
    <?php if (!empty($list)) { 
            $counter = 0;
            foreach ($list as $game) { 
                if ($counter >= 5) break;
    ?>  
        <tr>
            <td><?= htmlspecialchars($game['id']); ?></td>

            <td class="game-name">
                <span class="game-icon">🎮</span>
                <?= htmlspecialchars($game['nom']); ?>
            </td>

            <td>
                <span class="badge badge-console">
                    <?= htmlspecialchars($game['categorie']); ?>
                </span>
            </td>

            <td>
                <span class="status status-active">
                    <?= htmlspecialchars($game['prix']); ?>
                </span>
            </td>

            <td>
                <a href="updategame.php?id=<?= $game['id']; ?>">
                    <button class="icon-btn edit">✏️</button>
                </a>

                <a href="delete.php?id=<?= $game['id']; ?>"
                   onclick="return confirm('Supprimer ce jeu ?');">
                    <button type="button" class="icon-btn delete">🗑️</button>
                </a>
            </td>
        </tr>
    <?php 
        $counter++;
        } 
    } 
    ?>
    </tbody>

</table>

                </div>
            </section>

            <!-- Activity Timeline -->
            <section class="dashboard-card activity-timeline">
                <div class="card-header">
                    <h3 class="card-title">◄ ACTIVITÉ RÉCENTE ►</h3>
                </div>
                <div class="card-content">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker primary"></div>
                            <div class="timeline-content">
                                <span class="timeline-time">Il y a 5 min</span>
                                <p class="timeline-text">Nouveau jeu ajouté: <strong>Sonic 2</strong></p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker secondary"></div>
                            <div class="timeline-content">
                                <span class="timeline-time">Il y a 23 min</span>
                                <p class="timeline-text">Événement créé: <strong>Tournoi Retro</strong></p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker accent"></div>
                            <div class="timeline-content">
                                <span class="timeline-time">Il y a 1h</span>
                                <p class="timeline-text">Article publié: <strong>Histoire Nintendo</strong></p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker warning"></div>
                            <div class="timeline-content">
                                <span class="timeline-time">Il y a 2h</span>
                                <p class="timeline-text">Réclamation reçue: <strong>#1024</strong></p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker primary"></div>
                            <div class="timeline-content">
                                <span class="timeline-time">Il y a 3h</span>
                                <p class="timeline-text">127 nouveaux visiteurs aujourd'hui</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Upcoming Events -->
            <section class="dashboard-card upcoming-events">
                <div class="card-header">
                    <h3 class="card-title">◄ ÉVÉNEMENTS PROCHAINS ►</h3>
                    <button class="btn-view-all-small">VOIR TOUT</button>
                </div>
                <div class="card-content">
                    <div class="event-list">
                        <div class="event-item">
                            <div class="event-date">
                                <span class="event-day">25</span>
                                <span class="event-month">NOV</span>
                            </div>
                            <div class="event-info">
                                <h4>Tournoi Retro Arcade</h4>
                                <p>⏰ 14:00 - 22:00</p>
                                <span class="event-status active">Inscriptions ouvertes</span>
                            </div>
                            <button class="icon-btn edit">✏️</button>
                        </div>
                        <div class="event-item">
                            <div class="event-date">
                                <span class="event-day">02</span>
                                <span class="event-month">DEC</span>
                            </div>
                            <div class="event-info">
                                <h4>Exposition Nintendo</h4>
                                <p>⏰ 10:00 - 18:00</p>
                                <span class="event-status pending">En préparation</span>
                            </div>
                            <button class="icon-btn edit">✏️</button>
                        </div>
                        <div class="event-item">
                            <div class="event-date">
                                <span class="event-day">15</span>
                                <span class="event-month">DEC</span>
                            </div>
                            <div class="event-info">
                                <h4>Soirée Gaming Multiplayer</h4>
                                <p>⏰ 18:00 - 00:00</p>
                                <span class="event-status active">Confirmé</span>
                            </div>
                            <button class="icon-btn edit">✏️</button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Popular Games Chart -->
            <section class="dashboard-card popular-games">
                <div class="card-header">
                    <h3 class="card-title">◄ JEUX LES PLUS CONSULTÉS ►</h3>
                </div>
                <div class="card-content">
                    <div class="chart-list">
                        <div class="chart-item">
                            <div class="chart-info">
                                <span class="chart-rank">#1</span>
                                <span class="chart-name">PAC-MAN</span>
                            </div>
                            <div class="chart-bar">
                                <div class="chart-fill" style="width: 95%"></div>
                                <span class="chart-value">3.2K vues</span>
                            </div>
                        </div>
                        <div class="chart-item">
                            <div class="chart-info">
                                <span class="chart-rank">#2</span>
                                <span class="chart-name">TETRIS</span>
                            </div>
                            <div class="chart-bar">
                                <div class="chart-fill" style="width: 87%"></div>
                                <span class="chart-value">2.9K vues</span>
                            </div>
                        </div>
                        <div class="chart-item">
                            <div class="chart-info">
                                <span class="chart-rank">#3</span>
                                <span class="chart-name">SPACE INVADERS</span>
                            </div>
                            <div class="chart-bar">
                                <div class="chart-fill" style="width: 78%"></div>
                                <span class="chart-value">2.6K vues</span>
                            </div>
                        </div>
                        <div class="chart-item">
                            <div class="chart-info">
                                <span class="chart-rank">#4</span>
                                <span class="chart-name">DONKEY KONG</span>
                            </div>
                            <div class="chart-bar">
                                <div class="chart-fill" style="width: 65%"></div>
                                <span class="chart-value">2.2K vues</span>
                            </div>
                        </div>
                        <div class="chart-item">
                            <div class="chart-info">
                                <span class="chart-rank">#5</span>
                                <span class="chart-name">MARIO KART</span>
                            </div>
                            <div class="chart-bar">
                                <div class="chart-fill" style="width: 58%"></div>
                                <span class="chart-value">1.9K vues</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script src="admin-script.js"></script>
</body>
</html>