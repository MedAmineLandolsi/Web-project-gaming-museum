<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px;">
                            <span class="text-dark fw-bold fs-5">
                                <?php echo strtoupper(substr($this->publicationModel->auteur_nom, 0, 2)); ?>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0"><?php echo htmlspecialchars($this->publicationModel->auteur_nom); ?></h5>
                            <small class="text-muted">
                                <?php echo date('d/m/Y à H:i', strtotime($this->publicationModel->date_publication)); ?>
                            </small>
                        </div>
                    </div>
                    <span class="badge bg-secondary fs-6"><?php echo htmlspecialchars($this->publicationModel->communaute_nom); ?></span>
                </div>
                
                <div class="publication-content mb-4">
                    <?php echo nl2br(htmlspecialchars($this->publicationModel->contenu)); ?>
                </div>
                
                <?php if (!empty($this->publicationModel->images)): ?>
                <div class="mb-4">
                    <h6>Images jointes</h6>
                    <div class="bg-dark rounded p-3 text-center">
                        <i class="fas fa-image fa-3x text-muted"></i>
                        <p class="text-muted mt-2">Contenu multimédia</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                    <div class="d-flex gap-4 text-muted">
                        <span><i class="fas fa-heart"></i> <?php echo $this->publicationModel->likes; ?> likes</span>
                        <span><i class="fas fa-comment"></i> <?php echo $this->publicationModel->commentaires; ?> commentaires</span>
                    </div>
                    
                    <div>
                        <a href="/projet/publications" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Retour aux publications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>