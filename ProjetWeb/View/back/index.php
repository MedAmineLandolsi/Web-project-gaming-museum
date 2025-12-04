<?php
// Initialiser les variables si elles n'existent pas
if (!isset($reclamations)) {
    $reclamations = [];
}
if (!isset($reponses)) {
    $reponses = [];
}
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
    <link rel="stylesheet" href="/ProjetWeb/assets/css/admin.css">
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
                    <a href="index.php?action=back">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                        <span class="nav-count"><?= count($reclamations) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=back">
                        <span class="nav-icon">📋</span>
                        <span class="nav-text">RÉCLAMATIONS</span>
                        <span class="nav-count"><?= count($reclamations) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=back">
                        <span class="nav-icon">💬</span>
                        <span class="nav-text">RÉPONSES</span>
                        <span class="nav-count"><?= count($reponses) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=front">
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
            <a href="index.php?action=front" class="btn-logout">
                <span>←</span> RETOUR AU SITE
            </a>
            <button class="btn-logout" onclick="if(confirm('Déconnexion ?')) window.location.href='index.php?action=front';">
                <span>→</span> DÉCONNEXION
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
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
                    <input type="text" class="search-input" placeholder="RECHERCHER...">
                    <button class="search-btn">🔍</button>
                </div>
                <button class="notification-btn">
                    🔔
                    <span class="notif-badge"><?= $pending ?></span>
                </button>
                <a href="index.php?action=front" class="btn-view-site">VOIR LE SITE</a>
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
                    <span class="action-text">VOIR STATISTIQUES</span>
                </button>
                <button class="action-btn action-accent" onclick="if(confirm('Supprimer toutes les données ?')) window.location.href='index.php?action=back&method=clear'">
                    <span class="action-icon">🗑️</span>
                    <span class="action-text">TOUT SUPPRIMER</span>
                </button>
                <button class="action-btn action-warning" onclick="window.location.href='index.php?action=front'">
                    <span class="action-icon">🌐</span>
                    <span class="action-text">VOIR LE SITE</span>
                </button>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Recent Reclamations -->
            <div class="dashboard-card recent-games">
                <div class="card-header">
                    <h3 class="card-title">📋 RÉCLAMATIONS RÉCENTES</h3>
                    <button class="btn-view-all-small">VOIR TOUT</button>
                </div>
                <div class="card-content">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date/Heure</th>
                                <th>Client</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Titre</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableReclamations">
                            <?php if (empty($reclamations)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem;">
                                    Aucune réclamation reçue.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($reclamations as $r): 
                                    $reponse = null;
                                    foreach ($reponses as $rep) {
                                        if ($rep['reclamationId'] == $r['id']) {
                                            $reponse = $rep;
                                            break;
                                        }
                                    }
                                    $statut = $reponse ? 'Répondu' : 'En attente';
                                    $statutClass = $reponse ? 'status-resolved' : 'status-pending';
                                ?>
                                <tr class="reclamation-row" data-status="<?= $reponse ? 'resolved' : 'pending' ?>">
                                    <td>
                                        <strong><?= isset($r['date_creation']) ? date('d/m/Y', strtotime($r['date_creation'])) : (isset($r['date']) ? htmlspecialchars($r['date']) : 'N/A') ?></strong><br />
                                        <small style="color: var(--text-gray);"><?= isset($r['date_creation']) ? date('H:i:s', strtotime($r['date_creation'])) : (isset($r['heure']) ? htmlspecialchars($r['heure']) : '') ?></small>
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
                                            <button class="icon-btn edit" onclick="voirDetails(<?= $r['id'] ?>)" title="Voir détails">👁️</button>
                                            <?php if ($reponse): ?>
                                                <button class="icon-btn edit" onclick="modifierReponse(<?= $r['id'] ?>)" title="Modifier réponse">✏️</button>
                                            <?php else: ?>
                                                <button class="icon-btn edit" onclick="repondre(<?= $r['id'] ?>)" title="Répondre">💬</button>
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
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Notification -->
    <div id="notification" class="notification"></div>

    <script src="/ProjetWeb/assets/js/admin.js"></script>
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

    function filterTable(filter) {
        const rows = document.getElementsByClassName('reclamation-row');
        const buttons = document.querySelectorAll('.filter-btn');
        
        // Mettre à jour les boutons actifs
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
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
        showNotification('Action effectuée avec succès !', 'success');
    }

    // Animer les compteurs
    document.addEventListener('DOMContentLoaded', function() {
        const statValues = document.querySelectorAll('.stat-value[data-target]');
        statValues.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    stat.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    stat.textContent = target;
                }
            };
            updateCounter();
        });
    });
    </script>
</body>
</html>
