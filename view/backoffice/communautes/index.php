<div class="d-flex justify-content-between align-items-center mb-5 mt-2 pb-2 border-bottom">
    <h1 class="fs-2 fw-bold mb-0"><i class="fas fa-users me-2"></i>Liste des communautés</h1>
    <a href="/projet/admin/communautes/create" class="btn btn-primary btn-lg d-flex align-items-center gap-2">
        <i class="fas fa-plus"></i> <span>Nouvelle communauté</span>
    </a>
</div>

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
                        <tr>
                            <td><?php echo $communaute['id']; ?></td>
                            <td><?php echo htmlspecialchars($communaute['nom']); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($communaute['categorie']); ?></span>
                            </td>
                            <td>
                                <div class="creator-info">
                                    <span class="creator-name"><?php echo htmlspecialchars(($communaute['prenom'] ?? '') . ' ' . ($communaute['nom'] ?? '')); ?></span>
                                    <div class="creator-actions">
                                        <a href="/projet/admin/communautes/<?php echo $communaute['id']; ?>" class="btn btn-outline-info btn-sm" title="Voir la communauté">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/projet/admin/membres/<?php echo $communaute['createur_id']; ?>" class="btn btn-outline-secondary btn-sm" title="Voir le créateur">
                                            <i class="fas fa-user"></i>
                                        </a>
                                        <?php 
                                        // Récupérer isOwner et isMember depuis les données enrichies
                                        $isOwner = $communaute['isOwner'] ?? false;
                                        $isMember = $communaute['isMember'] ?? false;
                                        ?>
                                        <?php if (!$isOwner && $isMember): ?>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm leave-community-btn"
                                                data-communaute-id="<?php echo $communaute['id']; ?>"
                                                data-communaute-name="<?php echo htmlspecialchars($communaute['nom']); ?>"
                                                title="Quitter cette communauté"
                                            >
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        <?php else: ?>
                                            <button
                                                type="button"
                                                class="btn btn-outline-success btn-sm join-community-btn"
                                                data-communaute-id="<?php echo $communaute['id']; ?>"
                                                data-communaute-name="<?php echo htmlspecialchars($communaute['nom']); ?>"
                                                title="Rejoindre cette communauté"
                                            >
                                                <i class="fas fa-user-plus"></i>
                                            </button>
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
                                    <a href="/projet/admin/communautes/<?php echo $communaute['id']; ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                    <a href="/projet/admin/communautes/<?php echo $communaute['id']; ?>/edit" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <form action="/projet/admin/communautes/<?php echo $communaute['id']; ?>/delete" method="POST" class="d-inline">
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