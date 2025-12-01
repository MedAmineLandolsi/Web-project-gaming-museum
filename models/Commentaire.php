<?php
class Commentaire {
    private $conn;
    private $table_name = "commentaires";

    public $ID;
    public $Auteur;  // Changé de Utilisateur_ID à Auteur
    public $Article_ID;
    public $Contenu;
    public $Date_Commentaire;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function lire() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY Date_Commentaire DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lireAvecArticles() {
        $query = "SELECT c.*, a.Titre 
                  FROM " . $this->table_name . " c 
                  LEFT JOIN articles a ON c.Article_ID = a.Article_ID 
                  ORDER BY c.Date_Commentaire DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lireParArticle($article_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE Article_ID = ? 
                  ORDER BY Date_Commentaire ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $article_id);
        $stmt->execute();
        return $stmt;
    }

    public function creer() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET Auteur=:Auteur, Article_ID=:Article_ID, 
                     Contenu=:Contenu, Date_Commentaire=:Date_Commentaire";
        
        $stmt = $this->conn->prepare($query);
        
        $this->Auteur = htmlspecialchars(strip_tags($this->Auteur));
        $this->Article_ID = htmlspecialchars(strip_tags($this->Article_ID));
        $this->Contenu = htmlspecialchars(strip_tags($this->Contenu));
        
        $stmt->bindParam(":Auteur", $this->Auteur);
        $stmt->bindParam(":Article_ID", $this->Article_ID);
        $stmt->bindParam(":Contenu", $this->Contenu);
        $stmt->bindParam(":Date_Commentaire", $this->Date_Commentaire);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function supprimer() {
        $query = "DELETE FROM " . $this->table_name . " WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->ID);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function compterCommentaires() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // NOUVELLE MÉTHODE AJOUTÉE
    public function getCommentCountByArticle($articleId) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE Article_ID = :article_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':article_id', $articleId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>