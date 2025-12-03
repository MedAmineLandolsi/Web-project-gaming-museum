<div class="card">
    <div class="card-header">
        <h4>Modifier le membre</h4>
    </div>
    <div class="card-body">
        <form action="/projet/admin/membres/<?php echo $this->membreModel->id; ?>/edit" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control <?php echo isset($_SESSION['form_errors']['prenom']) ? 'is-invalid' : ''; ?>" 
                               id="prenom" name="prenom" 
                               value="<?php echo $_SESSION['old_input']['prenom'] ?? $this->membreModel->prenom; ?>" required>
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
                               value="<?php echo $_SESSION['old_input']['nom'] ?? $this->membreModel->nom; ?>" required>
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
                       value="<?php echo $_SESSION['old_input']['email'] ?? $this->membreModel->email; ?>" required>
                <?php if (isset($_SESSION['form_errors']['email'])): ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="statut" class="form-label">Statut *</label>
                <select class="form-control <?php echo isset($_SESSION['form_errors']['statut']) ? 'is-invalid' : ''; ?>" 
                        id="statut" name="statut" required>
                    <option value="">Sélectionnez un statut</option>
                    <option value="actif" <?php echo ($_SESSION['old_input']['statut'] ?? $this->membreModel->statut) == 'actif' ? 'selected' : ''; ?>>Actif</option>
                    <option value="inactif" <?php echo ($_SESSION['old_input']['statut'] ?? $this->membreModel->statut) == 'inactif' ? 'selected' : ''; ?>>Inactif</option>
                    <option value="suspendu" <?php echo ($_SESSION['old_input']['statut'] ?? $this->membreModel->statut) == 'suspendu' ? 'selected' : ''; ?>>Suspendu</option>
                </select>
                <?php if (isset($_SESSION['form_errors']['statut'])): ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['statut']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar (URL)</label>
                <input type="url" class="form-control" id="avatar" name="avatar" 
                       value="<?php echo $_SESSION['old_input']['avatar'] ?? $this->membreModel->avatar; ?>">
            </div>

            <div class="mb-3">
                <label for="bio" class="form-label">Biographie</label>
                <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo $_SESSION['old_input']['bio'] ?? $this->membreModel->bio; ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
                <a href="/projet/admin/membres" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>