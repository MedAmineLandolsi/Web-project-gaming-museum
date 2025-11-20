<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Liste des communautés</h3>
    <a href="/projet/admin/communautes/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Créer une communauté
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
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
                            <td><?php echo htmlspecialchars($communaute['prenom'] . ' ' . $communaute['nom']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $communaute['visibilite'] == 'publique' ? 'success' : ($communaute['visibilite'] == 'privee' ? 'warning' : 'secondary'); ?>">
                                    <?php echo ucfirst($communaute['visibilite']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($communaute['date_creation'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/projet/admin/communautes/<?php echo $communaute['id']; ?>" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/projet/admin/communautes/<?php echo $communaute['id']; ?>/edit" class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="/projet/admin/communautes/<?php echo $communaute['id']; ?>/delete" method="POST" class="d-inline">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
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