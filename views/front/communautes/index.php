<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-gradient">Nos Communautés</h1>
            <a href="/projet/communautes/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Créer une communauté
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
<?php endif; ?>

<div class="row">
    <?php if (!empty($communautes)): ?>
        <?php foreach ($communautes as $communaute): 
            $membres_count = rand(50, 200);
            $publications_count = rand(10, 50);
            $is_my_community = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $communaute['createur_id'];
        ?>
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="community-card p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <?php if (!empty($communaute['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($communaute['avatar']); ?>" 
                         alt="<?php echo htmlspecialchars($communaute['nom']); ?>" 
                         class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                    <?php else: ?>
                    <div class="avatar me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <?php echo strtoupper(substr($communaute['nom'], 0, 2)); ?>
                    </div>
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <h5 class="mb-1"><?php echo htmlspecialchars($communaute['nom']); ?></h5>
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>
                            Créée par <?php echo htmlspecialchars($communaute['prenom'] . ' ' . $communaute['nom']); ?>
                        </small>
                    </div>
                </div>
                
                <p class="text-muted mb-3">
                    <?php 
                    $description = $communaute['description'];
                    echo strlen($description) > 100 ? htmlspecialchars(substr($description, 0, 100)) . '...' : htmlspecialchars($description);
                    ?>
                </p>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-primary"><?php echo htmlspecialchars($communaute['categorie']); ?></span>
                    <span class="badge bg-<?php echo $communaute['visibilite'] == 'publique' ? 'success' : 'warning'; ?>">
                        <i class="fas fa-<?php echo $communaute['visibilite'] == 'publique' ? 'globe' : 'lock'; ?> me-1"></i>
                        <?php echo ucfirst($communaute['visibilite']); ?>
                    </span>
                </div>
                
                <div class="community-stats d-flex justify-content-between text-center mb-3">
                    <div>
                        <small class="text-muted">Membres</small>
                        <div class="fw-bold"><?php echo $membres_count; ?></div>
                    </div>
                    <div>
                        <small class="text-muted">Publications</small>
                        <div class="fw-bold"><?php echo $publications_count; ?></div>
                    </div>
                    <div>
                        <small class="text-muted">En ligne</small>
                        <div class="fw-bold"><?php echo rand(5, 25); ?></div>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mt-3">
                    <!-- Bouton Show pour toutes les communautés -->
                    <a href="/projet/communautes/<?php echo $communaute['id']; ?>" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-eye me-1"></i>Voir les publications
                    </a>
                    
                    <!-- Boutons supplémentaires pour mes communautés -->
                    <?php if ($is_my_community): ?>
                    <div class="btn-group">
                        <a href="/projet/admin/communautes/<?php echo $communaute['id']; ?>/edit" class="btn btn-warning btn-sm" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="/projet/admin/communautes/<?php echo $communaute['id']; ?>/delete" method="POST" class="d-inline">
                            <button type="submit" class="btn btn-danger btn-sm" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ?')"
                                    title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($is_my_community): ?>
                <div class="mt-2">
                    <small class="text-warning">
                        <i class="fas fa-crown me-1"></i>Ma communauté
                    </small>
                </div>
                <?php endif; ?>
                
                <div class="mt-3 pt-3 border-top border-secondary">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Créée le <?php echo date('d/m/Y', strtotime($communaute['date_creation'])); ?>
                    </small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="community-card p-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h3>Aucune communauté trouvée</h3>
                <p class="text-muted mb-4">Soyez le premier à créer une communauté !</p>
                <a href="/projet/communautes/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Créer la première communauté
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.community-stats {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 10px;
}

.community-stats > div {
    flex: 1;
}

.community-stats > div:not(:last-child) {
    border-right: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}
</style>