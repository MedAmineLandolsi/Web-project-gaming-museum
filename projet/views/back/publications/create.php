<?php
$communautes_result = $this->communauteModel->read();
$communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h4 class="text-primary">
            <i class="fas fa-plus me-2"></i>Créer une nouvelle publication
        </h4>
    </div>
    <div class="card-body">
        <form action="/projet/admin/publications/create" method="POST" id="publicationForm">
            <div class="mb-3">
                <label for="communaute_id" class="form-label">Communauté *</label>
                <select class="form-control" id="communaute_id" name="communaute_id">
                    <option value="">Sélectionnez une communauté</option>
                    <?php foreach ($communautes as $communaute): ?>
                        <option value="<?php echo $communaute['id']; ?>" 
                                <?php echo ($_SESSION['old_input']['communaute_id'] ?? '') == $communaute['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($communaute['nom']); ?> (<?php echo htmlspecialchars($communaute['categorie']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu *</label>
                <textarea class="form-control" 
                          id="contenu" name="contenu" rows="6" 
                          placeholder="Partagez vos pensées, idées ou questions..."><?php echo $_SESSION['old_input']['contenu'] ?? ''; ?></textarea>
                <div class="form-text">Le contenu doit contenir entre 5 et 1000 caractères.</div>
            </div>

            <div class="mb-4">
                <label for="images" class="form-label">Images (URLs séparées par des virgules)</label>
                <textarea class="form-control" id="images" name="images" rows="3" 
                          placeholder="https://exemple.com/image1.jpg, https://exemple.com/image2.jpg"><?php echo $_SESSION['old_input']['images'] ?? ''; ?></textarea>
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Entrez les URLs des images séparées par des virgules. Maximum 4 images recommandées.
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Créer la publication
                </button>
                <a href="/projet/admin/publications" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('publicationForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const communauteId = document.getElementById('communaute_id').value;
        const contenu = document.getElementById('contenu').value.trim();
        const images = document.getElementById('images').value.trim();
        
        let errors = [];
        
        if (!communauteId) {
            errors.push('La communauté est obligatoire');
        }
        
        if (!contenu) {
            errors.push('Le contenu est obligatoire');
        } else if (contenu.length < 5) {
            errors.push('Le contenu doit faire au moins 5 caractères');
        } else if (contenu.length > 1000) {
            errors.push('Le contenu ne doit pas dépasser 1000 caractères');
        }
        
        if (images) {
            const imageUrls = images.split(',').map(url => url.trim()).filter(url => url);
            for (let url of imageUrls) {
                if (!isValidUrl(url)) {
                    errors.push(`L'URL "${url}" n'est pas valide`);
                    break;
                }
            }
        }
        
        if (errors.length > 0) {
            showErrors(errors);
        } else {
            form.submit();
        }
    });
    
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
    
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
        
        form.insertAdjacentHTML('beforebegin', errorHtml);
    }
});
</script>