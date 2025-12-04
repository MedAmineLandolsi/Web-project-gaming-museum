<div class="container" style="padding: 6rem 0 4rem;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-container">
                <div class="text-center mb-4">
                    <h1 class="text-primary">Modifier la publication</h1>
                    <p class="text-muted">Modifiez votre publication dans la communauté</p>
                </div>

                <form action="/projet/publications/<?php echo $this->publicationModel->id; ?>/edit" method="POST" enctype="multipart/form-data" id="editPublicationForm">
                    <div class="mb-3">
                        <label for="contenu" class="form-label">Contenu *</label>
                        <textarea class="form-control" 
                                  id="contenu" name="contenu" rows="6" 
                                  placeholder="Modifiez le contenu de votre publication..."><?php echo $_SESSION['old_input']['contenu'] ?? $this->publicationModel->contenu; ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="images" class="form-label">Nouvelles images</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Sélectionnez de nouvelles images pour remplacer les anciennes. Formats acceptés: JPG, PNG, GIF.
                        </div>
                        
                        <!-- Afficher les images actuelles -->
                        <?php if (!empty($this->publicationModel->images) && is_array($this->publicationModel->images)): ?>
                        <div class="mt-3">
                            <label class="form-label">Images actuelles :</label>
                            <div class="row">
                                <?php foreach ($this->publicationModel->images as $image): ?>
                                <div class="col-3 mb-2">
                                    <img src="<?php echo htmlspecialchars($image); ?>" 
                                         alt="Image publication" 
                                         class="img-fluid rounded" 
                                         style="max-height: 100px; object-fit: cover;">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2 justify-content-between">
                        <div>
                            <a href="/projet/communautes/<?php echo $this->publicationModel->communaute_id; ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Modifier
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-2"></i>Supprimer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
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
                <form action="/projet/publications/<?php echo $this->publicationModel->id; ?>/delete" method="POST" class="d-inline">
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editPublicationForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const contenu = document.getElementById('contenu').value.trim();
        
        let errors = [];
        
        if (!contenu) {
            errors.push('Le contenu est obligatoire');
        } else if (contenu.length < 5) {
            errors.push('Le contenu doit faire au moins 5 caractères');
        } else if (contenu.length > 1000) {
            errors.push('Le contenu ne doit pas dépasser 1000 caractères');
        }
        
        if (errors.length > 0) {
            showErrors(errors);
        } else {
            form.submit();
        }
    });
    
    function showErrors(errors) {
        const oldAlerts = document.querySelectorAll('.alert-danger');
        oldAlerts.forEach(alert => alert.remove());
        
        const errorHtml = errors.map(error => 
            `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${error}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`
        ).join('');
        
        const formContainer = document.querySelector('.form-container');
        formContainer.insertAdjacentHTML('afterbegin', errorHtml);
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>