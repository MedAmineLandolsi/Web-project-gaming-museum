<div class="card">
    <div class="card-header">
        <h4 class="text-primary">
            <i class="fas fa-plus me-2"></i>Créer une nouvelle communauté
        </h4>
    </div>
    <div class="card-body">
        <form action="/projet/admin/communautes/create" method="POST" id="communauteForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" 
                               id="nom" name="nom" 
                               value="<?php echo $_SESSION['old_input']['nom'] ?? ''; ?>" 
                               placeholder="Nom de la communauté">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="categorie" class="form-label">Catégorie *</label>
                        <select class="form-control" id="categorie" name="categorie">
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="Technologie" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Technologie' ? 'selected' : ''; ?>>Technologie</option>
                            <option value="Art" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Art' ? 'selected' : ''; ?>>Art</option>
                            <option value="Sport" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Sport' ? 'selected' : ''; ?>>Sport</option>
                            <option value="Musique" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Musique' ? 'selected' : ''; ?>>Musique</option>
                            <option value="Jeux" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Jeux' ? 'selected' : ''; ?>>Jeux</option>
                            <option value="Éducation" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Éducation' ? 'selected' : ''; ?>>Éducation</option>
                            <option value="Autre" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description *</label>
                <textarea class="form-control" 
                          id="description" name="description" rows="4" 
                          placeholder="Décrivez le but de votre communauté..."><?php echo $_SESSION['old_input']['description'] ?? ''; ?></textarea>
                <div class="form-text">La description doit contenir au moins 10 caractères.</div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="visibilite" class="form-label">Visibilité</label>
                        <select class="form-control" id="visibilite" name="visibilite">
                            <option value="publique" <?php echo ($_SESSION['old_input']['visibilite'] ?? 'publique') == 'publique' ? 'selected' : ''; ?>>Publique - Tout le monde peut voir et rejoindre</option>
                            <option value="privee" <?php echo ($_SESSION['old_input']['visibilite'] ?? '') == 'privee' ? 'selected' : ''; ?>>Privée - Visible mais besoin d'approbation</option>
                            <option value="cachee" <?php echo ($_SESSION['old_input']['visibilite'] ?? '') == 'cachee' ? 'selected' : ''; ?>>Cachée - Seulement sur invitation</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Avatar (URL)</label>
                        <input type="text" class="form-control" 
                               id="avatar" name="avatar" 
                               value="<?php echo $_SESSION['old_input']['avatar'] ?? ''; ?>" 
                               placeholder="https://exemple.com/avatar.jpg">
                        <div class="form-text">URL de l'image représentant la communauté.</div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="regles" class="form-label">Règles de la communauté</label>
                <textarea class="form-control" id="regles" name="regles" rows="4" 
                          placeholder="Définissez les règles de comportement dans votre communauté..."><?php echo $_SESSION['old_input']['regles'] ?? ''; ?></textarea>
                <div class="form-text">Ces règles seront affichées à tous les membres (optionnel).</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Créer la communauté
                </button>
                <a href="/projet/admin/communautes" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('communauteForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const nom = document.getElementById('nom').value.trim();
        const categorie = document.getElementById('categorie').value;
        const description = document.getElementById('description').value.trim();
        const visibilite = document.getElementById('visibilite').value;
        const avatar = document.getElementById('avatar').value.trim();
        const regles = document.getElementById('regles').value.trim();
        
        let errors = [];
        
        // Validation nom
        if (!nom) {
            errors.push('Le nom est obligatoire');
        } else if (nom.length > 100) {
            errors.push('Le nom ne doit pas dépasser 100 caractères');
        } else if (!/^[a-zA-ZÀ-ÿ0-9\s\-_&]+$/.test(nom)) {
            errors.push('Le nom contient des caractères non autorisés');
        }
        
        // Validation catégorie
        if (!categorie) {
            errors.push('La catégorie est obligatoire');
        } else if (categorie.length > 50) {
            errors.push('La catégorie ne doit pas dépasser 50 caractères');
        }
        
        // Validation description
        if (!description) {
            errors.push('La description est obligatoire');
        } else if (description.length < 10) {
            errors.push('La description doit faire au moins 10 caractères');
        } else if (description.length > 500) {
            errors.push('La description ne doit pas dépasser 500 caractères');
        }
        
        // Validation visibilité
        const visibilitesValides = ['publique', 'privee', 'cachee'];
        if (visibilite && !visibilitesValides.includes(visibilite)) {
            errors.push('La visibilité sélectionnée est invalide');
        }
        
        // Validation avatar (URL)
        if (avatar) {
            if (!isValidUrl(avatar)) {
                errors.push('L\'URL de l\'avatar n\'est pas valide');
            } else if (avatar.length > 255) {
                errors.push('L\'URL de l\'avatar est trop longue');
            }
        }
        
        // Validation règles
        if (regles && regles.length > 1000) {
            errors.push('Les règles ne doivent pas dépasser 1000 caractères');
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