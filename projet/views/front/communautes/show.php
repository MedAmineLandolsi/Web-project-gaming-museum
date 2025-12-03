<?php
// views/front/communautes/show.php
// S'assurer que $publications est définie
if (!isset($publications)) {
    $publications = [];
}
?>
<div class="communaute-detail-container">
    <!-- Contenu principal -->
    <div class="main-content-section">
        <!-- Header de la communauté -->
        <div class="communaute-header mb-5">
            <div class="communaute-info-grid">
                <?php if (!empty($this->communauteModel->avatar)): ?>
                <img src="<?php echo htmlspecialchars($this->communauteModel->avatar); ?>" 
                     alt="<?php echo htmlspecialchars($this->communauteModel->nom); ?>" 
                     class="communaute-avatar-large-img">
                <?php else: ?>
                <div class="communaute-avatar-large">
                    <?php echo strtoupper(substr($this->communauteModel->nom, 0, 2)); ?>
                </div>
                <?php endif; ?>
                <div class="communaute-title-section">
                    <h1><?php echo htmlspecialchars($this->communauteModel->nom); ?></h1>
                    <div class="communaute-meta">
                        <span class="badge bg-primary">
                            <i class="fas fa-tag me-1"></i>
                            <?php echo htmlspecialchars($this->communauteModel->categorie); ?>
                        </span>
                        <span class="badge bg-<?php echo $this->communauteModel->visibilite == 'publique' ? 'success' : 'warning'; ?>">
                            <i class="fas fa-<?php echo $this->communauteModel->visibilite == 'publique' ? 'globe' : 'lock'; ?> me-1"></i>
                            <?php echo ucfirst($this->communauteModel->visibilite); ?>
                        </span>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $this->communauteModel->createur_id): ?>
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-crown me-1"></i>Vous êtes le créateur
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="communaute-creator-info mt-2">
                        <p class="text-muted mb-2">
                            <i class="fas fa-user me-1"></i>
                            <strong>Créée par:</strong> 
                            <a href="/projet/membres/<?php echo $this->communauteModel->createur_id; ?>" class="text-decoration-none text-primary">
                                <?php echo htmlspecialchars($this->communauteModel->createur_nom); ?>
                            </a>
                        </p>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar me-1"></i>
                            <strong>Date de création:</strong> 
                            <?php echo date('d/m/Y à H:i', strtotime($this->communauteModel->date_creation)); ?>
                            <small class="text-muted">
                                (<?php 
                                $days = floor((time() - strtotime($this->communauteModel->date_creation)) / (60 * 60 * 24));
                                if ($days == 0) echo "Aujourd'hui";
                                elseif ($days == 1) echo "Il y a 1 jour";
                                elseif ($days < 30) echo "Il y a $days jours";
                                elseif ($days < 365) echo "Il y a " . floor($days / 30) . " mois";
                                else echo "Il y a " . floor($days / 365) . " ans";
                                ?>)
                            </small>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Statistiques rapides -->
            <div class="communaute-stats-grid mb-5">
                <div class="stat-card">
                    <span class="stat-number"><?php echo count($publications); ?></span>
                    <span class="stat-label">Publications</span>
                    <small class="text-muted d-block mt-1">
                        <?php 
                        $totalLikes = !empty($publications) ? array_sum(array_column($publications, 'likes')) : 0;
                        echo $totalLikes > 0 ? $totalLikes . ' likes au total' : 'Aucun like';
                        ?>
                    </small>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo rand(50, 200); ?></span>
                    <span class="stat-label">Membres</span>
                    <small class="text-muted d-block mt-1">
                        <?php echo rand(10, 30); ?> actifs cette semaine
                    </small>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo rand(5, 25); ?></span>
                    <span class="stat-label">En ligne</span>
                    <small class="text-muted d-block mt-1">
                        En ce moment
                    </small>
                </div>
            </div>
        </div>

        <!-- Informations complémentaires -->
        <div class="communaute-info-cards mb-5">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="info-card">
                        <h6><i class="fas fa-info-circle me-2 text-primary"></i>Description</h6>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($this->communauteModel->description)); ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <h6><i class="fas fa-chart-line me-2 text-success"></i>Activité</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-newspaper me-2"></i><?php echo count($publications); ?> publication(s)</li>
                            <li><i class="fas fa-heart me-2"></i><?php echo !empty($publications) ? array_sum(array_column($publications, 'likes')) : 0; ?> like(s) total</li>
                            <li><i class="fas fa-comment me-2"></i><?php echo !empty($publications) ? array_sum(array_column($publications, 'commentaires')) : 0; ?> commentaire(s)</li>
                            <?php if (!empty($publications)): 
                                $lastPublication = $publications[0];
                            ?>
                            <li><i class="fas fa-clock me-2"></i>Dernière publication: <?php echo date('d/m/Y', strtotime($lastPublication['date_publication'])); ?></li>
                            <?php else: ?>
                            <li><i class="fas fa-info-circle me-2"></i>Aucune publication pour le moment</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($this->communauteModel->regles)): ?>
        <div class="regles-box mb-5">
            <h5><i class="fas fa-gavel me-2"></i>Règles de la communauté</h5>
            <div class="regles-content">
                <?php echo nl2br(htmlspecialchars($this->communauteModel->regles)); ?>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Cette communauté n'a pas encore de règles définies.
        </div>
        <?php endif; ?>

        <!-- Section Publications AVEC BONNE STRUCTURE -->
        <div class="publications-section mb-5">
            <div class="section-header mb-4">
                <h3 class="fs-4 fw-bold mb-0"><i class="fas fa-newspaper me-2"></i>Publications (<?php echo count($publications); ?>)</h3>
                <div class="d-flex gap-2 align-items-center">
                    <select class="form-select form-select-sm" id="publicationSort" onchange="updatePublicationSort()">
                        <option value="date_publication_desc" <?php echo ($_GET['order_by'] ?? 'date_publication') == 'date_publication' && ($_GET['order_dir'] ?? 'DESC') == 'DESC' ? 'selected' : ''; ?>>Plus récentes</option>
                        <option value="date_publication_asc" <?php echo ($_GET['order_by'] ?? '') == 'date_publication' && ($_GET['order_dir'] ?? '') == 'ASC' ? 'selected' : ''; ?>>Plus anciennes</option>
                        <option value="likes_desc" <?php echo ($_GET['order_by'] ?? '') == 'likes' && ($_GET['order_dir'] ?? '') == 'DESC' ? 'selected' : ''; ?>>Plus populaires</option>
                        <option value="commentaires_desc" <?php echo ($_GET['order_by'] ?? '') == 'commentaires' && ($_GET['order_dir'] ?? '') == 'DESC' ? 'selected' : ''; ?>>Plus commentées</option>
                    </select>
                    <a href="/projet/publications/create?communaute_id=<?php echo $this->communauteModel->id; ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvelle publication
                    </a>
                </div>
            </div>

            <!-- GRID DES PUBLICATIONS COMME LA PAGE PRINCIPALE -->
            <?php if (!empty($publications)): ?>
                <div class="publication-grid mb-5">
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
                                <?php if (!empty($image) && is_string($image)): ?>
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
                            <div class="publication-action-buttons d-flex gap-3 flex-wrap">
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

                        <!-- BOUTONS MODIFIER/SUPPRIMER POUR L'AUTEUR -->
                        <?php
                        $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $publication['auteur_id'];
                        ?>
                        
                        <?php if ($isOwner): ?>
                        <div class="publication-owner-actions">
                            <h6 class="fs-5 fw-bold mb-2"><i class="fas fa-user-cog me-2"></i>Gérer votre publication</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <!-- BOUTON MODIFIER AVEC LA BONNE ROUTE -->
                                <a href="/projet/publications/edit/<?php echo $publication['id']; ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>Modifier
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal<?php echo $publication['id']; ?>">
                                    <i class="fas fa-trash me-1"></i>Supprimer
                                </button>
                            </div>
                        </div>

                        <!-- Modal de confirmation de suppression -->
                        <div class="modal fade" id="deleteModal<?php echo $publication['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger">Supprimer la publication</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Êtes-vous sûr de vouloir supprimer cette publication ?</p>
                                        <p class="text-muted">Cette action est irréversible.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <form action="/projet/publications/<?php echo $publication['id']; ?>/delete" method="POST" class="d-inline">
                                            <input type="hidden" name="communaute_id" value="<?php echo $publication['communaute_id']; ?>">
                                            <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-newspaper fa-3x"></i>
                    <h4>Aucune publication</h4>
                    <p class="mb-4">Soyez le premier à partager dans cette communauté !</p>
                    <a href="/projet/publications/create?communaute_id=<?php echo $this->communauteModel->id; ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer la première publication
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="communaute-sidebar">
        <!-- Bouton rejoindre -->
        <div class="sidebar-section">
            <h5><i class="fas fa-user-plus me-2"></i>Rejoindre la communauté</h5>
            <p class="text-muted mb-3">Participez aux discussions et partagez vos idées.</p>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <button class="btn btn-primary w-100 join-community"
                        data-communaute-id="<?php echo $this->communauteModel->id; ?>"
                        data-communaute-name="<?php echo htmlspecialchars($this->communauteModel->nom); ?>">
                    <i class="fas fa-user-plus me-2"></i>Rejoindre
                </button>

            <?php elseif (isset($isOwner) && $isOwner): ?>
                <div class="alert alert-warning text-center mb-0">
                    <i class="fas fa-crown me-2"></i>Vous êtes le créateur de cette communauté
                </div>

            <?php elseif (isset($isMember) && $isMember): ?>
                <button type="button" class="btn btn-danger w-100 leave-community"
                        data-communaute-id="<?php echo $this->communauteModel->id; ?>"
                        data-communaute-name="<?php echo htmlspecialchars($this->communauteModel->nom); ?>">
                    <i class="fas fa-user-minus me-2"></i>Quitter
                </button>

            <?php else: ?>
                <button class="btn btn-primary w-100 join-community"
                        data-communaute-id="<?php echo $this->communauteModel->id; ?>"
                        data-communaute-name="<?php echo htmlspecialchars($this->communauteModel->nom); ?>">
                    <i class="fas fa-user-plus me-2"></i>Rejoindre
                </button>
            <?php endif; ?>
        </div>

        <!-- Membres actifs -->
        <div class="sidebar-section">
            <h5><i class="fas fa-users me-2"></i>Membres actifs</h5>
            <div class="membres-list">
                <div class="membre-item">
                    <div class="membre-avatar">
                        <?php echo strtoupper(substr($this->communauteModel->createur_nom, 0, 2)); ?>
                    </div>
                    <div class="membre-info">
                        <div class="membre-name"><?php echo htmlspecialchars($this->communauteModel->createur_nom); ?></div>
                        <div class="membre-role text-success">
                            <i class="fas fa-crown me-1"></i>Créateur
                        </div>
                    </div>
                </div>
                <?php 
                $nomsMembres = ['Alice Martin', 'Bob Dupont', 'Claire Bernard'];
                for ($i = 0; $i < 3; $i++): 
                ?>
                <div class="membre-item">
                    <div class="membre-avatar">
                        <?php echo strtoupper(substr($nomsMembres[$i], 0, 1) . substr(explode(' ', $nomsMembres[$i])[1], 0, 1)); ?>
                    </div>
                    <div class="membre-info">
                        <div class="membre-name"><?php echo $nomsMembres[$i]; ?></div>
                        <div class="membre-role text-primary">
                            <i class="fas fa-star me-1"></i>Membre actif
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-users me-1"></i>
                    Et <?php echo rand(10, 50); ?> autres membres...
                </small>
            </div>
        </div>

        <!-- Actions du créateur -->
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $this->communauteModel->createur_id): ?>
        <div class="sidebar-section">
            <h5><i class="fas fa-cog me-2"></i>Gestion</h5>
            <div class="admin-actions d-flex flex-column gap-3">
                <a href="/projet/admin/communautes/<?php echo $this->communauteModel->id; ?>/edit" class="btn btn-warning btn-sm w-100 mb-2">
                    <i class="fas fa-edit me-1"></i>Modifier la communauté
                </a>
                <form action="/projet/admin/communautes/<?php echo $this->communauteModel->id; ?>/delete" method="POST" class="w-100">
                    <button type="submit" class="btn btn-danger btn-sm w-100" 
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ? Toutes les publications seront perdues.')">
                        <i class="fas fa-trash me-1"></i>Supprimer la communauté
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


<style>
/* Structure principale */
.communaute-detail-container {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
}

.main-content-section {
    background: linear-gradient(135deg, rgba(30, 30, 40, 0.95), rgba(20, 20, 30, 0.95));
    border-radius: 20px;
    padding: 2.5rem 2rem;
    border: 1px solid rgba(0, 255, 136, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.main-content-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-green), var(--secondary-purple));
    border-radius: 20px 20px 0 0;
}

/* Header de la communauté */
.communaute-header {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid rgba(0, 255, 136, 0.2);
    position: relative;
}

.communaute-header::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100px;
    height: 2px;
    background: linear-gradient(90deg, var(--primary-green), transparent);
}

.communaute-info-grid {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 1.5rem;
    align-items: start;
    margin-bottom: 1.5rem;
}

.communaute-avatar-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    color: var(--dark-bg);
    border: 4px solid var(--primary-green);
    flex-shrink: 0;
    box-shadow: 0 0 20px rgba(0, 255, 136, 0.4);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
}

.communaute-avatar-large:hover {
    transform: scale(1.05);
    box-shadow: 0 0 30px rgba(0, 255, 136, 0.6);
}

.communaute-avatar-large::after {
    content: '';
    position: absolute;
    inset: -6px;
    border-radius: 50%;
    border: 2px solid transparent;
    border-top-color: var(--primary-green);
    border-right-color: var(--secondary-purple);
    animation: rotate 3s linear infinite;
    z-index: -1;
}

@keyframes rotate {
    to { transform: rotate(360deg); }
}

.communaute-avatar-large-img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--primary-green);
    flex-shrink: 0;
    box-shadow: 0 0 20px rgba(0, 255, 136, 0.4);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.communaute-avatar-large-img:hover {
    transform: scale(1.05);
    box-shadow: 0 0 30px rgba(0, 255, 136, 0.6);
}

.communaute-title-section h1 {
    color: var(--text-white);
    margin-bottom: 0.75rem;
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--text-white), var(--primary-green));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 2px 10px rgba(0, 255, 136, 0.3);
}

.communaute-meta {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
}

.communaute-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-top: 1rem;
}

.stat-card {
    background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(189, 0, 255, 0.1));
    border: 1px solid rgba(0, 255, 136, 0.3);
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.5s ease;
}

.stat-card:hover::before {
    left: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-green);
    box-shadow: 0 10px 30px rgba(0, 255, 136, 0.3);
}

.stat-number {
    display: block;
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--primary-green);
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.8rem;
    color: var(--text-gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card small {
    font-size: 0.7rem;
    color: var(--text-gray);
    display: block;
    margin-top: 0.25rem;
}

/* Informations complémentaires */
.communaute-info-cards {
    margin-bottom: 1.5rem;
}

.info-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
    border: 1px solid rgba(0, 255, 136, 0.2);
    border-radius: 15px;
    padding: 2rem;
    height: 100%;
    transition: all 0.3s ease;
    position: relative;
}

.info-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--primary-green), var(--secondary-purple));
    border-radius: 15px 0 0 15px;
}

.info-card:hover {
    transform: translateX(5px);
    border-color: var(--primary-green);
    box-shadow: 0 5px 20px rgba(0, 255, 136, 0.2);
}

.info-card h6 {
    color: var(--primary-green);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    font-weight: 600;
}

.info-card ul li {
    padding: 0.5rem 0;
    color: var(--text-light-gray);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.info-card ul li:last-child {
    border-bottom: none;
}

.communaute-creator-info {
    background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 255, 136, 0.05));
    padding: 1.25rem;
    border-radius: 12px;
    border-left: 4px solid var(--primary-green);
    box-shadow: 0 2px 10px rgba(0, 255, 136, 0.1);
    transition: all 0.3s ease;
}

.communaute-creator-info:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0, 255, 136, 0.2);
}

.communaute-creator-info a {
    transition: color 0.3s ease;
}

.communaute-creator-info a:hover {
    color: var(--primary-green) !important;
    text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
}

/* Description et règles */
.description-box, .regles-box {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
    border: 1px solid rgba(0, 255, 136, 0.2);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.description-box::before, .regles-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--primary-green), var(--secondary-purple));
}

.description-box h5, .regles-box h5 {
    color: var(--primary-green);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.regles-content {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
    padding: 1.5rem;
    border-radius: 10px;
    border-left: 4px solid #ffc107;
    white-space: pre-line;
    line-height: 1.8;
    color: var(--text-light-gray);
    box-shadow: inset 0 2px 10px rgba(255, 193, 7, 0.1);
}

/* Section Publications */
.publications-section {
    margin-top: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(189, 0, 255, 0.1));
    border-radius: 15px;
    border: 1px solid rgba(0, 255, 136, 0.2);
    position: relative;
}

.section-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--primary-green), var(--secondary-purple));
    border-radius: 15px 0 0 15px;
}

.section-header .btn-primary {
    background: linear-gradient(135deg, var(--primary-green), #00cc88);
    border: none;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 255, 136, 0.3);
}

.section-header .btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 255, 136, 0.5);
}

.section-header .form-select {
    background: rgba(30, 30, 40, 0.8);
    border: 1px solid rgba(0, 255, 136, 0.3);
    color: var(--text-white);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.section-header .form-select:focus {
    border-color: var(--primary-green);
    box-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
    outline: none;
}

.section-header h3 {
    color: var(--text-white);
    margin: 0;
    display: flex;
    align-items: center;
    font-size: 1.5rem;
    font-weight: 600;
    background: linear-gradient(135deg, var(--text-white), var(--primary-green));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* GRID DES PUBLICATIONS COMME LA PAGE PRINCIPALE */
.publication-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.publication-card-enhanced {
    background: linear-gradient(135deg, rgba(30, 30, 40, 0.95), rgba(20, 20, 30, 0.95));
    border: 1px solid rgba(0, 255, 136, 0.2);
    border-radius: 20px;
    padding: 2rem;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.publication-card-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-green), var(--secondary-purple));
    transform: scaleX(0);
    transition: transform 0.4s ease;
}

.publication-card-enhanced:hover::before {
    transform: scaleX(1);
}

.publication-card-enhanced:hover {
    transform: translateY(-8px) scale(1.02);
    border-color: var(--primary-green);
    box-shadow: 0 15px 40px rgba(0, 255, 136, 0.3);
}

.publication-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.avatar {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: var(--dark-bg);
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(0, 255, 136, 0.4);
    transition: all 0.3s ease;
    border: 2px solid rgba(0, 255, 136, 0.3);
}

.publication-card-enhanced:hover .avatar {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 6px 20px rgba(0, 255, 136, 0.6);
}

.publication-author {
    flex: 1;
}

.publication-author-name {
    font-weight: 600;
    color: var(--text-white);
    margin-bottom: 0.2rem;
}

.publication-date {
    font-size: 0.85rem;
    color: var(--text-gray);
}

.publication-content {
    line-height: 1.8;
    margin-bottom: 1.5rem;
    color: var(--text-light-gray);
    font-size: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.02);
    border-radius: 10px;
    border-left: 3px solid rgba(0, 255, 136, 0.3);
    transition: all 0.3s ease;
}

.publication-card-enhanced:hover .publication-content {
    background: rgba(255, 255, 255, 0.05);
    border-left-color: var(--primary-green);
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

.publication-action-buttons .btn {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.publication-action-buttons .btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.publication-action-buttons .btn:hover::before {
    width: 200px;
    height: 200px;
}

.publication-action-buttons .btn:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 5px 15px rgba(0, 255, 136, 0.4);
}

/* Styles pour les boutons de modification/suppression */
.publication-owner-actions {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.05));
    border: 1px solid rgba(255, 193, 7, 0.4);
    border-radius: 15px;
    padding: 1.5rem;
    margin: 1.5rem 0 0 0;
    position: relative;
    overflow: hidden;
}

.publication-owner-actions::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #ffc107, #ff9800);
    border-radius: 15px 0 0 15px;
}

.publication-owner-actions:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 20px rgba(255, 193, 7, 0.3);
}

.publication-owner-actions h6 {
    color: #ffc107;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    display: flex;
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
    padding: 4rem 2rem;
    color: var(--text-gray);
    background: linear-gradient(135deg, rgba(30, 30, 40, 0.95), rgba(20, 20, 30, 0.95));
    border-radius: 20px;
    border: 2px dashed rgba(0, 255, 136, 0.3);
    position: relative;
    overflow: hidden;
}

.empty-state::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(0, 255, 136, 0.1) 0%, transparent 70%);
    animation: pulse 3s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
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

/* Sidebar */
.communaute-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.sidebar-section {
    background: linear-gradient(135deg, rgba(30, 30, 40, 0.95), rgba(20, 20, 30, 0.95));
    border: 1px solid rgba(0, 255, 136, 0.2);
    border-radius: 15px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.sidebar-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-green), var(--secondary-purple));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.sidebar-section:hover::before {
    transform: scaleX(1);
}

.sidebar-section:hover {
    transform: translateY(-3px);
    border-color: var(--primary-green);
    box-shadow: 0 8px 25px rgba(0, 255, 136, 0.3);
}

.sidebar-section h5 {
    color: var(--text-white);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 1.1rem;
    background: linear-gradient(135deg, var(--text-white), var(--primary-green));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.sidebar-section .btn {
    transition: all 0.3s ease;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}

.sidebar-section .btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.sidebar-section .btn:hover::before {
    width: 300px;
    height: 300px;
}

.sidebar-section .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 255, 136, 0.4);
}

.sidebar-section .btn-primary {
    background: linear-gradient(135deg, var(--primary-green), #00cc88);
    border: none;
}

.sidebar-section .btn-warning {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    border: none;
    color: #000;
}

.sidebar-section .btn-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
}

/* Membres liste */
.membres-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.membre-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
    background: rgba(255, 255, 255, 0.02);
}

.membre-item:hover {
    background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(189, 0, 255, 0.1));
    border-color: rgba(0, 255, 136, 0.3);
    transform: translateX(5px);
    box-shadow: 0 3px 10px rgba(0, 255, 136, 0.2);
}

.membre-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: var(--dark-bg);
    font-size: 0.9rem;
    box-shadow: 0 3px 10px rgba(0, 255, 136, 0.3);
    transition: all 0.3s ease;
    border: 2px solid rgba(0, 255, 136, 0.3);
}

.membre-item:hover .membre-avatar {
    transform: scale(1.15) rotate(5deg);
    box-shadow: 0 5px 15px rgba(0, 255, 136, 0.5);
}

.membre-info {
    flex: 1;
}

.membre-name {
    color: var(--text-white);
    font-weight: 500;
    font-size: 0.9rem;
}

.membre-role {
    font-size: 0.75rem;
    display: flex;
    align-items: center;
}

/* Responsive */
@media (max-width: 1024px) {
    .communaute-detail-container {
        grid-template-columns: 1fr;
    }
    
    .communaute-sidebar {
        order: -1;
    }
}

@media (max-width: 768px) {
    .communaute-info-grid {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .communaute-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .section-header {
        flex-direction: column;
        align-items: stretch;
    }
    
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
        gap: 0.5rem;
    }
    
    .publication-owner-actions .btn {
        width: 100%;
        justify-content: center;
    }
    .main-content-section,.communaute-sidebar { padding:1.25rem 0.5rem;}
    .publication-card-enhanced { margin-bottom:1.75rem !important; }
}

@media (max-width: 480px) {
    .main-content-section {
        padding: 1rem;
    }
    
    .publication-card-enhanced {
        padding: 1rem;
    }
}

/* Modal personnalisé */
.custom-modal {
    background: linear-gradient(135deg, rgba(30, 30, 40, 0.98), rgba(20, 20, 30, 0.98));
    border: 1px solid rgba(0, 255, 136, 0.3);
    border-radius: 20px;
    backdrop-filter: blur(20px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.custom-modal-header {
    background: linear-gradient(135deg, rgba(0, 255, 136, 0.2), rgba(189, 0, 255, 0.2));
    border-bottom: 1px solid rgba(0, 255, 136, 0.3);
    border-radius: 20px 20px 0 0;
    padding: 1.5rem;
}

.custom-modal-header .modal-title {
    color: var(--primary-green);
    font-weight: 600;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
}

.custom-modal-body {
    padding: 2rem;
    text-align: center;
}

.modal-icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--dark-bg);
    box-shadow: 0 10px 30px rgba(0, 255, 136, 0.4);
    animation: pulse-icon 2s ease-in-out infinite;
}

@keyframes pulse-icon {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.modal-question {
    font-size: 1.1rem;
    color: var(--text-white);
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.modal-question strong {
    color: var(--primary-green);
    font-weight: 600;
}

.modal-info-box {
    background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(189, 0, 255, 0.1));
    border: 1px solid rgba(0, 255, 136, 0.2);
    border-radius: 12px;
    padding: 1rem;
    color: var(--text-light-gray);
    font-size: 0.9rem;
    line-height: 1.6;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.modal-info-box i {
    color: var(--primary-green);
    margin-top: 0.2rem;
}

.custom-modal-footer {
    border-top: 1px solid rgba(0, 255, 136, 0.2);
    padding: 1.5rem;
    border-radius: 0 0 20px 20px;
    background: rgba(0, 0, 0, 0.2);
}

.btn-modal {
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-modal::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.btn-modal:hover::before {
    width: 300px;
    height: 300px;
}

.btn-modal:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 255, 136, 0.4);
}

.btn-modal.btn-success {
    background: linear-gradient(135deg, var(--primary-green), #00cc88);
    border: none;
}

.btn-modal.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    border: none;
}

@media (max-width: 768px) {
    .custom-modal-body {
        padding: 1.5rem;
    }
    
    .modal-icon-wrapper {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const joinButtons = document.querySelectorAll('.join-community');

    joinButtons.forEach(button => {
        button.addEventListener('click', async function() {
            if (this.disabled) {
                return;
            }

            const communauteId = this.getAttribute('data-communaute-id');
            const communauteName = this.getAttribute('data-communaute-name') || 'cette communauté';
            const originalContent = this.innerHTML;

            if (!communauteId) {
                showAlert('❌ Impossible de déterminer la communauté.', 'danger');
                return;
            }

            // Désactiver le bouton et afficher le spinner
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>';

            try {
                const response = await fetch('/projet/api/join-community', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        communaute_id: communauteId
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Erreur parsing JSON:', text);
                    throw new Error('Réponse invalide du serveur');
                }

                if (data.success) {
                    // Mettre à jour tous les boutons pour cette communauté
                    const allJoinButtons = document.querySelectorAll(`.join-community[data-communaute-id="${communauteId}"]`);
                    allJoinButtons.forEach(btn => {
                        btn.innerHTML = '<i class="fas fa-check me-1"></i>Rejoint';
                        btn.classList.remove('btn-primary', 'btn-success');
                        btn.classList.add('btn-secondary');
                        btn.disabled = true;
                    });
                    
                    showAlert('✅ ' + (data.message || 'Vous avez rejoint la communauté "' + communauteName + '" avec succès !'), 'success');
                } else {
                    this.disabled = false;
                    this.innerHTML = originalContent;
                    showAlert('❌ ' + (data.message || 'Impossible de rejoindre cette communauté'), 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                this.disabled = false;
                this.innerHTML = originalContent;
                showAlert('❌ Erreur de connexion. Vérifiez votre connexion internet.', 'danger');
            }
        });
    });

    // Gestion du bouton "Quitter"
    const quitButtons = document.querySelectorAll('.leave-community');
    quitButtons.forEach(button => {
        button.addEventListener('click', async function() {
            if (this.disabled) {
                return;
            }

            const communauteId = this.getAttribute('data-communaute-id');
            const communauteName = this.getAttribute('data-communaute-name') || 'cette communauté';
            const originalContent = this.innerHTML;

            if (!communauteId) {
                showAlert('❌ Impossible de déterminer la communauté.', 'danger');
                return;
            }

            // Désactiver le bouton et afficher le spinner
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>';

            try {
                const response = await fetch('/projet/api/leave-community', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        communaute_id: communauteId
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Erreur parsing JSON:', text);
                    throw new Error('Réponse invalide du serveur');
                }

                if (data.success) {
                    // Transformer le bouton "Quitter" en "Rejoindre"
                    this.classList.remove('btn-danger', 'leave-community');
                    this.classList.add('btn-primary', 'join-community');
                    this.setAttribute('data-communaute-id', communauteId);
                    this.setAttribute('data-communaute-name', communauteName);
                    this.innerHTML = '<i class="fas fa-user-plus me-2"></i>Rejoindre';
                    this.disabled = false;
                    
                    // Réattacher l'événement "Rejoindre" au nouveau bouton
                    this.removeEventListener('click', arguments.callee);
                    this.addEventListener('click', async function() {
                        if (this.disabled) return;
                        const communauteId = this.getAttribute('data-communaute-id');
                        const communauteName = this.getAttribute('data-communaute-name') || 'cette communauté';
                        const originalContent = this.innerHTML;
                        if (!communauteId) {
                            showAlert('❌ Impossible de déterminer la communauté.', 'danger');
                            return;
                        }
                        this.disabled = true;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>';
                        try {
                            const response = await fetch('/projet/api/join-community', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ communaute_id: communauteId })
                            });
                            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                            const text = await response.text();
                            let data;
                            try { data = JSON.parse(text); } catch (e) { throw new Error('Réponse invalide'); }
                            if (data.success) {
                                this.classList.remove('btn-primary', 'join-community');
                                this.classList.add('btn-secondary');
                                this.innerHTML = '<i class="fas fa-check me-1"></i>Rejoint';
                                this.disabled = true;
                                showAlert('✅ ' + (data.message || 'Vous avez rejoint la communauté "' + communauteName + '" avec succès !'), 'success');
                            } else {
                                this.disabled = false;
                                this.innerHTML = originalContent;
                                showAlert('❌ ' + (data.message || 'Impossible de rejoindre'), 'danger');
                            }
                        } catch (error) {
                            this.disabled = false;
                            this.innerHTML = originalContent;
                            showAlert('❌ Erreur de connexion.', 'danger');
                        }
                    });
                    
                    showAlert('✅ ' + (data.message || 'Vous avez quitté la communauté "' + communauteName + '" avec succès !'), 'success');
                } else {
                    this.disabled = false;
                    this.innerHTML = originalContent;
                    showAlert('❌ ' + (data.message || 'Impossible de quitter cette communauté'), 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                this.disabled = false;
                this.innerHTML = originalContent;
                showAlert('❌ Erreur de connexion. Vérifiez votre connexion internet.', 'danger');
            }
        });
    });

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.main-content-section').prepend(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});

function updatePublicationSort() {
    const sortValue = document.getElementById('publicationSort').value;
    const [order_by, order_dir] = sortValue.split('_');
    
    const url = new URL(window.location.href);
    url.searchParams.set('order_by', order_by);
    url.searchParams.set('order_dir', order_dir);
    window.location.href = url.toString();
}
</script>