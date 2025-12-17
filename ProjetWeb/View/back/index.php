<?php
// Initialiser les variables si elles n'existent pas
if (!isset($reclamations)) {
    $reclamations = [];
}
if (!isset($reponses)) {
    $reponses = [];
}

require_once __DIR__ . '/../../config/paths.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gaming Support</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <!-- UPDATE THIS PATH TO MATCH YOUR PROJECT STRUCTURE -->
    <link rel="stylesheet" href="<?= asset('assets/css/admin-style.css') ?>">
    <!-- OR if admin-style.css is in the same directory as index.php, use: -->
    <!-- <link rel="stylesheet" href="admin-style.css"> -->
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
                    <a href="index.php?action=back#dashboard">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                        <span class="nav-count"><?= count($reclamations) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=back#reclamations">
                        <span class="nav-icon">📋</span>
                        <span class="nav-text">RÉCLAMATIONS</span>
                        <span class="nav-count"><?= count($reclamations) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=back#reponses">
                        <span class="nav-icon">💬</span>
                        <span class="nav-text">RÉPONSES</span>
                        <span class="nav-count"><?= count($reponses) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('index.php?action=front') ?>" style="color: #00FF41; font-weight: bold;">
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
                    <div class="admin-role">GAMING SUPPORT</div>
                </div>
            </div>
            <a href="<?= url('index.php?action=front') ?>" class="btn-logout" style="background: #00FF41; color: #000; border-color: #00FF41;">
                <span>←</span> RETOUR AU SITE
            </a>
            <button class="btn-logout" onclick="if(confirm('Déconnexion ?')) window.location.href='<?= url('index.php?action=front') ?>';">
                <span>→</span> DÉCONNEXION
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="dashboard">
        <?php
        // Calculer les statistiques AVANT de les utiliser
        $total = count($reclamations);
        $pending = 0;
        $resolved = 0;
        
        foreach ($reclamations as $r) {
            $hasReponse = false;
            foreach ($reponses as $rep) {
                if ($rep['reclamationId'] == $r['id']) {
                    $hasReponse = true;
                    break;
                }
            }
            if ($hasReponse) {
                $resolved++;
            } else {
                $pending++;
            }
        }
        $tauxResolution = $total > 0 ? round(($resolved / $total) * 100) : 0;
        ?>
        
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">Tableau de bord administrateur</h1>
            </div>
            <div class="top-bar-right">
                <div class="search-box">
                    <input type="text" id="searchInput" class="search-input" placeholder="RECHERCHER...">
                    <button class="search-btn">🔍</button>
                </div>
                <button class="notification-btn">
                    🔔
                    <span class="notif-badge"><?= $pending ?></span>
                </button>
                <a href="../gaming_museum/view/frontoffice/index.php" class="btn-view-site">
                    🌐 VOIR LE SITE
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card stat-primary">
                <div class="stat-icon">📋</div>
                <div class="stat-content">
                    <div class="stat-label">RÉCLAMATIONS TOTALES</div>
                    <div class="stat-value" data-target="<?= $total ?>"><?= $total ?></div>
                    <div class="stat-change positive">+<?= $total ?> au total</div>
                </div>
            </div>
            <div class="stat-card stat-secondary">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <div class="stat-label">EN ATTENTE</div>
                    <div class="stat-value" data-target="<?= $pending ?>"><?= $pending ?></div>
                    <div class="stat-change negative"><?= $pending ?> non traitées</div>
                </div>
            </div>
            <div class="stat-card stat-accent">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-label">RÉPONDUES</div>
                    <div class="stat-value" data-target="<?= $resolved ?>"><?= $resolved ?></div>
                    <div class="stat-change positive"><?= $resolved ?> résolues</div>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">📈</div>
                <div class="stat-content">
                    <div class="stat-label">TAUX DE RÉSOLUTION</div>
                    <div class="stat-value" data-target="<?= $tauxResolution ?>"><?= $tauxResolution ?>%</div>
                    <div class="stat-change">Performance</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2 class="section-title">⚡ Actions rapides</h2>
            <div class="action-grid">
                <button class="action-btn action-primary" onclick="window.location.href='index.php?action=back&method=export'">
                    <span class="action-icon">📤</span>
                    <span class="action-text">EXPORTER DONNÉES</span>
                </button>
                <button class="action-btn action-secondary" onclick="window.location.href='index.php?action=back'">
                    <span class="action-icon">📊</span>
                    <span class="action-text">STATISTIQUES</span>
                </button>
                <button class="action-btn action-accent" onclick="filterTable('all')">
                    <span class="action-icon">🔄</span>
                    <span class="action-text">ACTUALISER</span>
                </button>
                <button class="action-btn action-warning" onclick="window.location.href='index.php?action=back'">
                    <span class="action-icon">⚙️</span>
                    <span class="action-text">PARAMÈTRES</span>
                </button>
            </div>
        </div>

        <!-- Data Grid -->
        <div class="data-grid">
            <!-- Recent Reclamations Table -->
            <div class="dashboard-card reclamations-table" id="reclamations">
                <div class="card-header">
                    <h3 class="card-title">📋 Réclamations récentes</h3>
                    <div class="filter-buttons">
                        <button class="filter-btn active" onclick="filterTable('all', event)">TOUT</button>
                        <button class="filter-btn" onclick="filterTable('pending', event)">EN ATTENTE</button>
                        <button class="filter-btn" onclick="filterTable('resolved', event)">RÉPONDUES</button>
                    </div>
                </div>
                <div class="card-content">
                    <table class="data-table" id="reclamationsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Titre</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reclamations)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-gray);">
                                        📭 Aucune réclamation disponible
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reclamations as $r): 
                                    // Vérifier s'il y a une réponse
                                    $reponse = null;
                                    foreach ($reponses as $rep) {
                                        if ($rep['reclamationId'] == $r['id']) {
                                            $reponse = $rep;
                                            break;
                                        }
                                    }
                                    
                                    $statut = $reponse ? 'RÉPONDU' : 'EN ATTENTE';
                                    $statutClass = $reponse ? 'resolved' : 'pending';
                                    $dataStatus = $reponse ? 'resolved' : 'pending';
                                ?>
                                <tr class="reclamation-row" data-status="<?= $dataStatus ?>">
                                    <td>
                                        <div class="user-id">
                                            <span class="id-badge">#<?= str_pad($r['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($r['nomClient']) ?></td>
                                    <td><?= htmlspecialchars($r['emailClient']) ?></td>
                                    <td><span class="badge badge-console"><?= htmlspecialchars($r['typeReclamation']) ?></span></td>
                                    <td>
                                        <div class="game-name">
                                            <span class="game-icon">📝</span>
                                            <?= htmlspecialchars($r['titre']) ?>
                                        </div>
                                    </td>
                                    <td><span class="status <?= $statutClass ?>"><?= $statut ?></span></td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            <a class="icon-btn edit" href="index.php?action=back&method=details&id=<?= $r['id'] ?>" title="Voir détails">👁️</a>
                                            <?php if ($reponse): ?>
                                                <a class="icon-btn edit" href="index.php?action=back&method=details&id=<?= $r['id'] ?>#reponse" title="Modifier réponse">✏️</a>
                                            <?php else: ?>
                                                <a class="icon-btn edit" href="index.php?action=back&method=details&id=<?= $r['id'] ?>#reponse" title="Répondre">💬</a>
                                            <?php endif; ?>
                                            <a href="index.php?action=back&method=delete&id=<?= $r['id'] ?>" class="icon-btn delete" onclick="return confirm('Supprimer cette réclamation ?')" title="Supprimer">🗑️</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="dashboard-card activity-timeline">
                <div class="card-header">
                    <h3 class="card-title">⚡ Activité récente</h3>
                </div>
                <div class="card-content">
                    <div class="timeline">
                        <?php 
                        $recentReclamations = array_slice(array_reverse($reclamations), 0, 5);
                        if (empty($recentReclamations)): ?>
                            <div style="text-align: center; padding: 2rem; color: var(--text-gray);">
                                📭 Aucune activité récente
                            </div>
                        <?php else:
                            foreach ($recentReclamations as $r): 
                                $reponse = null;
                                foreach ($reponses as $rep) {
                                    if ($rep['reclamationId'] == $r['id']) {
                                        $reponse = $rep;
                                        break;
                                    }
                                }
                        ?>
                        <div class="timeline-item">
                            <div class="timeline-marker <?= $reponse ? 'primary' : 'warning' ?>"></div>
                            <div class="timeline-content">
                                <div class="timeline-time"><?= isset($r['date_creation']) ? date('d/m H:i', strtotime($r['date_creation'])) : 'N/A' ?></div>
                                <div class="timeline-text">
                                    <strong><?= htmlspecialchars($r['nomClient']) ?></strong> - <?= htmlspecialchars($r['titre']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; 
                        endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Responses -->
            <div class="dashboard-card" id="reponses">
                <div class="card-header">
                    <h3 class="card-title">💬 Réponses récentes</h3>
                </div>
                <div class="card-content">
                    <?php
                    $reclamationsById = [];
                    foreach ($reclamations as $r) {
                        $reclamationsById[$r['id']] = $r;
                    }
                    ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Réclamation</th>
                                <th>Client</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reponses)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-gray);">
                                        💬 Aucune réponse disponible
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_slice(array_reverse($reponses), 0, 10) as $rep):
                                    $rid = $rep['reclamationId'] ?? null;
                                    $linkedReclamation = $rid && isset($reclamationsById[$rid]) ? $reclamationsById[$rid] : null;
                                    $clientName = $linkedReclamation['nomClient'] ?? 'N/A';
                                    $date = $rep['dateReponse'] ?? (isset($rep['date_reponse']) ? date('d/m/Y', strtotime($rep['date_reponse'])) : 'N/A');
                                    $excerpt = isset($rep['message']) ? $rep['message'] : '';
                                    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                                        $excerpt = mb_strlen($excerpt) > 80 ? mb_substr($excerpt, 0, 80) . '…' : $excerpt;
                                    } else {
                                        $excerpt = strlen($excerpt) > 80 ? substr($excerpt, 0, 80) . '...' : $excerpt;
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <span class="id-badge">#<?= $rid ? str_pad((int)$rid, 3, '0', STR_PAD_LEFT) : '---' ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($clientName) ?></td>
                                    <td><?= htmlspecialchars($excerpt) ?></td>
                                    <td><?= htmlspecialchars($date) ?></td>
                                    <td>
                                        <?php if ($rid): ?>
                                            <a class="icon-btn edit" href="index.php?action=back&method=details&id=<?= (int)$rid ?>" title="Voir détails">👁️</a>
                                        <?php else: ?>
                                            <span style="color: var(--text-gray);">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Notification -->
    <div id="notification" class="notification"></div>

    <!-- UPDATE THIS PATH TO MATCH YOUR PROJECT STRUCTURE -->
    <script src="<?= asset('assets/js/admin-script.js') ?>"></script>
    <!-- OR if admin-script.js is in the same directory as index.php, use: -->
    <!-- <script src="admin-script.js"></script> -->
    
    <script type="text/javascript">
    function showNotification(message, type) {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.className = 'notification ' + type;
        notification.classList.add('show');
        
        setTimeout(function() {
            notification.classList.remove('show');
        }, 3000);
    }

    function filterTable(filter, ev) {
        const rows = document.getElementsByClassName('reclamation-row');
        const buttons = document.querySelectorAll('.filter-btn');
        
        // Mettre à jour les boutons actifs
        buttons.forEach(btn => btn.classList.remove('active'));
        if (ev && ev.target) {
            ev.target.classList.add('active');
        }
        
        // Filtrer les lignes
        for (let i = 0; i < rows.length; i++) {
            if (filter === 'all') {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = rows[i].getAttribute('data-status') === filter ? '' : 'none';
            }
        }
    }

    function repondre(id) {
        const reponse = prompt('💬 Répondre à la réclamation:');
        if (reponse !== null && reponse.trim() !== "") {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php?action=back&method=addReponse';
            
            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'reclamationId';
            inputId.value = id;
            
            const inputMessage = document.createElement('input');
            inputMessage.type = 'hidden';
            inputMessage.name = 'message';
            inputMessage.value = reponse.trim();
            
            form.appendChild(inputId);
            form.appendChild(inputMessage);
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    function modifierReponse(id) {
        const reponse = prompt('✏️ Modifier la réponse:');
        if (reponse !== null && reponse.trim() !== "") {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php?action=back&method=addReponse';
            
            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'reclamationId';
            inputId.value = id;
            
            const inputMessage = document.createElement('input');
            inputMessage.type = 'hidden';
            inputMessage.name = 'message';
            inputMessage.value = reponse.trim();
            
            form.appendChild(inputId);
            form.appendChild(inputMessage);
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    function voirDetails(id) {
        window.location.href = 'index.php?action=back&method=details&id=' + id;
    }

    // Afficher notification si paramètre présent dans l'URL
    const urlParams = window.location.search;
    if (urlParams.indexOf('success=1') !== -1) {
        showNotification('✓ Action effectuée avec succès !', 'success');
    }
    </script>
</body>
</html>
