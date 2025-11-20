<?php
// Utiliser la variable $communauteModel passée depuis le contrôleur
$communaute = $communauteModel ?? $this->communauteModel;
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Détails de la communauté</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <?php if (!empty($communaute->avatar)): ?>
                            <img src="<?php echo htmlspecialchars($communaute->avatar); ?>" 
                                 alt="<?php echo htmlspecialchars($communaute->nom); ?>" 
                                 class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 100px; height: 100px;">
                                <span class="text-dark fw-bold fs-3">
                                    <?php echo strtoupper(substr($communaute->nom, 0, 2)); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td><?php echo $communaute->id; ?></td>
                            </tr>
                            <tr>
                                <th>Nom</th>
                                <td><?php echo htmlspecialchars($communaute->nom); ?></td>
                            </tr>
                            <tr>
                                <th>Catégorie</th>
                                <td><?php echo htmlspecialchars($communaute->categorie); ?></td>
                            </tr>
                            <tr>
                                <th>Créateur</th>
                                <td><?php echo htmlspecialchars($communaute->createur_nom); ?></td>
                            </tr>
                            <tr>
                                <th>Visibilité</th>
                                <td>
                                    <span class="badge bg-<?php echo $communaute->visibilite == 'publique' ? 'success' : ($communaute->visibilite == 'privee' ? 'warning' : 'secondary'); ?>">
                                        <?php echo ucfirst($communaute->visibilite); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Date création</th>
                                <td><?php echo date('d/m/Y à H:i', strtotime($communaute->date_creation)); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Description</h5>
                    <div class="bg-light p-3 rounded">
                        <?php echo nl2br(htmlspecialchars($communaute->description)); ?>
                    </div>
                </div>

                <?php if (!empty($communaute->regles)): ?>
                <div class="mt-4">
                    <h5>Règles</h5>
                    <div class="bg-light p-3 rounded">
                        <?php echo nl2br(htmlspecialchars($communaute->regles)); ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-5">
                    <h5>Publications (<?php echo count($publications); ?>)</h5>
                    
                    <?php if (!empty($publications)): ?>
                        <div class="mt-3">
                            <?php foreach ($publications as $publication): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <?php if (!empty($publication['avatar'])): ?>
                                            <img src="<?php echo htmlspecialchars($publication['avatar']); ?>" 
                                                 alt="<?php echo htmlspecialchars($publication['prenom'] . ' ' . $publication['nom']); ?>" 
                                                 class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                <span class="text-dark fw-bold">
                                                    <?php echo strtoupper(substr($publication['prenom'], 0, 1) . substr($publication['nom'], 0, 1)); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($publication['prenom'] . ' ' . $publication['nom']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y à H:i', strtotime($publication['date_publication'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($publication['contenu'])); ?></p>
                                    <div class="d-flex gap-3 text-muted">
                                        <small><i class="fas fa-heart"></i> <?php echo $publication['likes']; ?> likes</small>
                                        <small><i class="fas fa-comment"></i> <?php echo $publication['commentaires']; ?> commentaires</small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Aucune publication dans cette communauté.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <a href="/projet/admin/communautes/<?php echo $communaute->id; ?>/edit" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form action="/projet/admin/communautes/<?php echo $communaute->id; ?>/delete" method="POST" class="d-inline">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ?')">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                    <a href="/projet/admin/communautes" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>