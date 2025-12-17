<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Liste des publications</h3>
    <a href="<?php echo BASE_URL; ?>/admin/publications/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Créer une publication
    </a>
</div>

<div class="card shadow rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?php echo BASE_URL; ?>/admin/publications" class="d-flex flex-wrap gap-2 align-items-center">
            <div class="fw-semibold">Filtrer par catégorie (communauté) :</div>
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

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Contenu</th>
                        <th>Auteur</th>
                        <th>Communauté</th>
                        <th>Likes</th>
                        <th>Commentaires</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($publications)): ?>
                        <?php foreach ($publications as $publication): ?>
                        <tr>
                            <td><?php echo $publication['id']; ?></td>
                            <td>
                                <?php 
                                $contenu = $publication['contenu'];
                                echo strlen($contenu) > 50 ? htmlspecialchars(substr($contenu, 0, 50)) . '...' : htmlspecialchars($contenu);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($publication['auteur_display_name'] ?? trim(($publication['prenom'] ?? '') . ' ' . ($publication['nom'] ?? ''))); ?></td>
                            <td><?php echo htmlspecialchars($publication['communaute_nom']); ?></td>
                            <td>
                                <span class="badge bg-success"><?php echo $publication['likes']; ?></span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $publication['commentaires']; ?></span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($publication['date_publication'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo BASE_URL; ?>/admin/publications/<?php echo $publication['id']; ?>" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/admin/publications/<?php echo $publication['id']; ?>/edit" class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo BASE_URL; ?>/admin/publications/<?php echo $publication['id']; ?>/delete" method="POST" class="d-inline">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Aucune publication trouvée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>