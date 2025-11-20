<?php
// Récupérer les données de la communauté
$communaute = [
    'id' => $this->communauteModel->id ?? ($communaute['id'] ?? null),
    'nom' => $this->communauteModel->nom ?? ($communaute['nom'] ?? ''),
    'categorie' => $this->communauteModel->categorie ?? ($communaute['categorie'] ?? ''),
    'description' => $this->communauteModel->description ?? ($communaute['description'] ?? ''),
    'createur_id' => $this->communauteModel->createur_id ?? ($communaute['createur_id'] ?? null),
    'createur_nom' => $this->communauteModel->createur_nom ?? ($communaute['createur_nom'] ?? ''),
    'avatar' => $this->communauteModel->avatar ?? ($communaute['avatar'] ?? ''),
    'visibilite' => $this->communauteModel->visibilite ?? ($communaute['visibilite'] ?? 'publique'),
    'regles' => $this->communauteModel->regles ?? ($communaute['regles'] ?? '')
];

// Vérifier que les données sont présentes
if (empty($communaute['nom'])) {
    echo "<div class='alert alert-danger'>Données de la communauté non disponibles</div>";
    return;
}
?>

<div class="row">
    <div class="col-lg-8">
        <!-- En-tête de la communauté -->
        <div class="community-card p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <?php if (!empty($communaute['avatar'])): ?>
                        <img src="<?php echo htmlspecialchars($communaute['avatar']); ?>" 
                             alt="<?php echo htmlspecialchars($communaute['nom']); ?>" 
                             class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                    <?php else: ?>
                        <div class="avatar mx-auto" style="width: 80px; height: 80px; font-size: 1.8rem;">
                            <?php echo strtoupper(substr($communaute['nom'], 0, 2)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <h1 class="h2 mb-2"><?php echo htmlspecialchars($communaute['nom']); ?></h1>
                    <p class="text-muted mb-2">
                        <i class="fas fa-user me-1"></i>
                        Créée par <?php echo htmlspecialchars($communaute['createur_nom']); ?>
                    </p>
                    <div class="d-flex gap-2 mb-2">
                        <span class="badge bg-primary"><?php echo htmlspecialchars($communaute['categorie']); ?></span>
                        <span class="badge bg-<?php echo $communaute['visibilite'] == 'publique' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($communaute['visibilite']); ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-2 text-end">
                    <!-- Boutons d'action pour le créateur -->
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $communaute['createur_id']): ?>
                    <div class="btn-group">
                        <a href="/projet/admin/communautes/<?php echo $communaute['id']; ?>/edit" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="/projet/admin/communautes/<?php echo $communaute['id']; ?>/delete" method="POST" class="d-inline">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mt-4">
                <h5>Description</h5>
                <p class="text-muted"><?php echo nl2br(htmlspecialchars($communaute['description'])); ?></p>
            </div>
            
            <!-- Règles -->
            <?php if (!empty($communaute['regles'])): ?>
            <div class="mt-4">
                <h5>Règles de la communauté</h5>
                <div class="bg-dark p-3 rounded">
                    <?php echo nl2br(htmlspecialchars($communaute['regles'])); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Section Publications -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Publications (<?php echo count($publications); ?>)</h3>
            <a href="/projet/publications/create?communaute_id=<?php echo $communaute['id']; ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Partager une publication
            </a>
        </div>

        <!-- Liste des publications -->
        <?php if (!empty($publications)): ?>
            <?php foreach ($publications as $publication): ?>
            <div class="publication-card p-4 mb-4">
                <!-- En-tête publication -->
                <div class="d-flex align-items-center mb-3">
                    <?php if (!empty($publication['avatar'])): ?>
                        <img src="<?php echo htmlspecialchars($publication['avatar']); ?>" 
                             alt="<?php echo htmlspecialchars($publication['prenom'] . ' ' . $publication['nom']); ?>" 
                             class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                    <?php else: ?>
                        <div class="avatar me-3">
                            <?php echo strtoupper(substr($publication['prenom'], 0, 1) . substr($publication['nom'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h6 class="mb-0"><?php echo htmlspecialchars($publication['prenom'] . ' ' . $publication['nom']); ?></h6>
                        <small class="text-muted">
                            <?php echo date('d/m/Y à H:i', strtotime($publication['date_publication'])); ?>
                        </small>
                    </div>
                </div>

                <!-- Contenu -->
                <div class="publication-content mb-3">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($publication['contenu'])); ?></p>
                </div>

                <!-- Images -->
                <?php if (!empty($publication['images'])): ?>
                <div class="publication-images mb-3">
                    <?php 
                    $images = json_decode($publication['images'], true);
                    if (is_array($images) && !empty($images)): 
                    ?>
                        <div class="row g-2">
                            <?php foreach ($images as $image): ?>
                            <div class="col-4">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Image publication" 
                                     class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="d-flex justify-content-between align-items-center">
                    <div class="publication-stats">
                        <span class="text-muted me-3">
                            <i class="fas fa-heart me-1"></i><?php echo $publication['likes']; ?>
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-comment me-1"></i><?php echo $publication['commentaires']; ?>
                        </span>
                    </div>
                    <div class="publication-actions">
                        <button class="btn btn-sm btn-outline-primary me-1">
                            <i class="fas fa-heart"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-comment"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="community-card p-5 text-center">
                <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                <h4>Aucune publication</h4>
                <p class="text-muted mb-4">Soyez le premier à partager dans cette communauté !</p>
                <a href="/projet/publications/create?communaute_id=<?php echo $communaute['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Créer la première publication
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Statistiques -->
        <div class="community-card p-4 mb-4">
            <h5 class="mb-3">Statistiques</h5>
            <div class="row text-center">
                <div class="col-4">
                    <div class="border-end border-secondary">
                        <h4 class="text-primary mb-1"><?php echo count($publications); ?></h4>
                        <small class="text-muted">Publications</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border-end border-secondary">
                        <h4 class="text-success mb-1"><?php echo rand(50, 200); ?></h4>
                        <small class="text-muted">Membres</small>
                    </div>
                </div>
                <div class="col-4">
                    <h4 class="text-warning mb-1"><?php echo rand(5, 25); ?></h4>
                    <small class="text-muted">En ligne</small>
                </div>
            </div>
        </div>

        <!-- Bouton rejoindre -->
        <div class="community-card p-4 mb-4 text-center">
            <h5 class="mb-3">Rejoindre la communauté</h5>
            <p class="text-muted mb-3">Participez aux discussions et partagez vos idées</p>
            <button class="btn btn-primary w-100 join-community" 
                    data-communaute-id="<?php echo $communaute['id']; ?>"
                    data-communaute-name="<?php echo htmlspecialchars($communaute['nom']); ?>">
                <i class="fas fa-user-plus me-2"></i>Rejoindre
            </button>
        </div>

        <!-- Derniers membres -->
        <div class="community-card p-4">
            <h5 class="mb-3">Membres actifs</h5>
            <div class="d-flex align-items-center mb-3">
                <?php if (!empty($communaute['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($communaute['avatar']); ?>" 
                         alt="<?php echo htmlspecialchars($communaute['createur_nom']); ?>" 
                         class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                <?php else: ?>
                    <div class="avatar me-3">
                        <?php echo strtoupper(substr($communaute['createur_nom'], 0, 2)); ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h6 class="mb-0"><?php echo htmlspecialchars($communaute['createur_nom']); ?></h6>
                    <small class="text-muted">Créateur</small>
                </div>
            </div>
            <!-- Ajouter d'autres membres ici -->
            <div class="text-center">
                <small class="text-muted">Et <?php echo rand(10, 50); ?> autres membres...</small>
            </div>
        </div>
    </div>
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
                    joinButton.classList.remove('btn-primary');
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