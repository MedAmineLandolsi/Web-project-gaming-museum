<?php
// Utiliser la variable $publicationModel passée depuis le contrôleur
$publication = $publicationModel ?? $this->publicationModel;
?>

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
                                <td><?php echo $publication->id; ?></td>
                            </tr>
                            <tr>
                                <th>Auteur</th>
                                <td><?php echo htmlspecialchars($publication->auteur_nom); ?></td>
                            </tr>
                            <tr>
                                <th>Communauté</th>
                                <td><?php echo htmlspecialchars($publication->communaute_nom); ?></td>
                            </tr>
                            <tr>
                                <th>Likes</th>
                                <td>
                                    <span class="badge bg-success"><?php echo $publication->likes; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Commentaires</th>
                                <td>
                                    <span class="badge bg-info"><?php echo $publication->commentaires; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Date publication</th>
                                <td><?php echo date('d/m/Y à H:i', strtotime($publication->date_publication)); ?></td>
                            </tr>
                            <tr>
                                <th>Dernière modification</th>
                                <td><?php echo date('d/m/Y à H:i', strtotime($publication->date_modification)); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Contenu</h5>
                    <div class="bg-light p-3 rounded">
                        <?php echo nl2br(htmlspecialchars($publication->contenu)); ?>
                    </div>
                </div>

                <?php if (!empty($publication->images)): ?>
                <div class="mt-4">
                    <h5>Images</h5>
                    <div class="bg-light p-3 rounded">
                        <?php 
                        // Vérifier si images est un tableau ou une chaîne
                        if (is_array($publication->images)) {
                            // Si c'est un tableau, afficher chaque image
                            foreach ($publication->images as $image) {
                                if (!empty($image)) {
                                    echo '<div class="mb-3">';
                                    echo '<img src="' . htmlspecialchars($image) . '" class="img-fluid rounded" style="max-height: 300px;">';
                                    echo '</div>';
                                }
                            }
                        } else {
                            // Si c'est une chaîne, essayer de la parser
                            $imagesArray = explode(',', $publication->images);
                            foreach ($imagesArray as $image) {
                                $image = trim($image);
                                if (!empty($image)) {
                                    echo '<div class="mb-3">';
                                    echo '<img src="' . htmlspecialchars($image) . '" class="img-fluid rounded" style="max-height: 300px;">';
                                    echo '</div>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="/projet/admin/publications/<?php echo $publication->id; ?>/edit" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form action="/projet/admin/publications/<?php echo $publication->id; ?>/delete" method="POST" class="d-inline">
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