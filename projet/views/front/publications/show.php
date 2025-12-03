<?php
// Utiliser la variable $communauteModel passée depuis le contrôleur
$communaute = $communauteModel ?? $this->communauteModel;
?>

<div class="communaute-detail-container">
    <!-- Contenu principal -->
    <div class="main-content-section">
        <!-- Header de la communauté -->
        <div class="communaute-header">
            <div class="communaute-info-grid">
                <div class="communaute-avatar-large">
                    <?php echo strtoupper(substr($this->communauteModel->nom, 0, 2)); ?>
                </div>
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
                    </div>
                    <p class="text-muted mb-0">
                        <i class="fas fa-user me-1"></i>
                        Créée par <?php echo htmlspecialchars($this->communauteModel->createur_nom); ?>
                        • <i class="fas fa-calendar me-1"></i>
                        Le <?php echo date('d/m/Y', strtotime($this->communauteModel->date_creation)); ?>
                    </p>
                </div>
            </div>
            
            <!-- Statistiques rapides -->
            <div class="communaute-stats-grid">
                <div class="stat-card">
                    <span class="stat-number"><?php echo count($publications); ?></span>
                    <span class="stat-label">Publications</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo rand(50, 200); ?></span>
                    <span class="stat-label">Membres</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo rand(5, 25); ?></span>
                    <span class="stat-label">En ligne</span>
                </div>
            </div>
        </div>

        <!-- Description et règles -->
        <div class="description-box">
            <h5><i class="fas fa-info-circle me-2"></i>Description</h5>
            <p class="mb-0"><?php echo nl2br(htmlspecialchars($this->communauteModel->description)); ?></p>
        </div>
        
        <?php if (!empty($this->communauteModel->regles)): ?>
        <div class="regles-box">
            <h5><i class="fas fa-gavel me-2"></i>Règles de la communauté</h5>
            <p class="mb-0"><?php echo nl2br(htmlspecialchars($this->communauteModel->regles)); ?></p>
        </div>
        <?php endif; ?>

        <!-- Section Publications -->
        <div class="publications-section">
            <div class="section-header">
                <h3><i class="fas fa-newspaper me-2"></i>Publications (<?php echo count($publications); ?>)</h3>
                <a href="/projet/publications/create?communaute_id=<?php echo $this->communauteModel->id; ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouvelle publication
                </a>
            </div>

            <!-- Liste des publications -->
            <?php if (!empty($publications)): ?>
                <div class="publication-grid">
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
                                        <br><small class="text-muted">Modifié le <?php echo date('d/m/Y à H:i', strtotime($publication['date_modification'])); ?></small>
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

                        <!-- Actions standard -->
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

                        <!-- BOUTONS MODIFIER/SUPPRIMER POUR L'AUTEUR -->
                        <?php
                        $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $publication['auteur_id'];
                        ?>
                        
                        <?php if ($isOwner): ?>
                        <div class="publication-owner-actions">
                            <h6><i class="fas fa-user-cog me-1"></i>Gérer votre publication</h6>
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
                    <i class="fas fa-newspaper"></i>
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
            <h5><i class="fas fa-user-plus"></i> Rejoindre la communauté</h5>
            <p class="text-muted mb-3">Participez aux discussions et partagez vos idées.</p>
            <button class="btn btn-primary w-100 join-community" 
                    data-communaute-id="<?php echo $this->communauteModel->id; ?>"
                    data-communaute-name="<?php echo htmlspecialchars($this->communauteModel->nom); ?>">
                <i class="fas fa-user-plus me-2"></i>Rejoindre
            </button>
        </div>

        <!-- Membres actifs -->
        <div class="sidebar-section">
            <h5><i class="fas fa-users"></i> Membres actifs</h5>
            <div class="membre-list">
                <div class="membre-item">
                    <div class="membre-avatar">
                        <?php echo strtoupper(substr($this->communauteModel->createur_nom, 0, 2)); ?>
                    </div>
                    <div class="membre-info">
                        <div class="membre-name"><?php echo htmlspecialchars($this->communauteModel->createur_nom); ?></div>
                        <div class="membre-role">Créateur</div>
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
                        <div class="membre-role">Membre actif</div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            <div class="text-center mt-2">
                <small class="text-muted">Et <?php echo rand(10, 50); ?> autres membres...</small>
            </div>
        </div>

        <!-- Actions du créateur -->
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $this->communauteModel->createur_id): ?>
        <div class="sidebar-section">
            <h5><i class="fas fa-cog"></i> Gestion</h5>
            <div class="d-flex gap-2">
                <a href="/projet/admin/communautes/<?php echo $this->communauteModel->id; ?>/edit" class="btn btn-warning btn-sm flex-fill">
                    <i class="fas fa-edit me-1"></i>Modifier
                </a>
                <form action="/projet/admin/communautes/<?php echo $this->communauteModel->id; ?>/delete" method="POST" class="d-inline flex-fill">
                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ?')">
                        <i class="fas fa-trash me-1"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

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

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.main-content .container').prepend(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
</script>