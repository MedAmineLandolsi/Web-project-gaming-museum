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
        
        // Si un filtre communauté est spécifié
        if ($communaute_id) {
            $publications = $this->publicationModel->readByCommunauteOrdered($communaute_id, $order_by, $order_dir);
            
            // Récupérer les infos de la communauté pour l'affichage
            $this->communauteModel->id = $communaute_id;
            $communaute_info = $this->communauteModel->read_single() ? $this->communauteModel : null;
        } else {
            // Toutes les publications
            $publications = $this->publicationModel->readOrdered($order_by, $order_dir);
            $communaute_info = null;
        }
        
        $title = $communaute_info ? "Publications - " . $communaute_info->nom : "Publications récentes";

        // Debug temporaire: nombre de publications récupérées
        try {
            $pubCount = is_array($publications) ? count($publications) : 0;
            error_log("[DEBUG] indexFront publications count: " . $pubCount);
            // debug count logged to error_log only
        } catch (Exception $e) {
            error_log("[DEBUG] indexFront error counting publications: " . $e->getMessage());
        }

        // Si aucune publication trouvée, essayer un fallback plus permissif (getLatest)
        if (empty($publications) || !is_array($publications)) {
            try {
                $fallback = $this->publicationModel->getLatest(10);
                if (!empty($fallback)) {
                    $publications = $fallback;
                    // fallback count logged to error_log only
                    error_log("[DEBUG] indexFront used fallback getLatest, count=" . count($fallback));
                } else {
                    // fallback returned 0 rows (no session debug)
                    error_log("[DEBUG] indexFront fallback returned 0 rows");
                }
            } catch (Exception $e) {
                error_log("[DEBUG] indexFront fallback error: " . $e->getMessage());
                // fallback error occurred (no session debug)
            }
        }

        // Ajouter un échantillon d'IDs pour debug rapide
        try {
            $sampleIds = [];
            if (is_array($publications)) {
                foreach (array_slice($publications, 0, 5) as $p) {
                    if (is_array($p) && isset($p['id'])) $sampleIds[] = $p['id'];
                    elseif (is_object($p) && isset($p->id)) $sampleIds[] = $p->id;
                }
            }
            // sample ids logged to error_log only
            error_log("[DEBUG] indexFront sample ids: " . implode(',', $sampleIds));
        } catch (Exception $e) {
            error_log("[DEBUG] indexFront sample ids error: " . $e->getMessage());
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
        
        $title = "Partager une publication";
        ob_start();
        include dirname(__DIR__) . '/view/frontoffice/publications/create.php';
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
        // Déterminer l'id du membre correspondant à l'utilisateur connecté
        $sessionUserId = $_SESSION['user_id'] ?? null;
        $membreId = null;
        if ($sessionUserId) {
            $membreId = $this->membreModel->findByUserId($sessionUserId);
        }
        // Si on trouve un membre, l'utiliser comme auteur, sinon fallback sur session user id
        $this->publicationModel->auteur_id = $membreId ?? ($sessionUserId ?? 1);
        // Stocker aussi la référence users.id si disponible
        $this->publicationModel->user_id = $sessionUserId ?? null;
        $this->publicationModel->contenu = $data['contenu'];
        
        // Gestion des images uploadées
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
            
            // CORRECTION : Rediriger vers les publications filtrées par communauté
            header('Location: /projet/publications?communaute=' . $data['communaute_id']);
            exit;
        } else {
            $this->showError("Erreur lors de la création de la publication");
        }
    }

    // MODIFIER UNE PUBLICATION - CORRIGÉ
    public function editFront($id) {
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->read_single()) {
            // Vérifier si l'utilisateur peut modifier
            if (!$this->canUserEditPublication($this->publicationModel->auteur_id)) {
                $_SESSION['error_message'] = "Vous n'avez pas la permission de modifier cette publication";
                header('Location: /projet/publications/' . $id);
                exit;
            }
            
            // Récupérer les communautés pour le select
            $communautes_result = $this->communauteModel->read();
            $communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);
            
            $title = "Modifier la publication";
            ob_start();
            
            // Vérifier si le fichier de vue existe (chemin correct)
            $viewPath = dirname(__DIR__) . '/view/frontoffice/publications/edit.php';
            if (file_exists($viewPath)) {
                include $viewPath;
            } else {
                // Vue de secours
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

    // METTRE À JOUR UNE PUBLICATION - CORRIGÉ
    public function updateFront($id, $data) {
        $this->publicationModel->id = $id;
        
        if(!$this->publicationModel->read_single()) {
            $_SESSION['error_message'] = "Publication non trouvée";
            header('Location: /projet/publications');
            exit;
        }
        
        // Vérifier les permissions
        if (!$this->canUserEditPublication($this->publicationModel->auteur_id)) {
            $_SESSION['error_message'] = "Vous n'avez pas la permission de modifier cette publication";
            header('Location: /projet/publications/' . $id);
            exit;
        }
        
        // Conserver la communauté existante par défaut (non modifiable)
        $data['communaute_id'] = $this->publicationModel->communaute_id;
        
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/publications/edit/' . $id);
            exit;
        }

        $this->publicationModel->contenu = $data['contenu'];
        
        // Gestion des images uploadées
        $images = $this->publicationModel->images; // Garder les images existantes par défaut
        
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
            // Ajouter les nouvelles images aux existantes
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

    // SUPPRIMER UNE PUBLICATION - CORRIGÉ
    public function deleteFront($id) {
        $this->publicationModel->id = $id;
        
        if(!$this->publicationModel->read_single()) {
            $_SESSION['error_message'] = "Publication non trouvée";
            header('Location: /projet/publications');
            exit;
        }
        
        // Vérifier les permissions
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

        // Normaliser le champ images (peut être une chaîne séparée par des virgules)
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

        // Normaliser le champ images (si l'admin saisit des URLs séparées par des virgules)
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

    // VÉRIFICATION DES PERMISSIONS - NOUVELLE MÉTHODE
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