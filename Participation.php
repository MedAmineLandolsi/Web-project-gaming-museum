<?php
class Participation {
    private $conn;
    private $table_name = "participation";

    public $id_participation;
    public $id_evenement;
    public $nom_participant;
    public $email;
    public $telephone;
    public $date_inscription;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT p.*, e.nom as evenement_nom 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN evenement e ON p.id_evenement = e.id_evenement 
                  ORDER BY p.date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByEvenement() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id_evenement = ? 
                  ORDER BY date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_evenement);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET id_evenement=:id_evenement, nom_participant=:nom_participant, 
                      email=:email, telephone=:telephone";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->nom_participant = htmlspecialchars(strip_tags($this->nom_participant));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));

        // Liaison des paramètres
        $stmt->bindParam(":id_evenement", $this->id_evenement);
        $stmt->bindParam(":nom_participant", $this->nom_participant);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telephone", $this->telephone);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_participation = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_participation);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function emailExists() {
        $query = "SELECT id_participation FROM " . $this->table_name . " 
                  WHERE email = ? AND id_evenement = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->bindParam(2, $this->id_evenement);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
?>