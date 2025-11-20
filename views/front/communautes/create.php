<div class="container" style="padding: 6rem 0 4rem;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-container">
                <div class="text-center mb-4">
                    <h1 class="text-primary">Créer une nouvelle communauté</h1>
                    <p class="text-muted">Rassemblez des personnes autour de vos centres d'intérêt</p>
                </div>

                <form action="/projet/communautes/create" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom de la communauté *</label>
                                <input type="text" class="form-control <?php echo isset($_SESSION['form_errors']['nom']) ? 'is-invalid' : ''; ?>" 
                                       id="nom" name="nom" 
                                       value="<?php echo $_SESSION['old_input']['nom'] ?? ''; ?>" 
                                       placeholder="Ex: Développeurs Web" required>
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
                                    <option value="">Choisir une catégorie</option>
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
                                       placeholder="https://example.com/avatar.jpg">
                                <?php if (isset($_SESSION['form_errors']['avatar'])): ?>
                                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['avatar']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="regles" class="form-label">Règles de la communauté</label>
                        <textarea class="form-control" id="regles" name="regles" rows="4" 
                                  placeholder="Définissez les règles de comportement dans votre communauté..."><?php echo $_SESSION['old_input']['regles'] ?? ''; ?></textarea>
                        <div class="form-text">Ces règles seront affichées à tous les membres.</div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="/projet/communautes" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer la communauté
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>