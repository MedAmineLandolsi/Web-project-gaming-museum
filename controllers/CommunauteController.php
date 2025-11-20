<?php
class CommunauteController {
    private $communauteModel;
    private $publicationModel;
    private $validation;

    public function __construct($db) {
        $this->communauteModel = new Communaute($db);
        $this->publicationModel = new Publication($db);
        $this->validation = new Validation();
    }

    // === FRONT OFFICE ===

    // Afficher toutes les communautés (Front)
    public function indexFront() {
        $result = $this->communauteModel->read();
        $communautes = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Ajouter des données simulées pour l'affichage
        foreach($communautes as &$communaute) {
            $communaute['membres_count'] = rand(50, 200);
            $communaute['publications_count'] = rand(10, 50);
        }
        
        $title = "Nos Communautés";
        ob_start();
        include 'views/front/communautes/index.php';
        $content = ob_get_clean();
        include 'views/front/layout.php';
    }

    // Afficher une communauté (Front) - CORRIGÉ POUR LES IMAGES
    public function showFront($id) {
        $this->communauteModel->id = $id;
        
        if($this->communauteModel->read_single()) {
            // Récupérer les publications de la communauté (déjà décodées)
            $publications = $this->publicationModel->read_by_communaute($id);
            
            $title = $this->communauteModel->nom;
            ob_start();
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
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contenu -->
                                    <div class="publication-content">
                                        <?php echo nl2br(htmlspecialchars($publication['contenu'])); ?>
                                    </div>

                                    <!-- Images - CORRIGÉ -->
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
            <?php
            $content = ob_get_clean();
            include 'views/front/layout.php';
        } else {
            $this->showError("Communauté non trouvée");
        }
    }

    // Créer une communauté (formulaire - Front)
    public function createFront() {
        $title = "Créer une communauté";
        ob_start();
        include 'views/front/communautes/create.php';
        $content = ob_get_clean();
        include 'views/front/layout.php';
    }

    // Stocker une communauté (Front)
    public function storeFront($data) {
        $errors = $this->validateCommunauteData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/communautes/create');
            exit;
        }

        $this->communauteModel->nom = $data['nom'];
        $this->communauteModel->categorie = $data['categorie'];
        $this->communauteModel->description = $data['description'];
        $this->communauteModel->createur_id = $_SESSION['user_id'] ?? 1;
        $this->communauteModel->avatar = $data['avatar'] ?? '';
        $this->communauteModel->visibilite = $data['visibilite'] ?? 'publique';
        $this->communauteModel->regles = $data['regles'] ?? '';

        if($this->communauteModel->create()) {
            $_SESSION['success_message'] = "Communauté créée avec succès !";
            header('Location: /projet/communautes');
        } else {
            $this->showError("Erreur lors de la création de la communauté");
        }
    }

    // === BACK OFFICE ===

    // Afficher toutes les communautés (Back)
    public function indexBack() {
        $result = $this->communauteModel->read();
        $communautes = $result->fetchAll(PDO::FETCH_ASSOC);
        
        $title = "Gestion des communautés";
        ob_start();
        include 'views/back/communautes/index.php';
        $content = ob_get_clean();
        include 'views/back/layout.php';
    }

    // Afficher une communauté (Back)
    public function showBack($id) {
        $this->communauteModel->id = $id;
        
        if($this->communauteModel->read_single()) {
            // Récupérer les publications de la communauté (déjà décodées)
            $publications = $this->publicationModel->read_by_communaute($id);
            
            $title = "Détail de la communauté";
            ob_start();
            include 'views/back/communautes/show.php';
            $content = ob_get_clean();
            include 'views/back/layout.php';
        } else {
            $this->showError("Communauté non trouvée");
        }
    }

    // Créer une communauté (formulaire - Back)
    public function create() {
        $title = "Créer une nouvelle communauté";
        ob_start();
        include 'views/back/communautes/create.php';
        $content = ob_get_clean();
        include 'views/back/layout.php';
    }

    // Stocker une nouvelle communauté (Back)
    public function store($data) {
        $errors = $this->validateCommunauteData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/admin/communautes/create');
            exit;
        }

        $this->communauteModel->nom = $data['nom'];
        $this->communauteModel->categorie = $data['categorie'];
        $this->communauteModel->description = $data['description'];
        $this->communauteModel->createur_id = $_SESSION['user_id'] ?? 1;
        $this->communauteModel->avatar = $data['avatar'] ?? '';
        $this->communauteModel->visibilite = $data['visibilite'] ?? 'publique';
        $this->communauteModel->regles = $data['regles'] ?? '';

        if($this->communauteModel->create()) {
            $_SESSION['success_message'] = "Communauté créée avec succès";
            header('Location: /projet/admin/communautes');
        } else {
            $this->showError("Erreur lors de la création de la communauté");
        }
    }

    // Modifier une communauté (formulaire - Back)
    public function edit($id) {
        $this->communauteModel->id = $id;
        
        if($this->communauteModel->read_single()) {
            $title = "Modifier la communauté";
            ob_start();
            include 'views/back/communautes/edit.php';
            $content = ob_get_clean();
            include 'views/back/layout.php';
        } else {
            $this->showError("Communauté non trouvée");
        }
    }

    // Mettre à jour une communauté (Back)
    public function update($id, $data) {
        $errors = $this->validateCommunauteData($data, $id);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/admin/communautes/' . $id . '/edit');
            exit;
        }

        $this->communauteModel->id = $id;
        $this->communauteModel->nom = $data['nom'];
        $this->communauteModel->categorie = $data['categorie'];
        $this->communauteModel->description = $data['description'];
        $this->communauteModel->avatar = $data['avatar'] ?? '';
        $this->communauteModel->visibilite = $data['visibilite'] ?? 'publique';
        $this->communauteModel->regles = $data['regles'] ?? '';

        if($this->communauteModel->update()) {
            $_SESSION['success_message'] = "Communauté mise à jour avec succès";
            header('Location: /projet/admin/communautes/' . $id);
        } else {
            $this->showError("Erreur lors de la mise à jour de la communauté");
        }
    }

    // Supprimer une communauté (Back)
    public function delete($id) {
        $this->communauteModel->id = $id;
        
        if($this->communauteModel->delete()) {
            $_SESSION['success_message'] = "Communauté supprimée avec succès";
            header('Location: /projet/admin/communautes');
        } else {
            $this->showError("Erreur lors de la suppression de la communauté");
        }
    }

    // === VALIDATION ===

    private function validateCommunauteData($data, $id = null) {
        $errors = [];

        // Validation nom
        if (empty($data['nom'])) {
            $errors['nom'] = "Le nom est obligatoire";
        } elseif (strlen($data['nom']) > 100) {
            $errors['nom'] = "Le nom ne doit pas dépasser 100 caractères";
        } elseif ($this->communauteModel->nomExists($data['nom'], $id)) {
            $errors['nom'] = "Ce nom de communauté est déjà utilisé";
        }

        // Validation catégorie
        if (empty($data['categorie'])) {
            $errors['categorie'] = "La catégorie est obligatoire";
        } elseif (strlen($data['categorie']) > 50) {
            $errors['categorie'] = "La catégorie ne doit pas dépasser 50 caractères";
        }

        // Validation description
        if (empty($data['description'])) {
            $errors['description'] = "La description est obligatoire";
        } elseif (strlen($data['description']) < 10) {
            $errors['description'] = "La description doit faire au moins 10 caractères";
        }

        // Validation visibilité
        $allowed_visibility = ['publique', 'privee', 'cachee'];
        if (!empty($data['visibilite']) && !in_array($data['visibilite'], $allowed_visibility)) {
            $errors['visibilite'] = "Visibilité invalide";
        }

        // Validation avatar (URL)
        if (!empty($data['avatar']) && !$this->validation->isValidUrl($data['avatar'])) {
            $errors['avatar'] = "L'URL de l'avatar n'est pas valide";
        }

        return $errors;
    }

    private function showError($message) {
        $title = "Erreur";
        ob_start();
        echo "<div class='alert alert-danger'>$message</div>";
        $content = ob_get_clean();
        include 'views/front/layout.php';
    }
}
?>