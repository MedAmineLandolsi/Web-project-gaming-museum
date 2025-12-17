<?php
class Interaction {
    private $conn;
    private $table = 'interactions';

    public $id;
    public $user_id; // users.id
    public $item_id; // publication.id
    public $interaction_type;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Récupérer les interactions pour un user (users.id)
    public function getByUserId($user_id) {
        try {
            // Récupérer les interactions en joignant publication + communaute + users
            $query = "SELECT i.*, p.contenu AS publication_contenu, p.communaute_id, 
                             c.nom AS communaute_nom, c.categorie AS communaute_categorie,
                             au.username AS auteur_username, au.profile_picture_url AS auteur_avatar,
                             u.id AS user_id, u.username AS username, u.profile_picture_url as user_avatar
                      FROM " . $this->table . " i
                      LEFT JOIN publication p ON i.item_id = p.id
                      LEFT JOIN communaute c ON p.communaute_id = c.id
                      LEFT JOIN users au ON p.auteur_id = au.id
                      LEFT JOIN users u ON i.user_id = u.id
                      WHERE i.user_id = :user_id
                      ORDER BY i.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            error_log('Erreur Interaction::getByUserId - ' . $e->getMessage());
            return [];
        }
    }

    // Récupérer les interactions pour une publication (avec info user)
    public function getByPublicationId($publication_id) {
        try {
            $query = "SELECT i.*, 
                             u.id AS user_id, u.username, u.profile_picture_url as user_avatar
                      FROM " . $this->table . " i
                      LEFT JOIN users u ON i.user_id = u.id
                      WHERE i.item_id = :publication_id
                      ORDER BY i.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':publication_id', $publication_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur Interaction::getByPublicationId - ' . $e->getMessage());
            return [];
        }
    }

    // Compter interactions par user (via users.id)
    public function countByUser($user_id) {
        try {
            $countQuery = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE user_id = :user_id";
            $countStmt = $this->conn->prepare($countQuery);
            $countStmt->bindParam(':user_id', $user_id);
            $countStmt->execute();
            $res = $countStmt->fetch(PDO::FETCH_ASSOC);
            return (int)($res['total'] ?? 0);
        } catch (PDOException $e) {
            error_log('Erreur Interaction::countByUser - ' . $e->getMessage());
            return 0;
        }
    }
}

?>
