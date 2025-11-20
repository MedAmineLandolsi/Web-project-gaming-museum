<div class="card">
    <div class="card-header">
        <h4 class="text-primary">
            <i class="fas fa-user-plus me-2"></i>Créer un nouveau membre
        </h4>
    </div>
    <div class="card-body">
        <form action="/projet/admin/membres/create" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control <?php echo isset($_SESSION['form_errors']['prenom']) ? 'is-invalid' : ''; ?>" 
                               id="prenom" name="prenom" 
                               value="<?php echo $_SESSION['old_input']['prenom'] ?? ''; ?>" 
                               placeholder="Entrez le prénom" required>
                        <?php if (isset($_SESSION['form_errors']['prenom'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['prenom']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control <?php echo isset($_SESSION['form_errors']['nom']) ? 'is-invalid' : ''; ?>" 
                               id="nom" name="nom" 
                               value="<?php echo $_SESSION['old_input']['nom'] ?? ''; ?>" 
                               placeholder="Entrez le nom" required>
                        <?php if (isset($_SESSION['form_errors']['nom'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['nom']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email *</label>
                <input type="email" class="form-control <?php echo isset($_SESSION['form_errors']['email']) ? 'is-invalid' : ''; ?>" 
                       id="email" name="email" 
                       value="<?php echo $_SESSION['old_input']['email'] ?? ''; ?>" 
                       placeholder="exemple@email.com" required>
                <?php if (isset($_SESSION['form_errors']['email'])): ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="mot_de_passe" class="form-label">Mot de passe *</label>
                <input type="password" class="form-control <?php echo isset($_SESSION['form_errors']['mot_de_passe']) ? 'is-invalid' : ''; ?>" 
                       id="mot_de_passe" name="mot_de_passe" 
                       placeholder="Minimum 6 caractères" required>
                <?php if (isset($_SESSION['form_errors']['mot_de_passe'])): ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['mot_de_passe']; ?></div>
                <?php endif; ?>
                <div class="form-text">Le mot de passe doit contenir au moins 6 caractères.</div>
            </div>

            <div class="mb-3">
                <label for="statut" class="form-label">Statut *</label>
                <select class="form-control <?php echo isset($_SESSION['form_errors']['statut']) ? 'is-invalid' : ''; ?>" 
                        id="statut" name="statut" required>
                    <option value="">Sélectionnez un statut</option>
                    <option value="actif" <?php echo ($_SESSION['old_input']['statut'] ?? '') == 'actif' ? 'selected' : ''; ?>>Actif</option>
                    <option value="inactif" <?php echo ($_SESSION['old_input']['statut'] ?? '') == 'inactif' ? 'selected' : ''; ?>>Inactif</option>
                    <option value="suspendu" <?php echo ($_SESSION['old_input']['statut'] ?? '') == 'suspendu' ? 'selected' : ''; ?>>Suspendu</option>
                </select>
                <?php if (isset($_SESSION['form_errors']['statut'])): ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['statut']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar (URL)</label>
                <input type="url" class="form-control" id="avatar" name="avatar" 
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
</style>