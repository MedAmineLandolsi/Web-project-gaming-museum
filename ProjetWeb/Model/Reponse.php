<?php
require_once 'config/database.php';

class Reponse {
    private $conn;
    private $table_name = "reponse";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Récupérer toutes les réponses
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter ou mettre à jour une réponse
    public function addOrUpdate($reponse) {
        // Vérifier si une réponse existe déjà
        $existing = $this->getByReclamationId($reponse['reclamationId']);
        
        if ($existing) {
            // Mettre à jour la réponse existante
            return $this->update($reponse);
        } else {
            // Ajouter une nouvelle réponse
            return $this->add($reponse);
        }
    }

    // Ajouter une nouvelle réponse
    private function add($reponse) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET reclamationId=:reclamationId, message=:message, 
                      adminName=:adminName";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $reclamationId = htmlspecialchars(strip_tags($reponse['reclamationId']));
        $message = htmlspecialchars(strip_tags($reponse['message']));
        $adminName = htmlspecialchars(strip_tags($reponse['adminName']));

        // Liaison des paramètres
        $stmt->bindParam(":reclamationId", $reclamationId);
        $stmt->bindParam(":message", $message);
        $stmt->bindParam(":adminName", $adminName);

        if ($stmt->execute()) {
            // Mettre à jour le statut de la réclamation
            $reclamationModel = new Reclamation();
            $reclamationModel->updateStatut($reclamationId, 'repondu');
            return true;
        }
        return false;
    }

    // Mettre à jour une réponse existante
    private function update($reponse) {
        $query = "UPDATE " . $this->table_name . " 
                  SET message=:message, adminName=:adminName, date_reponse=NOW()
                  WHERE reclamationId=:reclamationId";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $reclamationId = htmlspecialchars(strip_tags($reponse['reclamationId']));
        $message = htmlspecialchars(strip_tags($reponse['message']));
        $adminName = htmlspecialchars(strip_tags($reponse['adminName']));

        // Liaison des paramètres
        $stmt->bindParam(":reclamationId", $reclamationId);
        $stmt->bindParam(":message", $message);
        $stmt->bindParam(":adminName", $adminName);

        return $stmt->execute();
    }

    // Récupérer une réponse par ID de réclamation
    public function getByReclamationId($id) {
        $query = "SELECT *, DATE_FORMAT(date_reponse, '%d/%m/%Y') as dateReponse, 
                         DATE_FORMAT(date_reponse, '%H:%i:%s') as heureReponse 
                  FROM " . $this->table_name . " 
                  WHERE reclamationId = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Supprimer une réponse par ID de réclamation
    public function deleteByReclamationId($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE reclamationId = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Méthode save pour compatibilité
    public function save($reponses) {
        // Cette méthode n'est pas nécessaire en base de données
        return true;
    }
}
?>