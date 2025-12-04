<?php
// views/front/publications/edit.php
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="community-card p-4">
            <div class="text-center mb-4">
                <h1 class="h2">Modifier la publication</h1>
                <p class="text-muted">Modifiez votre publication ci-dessous</p>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['form_errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Veuillez corriger les erreurs suivantes :
                    <ul class="mb-0 mt-2">
                        <?php foreach ($_SESSION['form_errors'] as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['form_errors']); ?>
            <?php endif; ?>

            <form action="/projet/publications/update/<?php echo $this->publicationModel->id; ?>" method="POST" enctype="multipart/form-data">
                <!-- Contenu de la publication -->
                <div class="mb-4">
                    <label for="contenu" class="form-label">Contenu *</label>
                    <textarea class="form-control <?php echo isset($_SESSION['form_errors']['contenu']) ? 'is-invalid' : ''; ?>" 
                              id="contenu" name="contenu" rows="6" 
                              placeholder="Modifiez le contenu de votre publication..." required><?php 
                        echo $_SESSION['old_input']['contenu'] ?? htmlspecialchars($this->publicationModel->contenu); 
                    ?></textarea>
                    <?php if (isset($_SESSION['form_errors']['contenu'])): ?>
                        <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['contenu']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Images existantes -->
                <?php if (!empty($this->publicationModel->images) && is_array($this->publicationModel->images)): ?>
                <div class="mb-4">
                    <label class="form-label">Images actuelles</label>
                    <div class="row g-2" id="current-images">
                        <?php foreach ($this->publicationModel->images as $index => $image): ?>
                            <?php if (!empty($image)): ?>
                            <div class="col-4 col-md-3">
                                <div class="position-relative">
                                    <img src="<?php echo htmlspecialchars($image); ?>" 
                                         class="img-fluid rounded" 
                                         style="height: 100px; object-fit: cover;"
                                         alt="Image publication <?php echo $index + 1; ?>">
                                    <button type="button" 
                                            class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                            onclick="removeImage(this, '<?php echo htmlspecialchars($image); ?>')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="removed_images" name="removed_images" value="">
                </div>
                <?php endif; ?>

                <!-- Upload de nouvelles images -->
                <div class="mb-4">
                    <label for="images" class="form-label">Ajouter de nouvelles images</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple 
                           accept="image/*">
                    <div class="form-text">
                        Vous pouvez sélectionner plusieurs images. Formats acceptés: JPG, PNG, GIF.
                    </div>
                    
                    <!-- Aperçu des nouvelles images -->
                    <div id="image-preview" class="mt-3 row g-2 d-none">
                        <!-- Les aperçus des nouvelles images seront ajoutés ici -->
                    </div>
                </div>

                <!-- Informations sur la publication -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Informations</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Communauté:</small>
                                <p class="mb-1"><?php echo htmlspecialchars($this->publicationModel->communaute_nom); ?></p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Date de publication:</small>
                                <p class="mb-1"><?php echo date('d/m/Y à H:i', strtotime($this->publicationModel->date_publication)); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex gap-2 justify-content-end">
                    <a href="/projet/communautes/<?php echo $this->publicationModel->communaute_id; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gestion de l'aperçu des nouvelles images
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    preview.classList.add('d-none');
    
    const files = e.target.files;
    if (files.length > 0) {
        preview.classList.remove('d-none');
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4 col-md-3';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        }
    }
});

// Gestion de la suppression des images existantes
function removeImage(button, imageUrl) {
    const container = button.closest('.col-4, .col-md-3');
    container.style.opacity = '0.5';
    container.style.pointerEvents = 'none';
    
    // Ajouter l'image à la liste des images supprimées
    const removedInput = document.getElementById('removed_images');
    const removedImages = removedInput.value ? removedInput.value.split(',') : [];
    removedImages.push(imageUrl);
    removedInput.value = removedImages.join(',');
    
    // Optionnel: animation de suppression
    setTimeout(() => {
        container.remove();
    }, 300);
}
</script>

<style>
.community-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

#current-images img,
#image-preview img {
    transition: transform 0.3s ease;
}

#current-images img:hover,
#image-preview img:hover {
    transform: scale(1.05);
}

.btn-danger {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}
</style>