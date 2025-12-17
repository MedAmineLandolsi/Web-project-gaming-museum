<?php
// Utiliser la variable $publicationModel passée depuis le contrôleur
$publication = $publicationModel ?? $this->publicationModel;

$publishedAt = '';
if (!empty($publication->date_publication)) {
    $ts = strtotime((string) $publication->date_publication);
    if ($ts !== false) {
        $publishedAt = date('d/m/Y à H:i', $ts);
    }
}

$updatedAt = '';
if (!empty($publication->date_modification)) {
    $ts = strtotime((string) $publication->date_modification);
    if ($ts !== false) {
        $updatedAt = date('d/m/Y à H:i', $ts);
    }
}

$authorName = trim((string) ($publication->auteur_display_name ?? $publication->auteur_nom ?? ''));
if ($authorName === '') {
    $authorName = 'Utilisateur';
}

$isOwner = false;
if (isset($_SESSION['user_id'])) {
    $uid = (int) $_SESSION['user_id'];
    $pubUserId = isset($publication->user_id) ? (int) $publication->user_id : 0;
    $pubAuteurId = isset($publication->auteur_id) ? (int) $publication->auteur_id : 0;
    $isOwner = ($uid > 0) && ($uid === $pubUserId || $uid === $pubAuteurId);
}
?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="community-card p-4">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-start mb-3">
                <div>
                    <h1 class="h3 mb-2">Détails de la publication</h1>
                    <div class="text-muted">
                        <i class="fas fa-user me-1"></i>
                        <?php echo htmlspecialchars($authorName); ?>
                        <?php if (!empty($publication->communaute_nom) || !empty($publication->communaute_id)): ?>
                            <span class="mx-1">•</span>
                            <i class="fas fa-users me-1"></i>
                            <a class="text-decoration-none" href="<?php echo BASE_URL; ?>/communautes/<?php echo (int) ($publication->communaute_id ?? 0); ?>">
                                <?php echo htmlspecialchars((string) ($publication->communaute_nom ?? 'Communauté')); ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($publishedAt !== ''): ?>
                            <span class="mx-1">•</span>
                            <i class="fas fa-calendar me-1"></i>
                            <?php echo htmlspecialchars($publishedAt); ?>
                        <?php endif; ?>
                        <?php if ($updatedAt !== '' && $updatedAt !== $publishedAt): ?>
                            <div><small class="text-muted">Modifié le <?php echo htmlspecialchars($updatedAt); ?></small></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="<?php echo BASE_URL; ?>/publications" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <?php if (!empty($publication->communaute_id)): ?>
                        <a href="<?php echo BASE_URL; ?>/communautes/<?php echo (int) $publication->communaute_id; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-users me-2"></i>Communauté
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <div class="publication-content">
                    <?php echo nl2br(htmlspecialchars((string) ($publication->contenu ?? ''))); ?>
                </div>
            </div>

            <?php if (!empty($publication->images) && is_array($publication->images)): ?>
                <div class="row g-2 mb-3">
                    <?php foreach ($publication->images as $image): ?>
                        <?php if (!empty($image)): ?>
                            <div class="col-6 col-md-4">
                                <div class="p-2" style="border:1px solid var(--border);border-radius:12px;">
                                    <img
                                        src="<?php echo htmlspecialchars((string) $image); ?>"
                                        alt="Image publication"
                                        class="img-fluid rounded"
                                        onerror="this.style.display='none'"
                                    >
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                <div class="d-flex gap-2">
                    <span class="badge bg-success"><i class="fas fa-heart me-1"></i><?php echo (int) ($publication->likes ?? 0); ?></span>
                    <span class="badge bg-info"><i class="fas fa-comment me-1"></i><?php echo (int) ($publication->commentaires ?? 0); ?></span>
                </div>

                <?php if ($isOwner): ?>
                    <div class="d-flex gap-2">
                        <a href="<?php echo BASE_URL; ?>/publications/edit/<?php echo (int) ($publication->id ?? 0); ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                        <form action="<?php echo BASE_URL; ?>/publications/delete/<?php echo (int) ($publication->id ?? 0); ?>" method="POST" class="d-inline">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer cette publication ?');">
                                <i class="fas fa-trash me-2"></i>Supprimer
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div>

    <!-- Sidebar -->
    <div class="communaute-sidebar">
        <!-- Bouton rejoindre -->
            <!-- Rejoindre la communauté supprimé -->



        <!-- Actions du créateur -->
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $this->communauteModel->createur_id): ?>
        <div class="sidebar-section">
            <h5><i class="fas fa-cog"></i> Gestion</h5>
            <div class="d-flex gap-2">
                <a href="<?php echo BASE_URL; ?>/admin/communautes/<?php echo $this->communauteModel->id; ?>/edit" class="btn btn-warning btn-sm flex-fill">
                    <i class="fas fa-edit me-1"></i>Modifier
                </a>
                <form action="<?php echo BASE_URL; ?>/admin/communautes/<?php echo $this->communauteModel->id; ?>/delete" method="POST" class="d-inline flex-fill">
                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ?')">
                        <i class="fas fa-trash me-1"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Fonctionnalité "rejoindre/quitter" supprimée.
</script>