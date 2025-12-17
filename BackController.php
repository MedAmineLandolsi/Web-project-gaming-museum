<?php
class BackController {
    private $reclamationModel;
    private $reponseModel;
    
    public function __construct() {
        $this->reclamationModel = new Reclamation();
        $this->reponseModel = new Reponse();
    }
    
    public function index() {
        $reclamations = $this->reclamationModel->getAll();
        $reponses = $this->reponseModel->getAll();
        
        // Inverser l'ordre
        $reclamations = array_reverse($reclamations);
        
        $data = [
            'reclamations' => $reclamations,
            'reponses' => $reponses
        ];
        
        $this->render('back/index', $data);
    }
    
    public function addReponse() {
        if ($_POST) {
            // Validation de la réponse
            $errors = $this->validateReponse($_POST);
            
            if (empty($errors)) {
                $reponse = [
                    'reclamationId' => $_POST['reclamationId'],
                    'message' => $this->sanitizeInput($_POST['message']),
                    'dateReponse' => date('d/m/Y'),
                    'heureReponse' => date('H:i:s'),
                    'adminName' => 'Administrateur'
                ];
                
                $this->reponseModel->addOrUpdate($reponse);
                header('Location: index.php?action=back&success=1');
                exit;
            } else {
                // Retourner aux détails avec erreur
                $reclamation = $this->reclamationModel->getById($_POST['reclamationId']);
                $reponse = $this->reponseModel->getByReclamationId($_POST['reclamationId']);
                
                $data = [
                    'reclamation' => $reclamation,
                    'reponse' => $reponse,
                    'error' => $errors['message']
                ];
                
                $this->render('back/details', $data);
            }
        }
    }
    
    public function deleteReclamation($id) {
        if ($id) {
            // Supprimer d'abord les réponses (pour éviter les erreurs de contraintes FK)
            $this->reponseModel->deleteByReclamationId($id);
            $this->reclamationModel->delete($id);
            header('Location: index.php?action=back&success=1');
            exit;
        }
    }
    
    public function exportData() {
        $reclamations = $this->reclamationModel->getAll();
        $reponses = $this->reponseModel->getAll();
        
        $data = [
            'reclamations' => $reclamations,
            'reponses' => $reponses,
            'exportDate' => date('d/m/Y H:i:s')
        ];
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="reclamations_' . date('Y-m-d') . '.json"');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    
    public function clearAll() {
        $this->reclamationModel->save([]);
        $this->reponseModel->save([]);
        header('Location: index.php?action=back&success=1');
        exit;
    }
    
    public function showDetails($id) {
        $reclamation = $this->reclamationModel->getById($id);
        $reponse = $this->reponseModel->getByReclamationId($id);
        
        $data = [
            'reclamation' => $reclamation,
            'reponse' => $reponse
        ];
        
        $this->render('back/details', $data);
    }
    
    public function editReclamation($id) {
        $reclamation = $this->reclamationModel->getById($id);
        
        if (!$reclamation) {
            header('Location: index.php?action=back');
            exit;
        }
        
        $data = [
            'reclamation' => $reclamation
        ];
        
        $this->render('back/edit', $data);
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
            $statut = $_POST['statut'] ?? '';
            
            // 1. Validation du nom
            if (empty(trim($nomClient))) {
                $errors['nomClient'] = 'Le nom est obligatoire';
            } elseif (strlen(trim($nomClient)) < 2) {
                $errors['nomClient'] = 'Le nom doit contenir au moins 2 caractères';
            }
            
            // 2. Validation de l'email
            if (empty(trim($emailClient))) {
                $errors['emailClient'] = 'L\'email est obligatoire';
            } elseif (!filter_var(trim($emailClient), FILTER_VALIDATE_EMAIL)) {
                $errors['emailClient'] = 'Format d\'email invalide';
            }
            
            // 3. Validation du type
            $validTypes = ['problème de commande', 'produit défectueux', 'retard de livraison', 'service client', 'autre', 'technique', 'commercial', 'administratif'];
            if (empty(trim($typeReclamation))) {
                $errors['typeReclamation'] = 'Le type est obligatoire';
            } elseif (!in_array(trim($typeReclamation), $validTypes)) {
                $errors['typeReclamation'] = 'Type de réclamation invalide';
            }
            
            // 4. Validation du titre
            if (empty(trim($titre))) {
                $errors['titre'] = 'Le titre est obligatoire';
            } elseif (strlen(trim($titre)) < 5) {
                $errors['titre'] = 'Le titre doit contenir au moins 5 caractères';
            }
            
            // 5. Validation de la description
            if (empty(trim($description))) {
                $errors['description'] = 'La description est obligatoire';
            } elseif (strlen(trim($description)) < 10) {
                $errors['description'] = 'La description doit contenir au moins 10 caractères';
            }
            
            // 6. Validation du statut (optionnel)
            if (!empty(trim($statut))) {
                $validStatuts = ['en_attente', 'en_cours', 'resolu', 'ferme', 'repondu'];
                if (!in_array(trim($statut), $validStatuts)) {
                    $errors['statut'] = 'Statut invalide';
                }
            }
            
            // Si pas d'erreurs, procéder à la mise à jour
            if (empty($errors)) {
                $data = [
                    'nomClient' => htmlspecialchars(strip_tags(trim($nomClient))),
                    'emailClient' => htmlspecialchars(strip_tags(trim($emailClient))),
                    'typeReclamation' => htmlspecialchars(strip_tags(trim($typeReclamation))),
                    'titre' => htmlspecialchars(strip_tags(trim($titre))),
                    'description' => htmlspecialchars(strip_tags(trim($description)))
                ];
                
                // Ajouter le statut si spécifié
                if (!empty(trim($statut))) {
                    $data['statut'] = htmlspecialchars(strip_tags(trim($statut)));
                }
                
                $success = $this->reclamationModel->update($id, $data);
                
                if ($success) {
                    $_SESSION['message'] = 'Réclamation modifiée avec succès!';
                    header('Location: index.php?action=back&method=details&id=' . $id);
                    exit;
                } else {
                    $_SESSION['error'] = 'Erreur lors de la modification';
                }
            } else {
                // Stocker les erreurs et données
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                $_SESSION['error'] = 'Des erreurs ont été détectées dans le formulaire';
            }
        }
        
        // Redirection vers le formulaire d'édition
        header('Location: index.php?action=back&method=edit&id=' . $id);
        exit;
    }
    
    private function validateReponse($data) {
        $errors = [];
        
        if (empty($data['message'])) {
            $errors['message'] = 'La réponse est requise';
        } elseif (strlen($data['message']) < 5) {
            $errors['message'] = 'La réponse doit contenir au moins 5 caractères';
        }
        
        return $errors;
    }
    
    private function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    private function render($view, $data = []) {
        // Chemin absolu pour éviter les problèmes
        $viewPath = __DIR__ . '/../View/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            extract($data);
            require_once $viewPath;
        } else {
            die("La vue '{$view}' n'existe pas. Chemin recherché: {$viewPath}");
        }
    }
    
}
// CETTE LIGNE DOIT ÊTRE LA DERNIÈRE - NE RIEN AJOUTER APRÈS
?>