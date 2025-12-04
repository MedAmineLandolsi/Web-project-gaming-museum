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
                        btn.classList.remove('btn-success', 'btn-primary');
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