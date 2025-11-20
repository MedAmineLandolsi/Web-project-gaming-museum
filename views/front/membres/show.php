<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-4">
                    <div class="avatar avatar-large mx-auto mb-3">
                        <?php echo strtoupper(substr($this->membreModel->prenom, 0, 1) . substr($this->membreModel->nom, 0, 1)); ?>
                    </div>
                    
                    <h1 class="text-gradient"><?php echo htmlspecialchars($this->membreModel->prenom . ' ' . $this->membreModel->nom); ?></h1>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-envelope"></i> Email:</strong><br>
                            <?php echo htmlspecialchars($this->membreModel->email); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-user"></i> Statut:</strong><br>
                            <span class="badge bg-<?php echo $this->membreModel->statut == 'actif' ? 'success' : ($this->membreModel->statut == 'inactif' ? 'warning' : 'danger'); ?>">
                                <?php echo ucfirst($this->membreModel->statut); ?>
                            </span></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($this->membreModel->bio)): ?>
                    <div class="mt-4">
                        <h5>Biographie</h5>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($this->membreModel->bio)); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <p class="text-muted">
                            <i class="fas fa-calendar"></i> Membre depuis le 
                            <?php echo date('d/m/Y', strtotime($this->membreModel->date_inscription)); ?>
                        </p>
                    </div>
                </div>

                <!-- Communautés créées -->
                <?php if (!empty($communautes)): ?>
                <div class="mt-5">
                    <h4 class="text-primary mb-4">
                        <i class="fas fa-star me-2"></i>Communautés créées (<?php echo count($communautes); ?>)
                    </h4>
                    
                    <div class="row">
                        <?php foreach ($communautes as $communaute): ?>
                        <div class="col-md-6 mb-3">
                            <div class="community-card">
                                <div class="d-flex align-items-center mb-2">
                                    <?php if (!empty($communaute['avatar'])): ?>
                                    <img src="<?php echo htmlspecialchars($communaute['avatar']); ?>" 
                                         class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="avatar me-2" style="width: 40px; height: 40px; font-size: 1rem;">
                                        <?php echo strtoupper(substr($communaute['nom'], 0, 2)); ?>
                                    </div>
                                    <?php endif; ?>
                                    <div class="text-start">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($communaute['nom']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($communaute['categorie']); ?></small>
                                    </div>
                                </div>
                                <div class="btn-group w-100">
                                    <a href="/projet/communautes/<?php echo $communaute['id']; ?>" class="btn btn-primary btn-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </a>
                                    <button class="btn btn-success btn-sm join-community" 
                                            data-communaute-id="<?php echo $communaute['id']; ?>" 
                                            data-communaute-name="<?php echo htmlspecialchars($communaute['nom']); ?>">
                                        <i class="fas fa-user-plus me-1"></i>Rejoindre
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="mt-5 text-center">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5>Ce membre n'a créé aucune communauté</h5>
                    <p class="text-muted">Revenez plus tard pour découvrir ses créations.</p>
                </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="/projet/membres" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour aux membres
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure le même modal que dans index.php -->
<div class="modal fade" id="joinModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Rejoindre une communauté</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir rejoindre la communauté <strong id="communauteName"></strong> ?</p>
                <p class="text-muted">Vous pourrez participer aux discussions et partager vos publications.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="confirmJoin">Rejoindre</button>
            </div>
        </div>
    </div>
</div>

<script>
// Le même script JavaScript que dans index.php
document.addEventListener('DOMContentLoaded', function() {
    const joinButtons = document.querySelectorAll('.join-community');
    const joinModal = new bootstrap.Modal(document.getElementById('joinModal'));
    const communauteName = document.getElementById('communauteName');
    const confirmJoin = document.getElementById('confirmJoin');
    let currentCommunauteId = null;
    let currentCommunauteName = null;

    joinButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentCommunauteId = this.getAttribute('data-communaute-id');
            currentCommunauteName = this.getAttribute('data-communaute-name');
            
            communauteName.textContent = currentCommunauteName;
            joinModal.show();
        });
    });

    confirmJoin.addEventListener('click', function() {
        if (currentCommunauteId) {
            fetch('/projet/api/join-community', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    communaute_id: currentCommunauteId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const joinButton = document.querySelector(`.join-community[data-communaute-id="${currentCommunauteId}"]`);
                    joinButton.innerHTML = '<i class="fas fa-check me-1"></i>Rejoint';
                    joinButton.classList.remove('btn-success');
                    joinButton.classList.add('btn-secondary');
                    joinButton.disabled = true;
                    
                    showAlert('Vous avez rejoint la communauté "' + currentCommunauteName + '" avec succès !', 'success');
                } else {
                    showAlert('Erreur: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Erreur lors de la requête', 'danger');
            })
            .finally(() => {
                joinModal.hide();
            });
        }
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