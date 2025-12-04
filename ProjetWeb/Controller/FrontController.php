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
    
    // AJOUTEZ LES NOUVELLES MÉTHODES ICI, À L'INTÉRIEUR DE LA CLASSE :
    
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Adaptation à votre structure de données existante
            $nomClient = $_POST['nomClient'] ?? '';
            $emailClient = $_POST['emailClient'] ?? '';
            $typeReclamation = $_POST['typeReclamation'] ?? '';
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            
            // Validation des données (vous pouvez réutiliser votre méthode validateReclamation)
            $errors = $this->validateReclamation($_POST);
            
            if (empty($errors)) {
                $reclamationData = [
                    'nomClient' => $nomClient,
                    'emailClient' => $emailClient,
                    'typeReclamation' => $typeReclamation,
                    'titre' => $titre,
                    'description' => $description
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
                $_SESSION['error'] = 'Veuillez corriger les erreurs dans le formulaire';
                // Stocker les erreurs en session pour les afficher dans le formulaire
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
            }
        }
        
        header('Location: index.php?action=front&method=edit&id=' . $id);
        exit;
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
    
} // NE PAS OUBLIER : cette accolade ferme la classe
// RIEN ne doit être placé après cette accolade, sauf éventuellement la balise PHP fermante
?>