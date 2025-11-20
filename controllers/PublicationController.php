<?php
class PublicationController {
    private $publicationModel;
    private $communauteModel;
    private $validation;

    public function __construct($db) {
        $this->publicationModel = new Publication($db);
        $this->communauteModel = new Communaute($db);
        $this->validation = new Validation();
    }

    // === FRONT OFFICE ===

    // Afficher toutes les publications (Front) - CORRIGÉ
    public function indexFront() {
        $publications = $this->publicationModel->read(); // Déjà décodé dans le modèle
        
        $title = "Publications récentes";
        ob_start();
        include 'views/front/publications/index.php';
        $content = ob_get_clean();
        include 'views/front/layout.php';
    }

    // Afficher une publication (Front) - CORRIGÉ
    public function showFront($id) {
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->read_single()) {
            $title = "Publication de " . $this->publicationModel->auteur_nom;
            ob_start();
            include 'views/front/publications/show.php';
            $content = ob_get_clean();
            include 'views/front/layout.php';
        } else {
            $this->showError("Publication non trouvée");
        }
    }

    // Créer une publication (formulaire - Front)
    public function createFront() {
        // Récupérer les communautés pour le select
        $communautes_result = $this->communauteModel->read();
        $communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);
        
        $title = "Partager une publication";
        ob_start();
        include 'views/front/publications/create.php';
        $content = ob_get_clean();
        include 'views/front/layout.php';
    }

    // Stocker une publication (Front)
    public function storeFront($data) {
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/publications/create');
            exit;
        }

        $this->publicationModel->communaute_id = $data['communaute_id'];
        $this->publicationModel->auteur_id = $_SESSION['user_id'] ?? 1;
        $this->publicationModel->contenu = $data['contenu'];
        
        // Gestion des images uploadées
        $images = [];
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $image_name = time() . '_' . $_FILES['images']['name'][$key];
                    $upload_dir = 'uploads/publications/';
                    
                    // Créer le dossier s'il n'existe pas
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
            header('Location: /projet/communautes/' . $data['communaute_id']);
        } else {
            $this->showError("Erreur lors de la création de la publication");
        }
    }

    // === BACK OFFICE ===

    // Afficher toutes les publications (Back) - CORRIGÉ
    public function indexBack() {
        $publications = $this->publicationModel->read(); // Déjà décodé dans le modèle
        
        $title = "Gestion des publications";
        ob_start();
        include 'views/back/publications/index.php';
        $content = ob_get_clean();
        include 'views/back/layout.php';
    }

    // Afficher une publication (Back) - CORRIGÉ
    public function showBack($id) {
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->read_single()) {
            $title = "Détail de la publication";
            ob_start();
            include 'views/back/publications/show.php';
            $content = ob_get_clean();
            include 'views/back/layout.php';
        } else {
            $this->showError("Publication non trouvée");
        }
    }

    // Créer une publication (formulaire - Back)
    public function create() {
        // Récupérer les communautés pour le select
        $communautes_result = $this->communauteModel->read();
        $communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);
        
        $title = "Créer une nouvelle publication";
        ob_start();
        include 'views/back/publications/create.php';
        $content = ob_get_clean();
        include 'views/back/layout.php';
    }

    // Stocker une nouvelle publication (Back)
    public function store($data) {
        $errors = $this->validatePublicationData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/admin/publications/create');
            exit;
        }

        $this->publicationModel->communaute_id = $data['communaute_id'];
        $this->publicationModel->auteur_id = $_SESSION['user_id'] ?? 1;
        $this->publicationModel->contenu = $data['contenu'];
        
        // Gestion des images (URLs séparées par des virgules)
        $images = [];
        if (!empty($data['images'])) {
            $image_urls = explode(',', $data['images']);
            foreach ($image_urls as $url) {
                $url = trim($url);
                if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                    $images[] = $url;
                }
            }
        }
        
        $this->publicationModel->images = !empty($images) ? $images : null;
        $this->publicationModel->likes = 0;
        $this->publicationModel->commentaires = 0;

        if($this->publicationModel->create()) {
            $_SESSION['success_message'] = "Publication créée avec succès";
            header('Location: /projet/admin/publications');
        } else {
            $this->showError("Erreur lors de la création de la publication");
        }
    }

    // Modifier une publication (formulaire - Back)
    public function edit($id) {
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->read_single()) {
            // Récupérer les communautés pour le select
            $communautes_result = $this->communauteModel->read();
            $communautes = $communautes_result->fetchAll(PDO::FETCH_ASSOC);
            
            $title = "Modifier la publication";
            ob_start();
            include 'views/back/publications/edit.php';
            $content = ob_get_clean();
            include 'views/back/layout.php';
        } else {
            $this->showError("Publication non trouvée");
        }
    }

    // Mettre à jour une publication (Back)
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
        
        // Gestion des images (URLs séparées par des virgules)
        $images = [];
        if (!empty($data['images'])) {
            $image_urls = explode(',', $data['images']);
            foreach ($image_urls as $url) {
                $url = trim($url);
                if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                    $images[] = $url;
                }
            }
        }
        
        $this->publicationModel->images = !empty($images) ? $images : null;
        $this->publicationModel->likes = $data['likes'] ?? 0;
        $this->publicationModel->commentaires = $data['commentaires'] ?? 0;

        if($this->publicationModel->update()) {
            $_SESSION['success_message'] = "Publication mise à jour avec succès";
            header('Location: /projet/admin/publications/' . $id);
        } else {
            $this->showError("Erreur lors de la mise à jour de la publication");
        }
    }

    // Supprimer une publication (Back)
    public function delete($id) {
        $this->publicationModel->id = $id;
        
        if($this->publicationModel->delete()) {
            $_SESSION['success_message'] = "Publication supprimée avec succès";
            header('Location: /projet/admin/publications');
        } else {
            $this->showError("Erreur lors de la suppression de la publication");
        }
    }

    // === VALIDATION ===

    private function validatePublicationData($data) {
        $errors = [];

        // Validation contenu
        if (empty($data['contenu'])) {
            $errors['contenu'] = "Le contenu est obligatoire";
        } elseif (strlen($data['contenu']) < 5) {
            $errors['contenu'] = "Le contenu doit faire au moins 5 caractères";
        } elseif (strlen($data['contenu']) > 1000) {
            $errors['contenu'] = "Le contenu ne doit pas dépasser 1000 caractères";
        }

        // Validation communauté
        if (empty($data['communaute_id'])) {
            $errors['communaute_id'] = "La communauté est obligatoire";
        } elseif (!is_numeric($data['communaute_id'])) {
            $errors['communaute_id'] = "La communauté sélectionnée est invalide";
        }

        // Validation likes et commentaires (pour l'édition)
        if (isset($data['likes']) && !is_numeric($data['likes'])) {
            $errors['likes'] = "Le nombre de likes doit être un nombre";
        }

        if (isset($data['commentaires']) && !is_numeric($data['commentaires'])) {
            $errors['commentaires'] = "Le nombre de commentaires doit être un nombre";
        }

        return $errors;
    }

    private function showError($message) {
        $title = "Erreur";
        ob_start();
        echo "<div class='alert alert-danger'>$message</div>";
        $content = ob_get_clean();
        include 'views/front/layout.php';
    }
}
?>