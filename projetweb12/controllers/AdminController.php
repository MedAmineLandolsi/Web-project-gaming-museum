<?php
class AdminController {
    private $articleModel;
    private $commentaireModel;
    
    public function __construct($db) {
        $this->articleModel = new Article($db);
        $this->commentaireModel = new Commentaire($db);
    }
    
    public function dashboard() {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        // Récupérer les statistiques
        $stats_articles = $this->articleModel->getStatistiques();
        $stats_commentaires = $this->commentaireModel->compterCommentaires();
        $derniers_articles = $this->articleModel->lireDerniers(5)->fetchAll(PDO::FETCH_ASSOC);
        $derniers_commentaires = $this->commentaireModel->lireDerniers(5)->fetchAll(PDO::FETCH_ASSOC);
        
        include_once 'views/backoffice/dashboard.php';
    }
    
    public function blogAdmin() {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        $articles = $this->articleModel->lire()->fetchAll(PDO::FETCH_ASSOC);
        include_once 'views/backoffice/blog-admin.php';
    }
    
    public function blogEdit($id = null) {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        if ($id) {
            $this->articleModel->Article_ID = $id;
            $this->articleModel->lireUn();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->articleModel->Titre = $_POST['title'];
            $this->articleModel->Contenu = $_POST['content'];
            $this->articleModel->Categorie = $_POST['category'];
            $this->articleModel->Auteur_ID = $_POST['author'];
            $this->articleModel->Statut = $_POST['status'];
            
            if ($id) {
                // Mode édition
                $this->articleModel->Article_ID = $id;
                if ($this->articleModel->mettreAJour()) {
                    $_SESSION['success_message'] = 'Article mis à jour avec succès!';
                    header('Location: blog-admin.php');
                    exit();
                } else {
                    $_SESSION['error_message'] = 'Erreur lors de la mise à jour.';
                }
            } else {
                // Mode création
                $this->articleModel->Date_Publication = date('Y-m-d H:i:s');
                if ($this->articleModel->creer()) {
                    $_SESSION['success_message'] = 'Article créé avec succès!';
                    header('Location: blog-admin.php');
                    exit();
                } else {
                    $_SESSION['error_message'] = 'Erreur lors de la création.';
                }
            }
        }
        
        include_once 'views/backoffice/blog-edit.php';
    }
    
    public function deleteArticle($id) {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        $this->articleModel->Article_ID = $id;
        if ($this->articleModel->supprimer()) {
            $_SESSION['success_message'] = 'Article supprimé avec succès!';
        } else {
            $_SESSION['error_message'] = 'Erreur lors de la suppression.';
        }
        
        header('Location: blog-admin.php');
        exit();
    }
    
    public function updateArticleStatus($id, $status) {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        if ($this->articleModel->mettreAJourStatut($id, $status)) {
            $_SESSION['success_message'] = 'Statut mis à jour avec succès!';
        } else {
            $_SESSION['error_message'] = 'Erreur lors de la mise à jour du statut.';
        }
        
        header('Location: blog-admin.php');
        exit();
    }
    
    public function commentsAdmin() {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        $commentaires = $this->commentaireModel->lireAvecArticles()->fetchAll(PDO::FETCH_ASSOC);
        $articles = $this->articleModel->lire()->fetchAll(PDO::FETCH_ASSOC);
        include_once 'views/backoffice/comments-admin.php';
    }
    
    public function deleteComment($id) {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        $this->commentaireModel->ID = $id;
        if ($this->commentaireModel->supprimer()) {
            $_SESSION['success_message'] = 'Commentaire supprimé avec succès!';
        } else {
            $_SESSION['error_message'] = 'Erreur lors de la suppression.';
        }
        
        header('Location: comments-admin.php');
        exit();
    }
    
    public function settings() {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        include_once 'views/backoffice/settings.php';
    }
    
    public function login() {
        // Si déjà connecté, rediriger vers le dashboard
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: dashboard.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Version simple pour l'exemple
            // Dans la réalité, vérifier dans la table users avec role='admin'
            if ($username === 'admin' && $password === 'admin123') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_role'] = 'admin';
                $_SESSION['success_message'] = 'Connexion réussie!';
                header('Location: dashboard.php');
                exit();
            } else {
                $_SESSION['error_message'] = 'Identifiants incorrects';
            }
        }
        
        include_once 'views/backoffice/login.php';
    }
    
    public function logout() {
        session_destroy();
        $_SESSION['success_message'] = 'Déconnexion réussie.';
        header('Location: login.php');
        exit();
    }
    
    // NOUVELLE METHODE : Gestion des utilisateurs (si intégration avec module gestion utilisateurs)
    public function usersAdmin() {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        // Note: Cette méthode nécessite un modèle User
        // $userModel = new User($this->db);
        // $users = $userModel->lire()->fetchAll(PDO::FETCH_ASSOC);
        
        include_once 'views/backoffice/users-admin.php';
    }
    
    // NOUVELLE METHODE : Voir les commentaires par article
    public function articleComments($article_id) {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        $article = $this->articleModel->lireUnComplet($article_id);
        $commentaires = $this->commentaireModel->lireParArticle($article_id)->fetchAll(PDO::FETCH_ASSOC);
        
        include_once 'views/backoffice/article-comments.php';
    }
    
    // NOUVELLE METHODE : Recherche avancée dans les articles
    public function searchAdminArticles() {
        // Vérifier l'authentification admin
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
        
        $search_term = $_GET['q'] ?? '';
        $categorie = $_GET['category'] ?? '';
        $statut = $_GET['status'] ?? '';
        
        // Construire la requête dynamique
        $conditions = [];
        $params = [];
        
        if (!empty($search_term)) {
            $conditions[] = "(Titre LIKE :search OR Contenu LIKE :search)";
            $params[':search'] = "%$search_term%";
        }
        
        if (!empty($categorie)) {
            $conditions[] = "Categorie = :categorie";
            $params[':categorie'] = $categorie;
        }
        
        if (!empty($statut)) {
            $conditions[] = "Statut = :statut";
            $params[':statut'] = $statut;
        }
        
        $where = '';
        if (!empty($conditions)) {
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }
        
        // Exécuter la requête (nécessiterait une nouvelle méthode dans Article.php)
        // $articles = $this->articleModel->rechercheAvancee($where, $params);
        
        include_once 'views/backoffice/search-admin.php';
    }
}
?>