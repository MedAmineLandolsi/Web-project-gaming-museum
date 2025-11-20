<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Détails de la publication</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td><?php echo $this->publicationModel->id; ?></td>
                            </tr>
                            <tr>
                                <th>Auteur</th>
                                <td><?php echo htmlspecialchars($this->publicationModel->auteur_nom); ?></td>
                            </tr>
                            <tr>
                                <th>Communauté</th>
                                <td><?php echo htmlspecialchars($this->publicationModel->communaute_nom); ?></td>
                            </tr>
                            <tr>
                                <th>Likes</th>
                                <td>
                                    <span class="badge bg-success"><?php echo $this->publicationModel->likes; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Commentaires</th>
                                <td>
                                    <span class="badge bg-info"><?php echo $this->publicationModel->commentaires; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Date publication</th>
                                <td><?php echo date('d/m/Y à H:i', strtotime($this->publicationModel->date_publication)); ?></td>
                            </tr>
                            <tr>
                                <th>Dernière modification</th>
                                <td><?php echo date('d/m/Y à H:i', strtotime($this->publicationModel->date_modification)); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Contenu</h5>
                    <div class="bg-light p-3 rounded">
                        <?php echo nl2br(htmlspecialchars($this->publicationModel->contenu)); ?>
                    </div>
                </div>

                <?php if (!empty($this->publicationModel->images)): ?>
                <div class="mt-4">
                    <h5>Images</h5>
                    <div class="bg-light p-3 rounded">
                        <pre><?php echo $this->publicationModel->images; ?></pre>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="/projet/admin/publications/<?php echo $this->publicationModel->id; ?>/edit" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form action="/projet/admin/publications/<?php echo $this->publicationModel->id; ?>/delete" method="POST" class="d-inline">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                    <a href="/projet/admin/publications" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>