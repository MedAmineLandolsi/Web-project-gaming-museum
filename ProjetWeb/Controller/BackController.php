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
            $this->reclamationModel->delete($id);
            $this->reponseModel->deleteByReclamationId($id);
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
    
    // AJOUTEZ LES NOUVELLES MÉTHODES ICI, À L'INTÉRIEUR DE LA CLASSE :
    
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Adaptation à votre structure de données
            $nomClient = $_POST['nomClient'] ?? '';
            $emailClient = $_POST['emailClient'] ?? '';
            $typeReclamation = $_POST['typeReclamation'] ?? '';
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $statut = $_POST['statut'] ?? '';
            
            // Validation des données
            if (!empty($nomClient) && !empty($emailClient) && !empty($typeReclamation) && !empty($titre) && !empty($description)) {
                $data = [
                    'nomClient' => $nomClient,
                    'emailClient' => $emailClient,
                    'typeReclamation' => $typeReclamation,
                    'titre' => $titre,
                    'description' => $description
                ];
                
                // Ajouter le statut pour le backoffice
                if (!empty($statut)) {
                    $data['statut'] = $statut;
                }
                
                $success = $this->reclamationModel->update($id, $data);
                
                if ($success) {
                    $_SESSION['message'] = 'Réclamation modifiée avec succès!';
                    header('Location: index.php?action=back&method=details&id=' . $id);
                    exit;
                } else {
                    $_SESSION['error'] = 'Erreur lors de la modification de la réclamation';
                }
            } else {
                $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires';
            }
        }
        
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
    
} // CETTE ACCOLADE FERME LA CLASSE
// RIEN après cette accolade sauf éventuellement ?>