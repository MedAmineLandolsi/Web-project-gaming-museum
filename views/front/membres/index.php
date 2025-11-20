<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-gradient">Nos Membres</h1>
            <p class="text-muted mb-0">Découvrez les créateurs de nos communautés</p>
        </div>
    </div>
</div>

<div class="row">
    <?php if (!empty($membres)): ?>
        <?php foreach ($membres as $membre): 
            // Récupérer les communautés créées par ce membre
            $communautesMembre = [];
            foreach($communautes ?? [] as $communaute) {
                if ($communaute['createur_id'] == $membre['id']) {
                    $communautesMembre[] = $communaute;
                }
            }
            $nombreCommunautes = count($communautesMembre);
        ?>
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="community-card h-100">
                <div class="d-flex align-items-start mb-3">
                    <div class="avatar me-3" style="width: 70px; height: 70px; font-size: 1.5rem;">
                        <?php echo strtoupper(substr($membre['prenom'], 0, 1) . substr($membre['nom'], 0, 1)); ?>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-1"><?php echo htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']); ?></h4>
                        <p class="text-muted mb-2">
                            <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($membre['email']); ?>
                        </p>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-<?php echo $membre['statut'] == 'actif' ? 'success' : ($membre['statut'] == 'inactif' ? 'warning' : 'danger'); ?>">
                                <?php echo ucfirst($membre['statut']); ?>
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-users me-1"></i><?php echo $nombreCommunautes; ?> communauté(s)
                            </span>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($membre['bio'])): ?>
                <div class="mb-3">
                    <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($membre['bio'])); ?></p>
                </div>
                <?php endif; ?>

                <!-- Communautés créées par le membre -->
                <?php if ($nombreCommunautes > 0): ?>
                <div class="communautes-list">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-star me-1"></i>Communautés créées
                    </h6>
                    <?php foreach ($communautesMembre as $communaute): ?>
                    <div class="communaute-item card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($communaute['avatar'])): ?>
                                    <img src="<?php echo htmlspecialchars($communaute['avatar']); ?>" 
                                         alt="<?php echo htmlspecialchars($communaute['nom']); ?>" 
                                         class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="avatar me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                        <?php echo strtoupper(substr($communaute['nom'], 0, 2)); ?>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($communaute['nom']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($communaute['categorie']); ?></small>
                                    </div>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="/projet/communautes/<?php echo $communaute['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </a>
                                    <button class="btn btn-success join-community" 
                                            data-communaute-id="<?php echo $communaute['id']; ?>" 
                                            data-communaute-name="<?php echo htmlspecialchars($communaute['nom']); ?>">
                                        <i class="fas fa-user-plus me-1"></i>Rejoindre
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-3">
                    <i class="fas fa-users fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">Ce membre n'a pas encore créé de communauté</p>
                </div>
                <?php endif; ?>

                <div class="mt-3 pt-3 border-top border-secondary">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Membre depuis le <?php echo date('d/m/Y', strtotime($membre['date_inscription'])); ?>
                    </small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="community-card p-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h3>Aucun membre trouvé</h3>
                <p class="text-muted mb-4">Il n'y a pas encore de membres inscrits.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmation pour rejoindre une communauté -->
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
            
            console.log('Communaute ID:', currentCommunauteId);
            console.log('Communaute Name:', currentCommunauteName);
            
            communauteName.textContent = currentCommunauteName;
            joinModal.show();
        });
    });

    confirmJoin.addEventListener('click', function() {
        if (currentCommunauteId) {
            console.log('Tentative de rejoindre:', currentCommunauteId);
            
            // Désactiver le bouton pendant la requête
            confirmJoin.disabled = true;
            confirmJoin.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>En cours...';

            fetch('/projet/api/join-community', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    communaute_id: currentCommunauteId
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    // Mettre à jour tous les boutons pour cette communauté
                    const joinButtons = document.querySelectorAll(`.join-community[data-communaute-id="${currentCommunauteId}"]`);
                    joinButtons.forEach(btn => {
                        btn.innerHTML = '<i class="fas fa-check me-1"></i>Rejoint';
                        btn.classList.remove('btn-success', 'btn-primary');
                        btn.classList.add('btn-secondary');
                        btn.disabled = true;
                    });
                    
                    showAlert('✅ ' + data.message, 'success');
                } else {
                    showAlert('❌ ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showAlert('❌ Erreur de connexion', 'danger');
            })
            .finally(() => {
                joinModal.hide();
                // Réactiver le bouton après 1 seconde
                setTimeout(() => {
                    confirmJoin.disabled = false;
                    confirmJoin.innerHTML = '<i class="fas fa-user-plus me-1"></i>Rejoindre';
                }, 1000);
            });
        }
    });

    function showAlert(message, type) {
        // Supprimer les anciennes alertes
        const oldAlerts = document.querySelectorAll('.alert');
        oldAlerts.forEach(alert => alert.remove());

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.main-content .container');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
        }
        
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>

<style>
.communaute-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--border);
}

.communaute-item:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: var(--primary);
}

.communautes-list {
    max-height: 300px;
    overflow-y: auto;
}

.modal-content {
    background: var(--darker);
    border: 1px solid var(--border);
    color: var(--light);
}

.modal-header {
    border-bottom: 1px solid var(--border);
}

.modal-footer {
    border-top: 1px solid var(--border);
}

.btn-close {
    filter: invert(1);
}
</style>