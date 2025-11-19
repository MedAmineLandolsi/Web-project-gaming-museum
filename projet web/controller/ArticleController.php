<?php
require_once 'models/ArticleModel.php';
require_once 'config/database.php';

class ArticleController {
    private $articleModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->articleModel = new ArticleModel($db);
    }

    // Afficher tous les articles (FrontOffice)
    public function index() {
        $articles = $this->articleModel->getAllArticles();
        include 'views/front/articles.php';
    }

    // Afficher la liste des articles (BackOffice)
    public function adminIndex() {
        $articles = $this->articleModel->getAllArticles();
        include 'views/back/articles/list.php';
    }

    // Afficher le formulaire de création
    public function createForm() {
        include 'views/back/articles/create.php';
    }

    // Traitement de la création
    public function create() {
        if($_POST) {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $author = $_POST['author'];
            
            if($this->articleModel->createArticle($title, $content, $author)) {
                header("Location: index.php?action=admin");
                exit();
            }
        }
    }

    // Afficher le formulaire d'édition
    public function editForm($id) {
        $article = $this->articleModel->getArticle($id);
        include 'views/back/articles/edit.php';
    }

    // Traitement de l'édition
    public function update($id) {
        if($_POST) {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $author = $_POST['author'];
            
            if($this->articleModel->updateArticle($id, $title, $content, $author)) {
                header("Location: index.php?action=admin");
                exit();
            }
        }
    }

    // Suppression
    public function delete($id) {
        if($this->articleModel->deleteArticle($id)) {
            header("Location: index.php?action=admin");
            exit();
        }
    }
}
?>