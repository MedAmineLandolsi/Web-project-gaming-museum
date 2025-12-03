<?php
class MembreController {
    private $membreModel;
    private $communauteModel;
    private $validation;

    public function __construct($db) {
        $this->membreModel = new Membre($db);
        $this->communauteModel = new Communaute($db);
        $this->validation = new Validation();
    }

    // === FRONT OFFICE ===

    // Afficher tous les membres (Front)
    public function indexFront() {
        // Récupérer tous les membres
        $result = $this->membreModel->read();
        $membres = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer toutes les communautés pour afficher celles créées par chaque membre
        $communauteResult = $this->communauteModel->read();
        $communautes = $communauteResult->fetchAll(PDO::FETCH_ASSOC);
        
        // Enrichir chaque communauté avec isMember et isOwner
        $user_id = $_SESSION['user_id'] ?? null;
        foreach ($communautes as &$communaute) {
            $communaute['isOwner'] = ($user_id && $user_id == $communaute['createur_id']);
            $communaute['isMember'] = ($user_id && $this->communauteModel->hasJoined($user_id, $communaute['id']));
        }
        unset($communaute); // Libérer la référence
        
        $title = "Liste des Membres";
        ob_start();
        include 'views/front/membres/index.php';
        $content = ob_get_clean();
        include 'views/front/layout.php';
    }

    // Afficher un membre (Front)
    public function showFront($id) {
        $this->membreModel->id = $id;
        
        if($this->membreModel->read_single()) {
            // Récupérer les communautés créées par ce membre
            $communautesResult = $this->communauteModel->read_by_createur($id);
            $communautes = $communautesResult->fetchAll(PDO::FETCH_ASSOC);
            
            $title = "Profil de " . $this->membreModel->prenom . " " . $this->membreModel->nom;
            ob_start();
            include 'views/front/membres/show.php';
            $content = ob_get_clean();
            include 'views/front/layout.php';
        } else {
            $this->showError("Membre non trouvé");
        }
    }

    // === BACK OFFICE ===

    // Afficher tous les membres (Back)
    public function indexBack() {
        $result = $this->membreModel->read();
        $membres = $result->fetchAll(PDO::FETCH_ASSOC);
        
        $title = "Gestion des membres";
        ob_start();
        include 'views/back/membres/index.php';
        $content = ob_get_clean();
        include 'views/back/layout.php';
    }

    // Afficher un membre (Back)
    public function showBack($id) {
        $this->membreModel->id = $id;
        
        if($this->membreModel->read_single()) {
            $title = "Détail du membre";
            ob_start();
            include 'views/back/membres/show.php';
            $content = ob_get_clean();
            include 'views/back/layout.php';
        } else {
            $this->showError("Membre non trouvé", true);
        }
    }

    // Créer un membre (formulaire - Back)
    public function create() {
        $title = "Créer un nouveau membre";
        ob_start();
        include 'views/back/membres/create.php';
        $content = ob_get_clean();
        include 'views/back/layout.php';
    }

    // Stocker un nouveau membre (Back)
    public function store($data) {
        $errors = $this->validateMembreData($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/admin/membres/create');
            exit;
        }

        $this->membreModel->nom = $data['nom'];
        $this->membreModel->prenom = $data['prenom'];
        $this->membreModel->email = $data['email'];
        $this->membreModel->mot_de_passe = $data['mot_de_passe'];
        $this->membreModel->statut = $data['statut'];
        $this->membreModel->avatar = $data['avatar'];
        $this->membreModel->bio = $data['bio'];

        if($this->membreModel->create()) {
            $_SESSION['success_message'] = "Membre créé avec succès";
            header('Location: /projet/admin/membres');
        } else {
            $this->showError("Erreur lors de la création du membre");
        }
    }

    // Modifier un membre (formulaire - Back)
    public function edit($id) {
        $this->membreModel->id = $id;
        
        if($this->membreModel->read_single()) {
            $title = "Modifier le membre";
            ob_start();
            include 'views/back/membres/edit.php';
            $content = ob_get_clean();
        } else {
            $this->showError("Membre non trouvé", true);
        }
    }

    // Mettre à jour un membre (Back)
    public function update($id, $data) {
        $errors = $this->validateMembreData($data, $id);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            header('Location: /projet/admin/membres/' . $id . '/edit');
            exit;
        }

        $this->membreModel->id = $id;
        $this->membreModel->nom = $data['nom'];
        $this->membreModel->prenom = $data['prenom'];
        $this->membreModel->email = $data['email'];
        $this->membreModel->statut = $data['statut'];
        $this->membreModel->avatar = $data['avatar'];
        $this->membreModel->bio = $data['bio'];

        if($this->membreModel->update()) {
            $_SESSION['success_message'] = "Membre mis à jour avec succès";
            header('Location: /projet/admin/membres/' . $id);
        } else {
            $this->showError("Erreur lors de la mise à jour du membre", true);
        }
    }

    // Supprimer un membre (Back)
    public function delete($id) {
        $this->membreModel->id = $id;
        
        if($this->membreModel->delete()) {
            $_SESSION['success_message'] = "Membre supprimé avec succès";
            header('Location: /projet/admin/membres');
        } else {
            $this->showError("Erreur lors de la suppression du membre", true);
        }
    }

    // === VALIDATION ===

    private function validateMembreData($data, $id = null) {
        $errors = [];

        // Validation nom
        if (empty($data['nom'])) {
            $errors['nom'] = "Le nom est obligatoire";
        } elseif (strlen($data['nom']) > 50) {
            $errors['nom'] = "Le nom ne doit pas dépasser 50 caractères";
        } elseif (!$this->validation->isAlpha($data['nom'])) {
            $errors['nom'] = "Le nom ne doit contenir que des lettres";
        }

        // Validation prénom
        if (empty($data['prenom'])) {
            $errors['prenom'] = "Le prénom est obligatoire";
        } elseif (strlen($data['prenom']) > 50) {
            $errors['prenom'] = "Le prénom ne doit pas dépasser 50 caractères";
        } elseif (!$this->validation->isAlpha($data['prenom'])) {
            $errors['prenom'] = "Le prénom ne doit contenir que des lettres";
        }

        // Validation email
        if (empty($data['email'])) {
            $errors['email'] = "L'email est obligatoire";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Format d'email invalide";
        } elseif ($this->membreModel->emailExists($data['email'], $id)) {
            $errors['email'] = "Cet email est déjà utilisé";
        }

        // Validation mot de passe (seulement pour création)
        if (!$id && empty($data['mot_de_passe'])) {
            $errors['mot_de_passe'] = "Le mot de passe est obligatoire";
        } elseif (!$id && strlen($data['mot_de_passe']) < 6) {
            $errors['mot_de_passe'] = "Le mot de passe doit faire au moins 6 caractères";
        }

        // Validation statut
        $allowed_status = ['actif', 'inactif', 'suspendu'];
        if (empty($data['statut'])) {
            $errors['statut'] = "Le statut est obligatoire";
        } elseif (!in_array($data['statut'], $allowed_status)) {
            $errors['statut'] = "Statut invalide";
        }

        return $errors;
    }

    private function showError($message, $isBack = false) {
        $title = "Erreur";
        ob_start();
        echo "<div class='alert alert-danger'>$message</div>";
        $content = ob_get_clean();
        if ($isBack) {
            include 'views/back/layout.php';
        } else {
            include 'views/front/layout.php';
        }
    }
}

// Classe de validation
class Validation {
    public function isAlpha($string) {
        return preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $string);
    }

    public function isAlphanumeric($string) {
        return preg_match('/^[a-zA-Z0-9À-ÿ\s\-_]+$/', $string);
    }

    public function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
?>