<?php
// Utiliser la variable $communauteModel passée depuis le contrôleur
$communaute = $communauteModel ?? $this->communauteModel;
?>

<!-- HEADER MODERNE AVEC BADGE ET NOM -->
<div class="rounded p-4 mb-4" style="background:linear-gradient(90deg,#262837 50%, #212121 100%);box-shadow:0 6px 36px #10121622;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="fs-2 fw-bold text-light me-2"><i class="fas fa-users me-2"></i><?php echo htmlspecialchars($communaute->nom); ?></div>
        <span class="badge px-3 py-2 fs-6 bg-<?php echo $communaute->visibilite=='publique'?'success':'warning'; ?> text-uppercase">
            <?php echo ucfirst($communaute->visibilite); ?> <i class="fas fa-globe ms-1"></i>
        </span>
    </div>
</div>

<div class="card shadow-lg rounded mb-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <?php if (!empty($communaute->avatar)): ?>
                    <img src="<?php echo htmlspecialchars($communaute->avatar); ?>" class="rounded-circle border border-secondary shadow" style="width:110px;height:110px;object-fit:cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-3" style="width:110px;height:110px;">
                        <span class="text-light fw-bold fs-1"> <?php echo strtoupper(substr($communaute->nom,0,2)); ?> </span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <table class="table table-dark table-borderless rounded">
                    <tr><th class="w-25">ID</th><td><?php echo $communaute->id; ?></td></tr>
                    <tr><th>Catégorie</th><td><?php echo htmlspecialchars($communaute->categorie); ?></td></tr>
                    <tr><th>Créateur</th><td><?php echo htmlspecialchars($communaute->createur_nom); ?></td></tr>
                    <tr><th>Date création</th><td><?php echo date('d/m/Y à H:i', strtotime($communaute->date_creation)); ?></td></tr>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <h5 class="mb-2 text-light"><i class="fas fa-info-circle me-2"></i>Description</h5>
            <div class="bg-dark text-light p-3 rounded">
                <?php echo nl2br(htmlspecialchars($communaute->description)); ?>
            </div>
        </div>

        <?php if (!empty($communaute->regles)): ?>
        <div class="mt-4">
            <h5 class="mb-2 text-light"><i class="fas fa-scroll me-2"></i>Règles</h5>
            <div class="bg-dark text-light p-3 rounded">
                <?php echo nl2br(htmlspecialchars($communaute->regles)); ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mt-5">
            <h5 class="mb-4 text-light"><i class="fas fa-list me-2"></i>Publications (<?php echo count($publications); ?>)</h5>
            <?php if (!empty($publications)): ?>
                <div class="row g-4">
                <?php foreach ($publications as $pub): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card bg-dark text-light shadow-sm h-100 border-secondary mb-3">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?php echo !empty($pub['avatar']) ? htmlspecialchars($pub['avatar']) : 'https://ui-avatars.com/api/?name='.urlencode($pub['prenom'].' '.$pub['nom']); ?>" class="rounded-circle border border-info me-2" style="width:38px;height:38px;object-fit:cover;">
                                <span class="fw-semibold me-1"> <?php echo htmlspecialchars($pub['prenom'].' '.$pub['nom']); ?></span>
                                <small class="text-secondary ms-auto"><?php echo date('d/m/Y', strtotime($pub['date_publication'])); ?></small>
                            </div>
                            <div class="mb-2 overflow-hidden" style="max-height:60px"><span style="word-break:break-word;display:inline-block"> <?php echo mb_strimwidth(nl2br(htmlspecialchars($pub['contenu'])),0,120,'...'); ?> </span></div>
                            <div class="text-end text-muted">
                                <span class="me-3"><i class="fa fa-heart"></i> <?php echo $pub['likes']; ?></span>
                                <span><i class="fa fa-comment"></i> <?php echo $pub['commentaires']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info bg-dark text-light border-0 fst-italic">
                    <i class="fas fa-info-circle"></i> Aucune publication dans cette communauté.
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="/projet/admin/communautes/<?= $communaute->id ?>/edit" class="btn btn-warning btn-lg"><i class="fas fa-edit me-2"></i>Modifier</a>
            <form action="/projet/admin/communautes/<?= $communaute->id ?>/delete" method="POST" class="d-inline">
                <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ?')">
                    <i class="fas fa-trash me-2"></i>Supprimer
                </button>
            </form>
            <a href="/projet/admin/communautes" class="btn btn-secondary btn-lg"><i class="fas fa-arrow-left me-2"></i>Retour à la liste</a>
        </div>
    </div>
</div>