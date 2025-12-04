<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="community-card p-4">
            <div class="text-center mb-4">
                <h1 class="h2">Partager une publication</h1>
                <p class="text-muted">Partagez vos idées, photos ou questions avec la communauté</p>
            </div>

            <form action="/projet/publications/create" method="POST" enctype="multipart/form-data">
                <!-- Sélection de la communauté -->
                <div class="mb-4">
                    <label for="communaute_id" class="form-label">Communauté *</label>
                    <select class="form-control <?php echo isset($_SESSION['form_errors']['communaute_id']) ? 'is-invalid' : ''; ?>" 
                            id="communaute_id" name="communaute_id" required>
                        <option value="">Choisir une communauté</option>
                        <?php 
                        $communaute_id = $_GET['communaute_id'] ?? ($_SESSION['old_input']['communaute_id'] ?? '');
                        foreach ($communautes as $communaute): 
                        ?>
                            <option value="<?php echo $communaute['id']; ?>" 
                                    <?php echo $communaute_id == $communaute['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($communaute['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($_SESSION['form_errors']['communaute_id'])): ?>
                        <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['communaute_id']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Contenu de la publication -->
                <div class="mb-4">
                    <label for="contenu" class="form-label">Que voulez-vous partager ? *</label>
                    <textarea class="form-control <?php echo isset($_SESSION['form_errors']['contenu']) ? 'is-invalid' : ''; ?>" 
                              id="contenu" name="contenu" rows="6" 
                              placeholder="Partagez vos pensées, questions ou idées..." required><?php echo $_SESSION['old_input']['contenu'] ?? ''; ?></textarea>
                    <?php if (isset($_SESSION['form_errors']['contenu'])): ?>
                        <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['contenu']; ?></div>
                    <?php endif; ?>

                    <!-- Vérification IA supprimée -->
                </div>

                <!-- Upload d'images -->
                <div class="mb-4">
                    <label for="images" class="form-label">Images</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple 
                           accept="image/*">
                    <div class="form-text">
                        Vous pouvez sélectionner plusieurs images. Formats acceptés: JPG, PNG, GIF.
                    </div>
                    
                    <!-- Aperçu des images -->
                    <div id="image-preview" class="mt-3 row g-2 d-none">
                        <!-- Les aperçus d'images seront ajoutés ici -->
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex gap-2 justify-content-end">
                    <a href="/projet/communautes/<?php echo $communaute_id; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-share me-2"></i>Publier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gestion de l'aperçu des images
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
                    col.className = 'col-4';
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
</script>