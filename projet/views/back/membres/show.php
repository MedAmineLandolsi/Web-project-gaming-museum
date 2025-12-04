<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Détails du membre</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 100px; height: 100px;">
                            <span class="text-dark fw-bold fs-3">
                                <?php echo strtoupper(substr($this->membreModel->prenom, 0, 1) . substr($this->membreModel->nom, 0, 1)); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td><?php echo $this->membreModel->id; ?></td>
                            </tr>
                            <tr>
                                <th>Nom complet</th>
                                <td><?php echo htmlspecialchars($this->membreModel->prenom . ' ' . $this->membreModel->nom); ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo htmlspecialchars($this->membreModel->email); ?></td>
                            </tr>
                            <tr>
                                <th>Statut</th>
                                <td>
                                    <span class="badge bg-<?php echo $this->membreModel->statut == 'actif' ? 'success' : ($this->membreModel->statut == 'inactif' ? 'warning' : 'danger'); ?>">
                                        <?php echo ucfirst($this->membreModel->statut); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Date d'inscription</th>
                                <td><?php echo date('d/m/Y à H:i', strtotime($this->membreModel->date_inscription)); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if (!empty($this->membreModel->bio)): ?>
                <div class="mt-4">
                    <h5>Biographie</h5>
                    <div class="bg-light p-3 rounded">
                        <?php echo nl2br(htmlspecialchars($this->membreModel->bio)); ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="/projet/admin/membres/<?php echo $this->membreModel->id; ?>/edit" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form action="/projet/admin/membres/<?php echo $this->membreModel->id; ?>/delete" method="POST" class="d-inline">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce membre ?')">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                    <a href="/projet/admin/membres" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>