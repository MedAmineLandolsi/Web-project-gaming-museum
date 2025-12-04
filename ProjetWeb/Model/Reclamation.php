<?php
require_once 'config/database.php';

class Reclamation {
    private $conn;
    private $table_name = "reclamation";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Récupérer toutes les réclamations
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter une réclamation
    public function add($reclamation) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nomClient=:nomClient, emailClient=:emailClient, 
                      typeReclamation=:typeReclamation, titre=:titre, 
                      description=:description, statut=:statut";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $nomClient = htmlspecialchars(strip_tags($reclamation['nomClient']));
        $emailClient = htmlspecialchars(strip_tags($reclamation['emailClient']));
        $typeReclamation = htmlspecialchars(strip_tags($reclamation['typeReclamation']));
        $titre = htmlspecialchars(strip_tags($reclamation['titre']));
        $description = htmlspecialchars(strip_tags($reclamation['description']));
        $statut = htmlspecialchars(strip_tags($reclamation['statut']));

        // Liaison des paramètres
        $stmt->bindParam(":nomClient", $nomClient);
        $stmt->bindParam(":emailClient", $emailClient);
        $stmt->bindParam(":typeReclamation", $typeReclamation);
        $stmt->bindParam(":titre", $titre);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":statut", $statut);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Supprimer une réclamation
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Récupérer une réclamation par ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour le statut
    public function updateStatut($id, $statut) {
        $query = "UPDATE " . $this->table_name . " SET statut = :statut WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":statut", $statut);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Méthode save pour compatibilité (ne fait rien en DB)
    public function save($reclamations) {
        // Cette méthode n'est pas nécessaire en base de données
        // Gardée pour la compatibilité avec le code existant
        return true;
    }

    // AJOUTEZ LA MÉTHODE UPDATE ICI, À L'INTÉRIEUR DE LA CLASSE :
    public function update($id, $data) {
        // Adaptation à votre structure de base de données
        $query = "UPDATE " . $this->table_name . " SET 
                 nomClient = :nomClient, 
                 emailClient = :emailClient, 
                 typeReclamation = :typeReclamation, 
                 titre = :titre, 
                 description = :description";
        
        // Ajouter le statut si présent (pour le backoffice)
        if (isset($data['statut'])) {
            $query .= ", statut = :statut";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyage des données
        $nomClient = htmlspecialchars(strip_tags($data['nomClient']));
        $emailClient = htmlspecialchars(strip_tags($data['emailClient']));
        $typeReclamation = htmlspecialchars(strip_tags($data['typeReclamation']));
        $titre = htmlspecialchars(strip_tags($data['titre']));
        $description = htmlspecialchars(strip_tags($data['description']));
        
        // Liaison des paramètres
        $stmt->bindParam(":nomClient", $nomClient);
        $stmt->bindParam(":emailClient", $emailClient);
        $stmt->bindParam(":typeReclamation", $typeReclamation);
        $stmt->bindParam(":titre", $titre);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":id", $id);
        
        if (isset($data['statut'])) {
            $statut = htmlspecialchars(strip_tags($data['statut']));
            $stmt->bindParam(":statut", $statut);
        }
        
        return $stmt->execute();
    }

} // CETTE ACCOLADE FERME LA CLASSE
// RIEN après cette accolade sauf éventuellement ?>