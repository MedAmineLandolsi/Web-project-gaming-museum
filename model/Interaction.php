<?php
class Interaction {
    private $conn;
    private $table = 'interactions';

    public $id;
    public $user_id; // membre.id in DB
    public $item_id; // publication.id
    public $interaction_type;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Récupérer les interactions pour un user (en passant par users.id)
    public function getByUserId($user_id) {
        try {
            // Résoudre un membre à partir d'un identifiant qui peut être soit users.id soit membre.id
            $membre_id = null;

            // 1) Si un membre possède user_id = $user_id
            $q = "SELECT id FROM membre WHERE user_id = :uid LIMIT 1";
            $s = $this->conn->prepare($q);
            $s->bindParam(':uid', $user_id);
            $s->execute();
            $row = $s->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['id'])) {
                $membre_id = $row['id'];
            }

            // 2) Sinon, si on a reçu directement un membre.id
            if (!$membre_id) {
                $q2 = "SELECT id FROM membre WHERE id = :mid LIMIT 1";
                $s2 = $this->conn->prepare($q2);
                $s2->bindParam(':mid', $user_id);
                $s2->execute();
                $row2 = $s2->fetch(PDO::FETCH_ASSOC);
                if ($row2 && !empty($row2['id'])) {
                    $membre_id = $row2['id'];
                }
            }

            // 3) Dernier recours : chercher l'email dans users puis le membre par email (compatibilité ascendante)
            if (!$membre_id) {
                $uQ = "SELECT email FROM users WHERE id = :uid LIMIT 1";
                $uS = $this->conn->prepare($uQ);
                $uS->bindParam(':uid', $user_id);
                $uS->execute();
                $uRow = $uS->fetch(PDO::FETCH_ASSOC);
                if ($uRow && !empty($uRow['email'])) {
                    $mQ = "SELECT id FROM membre WHERE email = :email LIMIT 1";
                    $mS = $this->conn->prepare($mQ);
                    $mS->bindParam(':email', $uRow['email']);
                    $mS->execute();
                    $mRow = $mS->fetch(PDO::FETCH_ASSOC);
                    if ($mRow && !empty($mRow['id'])) {
                        $membre_id = $mRow['id'];
                    }
                }
            }

            if (!$membre_id) {
                return [];
            }

            // Récupérer les interactions en joignant publication + communaute + membre + users via membre.user_id
            $query = "SELECT i.*, p.contenu AS publication_contenu, p.communaute_id, 
                             c.nom AS communaute_nom, c.categorie AS communaute_categorie,
                             pm.prenom AS auteur_prenom, pm.nom AS auteur_nom,
                             mu.username AS auteur_username, mu.profile_picture_url AS auteur_avatar,
                             m.nom AS membre_nom, m.prenom AS membre_prenom, u.id AS user_id, u.username AS username, u.profile_picture_url as user_avatar
                      FROM " . $this->table . " i
                      LEFT JOIN publication p ON i.item_id = p.id
                      LEFT JOIN communaute c ON p.communaute_id = c.id
                      LEFT JOIN membre pm ON p.auteur_id = pm.id
                      LEFT JOIN users mu ON pm.user_id = mu.id
                      LEFT JOIN membre m ON i.user_id = m.id
                      LEFT JOIN users u ON m.user_id = u.id
                      WHERE i.user_id = :membre_id
                      ORDER BY i.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':membre_id', $membre_id);
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
            $query = "SELECT i.*, m.id AS membre_id, m.nom AS membre_nom, m.prenom AS membre_prenom, 
                             u.id AS user_id, u.username, u.profile_picture_url as user_avatar
                      FROM " . $this->table . " i
                      LEFT JOIN membre m ON i.user_id = m.id
                      LEFT JOIN users u ON m.user_id = u.id
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
            // Résoudre l'ID membre comme dans getByUserId
            $membre_id = null;

            $q = "SELECT id FROM membre WHERE user_id = :uid LIMIT 1";
            $s = $this->conn->prepare($q);
            $s->bindParam(':uid', $user_id);
            $s->execute();
            $row = $s->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['id'])) {
                $membre_id = $row['id'];
            }

            if (!$membre_id) {
                $q2 = "SELECT id FROM membre WHERE id = :mid LIMIT 1";
                $s2 = $this->conn->prepare($q2);
                $s2->bindParam(':mid', $user_id);
                $s2->execute();
                $row2 = $s2->fetch(PDO::FETCH_ASSOC);
                if ($row2 && !empty($row2['id'])) {
                    $membre_id = $row2['id'];
                }
            }

            if (!$membre_id) {
                $uQ = "SELECT email FROM users WHERE id = :uid LIMIT 1";
                $uS = $this->conn->prepare($uQ);
                $uS->bindParam(':uid', $user_id);
                $uS->execute();
                $uRow = $uS->fetch(PDO::FETCH_ASSOC);
                if ($uRow && !empty($uRow['email'])) {
                    $mQ = "SELECT id FROM membre WHERE email = :email LIMIT 1";
                    $mS = $this->conn->prepare($mQ);
                    $mS->bindParam(':email', $uRow['email']);
                    $mS->execute();
                    $mRow = $mS->fetch(PDO::FETCH_ASSOC);
                    if ($mRow && !empty($mRow['id'])) {
                        $membre_id = $mRow['id'];
                    }
                }
            }

            if (!$membre_id) return 0;

            $countQuery = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE user_id = :membre_id";
            $countStmt = $this->conn->prepare($countQuery);
            $countStmt->bindParam(':membre_id', $membre_id);
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
