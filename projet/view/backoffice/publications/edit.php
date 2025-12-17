<?php
// Récupérer les communautés pour le select
$communautes_result = $this->communauteModel->read();
$communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);

// Préparer la valeur des images pour le champ (string séparée par des virgules)
$imagesValue = '';
if (isset($_SESSION['old_input']['images'])) {
    $raw = $_SESSION['old_input']['images'];
    if (is_array($raw)) {
        $imagesValue = implode(', ', $raw);
    } else {
        $imagesValue = $raw;
    }
} else {
    $images = $this->publicationModel->images ?? [];
    if (is_array($images)) {
        $imagesValue = implode(', ', $images);
    } else {
        $imagesValue = $images;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h4>Modifier la publication</h4>
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/admin/publications/<?php echo $this->publicationModel->id; ?>/edit" method="POST" data-validate="true">
            <div class="mb-3">
                <label for="communaute_id" class="form-label">Communauté *</label>
                <select class="form-control <?php echo isset($_SESSION['form_errors']['communaute_id']) ? 'is-invalid' : ''; ?>" 
                    id="communaute_id" name="communaute_id" data-required>
                    <option value="">Sélectionnez une communauté</option>
                    <?php foreach ($communautes as $communaute): ?>
                        <option value="<?php echo $communaute['id']; ?>" 
                                <?php echo ($_SESSION['old_input']['communaute_id'] ?? $this->publicationModel->communaute_id) == $communaute['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($communaute['nom']); ?>
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
                          id="contenu" name="contenu" rows="6" data-required><?php echo isset($_SESSION['old_input']['contenu']) ? htmlspecialchars($_SESSION['old_input']['contenu']) : htmlspecialchars($this->publicationModel->contenu); ?></textarea>
                <?php if (isset($_SESSION['form_errors']['contenu'])): ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['contenu']; ?></div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="likes" class="form-label">Likes</label>
                           <input type="number" class="form-control <?php echo isset($_SESSION['form_errors']['likes']) ? 'is-invalid' : ''; ?>" 
                               id="likes" name="likes" 
                               value="<?php echo isset($_SESSION['old_input']['likes']) ? htmlspecialchars($_SESSION['old_input']['likes']) : htmlspecialchars($this->publicationModel->likes); ?>">
                        <?php if (isset($_SESSION['form_errors']['likes'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['likes']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="commentaires" class="form-label">Commentaires</label>
                           <input type="number" class="form-control <?php echo isset($_SESSION['form_errors']['commentaires']) ? 'is-invalid' : ''; ?>" 
                               id="commentaires" name="commentaires" 
                               value="<?php echo isset($_SESSION['old_input']['commentaires']) ? htmlspecialchars($_SESSION['old_input']['commentaires']) : htmlspecialchars($this->publicationModel->commentaires); ?>">
                        <?php if (isset($_SESSION['form_errors']['commentaires'])): ?>
                            <div class="invalid-feedback"><?php echo $_SESSION['form_errors']['commentaires']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="images" class="form-label">Images (URLs séparées par des virgules)</label>
                  <input type="text" class="form-control" id="images" name="images" 
                      value="<?php echo htmlspecialchars($imagesValue); ?>" 
                      placeholder="https://example.com/image1.jpg, https://example.com/image2.jpg">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
                <a href="<?php echo BASE_URL; ?>/admin/publications" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>