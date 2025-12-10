
<?php
class PublicationController {
    private $publicationModel;
    private $communauteModel;
    private $validation;
    private $membreModel;

    public function __construct($db) {
        $this->publicationModel = new Publication($db);
        $this->communauteModel = new Communaute($db);
        $this->validation = new Validation();
        $this->membreModel = new Membre($db);
    }

    // === FRONT OFFICE ===

    public function indexFront() {
        $order_by = $_GET['order_by'] ?? 'date_publication';
        $order_dir = $_GET['order_dir'] ?? 'DESC';
        $communaute_id = $_GET['communaute'] ?? null;
        
        if ($communaute_id) {
            $publications = $this->publicationModel->readByCommunauteOrdered($communaute_id, $order_by, $order_dir);
            $this->communauteModel->id = $communaute_id;
            $communaute_info = $this->communauteModel->read_single() ? $this->communauteModel : null;
        } else {
            $publications = $this->publicationModel->readOrdered($order_by, $order_dir);
            $communaute_info = null;
        }
        
        $title = $communaute_info ? "Publications - " . $communaute_info->nom : "Publications récentes";

        if (empty($publications) || !is_array($publications)) {
            $fallback = $this->publicationModel->getLatest(10);
            if (!empty($fallback)) {
                $publications = $fallback;
            }
        }
        
        ob_start();
        include dirname(__DIR__) . '/view/frontoffice/publications/index.php';
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/frontoffice/layout.php';
    }

    public function showFront($id) {
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->read_single()) {
            $title = "Publication de " . $this->publicationModel->auteur_nom;
            ob_start();
            include dirname(__DIR__) . '/view/frontoffice/publications/show.php';
            $content = ob_get_clean();
            include dirname(__DIR__) . '/view/frontoffice/layout.php';
        } else {
            $this->showError("Publication non trouvée");
        }
    }

    public function createFront() {
        $communautes_result = $this->communauteModel->read();
        $communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);
        
        // Intégrer l'assistant IA
        $aiAssistant = $this->addAIAssistantToForm();
        
        $title = "Partager une publication";
        ob_start();
        ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php echo $aiAssistant; ?>
                
                <div class="community-card p-4">
                    <div class="text-center mb-4">
                        <h1 class="h2">Partager une publication</h1>
                        <p class="text-muted">Partagez vos idées, photos ou questions avec la communauté</p>
                    </div>

                    <?php if (isset($_SESSION['form_errors'])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($_SESSION['form_errors'] as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/projet/publications/create" method="POST" enctype="multipart/form-data" id="publicationForm">
                        <!-- Sélection de la communauté -->
                        <div class="mb-4">
                            <label for="communaute_id" class="form-label">Communauté *</label>
                            <select class="form-control" id="communaute_id" name="communaute_id" required>
                                <option value="">Choisir une communauté</option>
                                <?php 
                                $communaute_id = $_GET['communaute_id'] ?? ($_SESSION['old_input']['communaute_id'] ?? '');
                                foreach ($communautes as $communaute): 
                                ?>
                                    <option value="<?php echo $communaute['id']; ?>" 
                                            <?php echo $communaute_id == $communaute['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($communaute['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Contenu de la publication -->
                        <div class="mb-4">
                            <label for="contenu" class="form-label">Que voulez-vous partager ? *</label>
                            <textarea class="form-control" id="contenu" name="contenu" rows="6" 
                                      placeholder="Partagez vos pensées, questions ou idées..." 
                                      required><?php echo $_SESSION['old_input']['contenu'] ?? ''; ?></textarea>
                            <div class="mt-2">
                                <small class="text-muted" id="charCount">0/1000 caractères</small>
                            </div>
                        </div>

                        <!-- Upload d'images -->
                        <div class="mb-4">
                            <label for="images" class="form-label">Images (optionnel)</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple 
                                   accept="image/*">
                            <div class="form-text">
                                Vous pouvez sélectionner plusieurs images. Formats acceptés: JPG, PNG, GIF.
                            </div>
                            
                            <!-- Aperçu des images -->
                            <div id="image-preview" class="mt-3 row g-2 d-none"></div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="/projet/publications" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-share me-2"></i>Publier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        // Compteur de caractères
        document.getElementById('contenu').addEventListener('input', function() {
            const length = this.value.length;
            document.getElementById('charCount').textContent = length + '/1000 caractères';
            
            if (length > 1000) {
                document.getElementById('charCount').classList.add('text-danger');
            } else {
                document.getElementById('charCount').classList.remove('text-danger');
            }
        });

        // Aperçu des images
        document.getElementById('images').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            preview.classList.add('d-none');
            
            const files = e.target.files;
            if (files.length > 0) {
                preview.classList.remove('d-none');
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-4 col-md-3';
                            col.innerHTML = `
                                <div class="position-relative">
                                    <img src="${e.target.result}" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                            onclick="this.parentElement.parentElement.remove()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `;
                            preview.appendChild(col);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        });

        // Validation du formulaire
        document.getElementById('publicationForm').addEventListener('submit', function(e) {
            const contenu = document.getElementById('contenu').value.trim();
            const communaute = document.getElementById('communaute_id').value;
            
            if (!contenu) {
                e.preventDefault();
                alert('Veuillez écrire un contenu pour votre publication');
                document.getElementById('contenu').focus();
                return;
            }
            
            if (contenu.length > 1000) {
                e.preventDefault();
                alert('Le contenu ne doit pas dépasser 1000 caractères');
                return;
            }
            
            if (!communaute) {
                e.preventDefault();
                alert('Veuillez sélectionner une communauté');
                return;
            }
            
            // Désactiver le bouton pendant l'envoi
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Publication en cours...';
        });
        </script>
        <?php
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/frontoffice/layout.php';
    }

    public function storeFront($data) {
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/publications/create');
            exit;
        }

        $this->publicationModel->communaute_id = $data['communaute_id'];
        $sessionUserId = $_SESSION['user_id'] ?? null;
        $membreId = null;
        if ($sessionUserId) {
            $membreId = $this->membreModel->findByUserId($sessionUserId);
        }
        $this->publicationModel->auteur_id = $membreId ?? ($sessionUserId ?? 1);
        $this->publicationModel->user_id = $sessionUserId ?? null;
        $this->publicationModel->contenu = $data['contenu'];
        
        $images = [];
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $image_name = time() . '_' . $_FILES['images']['name'][$key];
                    $upload_dir = 'uploads/publications/';
                    
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $upload_path = $upload_dir . $image_name;
                    
                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $images[] = '/projet/' . $upload_path;
                    }
                }
            }
        }
        
        $this->publicationModel->images = !empty($images) ? $images : null;
        $this->publicationModel->likes = 0;
        $this->publicationModel->commentaires = 0;

        if($this->publicationModel->create()) {
            $_SESSION['success_message'] = "Publication créée avec succès !";
            header('Location: /projet/publications?communaute=' . $data['communaute_id']);
            exit;
        } else {
            $this->showError("Erreur lors de la création de la publication");
        }
    }

    public function editFront($id) {
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->read_single()) {
            if (!$this->canUserEditPublication($this->publicationModel->auteur_id)) {
                $_SESSION['error_message'] = "Vous n'avez pas la permission de modifier cette publication";
                header('Location: /projet/publications/' . $id);
                exit;
            }
            
            $communautes_result = $this->communauteModel->read();
            $communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);
            
            $title = "Modifier la publication";
            ob_start();
            
            $viewPath = dirname(__DIR__) . '/view/frontoffice/publications/edit.php';
            if (file_exists($viewPath)) {
                include $viewPath;
            } else {
                echo '<div class="container mt-5">';
                echo '<h1>Modifier la publication</h1>';
                echo '<form method="POST" action="/projet/publications/update/' . $id . '" enctype="multipart/form-data">';
                echo '<div class="mb-3">';
                echo '<label for="contenu" class="form-label">Contenu</label>';
                echo '<textarea class="form-control" id="contenu" name="contenu" rows="6" required>' . htmlspecialchars($this->publicationModel->contenu) . '</textarea>';
                echo '</div>';
                echo '<div class="d-flex gap-2">';
                echo '<a href="/projet/publications" class="btn btn-secondary">Annuler</a>';
                echo '<button type="submit" class="btn btn-primary">Mettre à jour</button>';
                echo '</div>';
                echo '</form>';
                echo '</div>';
            }
            
            $content = ob_get_clean();
            include dirname(__DIR__) . '/view/frontoffice/layout.php';
        } else {
            $this->showError("Publication non trouvée");
        }
    }

    public function updateFront($id, $data) {
        $this->publicationModel->id = $id;
        
        if(!$this->publicationModel->read_single()) {
            $_SESSION['error_message'] = "Publication non trouvée";
            header('Location: /projet/publications');
            exit;
        }
        
        if (!$this->canUserEditPublication($this->publicationModel->auteur_id)) {
            $_SESSION['error_message'] = "Vous n'avez pas la permission de modifier cette publication";
            header('Location: /projet/publications/' . $id);
            exit;
        }
        
        $data['communaute_id'] = $this->publicationModel->communaute_id;
        
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/publications/edit/' . $id);
            exit;
        }

        $this->publicationModel->contenu = $data['contenu'];
        
        $images = $this->publicationModel->images;
        
        if (!empty($_FILES['images']['name'][0])) {
            $newImages = [];
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $image_name = time() . '_' . $_FILES['images']['name'][$key];
                    $upload_dir = 'uploads/publications/';
                    
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $upload_path = $upload_dir . $image_name;
                    
                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $newImages[] = '/projet/' . $upload_path;
                    }
                }
            }
            if (!empty($newImages)) {
                $images = array_merge($images, $newImages);
            }
        }
        
        $this->publicationModel->images = !empty($images) ? $images : null;

        if($this->publicationModel->updateWithImages()) {
            $_SESSION['success_message'] = "Publication modifiée avec succès !";
            header('Location: /projet/publications?communaute=' . $this->publicationModel->communaute_id);
            exit;
        } else {
            $_SESSION['error_message'] = "Erreur lors de la modification de la publication";
            header('Location: /projet/publications/edit/' . $id);
            exit;
        }
    }

    public function deleteFront($id) {
        $this->publicationModel->id = $id;
        
        if(!$this->publicationModel->read_single()) {
            $_SESSION['error_message'] = "Publication non trouvée";
            header('Location: /projet/publications');
            exit;
        }
        
        if (!$this->canUserEditPublication($this->publicationModel->auteur_id)) {
            $_SESSION['error_message'] = "Vous n'avez pas la permission de supprimer cette publication";
            header('Location: /projet/publications/' . $id);
            exit;
        }
        
        $communaute_id = $this->publicationModel->communaute_id;
        
        if($this->publicationModel->delete()) {
            $_SESSION['success_message'] = "Publication supprimée avec succès !";
            header('Location: /projet/publications?communaute=' . $communaute_id);
            exit;
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression de la publication";
            header('Location: /projet/publications/' . $id);
            exit;
        }
    }

    // === BACK OFFICE ===
    public function indexBack() {
        $publications = $this->publicationModel->read();
        
        $title = "Gestion des publications";
        ob_start();
        include dirname(__DIR__) . '/view/backoffice/publications/index.php';
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/backoffice/layout.php';
    }

    public function showBack($id) {
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->read_single()) {
            $title = "Détail de la publication";
            ob_start();
            include dirname(__DIR__) . '/view/backoffice/publications/show.php';
            $content = ob_get_clean();
            include dirname(__DIR__) . '/view/backoffice/layout.php';
        } else {
            $this->showError("Publication non trouvée");
        }
    }

    public function create() {
        $communautes_result = $this->communauteModel->read();
        $communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);
        
        $title = "Créer une nouvelle publication";
        ob_start();
        include dirname(__DIR__) . '/view/backoffice/publications/create.php';
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/backoffice/layout.php';
    }

    public function store($data) {
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/admin/publications/create');
            exit;
        }

        $this->publicationModel->communaute_id = $data['communaute_id'];
        $sessionUserId = $_SESSION['user_id'] ?? null;
        $membreId = null;
        if ($sessionUserId) {
            $membreId = $this->membreModel->findByUserId($sessionUserId);
        }
        $this->publicationModel->auteur_id = $membreId ?? ($sessionUserId ?? 1);
        $this->publicationModel->user_id = $sessionUserId ?? null;
        $this->publicationModel->contenu = $data['contenu'];

        $imagesArray = [];
        if (!empty($data['images'])) {
            if (is_array($data['images'])) {
                $imagesArray = $data['images'];
            } else {
                $parts = array_map('trim', explode(',', $data['images']));
                $imagesArray = array_filter($parts, function($v) { return $v !== ''; });
            }
        }
        $this->publicationModel->images = !empty($imagesArray) ? array_values($imagesArray) : null;
        $this->publicationModel->likes = 0;
        $this->publicationModel->commentaires = 0;

        if($this->publicationModel->create()) {
            $_SESSION['success_message'] = "Publication créée avec succès";
            header('Location: /projet/admin/publications');
            exit;
        } else {
            $this->showError("Erreur lors de la création de la publication");
        }
    }

    public function edit($id) {
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->read_single()) {
            $communautes_result = $this->communauteModel->read();
            $communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);
            
            $title = "Modifier la publication";
            ob_start();
            include dirname(__DIR__) . '/view/backoffice/publications/edit.php';
            $content = ob_get_clean();
            include dirname(__DIR__) . '/view/backoffice/layout.php';
        } else {
            $this->showError("Publication non trouvée");
        }
    }

    public function update($id, $data) {
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/admin/publications/' . $id . '/edit');
            exit;
        }

        $this->publicationModel->id = $id;
        $this->publicationModel->contenu = $data['contenu'];

        $imagesArray = [];
        if (!empty($data['images'])) {
            if (is_array($data['images'])) {
                $imagesArray = $data['images'];
            } else {
                $parts = array_map('trim', explode(',', $data['images']));
                $imagesArray = array_filter($parts, function($v) { return $v !== ''; });
            }
        }
        $this->publicationModel->images = !empty($imagesArray) ? array_values($imagesArray) : null;
        $this->publicationModel->likes = $data['likes'] ?? 0;
        $this->publicationModel->commentaires = $data['commentaires'] ?? 0;

        if($this->publicationModel->update()) {
            $_SESSION['success_message'] = "Publication mise à jour avec succès";
            header('Location: /projet/admin/publications/' . $id);
            exit;
        } else {
            $this->showError("Erreur lors de la mise à jour de la publication");
        }
    }

    public function delete($id) {
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->delete()) {
            $_SESSION['success_message'] = "Publication supprimée avec succès";
            header('Location: /projet/admin/publications');
            exit;
        } else {
            $this->showError("Erreur lors de la suppression de la publication");
        }
    }

    // === ASSISTANT IA FONCTIONNEL ===
    
    public function showAIAssistant() {
        $title = "Assistant de rédaction IA";
        ob_start();
        ?>
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center mb-5">
                        <h1 class="text-gradient mb-3">
                            <i class="fas fa-robot me-2"></i>Assistant de rédaction IA
                        </h1>
                        <p class="lead text-muted">
                            Utilisez l'intelligence artificielle pour améliorer vos publications
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-lightbulb text-warning me-2"></i>Générer une publication
                                    </h5>
                                    <div class="mb-3">
                                        <label class="form-label">Thème</label>
                                        <input type="text" class="form-control" id="aiTheme" placeholder="Ex: Les nouvelles technologies...">
                                    </div>
                                    <button class="btn btn-primary w-100" onclick="generateWithAI()" id="generateBtn">
                                        <i class="fas fa-bolt me-1"></i>Générer avec l'IA
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-magic text-info me-2"></i>Améliorer un texte
                                    </h5>
                                    <div class="mb-3">
                                        <label class="form-label">Votre texte</label>
                                        <textarea class="form-control" id="aiText" rows="3" placeholder="Collez votre texte ici..."></textarea>
                                    </div>
                                    <button class="btn btn-info w-100" onclick="improveWithAI()" id="improveBtn">
                                        <i class="fas fa-wand-magic-sparkles me-1"></i>Améliorer avec l'IA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-file-alt me-2"></i>Résultat
                            </h5>
                            <div class="border rounded p-3 bg-light" id="aiResult" style="min-height: 200px;">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-robot fa-3x mb-3"></i>
                                    <p>Le résultat apparaîtra ici</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-success" onclick="copyAIResult()">
                                    <i class="fas fa-copy me-1"></i>Copier le résultat
                                </button>
                                <button class="btn btn-primary" onclick="useAIResult()">
                                    <i class="fas fa-check me-1"></i>Utiliser dans une publication
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        const baseUrl = '<?php echo BASE_URL ?? ''; ?>';
        
        async function generateWithAI() {
            const theme = document.getElementById('aiTheme').value.trim();
            const button = document.getElementById('generateBtn');
            
            if (!theme) {
                alert('Veuillez entrer un thème');
                return;
            }
            
            // Désactiver le bouton
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Génération...';
            
            document.getElementById('aiResult').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Génération en cours...</p>
                </div>
            `;
            
            try {
                const formData = new FormData();
                formData.append('action', 'generate');
                formData.append('theme', theme);
                
                const response = await fetch(baseUrl + '/ai-assistant/handle', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('aiResult').innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>Suggestion générée avec succès !
                        </div>
                        <div class="mt-3">${data.content.replace(/\n/g, '<br>')}</div>
                    `;
                } else {
                    document.getElementById('aiResult').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>${data.error}
                        </div>
                    `;
                }
            } catch (error) {
                document.getElementById('aiResult').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>Erreur de connexion
                    </div>
                `;
            } finally {
                // Réactiver le bouton
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-bolt me-1"></i>Générer avec l\'IA';
            }
        }
        
        async function improveWithAI() {
            const text = document.getElementById('aiText').value.trim();
            const button = document.getElementById('improveBtn');
            
            if (!text) {
                alert('Veuillez entrer un texte');
                return;
            }
            
            // Désactiver le bouton
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Traitement...';
            
            document.getElementById('aiResult').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Amélioration en cours...</p>
                </div>
            `;
            
            try {
                const formData = new FormData();
                formData.append('action', 'improve');
                formData.append('text', text);
                
                const response = await fetch(baseUrl + '/ai-assistant/handle', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('aiResult').innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>Texte amélioré avec succès !
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6>Original :</h6>
                                <div class="border p-2 mb-3" style="max-height: 200px; overflow-y: auto;">
                                    ${text.substring(0, 500)}${text.length > 500 ? '...' : ''}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Amélioré :</h6>
                                <div class="border p-2" style="max-height: 200px; overflow-y: auto;">
                                    ${data.content.substring(0, 500)}${data.content.length > 500 ? '...' : ''}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    document.getElementById('aiResult').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>${data.error}
                        </div>
                    `;
                }
            } catch (error) {
                document.getElementById('aiResult').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>Erreur de connexion
                    </div>
                `;
            } finally {
                // Réactiver le bouton
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-wand-magic-sparkles me-1"></i>Améliorer avec l\'IA';
            }
        }
        
        function copyAIResult() {
            const resultDiv = document.getElementById('aiResult');
            const text = resultDiv.textContent || resultDiv.innerText;
            
            if (text && !text.includes('Le résultat apparaîtra ici')) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('✅ Résultat copié dans le presse-papier !');
                }).catch(err => {
                    console.error('Erreur de copie:', err);
                    alert('❌ Erreur lors de la copie');
                });
            } else {
                alert('⚠️ Aucun résultat à copier');
            }
        }
        
        function useAIResult() {
            const resultDiv = document.getElementById('aiResult');
            const text = resultDiv.textContent || resultDiv.innerText;
            
            if (text && !text.includes('Le résultat apparaîtra ici')) {
                // Ouvrir une nouvelle publication avec le texte généré
                window.open(baseUrl + '/publications/create?ai_content=' + encodeURIComponent(text), '_blank');
            } else {
                alert('⚠️ Aucun résultat à utiliser');
            }
        }
        
        // Raccourcis clavier
        document.addEventListener('keydown', function(e) {
            // Ctrl+Enter dans le champ thème
            if (e.ctrlKey && e.key === 'Enter' && document.activeElement.id === 'aiTheme') {
                generateWithAI();
            }
            
            // Ctrl+Enter dans le champ texte
            if (e.ctrlKey && e.key === 'Enter' && document.activeElement.id === 'aiText') {
                improveWithAI();
            }
        });
        </script>
        <?php
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/frontoffice/layout.php';
    }
    
    public function handleAIAssistant() {
        header('Content-Type: application/json');
        
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'generate':
                $theme = $_POST['theme'] ?? '';
                
                if (empty($theme)) {
                    echo json_encode(['success' => false, 'error' => 'Thème requis']);
                    exit;
                }
                
                // Suggestions prédéfinies
                $suggestions = [
                    "Salut à tous ! J'aimerais partager avec vous mes réflexions sur '$theme'. C'est un sujet qui me passionne vraiment et je serais ravi d'échanger avec vous à ce sujet. Qu'en pensez-vous ?",
                    "Bonjour la communauté ! Aujourd'hui, je voulais aborder le thème de '$theme'. J'ai récemment découvert des aspects fascinants et je me demandais si certains d'entre vous avaient des expériences à partager ?",
                    "Hey ! Petit partage sur '$theme'. C'est incroyable comment ce sujet évolue constamment. J'ai quelques questions si quelqu'un veut bien échanger :\n\n1. Quelle est votre expérience ?\n2. Des conseils à partager ?\n3. Où en êtes-vous personnellement ?",
                    "Discussion ouverte sur '$theme' ! 👋 Je trouve ce sujet vraiment captivant et j'aimerais connaître vos opinions. N'hésitez pas à partager vos pensées, questions ou ressources intéressantes !",
                    "'$theme' - un sujet qui mérite qu'on en parle ! J'ai récemment approfondi mes connaissances et je suis impressionné par les possibilités. Et vous, comment vivez-vous cette thématique au quotidien ?"
                ];
                
                $randomSuggestion = $suggestions[array_rand($suggestions)];
                
                echo json_encode([
                    'success' => true,
                    'content' => $randomSuggestion
                ]);
                break;
                
            case 'improve':
                $text = $_POST['text'] ?? '';
                
                if (empty($text)) {
                    echo json_encode(['success' => false, 'error' => 'Texte requis']);
                    exit;
                }
                
                // Amélioration simple
                $improvedText = "✨ **Version améliorée :**\n\n" . 
                               ucfirst(trim($text)) . 
                               "\n\nJ'espère que cette version vous convient mieux ! N'hésitez pas à l'adapter selon vos besoins.";
                
                echo json_encode([
                    'success' => true,
                    'content' => $improvedText
                ]);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Action non reconnue']);
        }
        
        exit;
    }
    
    public function addAIAssistantToForm() {
        ob_start();
        ?>
        <div class="card mb-4 border-primary ai-assistant-section">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-robot me-2"></i>Assistant IA pour votre publication
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="quickTheme" placeholder="Entrez un thème...">
                            <button class="btn btn-outline-primary" type="button" onclick="quickAIGenerate()" id="quickGenBtn">
                                <i class="fas fa-bolt"></i> Générer
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1">Générer une idée</small>
                    </div>
                </div>
                
                <div class="mt-3" id="aiQuickResult" style="display: none;">
                    <div class="alert alert-info">
                        <button type="button" class="btn-close float-end" onclick="document.getElementById('aiQuickResult').style.display='none'"></button>
                        <div id="aiQuickResultContent"></div>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-success" type="button" onclick="applyAIResult()">
                                <i class="fas fa-check me-1"></i>Appliquer
                            </button>
                            <button class="btn btn-sm btn-secondary" type="button" onclick="document.getElementById('aiQuickResult').style.display='none'">
                                <i class="fas fa-times me-1"></i>Fermer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        // Fonctions IA pour le formulaire
        async function quickAIGenerate() {
            const theme = document.getElementById('quickTheme').value.trim();
            const button = document.getElementById('quickGenBtn');
            const contenuField = document.getElementById('contenu');
            
            if (!theme) {
                alert('Veuillez entrer un thème');
                return;
            }
            
            if (!contenuField) {
                alert('Champ de contenu non trouvé');
                return;
            }
            
            // Désactiver le bouton
            button.disabled = true;
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            try {
                // Appeler l'assistant IA
                const formData = new FormData();
                formData.append('action', 'generate');
                formData.append('theme', theme);
                
                const response = await fetch('<?php echo BASE_URL ?? ''; ?>/ai-assistant/handle', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Afficher le résultat
                    document.getElementById('aiQuickResultContent').innerHTML = 
                        '<strong>✨ Suggestion générée :</strong><br><br>' + 
                        data.content.replace(/\n/g, '<br>');
                    document.getElementById('aiQuickResult').style.display = 'block';
                    
                    // Stocker le résultat pour l'appliquer
                    window.currentAIResult = data.content;
                    
                    // Scroll vers le résultat
                    document.getElementById('aiQuickResult').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest' 
                    });
                } else {
                    alert('Erreur : ' + data.error);
                }
            } catch (error) {
                console.error('Erreur IA:', error);
                alert('Erreur de connexion avec l\'assistant IA');
            } finally {
                // Réactiver le bouton
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }
        
        async function quickAIImprove() {
            const contenuField = document.getElementById('contenu');
            const button = document.getElementById('quickImpBtn');
            
            if (!contenuField) {
                alert('Champ de contenu non trouvé');
                return;
            }
            
            const text = contenuField.value.trim();
            if (!text) {
                alert('Veuillez d\'abord écrire quelque chose dans le champ de publication');
                contenuField.focus();
                return;
            }
            
            // Désactiver le bouton
            button.disabled = true;
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            try {
                // Appeler l'assistant IA
                const formData = new FormData();
                formData.append('action', 'improve');
                formData.append('text', text);
                
                const response = await fetch('<?php echo BASE_URL ?? ''; ?>/ai-assistant/handle', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Afficher le résultat
                    document.getElementById('aiQuickResultContent').innerHTML = 
                        '<strong>✅ Texte amélioré :</strong><br><br>' + 
                        data.content.replace(/\n/g, '<br>');
                    document.getElementById('aiQuickResult').style.display = 'block';
                    
                    // Stocker le résultat pour l'appliquer
                    window.currentAIResult = data.content;
                    
                    // Scroll vers le résultat
                    document.getElementById('aiQuickResult').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest' 
                    });
                } else {
                    alert('Erreur : ' + data.error);
                }
            } catch (error) {
                console.error('Erreur IA:', error);
                alert('Erreur de connexion avec l\'assistant IA');
            } finally {
                // Réactiver le bouton
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }
        
        function applyAIResult() {
            if (window.currentAIResult) {
                const contenuField = document.getElementById('contenu');
                if (contenuField) {
                    contenuField.value = window.currentAIResult;
                    document.getElementById('aiQuickResult').style.display = 'none';
                    
                    // Mettre à jour le compteur de caractères
                    if (typeof updateCharCount === 'function') {
                        updateCharCount();
                    } else {
                        // Fallback si la fonction n'existe pas
                        const length = contenuField.value.length;
                        const charCount = document.getElementById('charCount');
                        if (charCount) {
                            charCount.textContent = length + '/1000 caractères';
                            if (length > 1000) {
                                charCount.classList.add('text-danger');
                            } else {
                                charCount.classList.remove('text-danger');
                            }
                        }
                    }
                    
                    alert('✅ Texte appliqué avec succès !');
                    contenuField.focus();
                }
            }
        }
        
        // Raccourci clavier pour générer (Ctrl+G)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'g') {
                e.preventDefault();
                const themeInput = document.getElementById('quickTheme');
                if (themeInput && document.activeElement !== themeInput) {
                    themeInput.focus();
                }
            }
        });
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Focus sur le champ thème quand on clique sur le bouton
            const themeInput = document.getElementById('quickTheme');
            if (themeInput) {
                themeInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        quickAIGenerate();
                    }
                });
            }
            
            // Si un contenu AI est passé en paramètre
            const urlParams = new URLSearchParams(window.location.search);
            const aiContent = urlParams.get('ai_content');
            if (aiContent && document.getElementById('contenu')) {
                document.getElementById('contenu').value = decodeURIComponent(aiContent);
                
                // Mettre à jour le compteur
                const contenuField = document.getElementById('contenu');
                const charCount = document.getElementById('charCount');
                if (contenuField && charCount) {
                    const length = contenuField.value.length;
                    charCount.textContent = length + '/1000 caractères';
                    if (length > 1000) {
                        charCount.classList.add('text-danger');
                    }
                }
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }

    // === MÉTHODES UTILITAIRES ===

    private function validatePublicationData($data) {
        $errors = [];

        if (empty($data['contenu'])) {
            $errors['contenu'] = "Le contenu est obligatoire";
        } elseif (strlen($data['contenu']) < 5) {
            $errors['contenu'] = "Le contenu doit faire au moins 5 caractères";
        } elseif (strlen($data['contenu']) > 1000) {
            $errors['contenu'] = "Le contenu ne doit pas dépasser 1000 caractères";
        }

        if (empty($data['communaute_id'])) {
            $errors['communaute_id'] = "La communauté est obligatoire";
        } elseif (!is_numeric($data['communaute_id'])) {
            $errors['communaute_id'] = "La communauté sélectionnée est invalide";
        }

        return $errors;
    }

    private function canUserEditPublication($auteur_id) {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] == $auteur_id;
    }

    private function showError($message) {
        $title = "Erreur";
        ob_start();
        echo "<div class='alert alert-danger'>$message</div>";
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/frontoffice/layout.php';
    }
}
?>
