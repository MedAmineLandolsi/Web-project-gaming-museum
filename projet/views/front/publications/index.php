<?php
$communaute_info = $communaute_info ?? null;
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="text-gradient">
                    <?php if ($communaute_info): ?>
                        <i class="fas fa-users me-2"></i>Publications - <?php echo htmlspecialchars($communaute_info->nom); ?>
                        <small class="text-muted d-block mt-1">Communauté <?php echo htmlspecialchars($communaute_info->categorie); ?></small>
                    <?php else: ?>
                        <i class="fas fa-newspaper me-2"></i>Publications récentes
                    <?php endif; ?>
                </h1>
                <div class="mt-3 d-flex align-items-center gap-2">
                    <label for="creatorSelectHeader" class="mb-0 text-muted">Filtrer par auteur :</label>
                    <select id="creatorSelectHeader" class="form-select form-select-sm" style="width:260px;">
                        <option value="">Tous les membres</option>
                        <?php if (!empty($membres)): ?>
                            <?php foreach ($membres as $m): ?>
                                <option value="<?php echo htmlspecialchars($m['id']); ?>"><?php echo htmlspecialchars($m['prenom'] . ' ' . $m['nom']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <!-- Clear button removed per request -->
                </div>
                <style>
                    /* Styles sombres pour les contrôles de filtrage */
                    #creatorSelectHeader, #creatorSelect, #creatorFilter {
                        background-color: #0f1113 !important;
                        color: #e6f7ee !important;
                        border: 1px solid rgba(46, 204, 64, 0.15) !important;
                        box-shadow: none !important;
                    }
                    #creatorSelectHeader option, #creatorSelect option {
                        background-color: #0f1113;
                        color: #e6f7ee;
                    }
                    #clearCreatorSelectHeader, #clearCreatorFilter {
                        background-color: transparent;
                        color: #e6f7ee;
                        border-color: rgba(255,255,255,0.06);
                    }
                    /* Slight spacing fix to match the dark header */
                    .text-gradient + .mt-3 { margin-top: 0.25rem; }
                </style>
            </div>
            <a href="/projet/publications/create<?php echo $communaute_info ? '?communaute_id=' . $communaute_info->id : ''; ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle publication
            </a>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $_SESSION['success_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if ($communaute_info): ?>
<div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-filter me-2"></i>
    Affichage des publications de la communauté <strong><?php echo htmlspecialchars($communaute_info->nom); ?></strong>
    <a href="/projet/publications" class="btn btn-sm btn-outline-info ms-3">
        <i class="fas fa-times me-1"></i>Voir toutes les publications
    </a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- STRUCTURE CENTRÉE DES PUBLICATIONS -->
<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="publication-grid">
            <?php if (!empty($publications)): ?>
                <?php foreach ($publications as $publication): ?>
                <div class="publication-card-enhanced" data-auteur-id="<?php echo htmlspecialchars($publication['auteur_id']); ?>">
                    <!-- En-tête publication -->
                    <div class="publication-header">
                        <div class="avatar">
                            <?php echo strtoupper(substr($publication['prenom'], 0, 1) . substr($publication['nom'], 0, 1)); ?>
                        </div>
                        <div class="publication-author">
                            <div class="publication-author-name">
                                <?php echo htmlspecialchars($publication['prenom'] . ' ' . $publication['nom']); ?>
                                <?php if (!$communaute_info): ?>
                                    <small class="text-muted">dans</small>
                                    <strong class="text-primary"><?php echo htmlspecialchars($publication['communaute_nom']); ?></strong>
                                <?php endif; ?>
                            </div>
                            <div class="publication-date">
                                <?php echo date('d/m/Y à H:i', strtotime($publication['date_publication'])); ?>
                                <?php if ($publication['date_modification'] && $publication['date_modification'] != $publication['date_publication']): ?>
                                    • <em>modifié le <?php echo date('d/m/Y à H:i', strtotime($publication['date_modification'])); ?></em>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Contenu -->
                    <div class="publication-content">
                        <?php echo nl2br(htmlspecialchars($publication['contenu'])); ?>
                    </div>

                    <!-- Images -->
                    <?php if (!empty($publication['images']) && is_array($publication['images'])): ?>
                    <div class="publication-images-grid">
                        <?php foreach ($publication['images'] as $image): ?>
                            <?php if (!empty($image)): ?>
                            <div class="publication-image">
                                <img src="<?php echo htmlspecialchars($image); ?>" 
                                     alt="Image publication" 
                                     onerror="this.style.display='none'">
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Actions -->
                    <div class="publication-actions-enhanced">
                        <div class="publication-stats-enhanced">
                            <div class="publication-stat">
                                <i class="fas fa-heart"></i>
                                <span><?php echo $publication['likes']; ?> likes</span>
                            </div>
                            <div class="publication-stat">
                                <i class="fas fa-comment"></i>
                                <span><?php echo $publication['commentaires']; ?> commentaires</span>
                            </div>
                        </div>
                        <div class="publication-action-buttons">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-comment"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-share"></i>
                            </button>
                        </div>
                    </div>

                    <!-- BOUTONS MODIFIER/SUPPRIMER POUR L'AUTEUR - À DROITE -->
                    <?php
                    $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $publication['auteur_id'];
                    ?>
                    
                    <?php if ($isOwner): ?>
                    <div class="publication-owner-actions">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-user-cog me-1"></i>Gérer votre publication</h6>
                            <div class="publication-owner-buttons">
                                <!-- BOUTON MODIFIER -->
                                <a href="/projet/publications/edit/<?php echo $publication['id']; ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>Modifier
                                </a>
                                
                                <!-- BOUTON SUPPRIMER -->
                                <button type="button" 
                                        class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal<?php echo $publication['id']; ?>">
                                    <i class="fas fa-trash me-1"></i>Supprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de confirmation de suppression -->
                    <div class="modal fade" id="deleteModal<?php echo $publication['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Supprimer la publication
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Êtes-vous sûr de vouloir supprimer cette publication ?</p>
                                    <p class="text-muted"><small>Cette action est irréversible et supprimera définitivement votre publication.</small></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-1"></i>Annuler
                                    </button>
                                    <form action="/projet/publications/<?php echo $publication['id']; ?>/delete" method="POST" class="d-inline">
                                        <input type="hidden" name="communaute_id" value="<?php echo $publication['communaute_id']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash me-1"></i>Supprimer définitivement
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state text-center">
                    <i class="fas fa-newspaper fa-3x"></i>
                    <h4>Aucune publication</h4>
                    <p class="mb-4">
                        <?php if ($communaute_info): ?>
                            Aucune publication dans cette communauté pour le moment.
                        <?php else: ?>
                            Soyez le premier à partager une publication !
                        <?php endif; ?>
                    </p>
                    <a href="/projet/publications/create<?php echo $communaute_info ? '?communaute_id=' . $communaute_info->id : ''; ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        <?php if ($communaute_info): ?>
                            Créer la première publication
                        <?php else: ?>
                            Créer la première publication
                        <?php endif; ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.publication-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.publication-card-enhanced {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.publication-card-enhanced:hover {
    transform: translateY(-3px);
    border-color: var(--primary-green);
    box-shadow: 0 10px 30px rgba(0, 255, 136, 0.15);
}

.publication-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: var(--dark-bg);
    font-size: 1rem;
}

.publication-author {
    flex: 1;
}

.publication-author-name {
    font-weight: 600;
    color: var(--text-white);
    margin-bottom: 0.2rem;
}

.publication-author-name small {
    font-weight: normal;
    opacity: 0.7;
}

.publication-author-name strong {
    color: var(--primary-green);
}

.publication-date {
    font-size: 0.85rem;
    color: var(--text-gray);
}

.publication-content {
    line-height: 1.6;
    margin-bottom: 1rem;
    color: var(--text-light-gray);
}

.publication-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 0.5rem;
    margin: 1rem 0;
}

.publication-image {
    border-radius: 8px;
    overflow: hidden;
    aspect-ratio: 1;
}

.publication-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.publication-image:hover img {
    transform: scale(1.05);
}

.publication-actions-enhanced {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.publication-stats-enhanced {
    display: flex;
    gap: 1.5rem;
}

.publication-stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-gray);
    font-size: 0.9rem;
}

.publication-action-buttons {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

/* Styles pour les boutons de modification/suppression - À DROITE */
.publication-owner-actions {
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.3);
    border-radius: 10px;
    padding: 1rem;
    margin: 1rem 0 0 0;
}

.publication-owner-buttons {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.publication-owner-actions .btn {
    border-radius: 6px;
    font-weight: 600;
    padding: 0.5rem 1rem;
}

.publication-owner-actions .btn-warning {
    background: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.publication-owner-actions .btn-warning:hover {
    background: #e0a800;
    border-color: #e0a800;
    color: #000;
    transform: translateY(-1px);
}

.publication-owner-actions .btn-danger {
    background: #dc3545;
    border-color: #dc3545;
    color: #fff;
}

.publication-owner-actions .btn-danger:hover {
    background: #c82333;
    border-color: #c82333;
    color: #fff;
    transform: translateY(-1px);
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--text-gray);
    background: var(--card-bg);
    border-radius: 15px;
    border: 1px solid var(--border-color);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h4 {
    color: var(--text-white);
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .publication-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .publication-actions-enhanced {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .publication-images-grid {
        grid-template-columns: 1fr;
    }
    
    .publication-owner-actions .d-flex {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .publication-owner-buttons {
        justify-content: center;
        width: 100%;
    }
}
</style>