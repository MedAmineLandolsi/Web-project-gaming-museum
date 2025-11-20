<?php
// Récupérer les communautés pour le select
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
        <form action="/projet/admin/publications/create" method="POST">
            <div class="mb-3">
                <label for="communaute_id" class="form-label">Communauté *</label>
                <select class="form-control <?php echo isset($_SESSION['form_errors']['communaute_id']) ? 'is-invalid' : ''; ?>" 
                        id="communaute_id" name="communaute_id" required>
                    <option value="">Sélectionnez une communauté</option>
                    <?php foreach ($communautes as $communaute): ?>
                        <option value="<?php echo $communaute['id']; ?>" 
                                <?php echo ($_SESSION['old_input']['communaute_id'] ?? '') == $communaute['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($communaute['nom']); ?> (<?php echo htmlspecialchars($communaute['categorie']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($_SESSION['form_errors']['communaute_id'])): ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['communaute_id']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu *</label>
                <textarea class="form-control <?php echo isset($_SESSION['form_errors']['contenu']) ? 'is-invalid' : ''; ?>" 
                          id="contenu" name="contenu" rows="6" 
                          placeholder="Partagez vos pensées, idées ou questions..." required><?php echo $_SESSION['old_input']['contenu'] ?? ''; ?></textarea>
                <?php if (isset($_SESSION['form_errors']['contenu'])): ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['contenu']; ?></div>
                <?php endif; ?>
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

<style>
.form-control {
    background: var(--input-bg);
    border: 1px solid var(--border);
    color: var(--light);
    transition: all 0.3s ease;
    resize: vertical;
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

.invalid-feedback {
    color: var(--danger);
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.is-invalid {
    border-color: var(--danger) !important;
    box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1) !important;
}
</style>