<div class="card">
    <div class="card-header">
        <h4 class="text-primary">
            <i class="fas fa-plus me-2"></i>Créer une nouvelle communauté
        </h4>
    </div>
    <div class="card-body">
        <form action="/projet/admin/communautes/create" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control <?php echo isset($_SESSION['form_errors']['nom']) ? 'is-invalid' : ''; ?>" 
                               id="nom" name="nom" 
                               value="<?php echo $_SESSION['old_input']['nom'] ?? ''; ?>" 
                               placeholder="Nom de la communauté" required>
                        <?php if (isset($_SESSION['form_errors']['nom'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['nom']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="categorie" class="form-label">Catégorie *</label>
                        <select class="form-control <?php echo isset($_SESSION['form_errors']['categorie']) ? 'is-invalid' : ''; ?>" 
                                id="categorie" name="categorie" required>
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="Technologie" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Technologie' ? 'selected' : ''; ?>>Technologie</option>
                            <option value="Art" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Art' ? 'selected' : ''; ?>>Art</option>
                            <option value="Sport" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Sport' ? 'selected' : ''; ?>>Sport</option>
                            <option value="Musique" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Musique' ? 'selected' : ''; ?>>Musique</option>
                            <option value="Jeux" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Jeux' ? 'selected' : ''; ?>>Jeux</option>
                            <option value="Éducation" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Éducation' ? 'selected' : ''; ?>>Éducation</option>
                            <option value="Autre" <?php echo ($_SESSION['old_input']['categorie'] ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                        </select>
                        <?php if (isset($_SESSION['form_errors']['categorie'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['categorie']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description *</label>
                <textarea class="form-control <?php echo isset($_SESSION['form_errors']['description']) ? 'is-invalid' : ''; ?>" 
                          id="description" name="description" rows="4" 
                          placeholder="Décrivez le but de votre communauté..." required><?php echo $_SESSION['old_input']['description'] ?? ''; ?></textarea>
                <?php if (isset($_SESSION['form_errors']['description'])): ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['description']; ?></div>
                <?php endif; ?>
                <div class="form-text">La description doit contenir au moins 10 caractères.</div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="visibilite" class="form-label">Visibilité</label>
                        <select class="form-control <?php echo isset($_SESSION['form_errors']['visibilite']) ? 'is-invalid' : ''; ?>" 
                                id="visibilite" name="visibilite">
                            <option value="publique" <?php echo ($_SESSION['old_input']['visibilite'] ?? 'publique') == 'publique' ? 'selected' : ''; ?>>Publique - Tout le monde peut voir et rejoindre</option>
                            <option value="privee" <?php echo ($_SESSION['old_input']['visibilite'] ?? '') == 'privee' ? 'selected' : ''; ?>>Privée - Visible mais besoin d'approbation</option>
                            <option value="cachee" <?php echo ($_SESSION['old_input']['visibilite'] ?? '') == 'cachee' ? 'selected' : ''; ?>>Cachée - Seulement sur invitation</option>
                        </select>
                        <?php if (isset($_SESSION['form_errors']['visibilite'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['visibilite']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Avatar (URL)</label>
                        <input type="url" class="form-control <?php echo isset($_SESSION['form_errors']['avatar']) ? 'is-invalid' : ''; ?>" 
                               id="avatar" name="avatar" 
                               value="<?php echo $_SESSION['old_input']['avatar'] ?? ''; ?>" 
                               placeholder="https://exemple.com/avatar.jpg">
                        <?php if (isset($_SESSION['form_errors']['avatar'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['avatar']; ?></div>
                        <?php endif; ?>
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

<style>
.form-control {
    background: var(--input-bg);
    border: 1px solid var(--border);
    color: var(--light);
    transition: all 0.3s ease;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1);
    color: var(--light);
}

.form-control::placeholder {
    color: var(--gray);
    opacity: 0.7;
}

.form-label {
    color: var(--light);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-text {
    color: var(--gray);
    font-size: 0.85rem;
}

.card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 15px;
    box-shadow: 0 8px 32px var(--shadow);
}

.card-header {
    border-bottom: 1px solid var(--border);
    background: rgba(0, 255, 136, 0.05);
}

.btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--primary);
    border: none;
    color: var(--dark);
}

.btn-primary:hover {
    background: #00cc66;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 255, 136, 0.4);
}

.btn-secondary {
    background: transparent;
    border: 2px solid var(--primary);
    color: var(--light);
}

.btn-secondary:hover {
    background: var(--primary);
    color: var(--dark);
    transform: translateY(-2px);
}

select.form-control {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2300ff88' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 16px 12px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
</style>