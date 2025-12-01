<?php
class Article {
    private $conn;
    private $table_name = "articles";

    public $Article_ID;
    public $Auteur_ID;
    public $Titre;
    public $Contenu;
    public $Categorie;
    public $Date_Publication;
    public $Statut;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function lire() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY Date_Publication DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lirePublies() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE Statut = 'published' 
                  ORDER BY Date_Publication DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lireDerniers($limit = 3) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE Statut = 'published' 
                  ORDER BY Date_Publication DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function lireUn() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE Article_ID = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->Article_ID);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->Titre = $row['Titre'];
            $this->Contenu = $row['Contenu'];
            $this->Categorie = $row['Categorie'];
            $this->Date_Publication = $row['Date_Publication'];
            $this->Auteur_ID = $row['Auteur_ID'];
            $this->Statut = $row['Statut'] ?? 'pending';
            return true;
        }
        return false;
    }

    // Lire les articles par auteur
    public function lireParAuteur($auteur_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE Auteur_ID = ? 
                  ORDER BY Date_Publication DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $auteur_id);
        $stmt->execute();
        return $stmt;
    }

    public function creer() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET Titre=:Titre, Contenu=:Contenu, Categorie=:Categorie, 
                     Auteur_ID=:Auteur_ID, Date_Publication=:Date_Publication, Statut=:Statut";
        
        $stmt = $this->conn->prepare($query);
        
        $this->Titre = htmlspecialchars(strip_tags($this->Titre));
        $this->Contenu = htmlspecialchars(strip_tags($this->Contenu));
        $this->Categorie = htmlspecialchars(strip_tags($this->Categorie));
        $this->Auteur_ID = htmlspecialchars(strip_tags($this->Auteur_ID));
        $this->Statut = htmlspecialchars(strip_tags($this->Statut));
        
        $stmt->bindParam(":Titre", $this->Titre);
        $stmt->bindParam(":Contenu", $this->Contenu);
        $stmt->bindParam(":Categorie", $this->Categorie);
        $stmt->bindParam(":Auteur_ID", $this->Auteur_ID);
        $stmt->bindParam(":Date_Publication", $this->Date_Publication);
        $stmt->bindParam(":Statut", $this->Statut);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function mettreAJour() {
        $query = "UPDATE " . $this->table_name . " 
                 SET Titre=:Titre, Contenu=:Contenu, Categorie=:Categorie, 
                     Auteur_ID=:Auteur_ID, Statut=:Statut
                 WHERE Article_ID=:Article_ID";
        
        $stmt = $this->conn->prepare($query);
        
        $this->Titre = htmlspecialchars(strip_tags($this->Titre));
        $this->Contenu = htmlspecialchars(strip_tags($this->Contenu));
        $this->Categorie = htmlspecialchars(strip_tags($this->Categorie));
        $this->Auteur_ID = htmlspecialchars(strip_tags($this->Auteur_ID));
        $this->Statut = htmlspecialchars(strip_tags($this->Statut));
        $this->Article_ID = htmlspecialchars(strip_tags($this->Article_ID));
        
        $stmt->bindParam(":Titre", $this->Titre);
        $stmt->bindParam(":Contenu", $this->Contenu);
        $stmt->bindParam(":Categorie", $this->Categorie);
        $stmt->bindParam(":Auteur_ID", $this->Auteur_ID);
        $stmt->bindParam(":Statut", $this->Statut);
        $stmt->bindParam(":Article_ID", $this->Article_ID);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Modifier un article (version simplifiée)
    public function modifier() {
        $query = "UPDATE " . $this->table_name . " 
                 SET Titre=:Titre, Contenu=:Contenu, Categorie=:Categorie
                 WHERE Article_ID=:Article_ID AND Auteur_ID=:Auteur_ID";
        
        $stmt = $this->conn->prepare($query);
        
        $this->Titre = htmlspecialchars(strip_tags($this->Titre));
        $this->Contenu = htmlspecialchars(strip_tags($this->Contenu));
        $this->Categorie = htmlspecialchars(strip_tags($this->Categorie));
        $this->Article_ID = htmlspecialchars(strip_tags($this->Article_ID));
        $this->Auteur_ID = htmlspecialchars(strip_tags($this->Auteur_ID));
        
        $stmt->bindParam(":Titre", $this->Titre);
        $stmt->bindParam(":Contenu", $this->Contenu);
        $stmt->bindParam(":Categorie", $this->Categorie);
        $stmt->bindParam(":Article_ID", $this->Article_ID);
        $stmt->bindParam(":Auteur_ID", $this->Auteur_ID);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function supprimer() {
        try {
            $this->conn->beginTransaction();
            
            // Supprimer d'abord les commentaires associés
            $query_delete_comments = "DELETE FROM commentaires WHERE Article_ID = ?";
            $stmt_comments = $this->conn->prepare($query_delete_comments);
            $stmt_comments->bindParam(1, $this->Article_ID);
            $stmt_comments->execute();
            
            // Ensuite supprimer l'article
            $query = "DELETE FROM " . $this->table_name . " WHERE Article_ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->Article_ID);
            
            if($stmt->execute()) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
            
        } catch (PDOException $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Erreur suppression article: " . $exception->getMessage());
            return false;
        }
    }

    // Supprimer un article par auteur (pour la sécurité)
    public function supprimerParAuteur($article_id, $auteur_id) {
        try {
            $this->conn->beginTransaction();
            
            // Supprimer d'abord les commentaires associés
            $query_delete_comments = "DELETE FROM commentaires WHERE Article_ID = ?";
            $stmt_comments = $this->conn->prepare($query_delete_comments);
            $stmt_comments->bindParam(1, $article_id);
            $stmt_comments->execute();
            
            // Ensuite supprimer l'article seulement s'il appartient à l'auteur
            $query = "DELETE FROM " . $this->table_name . " WHERE Article_ID = ? AND Auteur_ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $article_id);
            $stmt->bindParam(2, $auteur_id);
            
            if($stmt->execute() && $stmt->rowCount() > 0) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
            
        } catch (PDOException $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Erreur suppression article: " . $exception->getMessage());
            return false;
        }
    }

    public function compterArticles() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Compter les articles par auteur
    public function compterArticlesParAuteur($auteur_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE Auteur_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $auteur_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // NOUVELLES MÉTHODES POUR LA PAGINATION
    public function compterPublies() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE Statut = 'published'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function lirePubliesAvecPagination($limit, $offset) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE Statut = 'published' 
                  ORDER BY Date_Publication DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Méthode pour récupérer les articles avec leurs informations d'auteur
    public function lireAvecAuteurs($limit = null, $offset = null) {
        $query = "SELECT a.*, u.prenom, u.nom 
                  FROM " . $this->table_name . " a 
                  LEFT JOIN utilisateurs u ON a.Auteur_ID = u.id 
                  WHERE a.Statut = 'published' 
                  ORDER BY a.Date_Publication DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit";
            if ($offset !== null) {
                $query .= " OFFSET :offset";
            }
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        return $stmt;
    }

    // Méthode pour rechercher des articles
    public function rechercher($search_term, $limit = null, $offset = null) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE Statut = 'published' 
                  AND (Titre LIKE :search OR Contenu LIKE :search) 
                  ORDER BY Date_Publication DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit";
            if ($offset !== null) {
                $query .= " OFFSET :offset";
            }
        }
        
        $stmt = $this->conn->prepare($query);
        $search_term = "%$search_term%";
        $stmt->bindParam(':search', $search_term);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        return $stmt;
    }

    // Compter les résultats de recherche
    public function compterRecherche($search_term) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE Statut = 'published' 
                  AND (Titre LIKE :search OR Contenu LIKE :search)";
        
        $stmt = $this->conn->prepare($query);
        $search_term = "%$search_term%";
        $stmt->bindParam(':search', $search_term);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Récupérer les articles par catégorie avec pagination
    public function lireParCategorie($categorie, $limit = null, $offset = null) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE Statut = 'published' AND Categorie = :categorie 
                  ORDER BY Date_Publication DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit";
            if ($offset !== null) {
                $query .= " OFFSET :offset";
            }
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':categorie', $categorie);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        return $stmt;
    }

    // Compter les articles par catégorie
    public function compterParCategorie($categorie) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE Statut = 'published' AND Categorie = :categorie";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':categorie', $categorie);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Récupérer les statistiques des articles
    public function getStatistiques() {
        $query = "SELECT 
                    COUNT(*) as total_articles,
                    COUNT(CASE WHEN Statut = 'published' THEN 1 END) as articles_publies,
                    COUNT(CASE WHEN Statut = 'pending' THEN 1 END) as articles_en_attente,
                    COUNT(CASE WHEN Statut = 'draft' THEN 1 END) as articles_brouillon,
                    COUNT(DISTINCT Auteur_ID) as total_auteurs
                  FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour le statut d'un article
    public function mettreAJourStatut($article_id, $statut) {
        $query = "UPDATE " . $this->table_name . " 
                  SET Statut = :statut 
                  WHERE Article_ID = :article_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':article_id', $article_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>