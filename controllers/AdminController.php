<?php
class AdminController {
    private $articleModel;
    private $commentaireModel;
    
    public function __construct($db) {
        $this->articleModel = new Article($db);
        $this->commentaireModel = new Commentaire($db);
    }
    
    public function blogAdmin() {
        $articles = $this->articleModel->lire()->fetchAll(PDO::FETCH_ASSOC);
        include_once 'views/backoffice/blog-admin.php';
    }
    
    public function blogEdit($id = null) {
        if ($id) {
            $this->articleModel->Article_ID = $id;
            $this->articleModel->lireUn();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->articleModel->Titre = $_POST['title'];
            $this->articleModel->Contenu = $_POST['content'];
            $this->articleModel->Catégorie = $_POST['category'];
            $this->articleModel->Auteur_ID = $_POST['author'];
            $this->articleModel->Statut = $_POST['status'];
            
            if ($id) {
                // Mode édition
                $this->articleModel->Article_ID = $id;
                if ($this->articleModel->mettreAJour()) {
                    $_SESSION['success_message'] = 'Article mis à jour avec succès!';
                    header('Location: blog-admin.php');
                    exit();
                }
            } else {
                // Mode création
                $this->articleModel->Date_Publication = date('Y-m-d H:i:s');
                if ($this->articleModel->creer()) {
                    $_SESSION['success_message'] = 'Article créé avec succès!';
                    header('Location: blog-admin.php');
                    exit();
                }
            }
        }
        
        include_once 'views/backoffice/blog-edit.php';
    }
    
    public function commentsAdmin() {
        $commentaires = $this->commentaireModel->lireAvecArticles()->fetchAll(PDO::FETCH_ASSOC);
        $articles = $this->articleModel->lire()->fetchAll(PDO::FETCH_ASSOC);
        include_once 'views/backoffice/comments-admin.php';
    }
    
    public function settings() {
        include_once 'views/backoffice/settings.php';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($username === 'admin' && $password === 'admin123') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                header('Location: blog-admin.php');
                exit();
            } else {
                $error_message = 'Identifiants incorrects';
            }
        }
        
        include_once 'views/backoffice/login.php';
    }
    
    public function logout() {
        session_destroy();
        header('Location: login.php');
        exit();
    }
}
?>