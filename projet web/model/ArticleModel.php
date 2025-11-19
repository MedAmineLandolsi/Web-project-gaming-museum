<?php
class ArticleModel {
    private $conn;
    private $table_name = "articles";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lire tous les articles
    public function getAllArticles() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lire un article par ID
    public function getArticle($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un article
    public function createArticle($title, $content, $author) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (title, content, author) 
                 VALUES (:title, :content, :author)";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyage des données
        $title = htmlspecialchars(strip_tags($title));
        $content = htmlspecialchars(strip_tags($content));
        $author = htmlspecialchars(strip_tags($author));
        
        // Liaison des paramètres
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":author", $author);
        
        return $stmt->execute();
    }

    // Mettre à jour un article
    public function updateArticle($id, $title, $content, $author) {
        $query = "UPDATE " . $this->table_name . " 
                 SET title = :title, content = :content, author = :author 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyage des données
        $title = htmlspecialchars(strip_tags($title));
        $content = htmlspecialchars(strip_tags($content));
        $author = htmlspecialchars(strip_tags($author));
        
        // Liaison des paramètres
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":author", $author);
        
        return $stmt->execute();
    }

    // Supprimer un article
    public function deleteArticle($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}
?>