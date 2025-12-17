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
        // Récupérer tous les articles publiés
        $articles = $this->articleModel->lirePublies()->fetchAll(PDO::FETCH_ASSOC);
        include_once 'views/frontoffice/blog.php';
    }
    
    public function blogSingle($id) {
        // Récupérer l'article spécifique avec informations auteur
        $article = $this->articleModel->lireUnComplet($id);
        
        if (!$article) {
            header('Location: blog.php');
            exit();
        }
        
        // Récupérer les commentaires de l'article avec infos utilisateur
        $commentaires = $this->commentaireModel->lireParArticle($id)->fetchAll(PDO::FETCH_ASSOC);
        
        // Compter les commentaires
        $nb_commentaires = $this->commentaireModel->getCommentCountByArticle($id);
        
        include_once 'views/frontoffice/blog-single.php';
    }
    
    public function about() {
        include_once 'views/frontoffice/about.php';
    }
    
    public function submitArticle() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Vous devez être connecté pour soumettre un article.";
            header('Location: login.php');
            exit();
        }
        
        // Traitement du formulaire de soumission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->articleModel->Titre = $_POST['title'];
            $this->articleModel->Contenu = $_POST['content'];
            $this->articleModel->Categorie = $_POST['category'];
            $this->articleModel->Auteur_ID = $_SESSION['user_id']; // Auteur = utilisateur connecté
            $this->articleModel->Date_Publication = date('Y-m-d H:i:s');
            $this->articleModel->Statut = 'pending'; // En attente de modération
            
            if ($this->articleModel->creer()) {
                $_SESSION['success_message'] = "Article soumis avec succès ! Il sera publié après modération.";
                header('Location: submit-article.php');
                exit();
            } else {
                $_SESSION['error_message'] = "Une erreur est survenue lors de la soumission.";
            }
        }
        
        include_once 'views/frontoffice/submit-article.php';
    }
    
    public function addComment($article_id) {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Vous devez être connecté pour poster un commentaire.";
            header('Location: login.php?redirect=blog-single.php?id=' . $article_id);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->commentaireModel->User_ID = $_SESSION['user_id']; // ID de l'utilisateur connecté
            $this->commentaireModel->Article_ID = $article_id;
            $this->commentaireModel->Contenu = $_POST['contenu'];
            $this->commentaireModel->Date_Commentaire = date('Y-m-d H:i:s');
            
            if ($this->commentaireModel->creer()) {
                $_SESSION['success_message'] = "Commentaire ajouté avec succès !";
                header('Location: blog-single.php?id=' . $article_id);
                exit();
            } else {
                $_SESSION['error_message'] = "Erreur lors de l'ajout du commentaire.";
                header('Location: blog-single.php?id=' . $article_id);
                exit();
            }
        }
    }
    
    // NOUVELLE METHODE : Recherche d'articles
    public function searchArticles() {
        if (isset($_GET['q']) && !empty($_GET['q'])) {
            $search_term = $_GET['q'];
            $articles = $this->articleModel->rechercher($search_term)->fetchAll(PDO::FETCH_ASSOC);
            $nb_resultats = $this->articleModel->compterRecherche($search_term);
            include_once 'views/frontoffice/search-results.php';
        } else {
            header('Location: blog.php');
            exit();
        }
    }
    
    // NOUVELLE METHODE : Articles par catégorie
    public function articlesByCategory($categorie) {
        $categories_valides = ['news', 'review', 'tutorial', 'trends'];
        
        if (!in_array($categorie, $categories_valides)) {
            header('Location: blog.php');
            exit();
        }
        
        $articles = $this->articleModel->lireParCategorie($categorie)->fetchAll(PDO::FETCH_ASSOC);
        $nb_articles = $this->articleModel->compterParCategorie($categorie);
        include_once 'views/frontoffice/category.php';
    }
    
    // NOUVELLE METHODE : Articles par auteur
    public function articlesByAuthor($auteur_id) {
        $articles = $this->articleModel->lireParAuteurComplet($auteur_id)->fetchAll(PDO::FETCH_ASSOC);
        $nb_articles = $this->articleModel->compterArticlesParAuteur($auteur_id);
        
        // Récupérer les infos de l'auteur (nécessite un modèle User)
        // $userModel = new User($this->db); // Si vous avez un modèle User
        // $auteur = $userModel->getUserById($auteur_id);
        
        include_once 'views/frontoffice/author.php';
    }
}
?>