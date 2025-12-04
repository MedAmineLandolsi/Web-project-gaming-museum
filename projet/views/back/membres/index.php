<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Gestion des membres</h3>
    <a href="/projet/admin/membres/create" class="btn btn-primary">
        <i class="fas fa-user-plus me-2"></i>Créer un membre
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filtres -->
        <div class="row mb-4">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Rechercher un membre...">
            </div>
            <div class="col-md-3">
                <select class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                    <option value="suspendu">Suspendu</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control">
                    <option value="">Trier par</option>
                    <option value="recent">Plus récent</option>
                    <option value="ancien">Plus ancien</option>
                    <option value="nom">Nom</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary w-100">
                    <i class="fas fa-filter me-2"></i>Filtrer
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Membre</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Date d'inscription</th>
                        <th>Communautés</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($membres)): ?>
                        <?php foreach ($membres as $membre): ?>
                        <tr>
                            <td><?php echo $membre['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                        <?php echo strtoupper(substr($membre['prenom'], 0, 1) . substr($membre['nom'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']); ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($membre['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $membre['statut'] == 'actif' ? 'success' : 
                                         ($membre['statut'] == 'inactif' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php echo ucfirst($membre['statut']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($membre['date_inscription'])); ?></td>
                            <td>
                                <span class="badge bg-info">0 communautés</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/projet/admin/membres/<?php echo $membre['id']; ?>" class="btn btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/projet/admin/membres/<?php echo $membre['id']; ?>/edit" class="btn btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="/projet/admin/membres/<?php echo $membre['id']; ?>/delete" method="POST" class="d-inline">
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce membre ?')"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-3"></i>
                                <h5>Aucun membre trouvé</h5>
                                <p class="text-muted">Commencez par créer le premier membre</p>
                                <a href="/projet/admin/membres/create" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Créer un membre
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Précédent</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Suivant</a>
                </li>
            </ul>
        </nav>
    </div>
</div>