<?php
class FrontController {
    private $articleModel;
    private $commentaireModel;
    
    public function __construct($db) {
        $this->articleModel = new Article($db);
        $this->commentaireModel = new Commentaire($db);
    }
    
    public function home() {
        // Récupérer les derniers articles pour la page d'accueil
        $articles = $this->articleModel->lireDerniers(3)->fetchAll(PDO::FETCH_ASSOC);
        include_once 'views/index.php';
    }
    
    public function blog() {
        // Récupérer tous les articles pour la page blog
        $articles = $this->articleModel->lire()->fetchAll(PDO::FETCH_ASSOC);
        include_once 'views/frontoffice/blog.php';
    }
    
    public function blogSingle($id) {
        // Récupérer l'article spécifique
        $this->articleModel->Article_ID = $id;
        $this->articleModel->lireUn();
        
        // Récupérer les commentaires de l'article
        $commentaires = $this->commentaireModel->lireParArticle($id)->fetchAll(PDO::FETCH_ASSOC);
        
        include_once 'views/frontoffice/blog-single.php';
    }
    
    public function about() {
        include_once 'views/frontoffice/about.php';
    }
    
    public function submitArticle() {
        // Traitement du formulaire de soumission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->articleModel->Titre = $_POST['title'];
            $this->articleModel->Contenu = $_POST['content'];
            $this->articleModel->Catégorie = $_POST['category'];
            $this->articleModel->Auteur_ID = 1; // Auteur par défaut
            $this->articleModel->Date_Publication = date('Y-m-d H:i:s');
            $this->articleModel->Statut = 'pending'; // En attente de modération
            
            if ($this->articleModel->creer()) {
                $_SESSION['success_message'] = "Article soumis avec succès !";
                header('Location: submit-article.php');
                exit();
            }
        }
        
        include_once 'views/frontoffice/submit-article.php';
    }
    
    public function addComment($article_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->commentaireModel->Utilisateur_ID = $_POST['auteur'];
            $this->commentaireModel->Article_ID = $article_id;
            $this->commentaireModel->Contenu = $_POST['contenu'];
            $this->commentaireModel->Date_Commentaire = date('Y-m-d H:i:s');
            
            if ($this->commentaireModel->creer()) {
                header('Location: blog-single.php?id=' . $article_id);
                exit();
            }
        }
    }
}
?>