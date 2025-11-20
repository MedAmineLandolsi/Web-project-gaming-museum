<?php
class Communaute {
    private $conn;
    private $table = 'communaute';

    public $id;
    public $nom;
    public $categorie;
    public $description;
    public $createur_id;
    public $date_creation;
    public $avatar;
    public $visibilite;
    public $regles;
    public $createur_nom;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Vérifier si le nom existe déjà
    public function nomExists($nom, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table . " WHERE nom = :nom";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Lire toutes les communautés
    public function read() {
        $query = "SELECT c.*, m.nom, m.prenom 
                  FROM " . $this->table . " c 
                  LEFT JOIN membre m ON c.createur_id = m.id 
                  ORDER BY c.date_creation DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire une communauté
    public function read_single() {
        $query = "SELECT c.*, m.nom, m.prenom 
                  FROM " . $this->table . " c 
                  LEFT JOIN membre m ON c.createur_id = m.id 
                  WHERE c.id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nom = $row['nom'];
            $this->categorie = $row['categorie'];
            $this->description = $row['description'];
            $this->createur_id = $row['createur_id'];
            $this->date_creation = $row['date_creation'];
            $this->avatar = $row['avatar'];
            $this->visibilite = $row['visibilite'];
            $this->regles = $row['regles'];
            $this->createur_nom = $row['prenom'] . ' ' . $row['nom'];
            return true;
        }
        return false;
    }

    // Lire les communautés par créateur
    public function read_by_createur($createur_id) {
        $query = "SELECT c.*, m.nom, m.prenom 
                  FROM " . $this->table . " c 
                  LEFT JOIN membre m ON c.createur_id = m.id 
                  WHERE c.createur_id = :createur_id 
                  ORDER BY c.date_creation DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':createur_id', $createur_id);
        $stmt->execute();
        return $stmt;
    }

    // Créer une communauté
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET nom=:nom, categorie=:categorie, description=:description, 
                      createur_id=:createur_id, avatar=:avatar, visibilite=:visibilite, 
                      regles=:regles";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->categorie = htmlspecialchars(strip_tags($this->categorie));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->createur_id = htmlspecialchars(strip_tags($this->createur_id));
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));
        $this->visibilite = htmlspecialchars(strip_tags($this->visibilite));
        $this->regles = htmlspecialchars(strip_tags($this->regles));

        // Liaison des paramètres
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':categorie', $this->categorie);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':createur_id', $this->createur_id);
        $stmt->bindParam(':avatar', $this->avatar);
        $stmt->bindParam(':visibilite', $this->visibilite);
        $stmt->bindParam(':regles', $this->regles);

        if($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Mettre à jour une communauté
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET nom=:nom, categorie=:categorie, description=:description, 
                      avatar=:avatar, visibilite=:visibilite, regles=:regles 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->categorie = htmlspecialchars(strip_tags($this->categorie));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));
        $this->visibilite = htmlspecialchars(strip_tags($this->visibilite));
        $this->regles = htmlspecialchars(strip_tags($this->regles));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Liaison des paramètres
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':categorie', $this->categorie);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':avatar', $this->avatar);
        $stmt->bindParam(':visibilite', $this->visibilite);
        $stmt->bindParam(':regles', $this->regles);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Supprimer une communauté
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // === NOUVELLES MÉTHODES POUR REJOINDRE ===

    // Vérifier si un membre a rejoint une communauté
    public function hasJoined($membre_id, $communaute_id) {
        try {
            $query = "SELECT id FROM membre_communaute 
                      WHERE membre_id = :membre_id AND communaute_id = :communaute_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':membre_id', $membre_id);
            $stmt->bindParam(':communaute_id', $communaute_id);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            // Si la table n'existe pas, retourner false
            error_log("Erreur hasJoined: " . $e->getMessage());
            return false;
        }
    }

    // Rejoindre une communauté
    public function join($membre_id, $communaute_id) {
        try {
            $query = "INSERT INTO membre_communaute (membre_id, communaute_id, date_join) 
                      VALUES (:membre_id, :communaute_id, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':membre_id', $membre_id);
            $stmt->bindParam(':communaute_id', $communaute_id);
            
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("Erreur join: " . implode(", ", $stmt->errorInfo()));
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Erreur join communauté: " . $e->getMessage());
            return false;
        }
    }
}
?>