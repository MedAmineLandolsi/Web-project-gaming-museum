<div class="card">
    <div class="card-header">
        <h4 class="text-primary">
            <i class="fas fa-user-plus me-2"></i>Créer un nouveau membre
        </h4>
    </div>
    <div class="card-body">
        <form action="/projet/admin/membres/create" method="POST" id="membreForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control" 
                               id="prenom" name="prenom" 
                               value="<?php echo $_SESSION['old_input']['prenom'] ?? ''; ?>" 
                               placeholder="Entrez le prénom">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" 
                               id="nom" name="nom" 
                               value="<?php echo $_SESSION['old_input']['nom'] ?? ''; ?>" 
                               placeholder="Entrez le nom">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email *</label>
                <input type="text" class="form-control" 
                       id="email" name="email" 
                       value="<?php echo $_SESSION['old_input']['email'] ?? ''; ?>" 
                       placeholder="exemple@email.com">
            </div>

            <div class="mb-3">
                <label for="mot_de_passe" class="form-label">Mot de passe *</label>
                <input type="password" class="form-control" 
                       id="mot_de_passe" name="mot_de_passe" 
                       placeholder="Minimum 6 caractères">
                <div class="form-text">Le mot de passe doit contenir au moins 6 caractères.</div>
            </div>

            <div class="mb-3">
                <label for="statut" class="form-label">Statut *</label>
                <select class="form-control" id="statut" name="statut">
                    <option value="">Sélectionnez un statut</option>
                    <option value="actif" <?php echo ($_SESSION['old_input']['statut'] ?? '') == 'actif' ? 'selected' : ''; ?>>Actif</option>
                    <option value="inactif" <?php echo ($_SESSION['old_input']['statut'] ?? '') == 'inactif' ? 'selected' : ''; ?>>Inactif</option>
                    <option value="suspendu" <?php echo ($_SESSION['old_input']['statut'] ?? '') == 'suspendu' ? 'selected' : ''; ?>>Suspendu</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar (URL)</label>
                <input type="text" class="form-control" id="avatar" name="avatar" 
                       value="<?php echo $_SESSION['old_input']['avatar'] ?? ''; ?>" 
                       placeholder="https://exemple.com/avatar.jpg">
                <div class="form-text">URL de l'image de profil du membre.</div>
            </div>

            <div class="mb-4">
                <label for="bio" class="form-label">Biographie</label>
                <textarea class="form-control" id="bio" name="bio" rows="4" 
                          placeholder="Description du membre..."><?php echo $_SESSION['old_input']['bio'] ?? ''; ?></textarea>
                <div class="form-text">Une brève description du membre (optionnel).</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Créer le membre
                </button>
                <a href="/projet/admin/membres" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('membreForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const prenom = document.getElementById('prenom').value.trim();
        const nom = document.getElementById('nom').value.trim();
        const email = document.getElementById('email').value.trim();
        const motDePasse = document.getElementById('mot_de_passe').value;
        const statut = document.getElementById('statut').value;
        const avatar = document.getElementById('avatar').value.trim();
        
        let errors = [];
        
        if (!prenom) {
            errors.push('Le prénom est obligatoire');
        } else if (prenom.length > 50) {
            errors.push('Le prénom ne doit pas dépasser 50 caractères');
        } else if (!/^[a-zA-ZÀ-ÿ\s\-]+$/.test(prenom)) {
            errors.push('Le prénom ne doit contenir que des lettres');
        }
        
        if (!nom) {
            errors.push('Le nom est obligatoire');
        } else if (nom.length > 50) {
            errors.push('Le nom ne doit pas dépasser 50 caractères');
        } else if (!/^[a-zA-ZÀ-ÿ\s\-]+$/.test(nom)) {
            errors.push('Le nom ne doit contenir que des lettres');
        }
        
        if (!email) {
            errors.push('L\'email est obligatoire');
        } else if (!isValidEmail(email)) {
            errors.push('Format d\'email invalide');
        }
        
        if (!motDePasse) {
            errors.push('Le mot de passe est obligatoire');
        } else if (motDePasse.length < 6) {
            errors.push('Le mot de passe doit faire au moins 6 caractères');
        }
        
        if (!statut) {
            errors.push('Le statut est obligatoire');
        }
        
        if (avatar && !isValidUrl(avatar)) {
            errors.push('L\'URL de l\'avatar n\'est pas valide');
        }
        
        if (errors.length > 0) {
            showErrors(errors);
        } else {
            form.submit();
        }
    });
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
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