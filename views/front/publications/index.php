<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-gradient">Publications récentes</h1>
            <a href="/projet/publications/create" class="btn btn-primary">
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
<?php endif; ?>

<div class="publication-grid">
    <?php if (!empty($publications)): ?>
        <?php foreach ($publications as $publication): ?>
        <div class="publication-card-enhanced">
            <!-- En-tête publication -->
            <div class="publication-header">
                <div class="avatar">
                    <?php echo strtoupper(substr($publication['prenom'], 0, 1) . substr($publication['nom'], 0, 1)); ?>
                </div>
                <div class="publication-author">
                    <div class="publication-author-name">
                        <?php echo htmlspecialchars($publication['prenom'] . ' ' . $publication['nom']); ?>
                        <small class="text-muted">dans</small>
                        <strong class="text-primary"><?php echo htmlspecialchars($publication['communaute_nom']); ?></strong>
                    </div>
                    <div class="publication-date">
                        <?php echo date('d/m/Y à H:i', strtotime($publication['date_publication'])); ?>
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
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-newspaper fa-3x"></i>
            <h4>Aucune publication</h4>
            <p class="mb-4">Soyez le premier à partager une publication !</p>
            <a href="/projet/publications/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Créer la première publication
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.publication-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.publication-card-enhanced {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 15px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.publication-card-enhanced:hover {
    transform: translateY(-3px);
    border-color: var(--primary);
    box-shadow: 0 10px 30px rgba(0, 255, 136, 0.15);
}

.publication-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.publication-author {
    flex: 1;
}

.publication-author-name {
    font-weight: 600;
    color: var(--light);
    margin-bottom: 0.2rem;
}

.publication-author-name small {
    font-weight: normal;
    opacity: 0.7;
}

.publication-author-name strong {
    color: var(--primary);
}

.publication-date {
    font-size: 0.85rem;
    color: var(--gray);
}

.publication-content {
    line-height: 1.6;
    margin-bottom: 1rem;
    color: var(--light);
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
    border-top: 1px solid var(--border);
}

.publication-stats-enhanced {
    display: flex;
    gap: 1.5rem;
}

.publication-stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray);
    font-size: 0.9rem;
}

.publication-action-buttons {
    display: flex;
    gap: 0.5rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--gray);
    background: var(--card-bg);
    border-radius: 15px;
    border: 1px solid var(--border);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h4 {
    color: var(--light);
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
}
</style>