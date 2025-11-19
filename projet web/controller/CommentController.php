<?php
class CommentController {
    private $commentModel;
    
    public function __construct() {
        $this->commentModel = new CommentModel();
    }
    
    // Ajouter un commentaire
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'article_id' => $_POST['article_id'],
                'contenu' => $_POST['contenu'],
                'utilisateur_id' => $this->getOrCreateUser($_POST['auteur'])
            ];
            
            $commentId = $this->commentModel->create($data);
            
            if ($commentId) {
                $_SESSION['flash_message'] = 'Commentaire ajouté avec succès!';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Erreur lors de l\'ajout du commentaire';
                $_SESSION['flash_type'] = 'error';
            }
            
            header('Location: index.php?action=blog-single&id=' . $data['article_id']);
            exit;
        }
    }
    
    // Afficher la gestion des commentaires (admin)
    public function admin() {
        $this->checkAdmin();
        $comments = $this->commentModel->getAll();
        require_once 'views/backoffice/comments-admin.php';
    }
    
    // Modifier un commentaire
    public function update($id) {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->commentModel->update($id, $_POST['contenu']);
            
            $message = $success ? 'Commentaire modifié avec succès!' : 'Erreur lors de la modification';
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = $success ? 'success' : 'error';
            
            header('Location: index.php?action=comments-admin');
            exit;
        }
    }
    
    // Supprimer un commentaire
    public function delete($id) {
        $this->checkAdmin();
        
        $success = $this->commentModel->delete($id);
        $message = $success ? 'Commentaire supprimé avec succès!' : 'Erreur lors de la suppression';
        
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $success ? 'success' : 'error';
        
        header('Location: index.php?action=comments-admin');
        exit;
    }
    
    // Obtenir ou créer un utilisateur pour les commentaires
    private function getOrCreateUser($authorName) {
        // Pour l'instant, on utilise un système simplifié
        // Dans une vraie application, on aurait un modèle User
        return 1; // ID d'utilisateur par défaut
    }
    
    // Vérifier si l'utilisateur est admin
    private function checkAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?action=login');
            exit;
        }
    }
}
?>