<div class="row">
<!-- Recommandations IA -->
<div id="recommendations-ia" class="mb-4"></div>
<!-- Formulaire de recherche intelligente -->
<form id="search-form" onsubmit="event.preventDefault(); searchCommunity(document.getElementById('search-input').value);" class="mb-4">
    <input type="text" id="search-input" class="form-control d-inline-block w-auto" placeholder="Rechercher une communauté..." required>
    <button type="submit" class="btn btn-secondary ms-2">Rechercher</button>
</form>

<!-- Conteneur pour afficher les résultats -->
<div id="search-results" class="mb-4"></div>
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-gradient fs-2 fw-bold mb-4"><i class="fas fa-users me-2"></i>Nos Communautés</h1>
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



<div class="row" id="communities-list">
    <?php if (!empty($communautes)): ?>
        <?php foreach ($communautes as $communaute): 
            $membres_count = rand(50, 200);
            $publications_count = rand(10, 50);
            $is_my_community = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $communaute['createur_id'];
        ?>
        <div class="col-lg-6 col-md-6 mb-5">
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
                        <h5 class="mb-1">
                            <a href="/projet/communautes/<?php echo $communaute['id']; ?>" class="text-decoration-none text-white">
                                <?php echo htmlspecialchars($communaute['nom']); ?>
                            </a>
                        </h5>
                        <small class="text-muted d-block">
                            <i class="fas fa-user me-1"></i>
                            Créée par 
                            <a href="/projet/membres/<?php echo $communaute['createur_id']; ?>" class="text-decoration-none text-primary">
                                <?php echo htmlspecialchars($communaute['prenom'] . ' ' . $communaute['nom']); ?>
                            </a>
                        </small>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-calendar me-1"></i>
                            Le <?php echo date('d/m/Y à H:i', strtotime($communaute['date_creation'])); ?>
                        </small>
                    </div>
                </div>
                
                <p class="text-muted mb-3">
                    <?php 
                    $description = $communaute['description'];
                    echo strlen($description) > 150 ? htmlspecialchars(substr($description, 0, 150)) . '...' : htmlspecialchars($description);
                    ?>
                </p>
                
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <span class="badge bg-primary">
                        <i class="fas fa-tag me-1"></i>
                        <?php echo htmlspecialchars($communaute['categorie']); ?>
                    </span>
                    <span class="badge bg-<?php echo $communaute['visibilite'] == 'publique' ? 'success' : 'warning'; ?>">
                        <i class="fas fa-<?php echo $communaute['visibilite'] == 'publique' ? 'globe' : 'lock'; ?> me-1"></i>
                        <?php echo ucfirst($communaute['visibilite']); ?>
                    </span>
                    <?php if ($is_my_community): ?>
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-crown me-1"></i>Ma communauté
                    </span>
                    <?php endif; ?>
                </div>
                
                <div class="community-stats d-flex justify-content-between text-center mb-3">
                    <div>
                        <small class="text-muted d-block">Membres</small>
                        <div class="fw-bold text-primary"><?php echo $membres_count; ?></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Publications</small>
                        <div class="fw-bold text-success"><?php echo $publications_count; ?></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">En ligne</small>
                        <div class="fw-bold text-info"><?php echo rand(5, 25); ?></div>
                    </div>
                </div>
                
                <?php if (!empty($communaute['regles'])): ?>
                <div class="alert alert-info py-4 px-4 mb-4">
                    <small>
                        <i class="fas fa-gavel me-1"></i>
                        <strong>Règles définies</strong> - Cette communauté a des règles spécifiques
                    </small>
                </div>
                <?php endif; ?>
                
                <div class="d-flex flex-wrap gap-3 mt-3">
                    <!-- Bouton pour voir les détails de la communauté -->
                    <a href="/projet/communautes/<?php echo $communaute['id']; ?>" class="btn btn-info btn-sm flex-fill">
                        <i class="fas fa-info-circle me-1"></i>Détails
                    </a>
                    
                    <!-- BOUTON MODIFIÉ : Redirige vers les publications filtrées par communauté -->
                    <a href="/projet/publications?communaute=<?php echo $communaute['id']; ?>" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-eye me-1"></i>Publications
                    </a>
                    
                    <!-- Boutons supplémentaires pour mes communautés -->
                    <?php if ($is_my_community): ?>
                    <div class="btn-group w-100 gap-2 mt-2">
                        <a href="/projet/admin/communautes/<?php echo $communaute['id']; ?>/edit" class="btn btn-warning btn-sm" title="Modifier">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                        <form action="/projet/admin/communautes/<?php echo $communaute['id']; ?>/delete" method="POST" class="d-inline">
                            <button type="submit" class="btn btn-danger btn-sm" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ?')"
                                    title="Supprimer">
                                <i class="fas fa-trash me-1"></i>Supprimer
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="community-card p-5 mb-4">
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

<script>
function updateSort() {
    const sortValue = document.getElementById('sortOrder').value;
    const [order_by, order_dir] = sortValue.split('_');
    
    const url = new URL(window.location.href);
    url.searchParams.set('order_by', order_by);
    url.searchParams.set('order_dir', order_dir);
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', function () {
    const userId = document.body.dataset.userId; // Assurez-vous que l'ID utilisateur est disponible dans le DOM
    const recommendationsContainer = document.getElementById('recommendations');

    if (userId && recommendationsContainer) {
        fetch(`/api/recommend-communities.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    recommendationsContainer.innerHTML = `<p>${data.error}</p>`;
                } else {
                    let html = '<h3>Communautés recommandées :</h3><ul>';
                    data.forEach(community => {
                        html += `<li>Communauté ID: ${community}</li>`;
                    });
                    html += '</ul>';
                    recommendationsContainer.innerHTML = html;
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des recommandations :', error);
                recommendationsContainer.innerHTML = '<p>Impossible de charger les recommandations.</p>';
            });
    }
});
</script>

<div id="recommendations"></div>

<style>
.community-stats {
    background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(189, 0, 255, 0.1));
    border: 1px solid rgba(0, 255, 136, 0.2);
    border-radius: 12px;
    padding: 1rem;
    position: relative;
    overflow: hidden;
}

.community-stats::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.5s ease;
}

.community-card:hover .community-stats::before {
    left: 100%;
}

.community-stats > div {
    flex: 1;
    position: relative;
    z-index: 1;
}

.community-stats > div:not(:last-child) {
    border-right: 1px solid rgba(0, 255, 136, 0.3);
}

.community-stats .fw-bold {
    font-size: 1.4rem;
    text-shadow: 0 0 10px currentColor;
    transition: transform 0.3s ease;
}

.community-card:hover .community-stats .fw-bold {
    transform: scale(1.1);
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.sorting-options {
    background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
    border: 2px solid var(--border-color);
    border-radius: 10px;
}

.sorting-options select {
    background: var(--card-bg);
    border: 2px solid var(--primary-green);
    color: var(--text-white);
    border-radius: 5px;
}

.sorting-options select:focus {
    border-color: var(--secondary-purple);
    box-shadow: 0 0 10px rgba(189, 0, 255, 0.3);
}

.community-card {
    background: linear-gradient(135deg, rgba(30, 30, 40, 0.95), rgba(20, 20, 30, 0.95));
    border: 1px solid rgba(0, 255, 136, 0.2);
    border-radius: 20px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.community-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-green), var(--secondary-purple));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.community-card:hover::before {
    transform: scaleX(1);
}

.community-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 255, 136, 0.3);
    border-color: var(--primary-green);
}

.community-card h5 a {
    transition: color 0.3s ease;
}

.community-card h5 a:hover {
    color: var(--primary-green) !important;
}

.community-stats .fw-bold {
    font-size: 1.2rem;
    margin-top: 0.25rem;
}

.alert-info {
    background: linear-gradient(135deg, rgba(13, 202, 240, 0.15), rgba(13, 202, 240, 0.05));
    border: 1px solid rgba(13, 202, 240, 0.4);
    border-left: 4px solid #0dcaf0;
    color: var(--text-light-gray);
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(13, 202, 240, 0.1);
    transition: all 0.3s ease;
}

.alert-info:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(13, 202, 240, 0.2);
}

.avatar {
    background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
    box-shadow: 0 0 15px rgba(0, 255, 136, 0.4);
    transition: all 0.3s ease;
}

.community-card:hover .avatar {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 0 25px rgba(0, 255, 136, 0.6);
}

.community-card .badge {
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.community-card:hover .badge {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.community-card .btn {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.85rem;
}

.community-card .btn::before {
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

.community-card .btn:hover::before {
    width: 300px;
    height: 300px;
}

.community-card .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 255, 136, 0.4);
}

.community-card .btn-primary {
    background: linear-gradient(135deg, var(--primary-green), #00cc88);
    border: none;
}

.community-card .btn-info {
    background: linear-gradient(135deg, #0dcaf0, #0aa2c0);
    border: none;
}

.community-card .btn-warning {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    border: none;
    color: #000;
}

.community-card .btn-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
}

.community-card h5 a {
    transition: all 0.3s ease;
    display: inline-block;
}

.community-card h5 a:hover {
    color: var(--primary-green) !important;
    text-shadow: 0 0 15px rgba(0, 255, 136, 0.5);
    transform: translateX(5px);
}

.community-card p.text-muted {
    line-height: 1.6;
    min-height: 3em;
}

@media (max-width: 768px) {
    .community-card .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .community-card .btn {
        width: 100%;
    }
    
    .community-card {
        padding: 1.5rem !important;
    }
}
</style>