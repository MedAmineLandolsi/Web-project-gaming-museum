<?php
$defaultStats = [
    'communautes_total' => 0,
    'publications_total' => 0,
    'comments_total' => 0,
    'communautes_new_month' => 0,
    'publications_today' => 0,
    'engagement_rate' => 0,
];
$stats = isset($stats) && is_array($stats) ? array_merge($defaultStats, $stats) : $defaultStats;
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

$__base = rtrim((string) BASE_URL, '/');
$__root = preg_replace('#/projet/?$#', '', $__base);
$__uploadsBase = ($__root === '') ? '/gaming_museum/uploads' : ($__root . '/gaming_museum/uploads');
$__makeAvatarUrl = function ($profilePictureUrl) use ($__uploadsBase) {
    $pp = trim((string) $profilePictureUrl);
    if ($pp === '') {
        return null;
    }
    if (preg_match('#^https?://#i', $pp) || substr($pp, 0, 1) === '/') {
        return $pp;
    }
    return rtrim($__uploadsBase, '/') . '/' . ltrim($pp, '/');
};
?>

<section class="stats-overview">
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
                    <!-- Action supprimée -->
            <a href="<?php echo BASE_URL; ?>/admin/communautes/create" class="quick-action">
                <i class="fas fa-users-medical"></i>
                <span>Nouvelle communauté</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/publications/create" class="quick-action">
                <i class="fas fa-pen-nib"></i>
                <span>Nouvelle publication</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/" class="quick-action">
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
                        <?php
                            $authorName = $publication['auteur_display_name']
                                ?? trim(($publication['prenom'] ?? '') . ' ' . ($publication['nom'] ?? ''));
                            $authorAvatar = $__makeAvatarUrl($publication['profile_picture_url'] ?? null);
                        ?>
                        <li class="activity-item">
                            <div class="activity-icon">
                                <?php if (!empty($authorAvatar)): ?>
                                    <img src="<?= htmlspecialchars($authorAvatar) ?>" alt="Avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                                <?php else: ?>
                                    <i class="fas fa-comment-dots"></i>
                                <?php endif; ?>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    <?= htmlspecialchars($authorName) ?>
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
                <h4><i class="fas fa-user-clock me-2"></i>Informations utilisateurs</h4>
                <div class="empty-state small">
                    <p>Section utilisateurs indisponible</p>
                </div>
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
                                <?php
                                    $creatorName = $communaute['createur_display_name']
                                        ?? trim(($communaute['prenom'] ?? '') . ' ' . ($communaute['nom'] ?? ''));
                                    $creatorAvatar = $__makeAvatarUrl($communaute['profile_picture_url'] ?? null);
                                ?>
                            <li class="mini-item">
                                <div>
                                    <span class="mini-title"><?= htmlspecialchars($communaute['nom'] ?? 'Communauté') ?></span>
                                    <span class="mini-meta"><?= htmlspecialchars($communaute['categorie'] ?? 'Catégorie') ?></span>
                                </div>
                                <div class="mini-extra">
                                    <span class="mini-date"><?= date('d/m', strtotime($communaute['date_creation'] ?? 'now')) ?></span>
                                    <span class="mini-author">
                                            <?php if (!empty($creatorAvatar)): ?>
                                                <img src="<?= htmlspecialchars($creatorAvatar) ?>" alt="Avatar" style="width:18px;height:18px;border-radius:50%;object-fit:cover;vertical-align:-3px;">
                                            <?php else: ?>
                                                <i class="fas fa-user"></i>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($creatorName) ?>
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