<?php
class CommunauteController {
    private $communauteModel;
    private $publicationModel;
    private $validation;

    public function __construct($db) {
        $this->communauteModel = new Communaute($db);
        $this->publicationModel = new Publication($db);
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

    private function redirectBack(string $fallbackPath = '/communautes'): void {
        $target = $_SERVER['HTTP_REFERER'] ?? '';
        if (!$target) {
            $target = (defined('BASE_URL') ? BASE_URL : '') . $fallbackPath;
        }
        header('Location: ' . $target);
        exit;
    }

    // === FRONT OFFICE ===

    // Afficher toutes les communautés (Front) - MODIFIÉ AVEC TRI
    public function indexFront() {
        $order_by = $_GET['order_by'] ?? 'date_creation';
        $order_dir = $_GET['order_dir'] ?? 'DESC';

        $selectedCategorie = trim((string) ($_GET['categorie'] ?? ''));
        $categories = $this->communauteModel->getCategories();
        
        $result = $this->communauteModel->readOrderedFiltered($selectedCategorie, $order_by, $order_dir);
        $communautes = $result->fetchAll(PDO::FETCH_ASSOC);

        $joinedIds = [];
        if (isset($_SESSION['user_id'])) {
            $joinedIds = $this->communauteModel->getJoinedCommunauteIds($_SESSION['user_id']);
        }
        
        // Ajouter des données simulées pour l'affichage
        foreach($communautes as &$communaute) {
            $communaute['publications_count'] = rand(10, 50);
            $communaute['has_joined'] = isset($_SESSION['user_id'])
                ? in_array((int)($communaute['id'] ?? 0), $joinedIds, true)
                : false;
        }
        
        $title = "Nos Communautés";
        ob_start();
        include dirname(__DIR__) . '/view/frontoffice/communautes/index.php';
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/frontoffice/layout.php';
    }

    // Afficher une communauté (Front) - MODIFIÉ AVEC TRI DES PUBLICATIONS
    public function showFront($id) {
        $this->communauteModel->id = $id;
        
        if($this->communauteModel->read_single()) {
            // Récupérer les publications de la communauté (méthode simple et fiable)
            // On utilise la méthode read_by_communaute qui renvoie un tableau déjà décodé
            $publications = $this->publicationModel->read_by_communaute($id);
            if (!is_array($publications)) {
                $publications = [];
            }
            // CALCULER isOwner dispo globalement (fonctionnalité supprimée)
            global $isOwner;
            $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $this->communauteModel->createur_id;

            $hasJoined = false;
            if (isset($_SESSION['user_id']) && !$isOwner) {
                $hasJoined = $this->communauteModel->hasJoined($_SESSION['user_id'], $id);
            }

            $title = $this->communauteModel->nom;
                  ob_start();
                  include dirname(__DIR__) . '/view/frontoffice/communautes/show.php';
                  $content = ob_get_clean();
                  include dirname(__DIR__) . '/view/frontoffice/layout.php';
        } else {
            $this->showError("Communauté non trouvée");
        }
    }

    public function join(int $id): void {
        $this->requireLogin();

        $this->communauteModel->id = $id;
        if (!$this->communauteModel->read_single()) {
            $_SESSION['error_message'] = "Communauté introuvable.";
            $this->redirectBack('/communautes');
        }

        if ((int)$_SESSION['user_id'] === (int)$this->communauteModel->createur_id) {
            $_SESSION['error_message'] = "Vous êtes le créateur de cette communauté.";
            $this->redirectBack('/communautes/' . $id);
        }

        $this->communauteModel->join($_SESSION['user_id'], $id);
        $_SESSION['success_message'] = "Vous avez rejoint la communauté.";
        $this->redirectBack('/communautes/' . $id);
    }

    public function leave(int $id): void {
        $this->requireLogin();

        $this->communauteModel->id = $id;
        if (!$this->communauteModel->read_single()) {
            $_SESSION['error_message'] = "Communauté introuvable.";
            $this->redirectBack('/communautes');
        }

        if ((int)$_SESSION['user_id'] === (int)$this->communauteModel->createur_id) {
            $_SESSION['error_message'] = "Vous êtes le créateur de cette communauté.";
            $this->redirectBack('/communautes/' . $id);
        }

        $this->communauteModel->leave($_SESSION['user_id'], $id);
        $_SESSION['success_message'] = "Vous avez quitté la communauté.";
        $this->redirectBack('/communautes/' . $id);
    }

    // Créer une communauté (formulaire - Front)
    public function createFront() {
        $this->requireLogin();
        $title = "Créer une communauté";
        ob_start();
        include dirname(__DIR__) . '/view/frontoffice/communautes/create.php';
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/frontoffice/layout.php';
    }

    // Stocker une communauté (Front)
    public function storeFront($data) {
        $this->requireLogin();
        $errors = $this->validateCommunauteData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: ' . BASE_URL . '/communautes/create');
            exit;
        }

        $this->communauteModel->nom = $data['nom'];
        $this->communauteModel->categorie = $data['categorie'];
        $this->communauteModel->description = $data['description'];
        $this->communauteModel->createur_id = $_SESSION['user_id'] ?? 1;
        $this->communauteModel->avatar = $data['avatar'] ?? '';
        $this->communauteModel->visibilite = $data['visibilite'] ?? 'publique';
        $this->communauteModel->regles = $data['regles'] ?? '';

        if($this->communauteModel->create()) {
            $_SESSION['success_message'] = "Communauté créée avec succès !";
            header('Location: ' . BASE_URL . '/communautes');
        } else {
            $this->showError("Erreur lors de la création de la communauté");
        }
    }

    // === BACK OFFICE ===

    // Afficher toutes les communautés (Back)
    public function indexBack() {
        $selectedCategorie = trim((string) ($_GET['categorie'] ?? ''));
        $categories = $this->communauteModel->getCategories();

        $result = $this->communauteModel->readFiltered($selectedCategorie);
        $communautes = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Enrichir chaque communauté avec isOwner (fonctionnalité supprimée)
        $user_id = $_SESSION['user_id'] ?? null;

        $joinedIds = [];
        if ($user_id) {
            $joinedIds = $this->communauteModel->getJoinedCommunauteIds($user_id);
        }
        foreach ($communautes as &$communaute) {
            $communaute['isOwner'] = ($user_id && (int)$user_id === (int)($communaute['createur_id'] ?? 0));
            $cid = (int) ($communaute['id'] ?? 0);
            $communaute['has_joined'] = ($user_id && !$communaute['isOwner'] && $cid > 0)
                ? in_array($cid, $joinedIds, true)
                : false;
        }
        unset($communaute); // Libérer la référence
        
        $title = "Gestion des communautés";
        ob_start();
        include dirname(__DIR__) . '/view/backoffice/communautes/index.php';
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/backoffice/layout.php';
    }

    // Afficher une communauté (Back)
    public function showBack($id) {
        $this->communauteModel->id = $id;
        
        if($this->communauteModel->read_single()) {
            // Calculer isOwner (fonctionnalité supprimée)
            $isOwner = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$this->communauteModel->createur_id;
            
            // Récupérer les publications de la communauté (déjà décodées)
            $publications = $this->publicationModel->read_by_communaute($id);
            
            $title = "Détail de la communauté";
            ob_start();
            include dirname(__DIR__) . '/view/backoffice/communautes/show.php';
            $content = ob_get_clean();
            include dirname(__DIR__) . '/view/backoffice/layout.php';
        } else {
            $this->showError("Communauté non trouvée");
        }
    }

    // Créer une communauté (formulaire - Back)
    public function create() {
        $this->requireLogin();
        $title = "Créer une nouvelle communauté";
        ob_start();
        include dirname(__DIR__) . '/view/backoffice/communautes/create.php';
        $content = ob_get_clean();
        include dirname(__DIR__) . '/view/backoffice/layout.php';
    }

    // Stocker une nouvelle communauté (Back)
    public function store($data) {
        $this->requireLogin();
        $errors = $this->validateCommunauteData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: ' . BASE_URL . '/admin/communautes/create');
            exit;
        }

        $this->communauteModel->nom = $data['nom'];
        $this->communauteModel->categorie = $data['categorie'];
        $this->communauteModel->description = $data['description'];
        $this->communauteModel->createur_id = $_SESSION['user_id'] ?? 1;
        $this->communauteModel->avatar = $data['avatar'] ?? '';
        $this->communauteModel->visibilite = $data['visibilite'] ?? 'publique';
        $this->communauteModel->regles = $data['regles'] ?? '';

        if($this->communauteModel->create()) {
            $_SESSION['success_message'] = "Communauté créée avec succès";
            header('Location: ' . BASE_URL . '/admin/communautes');
        } else {
            $this->showError("Erreur lors de la création de la communauté");
        }
    }

    // Modifier une communauté (formulaire - Back)
    public function edit($id) {
        $this->requireLogin();
        $this->communauteModel->id = $id;
        
        if($this->communauteModel->read_single()) {
            $title = "Modifier la communauté";
            ob_start();
            include dirname(__DIR__) . '/view/backoffice/communautes/edit.php';
            $content = ob_get_clean();
            include dirname(__DIR__) . '/view/backoffice/layout.php';
        } else {
            $this->showError("Communauté non trouvée");
        }
    }

    // Mettre à jour une communauté (Back)
    public function update($id, $data) {
        $this->requireLogin();
        $errors = $this->validateCommunauteData($data, $id);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: ' . BASE_URL . '/admin/communautes/' . $id . '/edit');
            exit;
        }

        $this->communauteModel->id = $id;
        $this->communauteModel->nom = $data['nom'];
        $this->communauteModel->categorie = $data['categorie'];
        $this->communauteModel->description = $data['description'];
        $this->communauteModel->avatar = $data['avatar'] ?? '';
        $this->communauteModel->visibilite = $data['visibilite'] ?? 'publique';
        $this->communauteModel->regles = $data['regles'] ?? '';

        if($this->communauteModel->update()) {
            $_SESSION['success_message'] = "Communauté mise à jour avec succès";
            header('Location: ' . BASE_URL . '/admin/communautes/' . $id);
        } else {
            $this->showError("Erreur lors de la mise à jour de la communauté");
        }
    }

    // Supprimer une communauté (Back)
    public function delete($id) {
        $this->requireLogin();
        $this->communauteModel->id = $id;
        
        if($this->communauteModel->delete()) {
            $_SESSION['success_message'] = "Communauté supprimée avec succès";
            header('Location: ' . BASE_URL . '/admin/communautes');
        } else {
            $this->showError("Erreur lors de la suppression de la communauté");
        }
    }

    // === VALIDATION ===

    private function validateCommunauteData($data, $id = null) {
        $errors = [];

        // Validation nom
        if (empty($data['nom'])) {
            $errors['nom'] = "Le nom est obligatoire";
        } elseif (strlen($data['nom']) > 100) {
            $errors['nom'] = "Le nom ne doit pas dépasser 100 caractères";
        } elseif ($this->communauteModel->nomExists($data['nom'], $id)) {
            $errors['nom'] = "Ce nom de communauté est déjà utilisé";
        }

        // Validation catégorie
        if (empty($data['categorie'])) {
            $errors['categorie'] = "La catégorie est obligatoire";
        } elseif (strlen($data['categorie']) > 50) {
            $errors['categorie'] = "La catégorie ne doit pas dépasser 50 caractères";
        }

        // Validation description
        if (empty($data['description'])) {
            $errors['description'] = "La description est obligatoire";
        } elseif (strlen($data['description']) < 10) {
            $errors['description'] = "La description doit faire au moins 10 caractères";
        }

        // Validation visibilité
        $allowed_visibility = ['publique', 'privee', 'cachee'];
        if (!empty($data['visibilite']) && !in_array($data['visibilite'], $allowed_visibility)) {
            $errors['visibilite'] = "Visibilité invalide";
        }

        // Validation avatar (URL)
        if (!empty($data['avatar']) && !$this->validation->isValidUrl($data['avatar'])) {
            $errors['avatar'] = "L'URL de l'avatar n'est pas valide";
        }

        return $errors;
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