
<?php

require_once dirname(__DIR__) . '/tools/OpenAIClient.php';
require_once dirname(__DIR__) . '/tools/GeminiClient.php';

class PublicationController {
    private $publicationModel;
    private $communauteModel;
    private $validation;

    public function __construct($db) {
        $this->publicationModel = new Publication($db);
        $this->communauteModel = new Communaute($db);
        $this->validation = new Validation();
    }

    private function getLoginUrl(): string {
        $base = defined('BASE_URL') ? (string) BASE_URL : '';
        $root = rtrim(str_replace('\\', '/', dirname($base)), '/');
        if ($root === '.' || $root === '/') {
            $root = '';
        }
        return $root . '/gaming_museum/view/frontoffice/login.php';
    }

    private function requireLogin(): void {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Veuillez vous connecter pour continuer.";
            header('Location: ' . $this->getLoginUrl());
            exit;
        }
    }

    // === FRONT OFFICE ===

    public function indexFront() {
        $order_by = $_GET['order_by'] ?? 'date_publication';
        $order_dir = $_GET['order_dir'] ?? 'DESC';
        $communaute_id = $_GET['communaute'] ?? null;

        $selectedCategorie = trim((string) ($_GET['categorie'] ?? ''));
        $categories = $this->communauteModel->getCategories();
        
        if ($communaute_id) {
            $publications = $this->publicationModel->readOrderedFiltered($order_by, $order_dir, (int) $communaute_id, $selectedCategorie);
            $this->communauteModel->id = $communaute_id;
            $communaute_info = $this->communauteModel->read_single() ? $this->communauteModel : null;
        } else {
            $publications = $this->publicationModel->readOrderedFiltered($order_by, $order_dir, null, $selectedCategorie);
            $communaute_info = null;
        }
        
        $title = $communaute_info ? "Publications - " . $communaute_info->nom : "Publications récentes";

        if ((empty($publications) || !is_array($publications)) && !$communaute_id && $selectedCategorie === '') {
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
        $this->requireLogin();
        $communautes_result = $this->communauteModel->read();
        $communautesRaw = $communautes_result->fetchAll(PDO::FETCH_ASSOC);

        // Nettoyer la liste: éviter doublons éventuels et garantir un libellé affichable
        $communautesById = [];
        foreach ($communautesRaw as $c) {
            $cid = isset($c['id']) ? (int)$c['id'] : 0;
            if ($cid <= 0) continue;
            if (!isset($communautesById[$cid])) {
                $name = trim((string)($c['nom'] ?? ''));
                if ($name === '') {
                    $c['nom'] = 'Communauté #' . $cid;
                }
                $communautesById[$cid] = $c;
            }
        }
        $communautes = array_values($communautesById);
        usort($communautes, function ($a, $b) {
            return strcasecmp((string)($a['nom'] ?? ''), (string)($b['nom'] ?? ''));
        });
        
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

                    <form action="<?php echo BASE_URL; ?>/publications/create" method="POST" enctype="multipart/form-data" id="publicationForm">
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
                            <a href="<?php echo BASE_URL; ?>/publications" class="btn btn-secondary">
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
        function updateCharCount() {
            const field = document.getElementById('contenu');
            const counter = document.getElementById('charCount');
            if (!field || !counter) return;

            const length = field.value.length;
            counter.textContent = length + '/1000 caractères';

            if (length > 1000) {
                counter.classList.add('text-danger');
            } else {
                counter.classList.remove('text-danger');
            }
        }

        // Compteur de caractères
        document.getElementById('contenu').addEventListener('input', updateCharCount);
        // Initialiser (old_input / ai_content)
        document.addEventListener('DOMContentLoaded', updateCharCount);

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
        $this->requireLogin();
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: ' . BASE_URL . '/publications/create');
            exit;
        }

        $this->publicationModel->communaute_id = $data['communaute_id'];
        $sessionUserId = $_SESSION['user_id'] ?? null;
        $this->publicationModel->auteur_id = $sessionUserId ?? 1;
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
                        $prefix = (BASE_URL === '' ? '' : BASE_URL);
                        $images[] = $prefix . '/' . $upload_path;
                    }
                }
            }
        }
        
        $this->publicationModel->images = !empty($images) ? $images : null;
        $this->publicationModel->likes = 0;
        $this->publicationModel->commentaires = 0;

        if($this->publicationModel->create()) {
            $_SESSION['success_message'] = "Publication créée avec succès !";
            header('Location: ' . BASE_URL . '/publications?communaute=' . $data['communaute_id']);
            exit;
        } else {
            $this->showError("Erreur lors de la création de la publication");
        }
    }

    public function editFront($id) {
        $this->requireLogin();
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->read_single()) {
            if (!$this->canUserEditPublication($this->publicationModel->auteur_id)) {
                $_SESSION['error_message'] = "Vous n'avez pas la permission de modifier cette publication";
                header('Location: ' . BASE_URL . '/publications/' . $id);
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
                echo '<form method="POST" action="' . BASE_URL . '/publications/update/' . $id . '" enctype="multipart/form-data">';
                echo '<div class="mb-3">';
                echo '<label for="contenu" class="form-label">Contenu</label>';
                echo '<textarea class="form-control" id="contenu" name="contenu" rows="6" required>' . htmlspecialchars($this->publicationModel->contenu) . '</textarea>';
                echo '</div>';
                echo '<div class="d-flex gap-2">';
                echo '<a href="' . BASE_URL . '/publications" class="btn btn-secondary">Annuler</a>';
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
        $this->requireLogin();
        $this->publicationModel->id = $id;
        
        if(!$this->publicationModel->read_single()) {
            $_SESSION['error_message'] = "Publication non trouvée";
            header('Location: ' . BASE_URL . '/publications');
            exit;
        }
        
        if (!$this->canUserEditPublication($this->publicationModel->auteur_id)) {
            $_SESSION['error_message'] = "Vous n'avez pas la permission de modifier cette publication";
            header('Location: ' . BASE_URL . '/publications/' . $id);
            exit;
        }
        
        $data['communaute_id'] = $this->publicationModel->communaute_id;
        
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: ' . BASE_URL . '/publications/edit/' . $id);
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
                        $prefix = (BASE_URL === '' ? '' : BASE_URL);
                        $newImages[] = $prefix . '/' . $upload_path;
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
            header('Location: ' . BASE_URL . '/publications?communaute=' . $this->publicationModel->communaute_id);
            exit;
        } else {
            $_SESSION['error_message'] = "Erreur lors de la modification de la publication";
            header('Location: ' . BASE_URL . '/publications/edit/' . $id);
            exit;
        }
    }

    public function deleteFront($id) {
        $this->requireLogin();
        $this->publicationModel->id = $id;
        
        if(!$this->publicationModel->read_single()) {
            $_SESSION['error_message'] = "Publication non trouvée";
            header('Location: ' . BASE_URL . '/publications');
            exit;
        }
        
        if (!$this->canUserEditPublication($this->publicationModel->auteur_id)) {
            $_SESSION['error_message'] = "Vous n'avez pas la permission de supprimer cette publication";
            header('Location: ' . BASE_URL . '/publications/' . $id);
            exit;
        }
        
        $communaute_id = $this->publicationModel->communaute_id;
        
        if($this->publicationModel->delete()) {
            $_SESSION['success_message'] = "Publication supprimée avec succès !";
            header('Location: ' . BASE_URL . '/publications?communaute=' . $communaute_id);
            exit;
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression de la publication";
            header('Location: ' . BASE_URL . '/publications/' . $id);
            exit;
        }
    }

    // === BACK OFFICE ===
    public function indexBack() {
        $this->requireLogin();

        $selectedCategorie = trim((string) ($_GET['categorie'] ?? ''));
        $categories = $this->communauteModel->getCategories();
        $publications = $this->publicationModel->readOrderedFiltered('date_publication', 'DESC', null, $selectedCategorie);
        
        $title = "Gestion des publications";
        ob_start();
        include dirname(__DIR__) . '/view/backoffice/publications/index.php';
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/backoffice/layout.php';
    }

    public function showBack($id) {
        $this->requireLogin();
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
        $this->requireLogin();
        $communautes_result = $this->communauteModel->read();
        $communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);
        
        $title = "Créer une nouvelle publication";
        ob_start();
        include dirname(__DIR__) . '/view/backoffice/publications/create.php';
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/backoffice/layout.php';
    }

    public function store($data) {
        $this->requireLogin();
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: ' . BASE_URL . '/admin/publications/create');
            exit;
        }

        $this->publicationModel->communaute_id = $data['communaute_id'];
        $sessionUserId = $_SESSION['user_id'] ?? null;
        $this->publicationModel->auteur_id = $sessionUserId ?? 1;
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
            header('Location: ' . BASE_URL . '/admin/publications');
            exit;
        } else {
            $this->showError("Erreur lors de la création de la publication");
        }
    }

    public function edit($id) {
        $this->requireLogin();
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
        $this->requireLogin();
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: ' . BASE_URL . '/admin/publications/' . $id . '/edit');
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
            header('Location: ' . BASE_URL . '/admin/publications/' . $id);
            exit;
        } else {
            $this->showError("Erreur lors de la mise à jour de la publication");
        }
    }

    public function delete($id) {
        $this->requireLogin();
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->delete()) {
            $_SESSION['success_message'] = "Publication supprimée avec succès";
            header('Location: ' . BASE_URL . '/admin/publications');
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
        const baseUrlFromPhp = '<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>';
        function getProjetBaseUrl() {
            if (baseUrlFromPhp && baseUrlFromPhp !== 'BASE_URL') return baseUrlFromPhp;
            const parts = window.location.pathname.split('/').filter(Boolean);
            const idx = parts.indexOf('projet');
            if (idx >= 0) return '/' + parts.slice(0, idx + 1).join('/');
            return '';
        }
        const baseUrl = getProjetBaseUrl();
        window.lastAIContent = '';

        async function postAI(formData) {
            const endpoint = baseUrl + '/ai-assistant/handle';
            const response = await fetch(endpoint, { method: 'POST', body: formData });
            const ct = (response.headers.get('content-type') || '').toLowerCase();
            if (!ct.includes('application/json')) {
                const text = await response.text();
                throw new Error('Réponse non-JSON (' + response.status + ')\n' + text.substring(0, 300));
            }
            const data = await response.json();
            if (!response.ok) {
                throw new Error((data && data.error) ? data.error : ('Erreur HTTP ' + response.status));
            }
            return data;
        }
        
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

                const data = await postAI(formData);
                
                if (data.success) {
                    window.lastAIContent = data.content || '';
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
                console.error('Erreur IA:', error);
                document.getElementById('aiResult').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>${(error && error.message) ? error.message : 'Erreur de connexion'}
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

                const data = await postAI(formData);
                
                if (data.success) {
                    window.lastAIContent = data.content || '';
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
                console.error('Erreur IA:', error);
                document.getElementById('aiResult').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>${(error && error.message) ? error.message : 'Erreur de connexion'}
                    </div>
                `;
            } finally {
                // Réactiver le bouton
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-wand-magic-sparkles me-1"></i>Améliorer avec l\'IA';
            }
        }
        
        function copyAIResult() {
            const text = (window.lastAIContent || '').trim();
            
            if (text) {
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
            const text = (window.lastAIContent || '').trim();
            
            if (text) {
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

    private function textLen(string $text): int {
        if (function_exists('mb_strlen')) {
            return (int) mb_strlen($text, 'UTF-8');
        }
        return (int) strlen($text);
    }

    private function textSubstr(string $text, int $start, int $length): string {
        if (function_exists('mb_substr')) {
            return (string) mb_substr($text, $start, $length, 'UTF-8');
        }
        return (string) substr($text, $start, $length);
    }

    private function improvePublicationText(string $text): string {
        $t = trim($text);
        // Normaliser les sauts de ligne
        $t = preg_replace("/\r\n?/", "\n", $t);
        // Nettoyage des espaces
        $t = preg_replace("/[\t ]+/", " ", $t);
        $t = preg_replace("/ *\n{3,} */", "\n\n", $t);
        $t = preg_replace("/ +([,.!?;:])/u", "$1", $t);
        $t = preg_replace("/([,.!?;:])(?=\S)/u", "$1 ", $t);
        $t = trim($t);
        $t = preg_replace("/[ ]{2,}/", " ", $t);

        // Si l'utilisateur a déjà collé une ancienne sortie IA, enlever l'entête
        $t = preg_replace("/^\s*✨\s*\*\*Version\s+am\p{L}+\s*:\*\*\s*/u", "", $t);
        $t = trim($t);

        $len = $this->textLen($t);

        // Si texte très long (proche de la limite), ne pas ajouter trop de contenu
        $lightOnly = $len > 880;

        // Capitaliser le premier caractère si utile
        if ($t !== '') {
            $first = $this->textSubstr($t, 0, 1);
            $rest = $this->textSubstr($t, 1, $this->textLen($t));
            if (preg_match('/[a-zà-ÿ]/u', $first)) {
                if (function_exists('mb_strtoupper')) {
                    $first = mb_strtoupper($first, 'UTF-8');
                } else {
                    $first = strtoupper($first);
                }
                $t = $first . $rest;
            }
        }

        // Petites reformulations sans changer le sens
        $t = preg_replace('/\bje\s+veux\s+partager\b/iu', 'Je voulais partager', $t, 1);

        // Capitaliser après ponctuation / paragraphes
        $t = preg_replace_callback('/(^|[.!?]\s+|\n\n+)([a-zà-ÿ])/u', function ($m) {
            $prefix = $m[1];
            $ch = $m[2];
            if (function_exists('mb_strtoupper')) {
                $ch = mb_strtoupper($ch, 'UTF-8');
            } else {
                $ch = strtoupper($ch);
            }
            return $prefix . $ch;
        }, $t);

        // Ajouter une structure lisible (paragraphes) si texte dense
        if (!$lightOnly && strpos($t, "\n") === false && $this->textLen($t) > 220) {
            $sentences = preg_split('/(?<=[.!?])\s+/u', $t) ?: [$t];
            $rebuilt = '';
            $count = 0;
            foreach ($sentences as $s) {
                $s = trim($s);
                if ($s === '') continue;
                $rebuilt .= ($rebuilt === '' ? '' : ' ') . $s;
                $count++;
                if ($count % 2 === 0) {
                    $rebuilt .= "\n\n";
                }
            }
            $t = trim($rebuilt);
        }

        // Ajouter une petite intro + question (si absent)
        $hasQuestion = (strpos($t, '?') !== false)
            || (bool) preg_match('/\b(qu[’\']?en\s+pensez|vous\s+en\s+pensez|pensez\s*-?vous|quels?\s+conseils?|des\s+avis|vos\s+avis)\b/iu', $t);
        if (!$lightOnly) {
            $hasGreeting = (bool) preg_match('/^(salut|bonjour|coucou|hey|hello)\b/iu', $t);
            if (!$hasGreeting) {
                $t = "Salut à tous !\n\n" . $t;
            }
            // Si le texte ressemble à une question mais n'a pas de '?', ajouter le point d'interrogation
            if (strpos($t, '?') === false && preg_match('/\b(vous\s+en\s+pensez\s+quoi|qu[’\']?en\s+pensez|pensez\s*-?vous)\s*$/iu', $t)) {
                $t = rtrim($t);
                $t .= ' ?';
            }
            if (!$hasQuestion) {
                $t = rtrim($t);
                if (!preg_match('/[.!?]$/u', $t)) {
                    $t .= '.';
                }
                $t .= "\n\nQu'en pensez-vous ?";
            }
        } else {
            // Assurer une ponctuation finale
            $t = rtrim($t);
            if ($t !== '' && !preg_match('/[.!?]$/u', $t)) {
                $t .= '.';
            }
        }

        // Respecter la limite de 1000 caractères côté formulaire
        if ($this->textLen($t) > 1000) {
            $t = rtrim($this->textSubstr($t, 0, 997));
            $t .= '...';
        }

        return $t;
    }

    /**
     * @param array<int, array{role:string, content:string}> $messages
     */
    private function aiChat(array $messages, int $maxTokens = 450, float $temperature = 0.2): string
    {
        $provider = '';
        if (defined('AI_PROVIDER')) {
            $provider = strtolower(trim((string) AI_PROVIDER));
        } else {
            $provider = strtolower(trim((string) getenv('AI_PROVIDER')));
        }

        $hasGeminiKey = (defined('GEMINI_API_KEY') && trim((string) GEMINI_API_KEY) !== '')
            || (trim((string) getenv('GEMINI_API_KEY')) !== '');

        if ($provider === '') {
            $provider = $hasGeminiKey ? 'gemini' : 'openai';
        }

        if ($provider === 'gemini') {
            $client = new GeminiClient();
            return $client->chat($messages, $maxTokens, $temperature);
        }

        $client = new OpenAIClient();
        return $client->chat($messages, $maxTokens, $temperature);
    }
    
    public function handleAIAssistant() {
        header('Content-Type: application/json; charset=utf-8');
        
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'generate':
                $theme = $_POST['theme'] ?? '';

                $theme = trim((string) $theme);
                if ($theme === '') {
                    echo json_encode(['success' => false, 'error' => 'Thème requis'], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                if (function_exists('mb_strlen') ? (mb_strlen($theme, 'UTF-8') > 120) : (strlen($theme) > 120)) {
                    echo json_encode(['success' => false, 'error' => 'Thème trop long (max 120 caractères).'], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                try {
                    $system = "Tu es un assistant de rédaction pour un site de communautés gaming. "
                        . "Réponds en français. "
                        . "Génère UNE proposition de publication prête à poster sur le thème donné. "
                        . "Contraintes: 450 à 850 caractères max, ton amical, 1 à 2 paragraphes, "
                        . "et termine par une question pour lancer la discussion. "
                        . "Pas de HTML, pas de markdown.";

                    $messages = [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user', 'content' => "Thème: " . $theme],
                    ];

                    $aiText = $this->aiChat($messages, 320, 0.7);
                    $aiText = trim((string) $aiText);
                    if ($aiText !== '' && $this->textLen($aiText) > 1000) {
                        $aiText = rtrim($this->textSubstr($aiText, 0, 997)) . '...';
                    }
                    if ($aiText !== '') {
                        echo json_encode(['success' => true, 'content' => $aiText], JSON_UNESCAPED_UNICODE);
                        exit;
                    }
                } catch (Throwable $e) {
                    // Fallback below
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
                ], JSON_UNESCAPED_UNICODE);
                break;
                
            case 'improve':
                $text = $_POST['text'] ?? '';

                $text = trim((string) $text);
                if ($text === '') {
                    echo json_encode(['success' => false, 'error' => 'Texte requis'], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                if ($this->textLen($text) > 2000) {
                    echo json_encode(['success' => false, 'error' => 'Texte trop long (max 2000 caractères).'], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                try {
                    $system = "Tu es un assistant de rédaction. Réécris le texte en français pour qu'il soit plus clair, "
                        . "plus agréable à lire et bien ponctué, sans changer le sens. "
                        . "Garde un ton amical et termine par une question si ce n'est pas déjà le cas. "
                        . "Limite: 1000 caractères max. Pas de HTML, pas de markdown.";

                    $messages = [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user', 'content' => "Texte à améliorer:\n" . $text],
                    ];

                    $aiText = $this->aiChat($messages, 420, 0.35);
                    $aiText = trim((string) $aiText);
                    if ($aiText !== '' && $this->textLen($aiText) > 1000) {
                        $aiText = rtrim($this->textSubstr($aiText, 0, 997)) . '...';
                    }
                    if ($aiText !== '') {
                        echo json_encode(['success' => true, 'content' => $aiText], JSON_UNESCAPED_UNICODE);
                        exit;
                    }
                } catch (Throwable $e) {
                    // Fallback to local improvement below
                }
                
                $improvedText = $this->improvePublicationText((string) $text);
                
                echo json_encode([
                    'success' => true,
                    'content' => $improvedText
                ], JSON_UNESCAPED_UNICODE);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Action non reconnue'], JSON_UNESCAPED_UNICODE);
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
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control" id="quickTheme" placeholder="Entrez un thème...">
                            <button class="btn btn-outline-primary" type="button" id="quickGenBtn">
                                <i class="fas fa-bolt"></i> Générer
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1">Générer une idée</small>
                    </div>
                </div>
                
                <div class="mt-3" id="aiQuickResult" style="display: none;">
                    <div class="alert alert-info" data-persist="true">
                        <button type="button" class="btn-close float-end" onclick="document.getElementById('aiQuickResult').style.display='none'"></button>
                        <div id="aiQuickResultContent"></div>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-success" type="button" id="aiQuickAddBtn">
                                <i class="fas fa-check me-1"></i>Ajouter
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
        const baseUrlFromPhp = '<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>';
        function getProjetBaseUrl() {
            if (baseUrlFromPhp && baseUrlFromPhp !== 'BASE_URL') return baseUrlFromPhp;
            const parts = window.location.pathname.split('/').filter(Boolean);
            const idx = parts.indexOf('projet');
            if (idx >= 0) return '/' + parts.slice(0, idx + 1).join('/');
            return '';
        }
        const baseUrl = getProjetBaseUrl();

        async function postAI(formData) {
            const endpoint = baseUrl + '/ai-assistant/handle';
            const response = await fetch(endpoint, { method: 'POST', body: formData });
            const ct = (response.headers.get('content-type') || '').toLowerCase();
            if (!ct.includes('application/json')) {
                const text = await response.text();
                throw new Error('Réponse non-JSON (' + response.status + ')');
            }
            const data = await response.json();
            if (!response.ok) {
                throw new Error((data && data.error) ? data.error : ('Erreur HTTP ' + response.status));
            }
            return data;
        }

        function ensureAIQuickResultElements() {
            let wrapper = document.getElementById('aiQuickResult');
            let content = document.getElementById('aiQuickResultContent');
            if (wrapper && content) return { wrapper, content };

            const host = document.querySelector('.ai-assistant-section .card-body')
                || document.querySelector('.ai-assistant-section')
                || document.body;

            if (!wrapper) {
                wrapper = document.createElement('div');
                wrapper.id = 'aiQuickResult';
                wrapper.className = 'mt-3';
                wrapper.style.display = 'none';
                wrapper.innerHTML = `
                    <div class="alert alert-info" data-persist="true">
                        <button type="button" class="btn-close float-end" onclick="document.getElementById('aiQuickResult').style.display='none'"></button>
                        <div id="aiQuickResultContent"></div>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-success" type="button" id="aiQuickAddBtn">
                                <i class="fas fa-check me-1"></i>Ajouter
                            </button>
                            <button class="btn btn-sm btn-secondary" type="button" onclick="document.getElementById('aiQuickResult').style.display='none'">
                                <i class="fas fa-times me-1"></i>Fermer
                            </button>
                        </div>
                    </div>
                `;
                host.appendChild(wrapper);
            }

            // Rechercher le contenu dans le wrapper (plus robuste que document.getElementById)
            content = wrapper.querySelector('#aiQuickResultContent') || document.getElementById('aiQuickResultContent');
            if (!content) {
                const alertBox = wrapper.querySelector('.alert') || wrapper;
                content = document.createElement('div');
                content.id = 'aiQuickResultContent';
                alertBox.insertBefore(content, alertBox.firstChild);
            }

            return { wrapper, content };
        }

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
            
            // Désactiver le bouton (si trouvé)
            const originalText = button ? button.innerHTML : '';
            if (button) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }
            
            try {
                // Appeler l'assistant IA
                const formData = new FormData();
                formData.append('action', 'generate');
                formData.append('theme', theme);

                const data = await postAI(formData);
                
                if (data.success) {
                    const els = ensureAIQuickResultElements();
                    if (els && els.content && els.wrapper) {
                        // Afficher le résultat
                        els.content.innerHTML = 
                            '<strong>✨ Suggestion générée :</strong><br><br>' + 
                            data.content.replace(/\n/g, '<br>');
                        els.wrapper.style.display = 'block';
                    } else {
                        // Fallback minimal
                        window.currentAIResult = data.content;
                        alert('Suggestion IA générée. Cliquez sur Appliquer si disponible.');
                    }
                    
                    // Stocker le résultat pour l'appliquer
                    window.currentAIResult = data.content;
                    
                    // Scroll vers le résultat
                    if (els && els.wrapper) {
                        els.wrapper.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'nearest' 
                        });
                    }
                } else {
                    alert('Erreur : ' + data.error);
                }
            } catch (error) {
                console.error('Erreur IA:', error);
                alert((error && error.message) ? error.message : 'Erreur de connexion avec l\'assistant IA');
            } finally {
                // Réactiver le bouton
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
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
            
            // Désactiver le bouton (si trouvé)
            const originalText = button ? button.innerHTML : '';
            if (button) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }
            
            try {
                // Appeler l'assistant IA
                const formData = new FormData();
                formData.append('action', 'improve');
                formData.append('text', text);

                const data = await postAI(formData);
                
                if (data.success) {
                    // Toujours stocker et appliquer directement dans le champ
                    window.currentAIResult = data.content;
                    contenuField.value = data.content;
                    if (typeof updateCharCount === 'function') updateCharCount();

                    const els = ensureAIQuickResultElements();
                    if (els && els.content && els.wrapper) {
                        // Afficher le résultat
                        els.content.innerHTML = 
                            '<strong>✅ Texte amélioré :</strong><br><br>' + 
                            data.content.replace(/\n/g, '<br>');
                        els.wrapper.style.display = 'block';
                    } else {
                        alert('✅ Texte amélioré et appliqué.');
                    }

                    // Petit feedback même si le bloc est affiché
                    if (els && els.wrapper) {
                        // rien
                    }
                    
                    // Scroll vers le résultat
                    if (els && els.wrapper) {
                        els.wrapper.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'nearest' 
                        });
                    }
                } else {
                    alert('Erreur : ' + data.error);
                }
            } catch (error) {
                console.error('Erreur IA:', error);
                alert((error && error.message) ? error.message : 'Erreur de connexion avec l\'assistant IA');
            } finally {
                // Réactiver le bouton
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }
        }
        
        function applyAIResult() {
            if (window.currentAIResult) {
                const contenuField = document.getElementById('contenu');
                if (contenuField) {
                    contenuField.value = window.currentAIResult;
                    const wrapper = document.getElementById('aiQuickResult');
                    if (wrapper) wrapper.style.display = 'none';
                    
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
                    
                    alert('✅ Suggestion ajoutée dans votre publication !');
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
            // Rendre les fonctions accessibles même si onclick inline est utilisé
            window.quickAIGenerate = quickAIGenerate;
            window.quickAIImprove = quickAIImprove;
            window.applyAIResult = applyAIResult;

            // Binder robuste: garantit que les clics fonctionnent
            const genBtn = document.getElementById('quickGenBtn');
            if (genBtn) {
                genBtn.onclick = null;
                genBtn.removeAttribute('onclick');
                genBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    quickAIGenerate();
                });
            }
            const addBtn = document.getElementById('aiQuickAddBtn');
            if (addBtn) {
                addBtn.onclick = null;
                addBtn.removeAttribute('onclick');
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    applyAIResult();
                });
            }

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
                document.getElementById('contenu').value = aiContent;
                
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
