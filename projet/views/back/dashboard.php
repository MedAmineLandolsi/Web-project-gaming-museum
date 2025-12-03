<?php
$defaultStats = [
    'members_total' => 0,
    'communautes_total' => 0,
    'publications_total' => 0,
    'comments_total' => 0,
    'members_new_week' => 0,
    'communautes_new_month' => 0,
    'publications_today' => 0,
    'engagement_rate' => 0,
];
$stats = isset($stats) && is_array($stats) ? array_merge($defaultStats, $stats) : $defaultStats;
$latestMembers = $latestMembers ?? [];
$latestCommunautes = $latestCommunautes ?? [];
$latestPublications = $latestPublications ?? [];
$statusClasses = [
    'actif' => 'status-actif',
    'inactif' => 'status-inactif',
    'suspendu' => 'status-suspendu',
];
$truncate = function ($text, $limit = 90) {
    $clean = strip_tags((string) $text);
    return strlen($clean) > $limit ? substr($clean, 0, $limit) . '...' : $clean;
};
?>

<section class="stats-overview">
    <article class="stat-card stat-primary">
        <i class="fas fa-users stat-icon"></i>
        <div class="stat-content">
            <span class="stat-label">Membres actifs</span>
            <span class="stat-value"><?= number_format($stats['members_total'], 0, ',', ' ') ?></span>
            <span class="stat-change positive">+<?= number_format($stats['members_new_week'], 0, ',', ' ') ?> cette semaine</span>
        </div>
    </article>

    <article class="stat-card stat-secondary">
        <i class="fas fa-layer-group stat-icon"></i>
        <div class="stat-content">
            <span class="stat-label">Communautés</span>
            <span class="stat-value"><?= number_format($stats['communautes_total'], 0, ',', ' ') ?></span>
            <span class="stat-change"><?= number_format($stats['communautes_new_month'], 0, ',', ' ') ?> nouvelles ce mois</span>
        </div>
    </article>

    <article class="stat-card stat-accent">
        <i class="fas fa-newspaper stat-icon"></i>
        <div class="stat-content">
            <span class="stat-label">Publications</span>
            <span class="stat-value"><?= number_format($stats['publications_total'], 0, ',', ' ') ?></span>
            <span class="stat-change"><?= number_format($stats['publications_today'], 0, ',', ' ') ?> publiées aujourd'hui</span>
        </div>
    </article>

    <article class="stat-card stat-warning">
        <i class="fas fa-bolt stat-icon"></i>
        <div class="stat-content">
            <span class="stat-label">Taux d'engagement</span>
            <span class="stat-value"><?= number_format($stats['engagement_rate'], 0) ?>%</span>
            <div class="engagement-bar">
                <div class="engagement-fill" style="width: <?= min(100, (int) $stats['engagement_rate']) ?>%;"></div>
            </div>
        </div>
    </article>
</section>

<div class="admin-card">
    <div class="admin-card-header">
        <h3><i class="fas fa-tachometer-alt me-2"></i>Actions rapides</h3>
    </div>
    <div class="admin-card-body">
        <div class="quick-action-grid">
            <a href="/projet/admin/membres/create" class="quick-action">
                <i class="fas fa-user-plus"></i>
                <span>Nouveau membre</span>
            </a>
            <a href="/projet/admin/communautes/create" class="quick-action">
                <i class="fas fa-users-medical"></i>
                <span>Nouvelle communauté</span>
            </a>
            <a href="/projet/admin/publications/create" class="quick-action">
                <i class="fas fa-pen-nib"></i>
                <span>Nouvelle publication</span>
            </a>
            <a href="/projet/" class="quick-action">
                <i class="fas fa-eye"></i>
                <span>Voir le site</span>
            </a>
        </div>
    </div>
</div>

<section class="dashboard-grid">
    <div class="recent-activity admin-card">
        <div class="admin-card-header">
            <h3><i class="fas fa-bolt me-2"></i>Activité récente</h3>
        </div>
        <div class="admin-card-body">
            <?php if (empty($latestPublications)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Aucune publication récente</p>
                </div>
            <?php else: ?>
                <ul class="activity-list">
                    <?php foreach ($latestPublications as $publication): ?>
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    <?= htmlspecialchars(($publication['prenom'] ?? '') . ' ' . ($publication['nom'] ?? '')) ?>
                                    <span>→ <?= htmlspecialchars($publication['communaute_nom'] ?? 'Communauté') ?></span>
                                </div>
                                <div class="activity-meta">
                                    <?= htmlspecialchars($truncate($publication['contenu'] ?? '')) ?>
                                </div>
                            </div>
                            <div class="activity-extra">
                                <span class="activity-date"><?= date('d/m H:i', strtotime($publication['date_publication'] ?? 'now')) ?></span>
                                <span class="activity-tags">
                                    <i class="fas fa-thumbs-up"></i> <?= (int) ($publication['likes'] ?? 0) ?>
                                    <i class="fas fa-comment ms-2"></i> <?= (int) ($publication['commentaires'] ?? 0) ?>
                                </span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="quick-stats admin-card">
        <div class="admin-card-header">
            <h3><i class="fas fa-stream me-2"></i>Vue d'ensemble</h3>
        </div>
        <div class="admin-card-body">
            <div class="list-card">
                <h4><i class="fas fa-user-clock me-2"></i>Derniers membres</h4>
                <?php if (empty($latestMembers)): ?>
                    <div class="empty-state small">
                        <p>Aucun membre récent</p>
                    </div>
                <?php else: ?>
                    <ul class="mini-list">
                        <?php foreach ($latestMembers as $member): ?>
                            <li class="mini-item">
                                <div>
                                    <span class="mini-title">
                                        <?= htmlspecialchars(($member['prenom'] ?? '') . ' ' . ($member['nom'] ?? '')) ?>
                                    </span>
                                    <span class="mini-meta"><?= htmlspecialchars($member['email'] ?? '') ?></span>
                                </div>
                                <div class="mini-extra">
                                    <span class="mini-date"><?= date('d/m', strtotime($member['date_inscription'] ?? 'now')) ?></span>
                                    <span class="status-badge <?= $statusClasses[$member['statut']] ?? 'status-actif' ?>">
                                        <?= strtoupper($member['statut'] ?? 'actif') ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="list-card mt-4">
                <h4><i class="fas fa-users me-2"></i>Nouvelles communautés</h4>
                <?php if (empty($latestCommunautes)): ?>
                    <div class="empty-state small">
                        <p>Pas encore de communautés</p>
                    </div>
                <?php else: ?>
                    <ul class="mini-list">
                        <?php foreach ($latestCommunautes as $communaute): ?>
                            <li class="mini-item">
                                <div>
                                    <span class="mini-title"><?= htmlspecialchars($communaute['nom'] ?? 'Communauté') ?></span>
                                    <span class="mini-meta"><?= htmlspecialchars($communaute['categorie'] ?? 'Catégorie') ?></span>
                                </div>
                                <div class="mini-extra">
                                    <span class="mini-date"><?= date('d/m', strtotime($communaute['date_creation'] ?? 'now')) ?></span>
                                    <span class="mini-author">
                                        <i class="fas fa-user"></i>
                                        <?= htmlspecialchars(($communaute['prenom'] ?? '') . ' ' . ($communaute['nom_membre'] ?? '')) ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>