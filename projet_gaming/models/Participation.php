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
    public $user_id;  // AJOUTÉ

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        // MODIFIÉ : ajout de la jointure avec users
        $query = "SELECT p.*, e.nom as evenement_nom, u.username as user_nom 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN evenement e ON p.id_evenement = e.id_evenement 
                  LEFT JOIN users u ON p.User_ID = u.id 
                  ORDER BY p.date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByEvenement() {
        // MODIFIÉ : ajout de la jointure avec users
        $query = "SELECT p.*, u.username as user_nom, u.email as user_email 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN users u ON p.User_ID = u.id 
                  WHERE p.id_evenement = ? 
                  ORDER BY p.date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_evenement);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        // MODIFIÉ : ajout de User_ID dans la requête
        $query = "INSERT INTO " . $this->table_name . " 
                  SET id_evenement=:id_evenement, nom_participant=:nom_participant, 
                      email=:email, telephone=:telephone, User_ID=:user_id";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->nom_participant = htmlspecialchars(strip_tags($this->nom_participant));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));

        // Liaison des paramètres (AJOUTÉ :user_id)
        $stmt->bindParam(":id_evenement", $this->id_evenement);
        $stmt->bindParam(":nom_participant", $this->nom_participant);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telephone", $this->telephone);
        $stmt->bindParam(":user_id", $this->user_id);  // AJOUTÉ

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

    // NOUVELLE MÉTHODE : vérifier si l'utilisateur est déjà inscrit
    public function userAlreadyRegistered() {
        $query = "SELECT id_participation FROM " . $this->table_name . " 
                  WHERE User_ID = ? AND id_evenement = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->id_evenement);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // NOUVELLE MÉTHODE : récupérer les participations d'un utilisateur
    public function readByUser($user_id) {
        $query = "SELECT p.*, e.nom as evenement_nom, e.date_debut, e.lieu, e.jeu 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN evenement e ON p.id_evenement = e.id_evenement 
                  WHERE p.User_ID = ? 
                  ORDER BY p.date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }
}
?>