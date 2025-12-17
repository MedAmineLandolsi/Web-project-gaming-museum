<?php
class FrontController {
    private $reclamationModel;
    private $reponseModel;
    
    public function __construct() {
        $this->reclamationModel = new Reclamation();
        $this->reponseModel = new Reponse();
    }
    
    public function index() {
        $reclamations = $this->reclamationModel->getAll();
        $reponses = $this->reponseModel->getAll();
        
        $data = [
            'reclamations' => $reclamations,
            'reponses' => $reponses
        ];
        
        $this->render('front/index', $data);
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();

        header('Location: ../gaming_museum/view/frontoffice/index.php');
        exit;
    }
    
    public function addReclamation() {
        if ($_POST) {
            // Validation PHP
            $errors = $this->validateReclamation($_POST);
            
            if (empty($errors)) {
                $reclamation = [
                    'nomClient' => $_POST['nomClient'],
                    'emailClient' => $_POST['emailClient'],
                    'typeReclamation' => $_POST['typeReclamation'],
                    'titre' => $_POST['titre'],
                    'description' => $_POST['description'],
                    'statut' => 'en_attente'
                ];
                
                $result = $this->reclamationModel->add($reclamation);
                if ($result) {
                    header('Location: index.php?action=front&success=1');
                    exit;
                } else {
                    $errors['general'] = 'Erreur lors de l\'ajout de la réclamation';
                }
            }
            
            // Si erreurs, réafficher le formulaire
            $reclamations = $this->reclamationModel->getAll();
            $reponses = $this->reponseModel->getAll();
            
            $data = [
                'reclamations' => $reclamations,
                'reponses' => $reponses,
                'errors' => $errors,
                'formData' => $_POST
            ];
            
            $this->render('front/index', $data);
        }
    }
    
    private function validateReclamation($data) {
        $errors = [];
        
        // Validation du nom
        if (empty($data['nomClient'])) {
            $errors['nomClient'] = 'Le nom est requis';
        } elseif (strlen($data['nomClient']) < 2) {
            $errors['nomClient'] = 'Le nom doit contenir au moins 2 caractères';
        }
        
        // Validation de l'email
        if (empty($data['emailClient'])) {
            $errors['emailClient'] = 'L\'email est requis';
        } elseif (!filter_var($data['emailClient'], FILTER_VALIDATE_EMAIL)) {
            $errors['emailClient'] = 'L\'email n\'est pas valide';
        }
        
        // Validation du type
        if (empty($data['typeReclamation'])) {
            $errors['typeReclamation'] = 'Le type de réclamation est requis';
        }
        
        // Validation du titre
        if (empty($data['titre'])) {
            $errors['titre'] = 'Le titre est requis';
        } elseif (strlen($data['titre']) < 5) {
            $errors['titre'] = 'Le titre doit contenir au moins 5 caractères';
        }
        
        // Validation de la description
        if (empty($data['description'])) {
            $errors['description'] = 'La description est requise';
        } elseif (strlen($data['description']) < 10) {
            $errors['description'] = 'La description doit contenir au moins 10 caractères';
        }
        
        return $errors;
    }
    
    public function showDetails($id) {
        $reclamation = $this->reclamationModel->getById($id);
        $reponse = $this->reponseModel->getByReclamationId($id);
        
        $data = [
            'reclamation' => $reclamation,
            'reponse' => $reponse
        ];
        
        $this->render('front/details', $data);
    }
    
    public function editReclamation($id) {
        $reclamation = $this->reclamationModel->getById($id);
        
        if (!$reclamation) {
            header('Location: index.php?action=front');
            exit;
        }
        
        $data = [
            'reclamation' => $reclamation
        ];
        
        $this->render('front/edit', $data);
    }

    public function updateReclamation($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation PHP côté serveur
            $errors = [];
            
            // Récupération des données
            $nomClient = $_POST['nomClient'] ?? '';
            $emailClient = $_POST['emailClient'] ?? '';
            $typeReclamation = $_POST['typeReclamation'] ?? '';
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            
            // 1. Validation du nom
            if (empty(trim($nomClient))) {
                $errors['nomClient'] = 'Le nom est obligatoire';
            } elseif (strlen(trim($nomClient)) < 2) {
                $errors['nomClient'] = 'Le nom doit contenir au moins 2 caractères';
            } elseif (strlen(trim($nomClient)) > 50) {
                $errors['nomClient'] = 'Le nom ne doit pas dépasser 50 caractères';
            }
            
            // 2. Validation de l'email
            if (empty(trim($emailClient))) {
                $errors['emailClient'] = 'L\'email est obligatoire';
            } elseif (!filter_var(trim($emailClient), FILTER_VALIDATE_EMAIL)) {
                $errors['emailClient'] = 'Format d\'email invalide';
            } elseif (strlen(trim($emailClient)) > 100) {
                $errors['emailClient'] = 'L\'email ne doit pas dépasser 100 caractères';
            }
            
            // 3. Validation du type
            $validTypes = ['problème de commande', 'produit défectueux', 'retard de livraison', 'service client', 'autre'];
            if (empty(trim($typeReclamation))) {
                $errors['typeReclamation'] = 'Le type de réclamation est obligatoire';
            } elseif (!in_array(trim($typeReclamation), $validTypes)) {
                $errors['typeReclamation'] = 'Type de réclamation invalide';
            }
            
            // 4. Validation du titre
            if (empty(trim($titre))) {
                $errors['titre'] = 'Le titre est obligatoire';
            } elseif (strlen(trim($titre)) < 5) {
                $errors['titre'] = 'Le titre doit contenir au moins 5 caractères';
            } elseif (strlen(trim($titre)) > 200) {
                $errors['titre'] = 'Le titre ne doit pas dépasser 200 caractères';
            }
            
            // 5. Validation de la description
            if (empty(trim($description))) {
                $errors['description'] = 'La description est obligatoire';
            } elseif (strlen(trim($description)) < 10) {
                $errors['description'] = 'La description doit contenir au moins 10 caractères';
            } elseif (strlen(trim($description)) > 2000) {
                $errors['description'] = 'La description ne doit pas dépasser 2000 caractères';
            }
            
            // Si pas d'erreurs, procéder à la mise à jour
            if (empty($errors)) {
                $reclamationData = [
                    'nomClient' => htmlspecialchars(strip_tags(trim($nomClient))),
                    'emailClient' => htmlspecialchars(strip_tags(trim($emailClient))),
                    'typeReclamation' => htmlspecialchars(strip_tags(trim($typeReclamation))),
                    'titre' => htmlspecialchars(strip_tags(trim($titre))),
                    'description' => htmlspecialchars(strip_tags(trim($description)))
                ];
                
                $success = $this->reclamationModel->update($id, $reclamationData);
                
                if ($success) {
                    $_SESSION['message'] = 'Réclamation modifiée avec succès!';
                    header('Location: index.php?action=front&method=details&id=' . $id);
                    exit;
                } else {
                    $_SESSION['error'] = 'Erreur lors de la modification de la réclamation';
                }
            } else {
                // Stocker les erreurs et données pour les afficher dans le formulaire
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                $_SESSION['error'] = 'Veuillez corriger les erreurs dans le formulaire';
            }
        }
        
        // Redirection vers le formulaire d'édition avec les erreurs
        header('Location: index.php?action=front&method=edit&id=' . $id);
        exit;
    }
   public function historique() {
    $reclamations = $this->reclamationModel->getAll();
    $reponses = $this->reponseModel->getAll();
    
    $data = [
        'reclamations' => $reclamations,
        'reponses' => $reponses
    ];
    
    $this->render('front/historique', $data);
}
    private function render($view, $data = []) {
        $viewPath = __DIR__ . '/../View/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            extract($data);
            require_once $viewPath;
        } else {
            die("La vue '{$view}' n'existe pas. Chemin recherché: {$viewPath}");
        }
    }
    
    // AJOUTEZ CETTE MÉTHODE POUR NETTOYER LES DONNÉES
    private function sanitizeInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeInput($value);
            }
            return $data;
        }
        
        // Convertir les caractères spéciaux en entités HTML
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        // Supprimer les balises HTML/PHP
        $data = strip_tags($data);
        
        // Supprimer les espaces en début et fin
        $data = trim($data);
        
        // Supprimer les antislashs
        $data = stripslashes($data);
        
        return $data;
    }
    
}
// NE RIEN METTRE APRÈS CETTE LIGNE SAUF LA BALISE PHP FERMANTE
?>