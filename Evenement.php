<?php
class Evenement {
    private $conn;
    private $table_name = "evenement";

    public $id_evenement;
    public $nom;
    public $description;
    public $date_debut;
    public $date_fin;
    public $lieu;
    public $jeu;
    public $places_max;
    public $prix;
    public $image;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY date_debut ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_evenement = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_evenement);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nom = $row['nom'];
            $this->description = $row['description'];
            $this->date_debut = $row['date_debut'];
            $this->date_fin = $row['date_fin'];
            $this->lieu = $row['lieu'];
            $this->jeu = $row['jeu'];
            $this->places_max = $row['places_max'];
            $this->prix = $row['prix'];
            $this->image = $row['image'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nom=:nom, description=:description, date_debut=:date_debut, 
                      date_fin=:date_fin, lieu=:lieu, jeu=:jeu, places_max=:places_max, prix=:prix";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->lieu = htmlspecialchars(strip_tags($this->lieu));
        $this->jeu = htmlspecialchars(strip_tags($this->jeu));

        // Liaison des paramètres
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":date_debut", $this->date_debut);
        $stmt->bindParam(":date_fin", $this->date_fin);
        $stmt->bindParam(":lieu", $this->lieu);
        $stmt->bindParam(":jeu", $this->jeu);
        $stmt->bindParam(":places_max", $this->places_max);
        $stmt->bindParam(":prix", $this->prix);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nom=:nom, description=:description, date_debut=:date_debut, 
                      date_fin=:date_fin, lieu=:lieu, jeu=:jeu, places_max=:places_max, prix=:prix
                  WHERE id_evenement=:id_evenement";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->lieu = htmlspecialchars(strip_tags($this->lieu));
        $this->jeu = htmlspecialchars(strip_tags($this->jeu));

        // Liaison des paramètres
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":date_debut", $this->date_debut);
        $stmt->bindParam(":date_fin", $this->date_fin);
        $stmt->bindParam(":lieu", $this->lieu);
        $stmt->bindParam(":jeu", $this->jeu);
        $stmt->bindParam(":places_max", $this->places_max);
        $stmt->bindParam(":prix", $this->prix);
        $stmt->bindParam(":id_evenement", $this->id_evenement);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_evenement = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_evenement);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function countParticipations() {
        $query = "SELECT COUNT(*) as total FROM participation WHERE id_evenement = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_evenement);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>