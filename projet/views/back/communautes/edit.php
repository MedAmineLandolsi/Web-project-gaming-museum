<div class="card">
    <div class="card-header">
        <h4>Modifier la communauté</h4>
    </div>
    <div class="card-body">
        <form action="/projet/admin/communautes/<?php echo $this->communauteModel->id; ?>/edit" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control <?php echo isset($_SESSION['form_errors']['nom']) ? 'is-invalid' : ''; ?>" 
                               id="nom" name="nom" 
                               value="<?php echo $_SESSION['old_input']['nom'] ?? $this->communauteModel->nom; ?>" required>
                        <?php if (isset($_SESSION['form_errors']['nom'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['nom']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="categorie" class="form-label">Catégorie *</label>
                        <input type="text" class="form-control <?php echo isset($_SESSION['form_errors']['categorie']) ? 'is-invalid' : ''; ?>" 
                               id="categorie" name="categorie" 
                               value="<?php echo $_SESSION['old_input']['categorie'] ?? $this->communauteModel->categorie; ?>" required>
                        <?php if (isset($_SESSION['form_errors']['categorie'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['categorie']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description *</label>
                <textarea class="form-control <?php echo isset($_SESSION['form_errors']['description']) ? 'is-invalid' : ''; ?>" 
                          id="description" name="description" rows="3" required><?php echo $_SESSION['old_input']['description'] ?? $this->communauteModel->description; ?></textarea>
                <?php if (isset($_SESSION['form_errors']['description'])): ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['description']; ?></div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="visibilite" class="form-label">Visibilité</label>
                        <select class="form-control <?php echo isset($_SESSION['form_errors']['visibilite']) ? 'is-invalid' : ''; ?>" 
                                id="visibilite" name="visibilite">
                            <option value="publique" <?php echo ($_SESSION['old_input']['visibilite'] ?? $this->communauteModel->visibilite) == 'publique' ? 'selected' : ''; ?>>Publique</option>
                            <option value="privee" <?php echo ($_SESSION['old_input']['visibilite'] ?? $this->communauteModel->visibilite) == 'privee' ? 'selected' : ''; ?>>Privée</option>
                            <option value="cachee" <?php echo ($_SESSION['old_input']['visibilite'] ?? $this->communauteModel->visibilite) == 'cachee' ? 'selected' : ''; ?>>Cachée</option>
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
                               value="<?php echo $_SESSION['old_input']['avatar'] ?? $this->communauteModel->avatar; ?>">
                        <?php if (isset($_SESSION['form_errors']['avatar'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['avatar']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="regles" class="form-label">Règles de la communauté</label>
                <textarea class="form-control" id="regles" name="regles" rows="4"><?php echo $_SESSION['old_input']['regles'] ?? $this->communauteModel->regles; ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
                <a href="/projet/admin/communautes" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>