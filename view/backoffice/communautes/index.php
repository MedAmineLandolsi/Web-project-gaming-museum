<div class="d-flex justify-content-between align-items-center mb-5 mt-2 pb-2 border-bottom">
    <h1 class="fs-2 fw-bold mb-0"><i class="fas fa-users me-2"></i>Liste des communautés</h1>
    <a href="<?php echo BASE_URL; ?>/admin/communautes/create" class="btn btn-primary btn-lg d-flex align-items-center gap-2">
        <i class="fas fa-plus"></i> <span>Nouvelle communauté</span>
    </a>
</div>

<div class="card shadow rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?php echo BASE_URL; ?>/admin/communautes" class="d-flex flex-wrap gap-2 align-items-center">
            <div class="fw-semibold">Filtrer par catégorie :</div>
            <select class="form-select" name="categorie" style="max-width:320px" onchange="this.form.submit()">
                <option value="">Toutes catégories</option>
                <?php foreach (($categories ?? []) as $cat): ?>
                    <?php $cat = (string) $cat; ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo (($_GET['categorie'] ?? '') === $cat) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<?php
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

<div class="card shadow rounded-3 mb-5">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle table-striped mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Créateur</th>
                        <th>Visibilité</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($communautes)): ?>
                        <?php foreach ($communautes as $communaute): ?>
                        <?php
                            $creatorName = $communaute['createur_display_name']
                                ?? trim(($communaute['prenom'] ?? '') . ' ' . ($communaute['nom'] ?? ''));
                            $creatorAvatar = $__makeAvatarUrl($communaute['profile_picture_url'] ?? null);
                        ?>
                        <tr>
                            <td><?php echo $communaute['id']; ?></td>
                            <td><?php echo htmlspecialchars($communaute['nom']); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($communaute['categorie']); ?></span>
                            </td>
                            <td>
                                <div class="creator-info">
                                    <span class="creator-name">
                                        <?php if (!empty($creatorAvatar)): ?>
                                            <img src="<?php echo htmlspecialchars($creatorAvatar); ?>" alt="Avatar" style="width:22px;height:22px;border-radius:50%;object-fit:cover;vertical-align:-5px;margin-right:8px;">
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($creatorName); ?>
                                    </span>
                                    <div class="creator-actions">
                                        <a href="<?php echo BASE_URL; ?>/admin/communautes/<?php echo $communaute['id']; ?>" class="btn btn-outline-info btn-sm" title="Voir la communauté">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (!empty($communaute['isOwner'])): ?>
                                            <button class="btn btn-outline-secondary btn-sm" disabled title="Vous êtes le créateur">
                                                <i class="fas fa-crown"></i>
                                            </button>
                                        <?php else: ?>
                                            <?php if (!empty($communaute['has_joined'])): ?>
                                                <form method="POST" action="<?php echo BASE_URL; ?>/communautes/<?php echo $communaute['id']; ?>/leave" class="d-inline">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Quitter">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" action="<?php echo BASE_URL; ?>/communautes/<?php echo $communaute['id']; ?>/join" class="d-inline">
                                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Rejoindre">
                                                        <i class="fas fa-sign-in-alt"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $communaute['visibilite'] == 'publique' ? 'success' : ($communaute['visibilite'] == 'privee' ? 'warning' : 'secondary'); ?>">
                                    <?php echo ucfirst($communaute['visibilite']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($communaute['date_creation'])); ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="<?php echo BASE_URL; ?>/admin/communautes/<?php echo $communaute['id']; ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo BASE_URL; ?>/admin/communautes/<?php echo $communaute['id']; ?>/edit" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <form action="<?php echo BASE_URL; ?>/admin/communautes/<?php echo $communaute['id']; ?>/delete" method="POST" class="d-inline">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Aucune communauté trouvée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>