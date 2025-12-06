<?php
class Commentaire {
    private $conn;
    private $table_name = "commentaires";

    public $ID;
    public $User_ID;          // Pour l'insertion (ID de l'utilisateur connecté)
    public $Article_ID;
    public $Contenu;
    public $Date_Commentaire;
    public $created_at;
    public $AuteurNom;        // Pour l'affichage (nom complet de l'utilisateur)
    public $username;         // Pour l'affichage (username)

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lire tous les commentaires avec infos utilisateur
    public function lire() {
        $query = "SELECT c.*, 
                  CONCAT(u.first_name, ' ', u.last_name) as auteur_nom,
                  u.username
                  FROM " . $this->table_name . " c 
                  LEFT JOIN users u ON c.User_ID = u.id 
                  ORDER BY c.Date_Commentaire DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire commentaires avec articles
    public function lireAvecArticles() {
        $query = "SELECT c.*, 
                  CONCAT(u.first_name, ' ', u.last_name) as auteur_nom,
                  u.username,
                  a.Titre as article_titre
                  FROM " . $this->table_name . " c 
                  LEFT JOIN users u ON c.User_ID = u.id 
                  LEFT JOIN articles a ON c.Article_ID = a.Article_ID 
                  ORDER BY c.Date_Commentaire DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire commentaires par article avec infos utilisateur
    public function lireParArticle($article_id) {
        $query = "SELECT c.*, 
                  CONCAT(u.first_name, ' ', u.last_name) as auteur_nom,
                  u.username,
                  u.profile_picture_url as avatar
                  FROM " . $this->table_name . " c 
                  LEFT JOIN users u ON c.User_ID = u.id 
                  WHERE c.Article_ID = ? 
                  ORDER BY c.Date_Commentaire ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $article_id);
        $stmt->execute();
        return $stmt;
    }

    // Créer un commentaire (utilise User_ID)
    public function creer() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET User_ID=:User_ID, Article_ID=:Article_ID, 
                     Contenu=:Contenu, Date_Commentaire=:Date_Commentaire, 
                     created_at=NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        $this->User_ID = htmlspecialchars(strip_tags($this->User_ID));
        $this->Article_ID = htmlspecialchars(strip_tags($this->Article_ID));
        $this->Contenu = htmlspecialchars(strip_tags($this->Contenu));
        
        $stmt->bindParam(":User_ID", $this->User_ID);
        $stmt->bindParam(":Article_ID", $this->Article_ID);
        $stmt->bindParam(":Contenu", $this->Contenu);
        $stmt->bindParam(":Date_Commentaire", $this->Date_Commentaire);
        
        if($stmt->execute()) {
            $this->ID = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // SUPPRIMÉ : Ancienne méthode qui utilisait Auteur (texte)
    // Utilisez maintenant creer() avec User_ID

    // Supprimer un commentaire
    public function supprimer() {
        $query = "DELETE FROM " . $this->table_name . " WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->ID);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Compter tous les commentaires
    public function compterCommentaires() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Compter commentaires par article
    public function getCommentCountByArticle($articleId) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE Article_ID = :article_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':article_id', $articleId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // NOUVELLES METHODES

    // Lire un commentaire spécifique
    public function lireUn() {
        $query = "SELECT c.*, 
                  CONCAT(u.first_name, ' ', u.last_name) as auteur_nom,
                  u.username
                  FROM " . $this->table_name . " c 
                  LEFT JOIN users u ON c.User_ID = u.id 
                  WHERE c.ID = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->ID);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->User_ID = $row['User_ID'];
            $this->Article_ID = $row['Article_ID'];
            $this->Contenu = $row['Contenu'];
            $this->Date_Commentaire = $row['Date_Commentaire'];
            $this->created_at = $row['created_at'];
            $this->AuteurNom = $row['auteur_nom'];
            $this->username = $row['username'];
            return true;
        }
        return false;
    }

    // Mettre à jour un commentaire (sécurité : vérifie que l'utilisateur est l'auteur)
    public function mettreAJour() {
        $query = "UPDATE " . $this->table_name . " 
                 SET Contenu=:Contenu 
                 WHERE ID=:ID AND User_ID=:User_ID";
        
        $stmt = $this->conn->prepare($query);
        
        $this->Contenu = htmlspecialchars(strip_tags($this->Contenu));
        $this->ID = htmlspecialchars(strip_tags($this->ID));
        $this->User_ID = htmlspecialchars(strip_tags($this->User_ID));
        
        $stmt->bindParam(":Contenu", $this->Contenu);
        $stmt->bindParam(":ID", $this->ID);
        $stmt->bindParam(":User_ID", $this->User_ID);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lire derniers commentaires
    public function lireDerniers($limit = 5) {
        $query = "SELECT c.*, 
                  CONCAT(u.first_name, ' ', u.last_name) as auteur_nom,
                  u.username,
                  a.Titre as article_titre
                  FROM " . $this->table_name . " c 
                  LEFT JOIN users u ON c.User_ID = u.id 
                  LEFT JOIN articles a ON c.Article_ID = a.Article_ID 
                  ORDER BY c.Date_Commentaire DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Lire commentaires par utilisateur
    public function lireParUtilisateur($user_id) {
        $query = "SELECT c.*, a.Titre as article_titre
                  FROM " . $this->table_name . " c 
                  LEFT JOIN articles a ON c.Article_ID = a.Article_ID 
                  WHERE c.User_ID = ? 
                  ORDER BY c.Date_Commentaire DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Supprimer commentaire avec vérification utilisateur
    public function supprimerParUtilisateur($comment_id, $user_id) {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE ID = ? AND User_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $comment_id);
        $stmt->bindParam(2, $user_id);
        
        if($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // Compter commentaires par utilisateur
    public function compterParUtilisateur($user_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE User_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Méthode de compatibilité pour migration
    // Crée un commentaire en cherchant l'ID utilisateur à partir du nom
    public function creerAvecNomAuteur($auteur_nom, $article_id, $contenu) {
        // Chercher l'utilisateur par son nom
        $query_user = "SELECT id FROM users 
                      WHERE CONCAT(first_name, ' ', last_name) LIKE :nom 
                      OR username LIKE :nom 
                      LIMIT 1";
        $stmt_user = $this->conn->prepare($query_user);
        $stmt_user->bindParam(":nom", $auteur_nom);
        $stmt_user->execute();
        
        if ($user = $stmt_user->fetch(PDO::FETCH_ASSOC)) {
            // Utilisateur trouvé
            $this->User_ID = $user['id'];
            $this->Article_ID = $article_id;
            $this->Contenu = $contenu;
            $this->Date_Commentaire = date('Y-m-d H:i:s');
            
            return $this->creer();
        } else {
            // Utilisateur non trouvé, utiliser admin par défaut
            $this->User_ID = 1; // ID admin par défaut
            $this->Article_ID = $article_id;
            $this->Contenu = "[Posté par: $auteur_nom] " . $contenu;
            $this->Date_Commentaire = date('Y-m-d H:i:s');
            
            return $this->creer();
        }
    }
}
?>