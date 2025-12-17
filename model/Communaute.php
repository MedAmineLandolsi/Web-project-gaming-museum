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
    public $createur_profile_picture_url;

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
          $query = "SELECT c.*,
                                 COALESCE(NULLIF(u.username, ''), NULLIF(u.first_name, ''), 'Utilisateur') AS prenom,
                                 COALESCE(NULLIF(u.last_name, ''), '') AS nom,
                                 u.profile_picture_url AS profile_picture_url,
                                 COALESCE(
                                     NULLIF(u.username, ''),
                                     NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
                                     CONCAT('Utilisateur #', c.createur_id)
                                 ) AS createur_display_name
                        FROM " . $this->table . " c
                        LEFT JOIN users u ON u.id = c.createur_id
                        ORDER BY c.date_creation DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire toutes les communautés avec tri
    public function readOrdered($order_by = 'date_creation', $order_dir = 'DESC') {
        $allowed_columns = ['nom', 'categorie', 'date_creation', 'createur_id'];
        $allowed_directions = ['ASC', 'DESC'];
        
        $order_by = in_array($order_by, $allowed_columns) ? $order_by : 'date_creation';
        $order_dir = in_array($order_dir, $allowed_directions) ? $order_dir : 'DESC';
        
                $query = "SELECT c.*,
                                    COALESCE(NULLIF(u.username, ''), NULLIF(u.first_name, ''), 'Utilisateur') AS prenom,
                                    COALESCE(NULLIF(u.last_name, ''), '') AS nom,
                                    u.profile_picture_url AS profile_picture_url,
                                    COALESCE(
                                        NULLIF(u.username, ''),
                                        NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
                                        CONCAT('Utilisateur #', c.createur_id)
                                    ) AS createur_display_name
                            FROM " . $this->table . " c
                            LEFT JOIN users u ON u.id = c.createur_id
                            ORDER BY c." . $order_by . " " . $order_dir;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * @return array<int, string>
     */
    public function getCategories(): array {
        $query = "SELECT DISTINCT categorie FROM {$this->table} WHERE categorie IS NOT NULL AND categorie <> '' ORDER BY categorie ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $out = [];
        foreach (($rows ?: []) as $c) {
            $c = trim((string) $c);
            if ($c !== '') {
                $out[] = $c;
            }
        }
        return $out;
    }

    // Lire toutes les communautés (Back) avec filtre catégorie optionnel
    public function readFiltered(?string $categorie = null) {
        $categorie = $categorie !== null ? trim($categorie) : '';

        $where = '';
        if ($categorie !== '') {
            $where = 'WHERE c.categorie = :categorie';
        }

        $query = "SELECT c.*,
                         COALESCE(NULLIF(u.username, ''), NULLIF(u.first_name, ''), 'Utilisateur') AS prenom,
                         COALESCE(NULLIF(u.last_name, ''), '') AS nom,
                         u.profile_picture_url AS profile_picture_url,
                         COALESCE(
                             NULLIF(u.username, ''),
                             NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
                             CONCAT('Utilisateur #', c.createur_id)
                         ) AS createur_display_name
                FROM {$this->table} c
                LEFT JOIN users u ON u.id = c.createur_id
                {$where}
                ORDER BY c.date_creation DESC";

        $stmt = $this->conn->prepare($query);
        if ($categorie !== '') {
            $stmt->bindValue(':categorie', $categorie, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt;
    }

    // Lire toutes les communautés avec tri + filtre catégorie optionnel
    public function readOrderedFiltered(?string $categorie = null, $order_by = 'date_creation', $order_dir = 'DESC') {
        $allowed_columns = ['nom', 'categorie', 'date_creation', 'createur_id'];
        $allowed_directions = ['ASC', 'DESC'];

        $order_by = in_array($order_by, $allowed_columns) ? $order_by : 'date_creation';
        $order_dir = in_array($order_dir, $allowed_directions) ? $order_dir : 'DESC';
        $categorie = $categorie !== null ? trim($categorie) : '';

        $where = '';
        if ($categorie !== '') {
            $where = 'WHERE c.categorie = :categorie';
        }

        $query = "SELECT c.*,
                            COALESCE(NULLIF(u.username, ''), NULLIF(u.first_name, ''), 'Utilisateur') AS prenom,
                            COALESCE(NULLIF(u.last_name, ''), '') AS nom,
                            u.profile_picture_url AS profile_picture_url,
                            COALESCE(
                                NULLIF(u.username, ''),
                                NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
                                CONCAT('Utilisateur #', c.createur_id)
                            ) AS createur_display_name
                    FROM {$this->table} c
                    LEFT JOIN users u ON u.id = c.createur_id
                    {$where}
                    ORDER BY c.{$order_by} {$order_dir}";

        $stmt = $this->conn->prepare($query);
        if ($categorie !== '') {
            $stmt->bindValue(':categorie', $categorie, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt;
    }

    // Lire une communauté
    public function read_single() {
          $query = "SELECT c.*,
                                 COALESCE(NULLIF(u.username, ''), NULLIF(u.first_name, ''), 'Utilisateur') AS prenom,
                                 COALESCE(NULLIF(u.last_name, ''), '') AS nom,
                                 u.profile_picture_url AS profile_picture_url,
                                 COALESCE(
                                     NULLIF(u.username, ''),
                                     NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
                                     CONCAT('Utilisateur #', c.createur_id)
                                 ) AS createur_display_name
                        FROM " . $this->table . " c
                        LEFT JOIN users u ON u.id = c.createur_id
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
            $this->createur_nom = $row['createur_display_name'] ?? (!empty($this->createur_id) ? ('Utilisateur #' . $this->createur_id) : 'Utilisateur');
            $this->createur_profile_picture_url = $row['profile_picture_url'] ?? null;
            return true;
        }
        return false;
    }

    // Lire les communautés par créateur
    public function read_by_createur($createur_id) {
          $query = "SELECT c.*,
                                 COALESCE(NULLIF(u.username, ''), NULLIF(u.first_name, ''), 'Utilisateur') AS prenom,
                                 COALESCE(NULLIF(u.last_name, ''), '') AS nom,
                                 u.profile_picture_url AS profile_picture_url,
                                 COALESCE(
                                     NULLIF(u.username, ''),
                                     NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
                                     CONCAT('Utilisateur #', c.createur_id)
                                 ) AS createur_display_name
                        FROM " . $this->table . " c
                        LEFT JOIN users u ON u.id = c.createur_id
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

    // Fonctionnalité supprimée : ces méthodes restent pour compatibilité
    // mais sont maintenant actives (join/quitter) via communaute_membres.
    public function hasJoined($user_id, $communaute_id) {
        $query = "SELECT 1 FROM communaute_membres WHERE communaute_id = :cid AND user_id = :uid LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cid', (int) $communaute_id, PDO::PARAM_INT);
        $stmt->bindValue(':uid', (int) $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    public function join($user_id, $communaute_id) {
        $query = "INSERT IGNORE INTO communaute_membres (communaute_id, user_id) VALUES (:cid, :uid)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cid', (int) $communaute_id, PDO::PARAM_INT);
        $stmt->bindValue(':uid', (int) $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function leave($user_id, $communaute_id) {
        $query = "DELETE FROM communaute_membres WHERE communaute_id = :cid AND user_id = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cid', (int) $communaute_id, PDO::PARAM_INT);
        $stmt->bindValue(':uid', (int) $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getJoinedCommunauteIds($user_id) {
        $query = "SELECT communaute_id FROM communaute_membres WHERE user_id = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':uid', (int) $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return array_map('intval', $rows ?: []);
    }

    // Compter toutes les communautés
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    // Compter les communautés créées depuis une date donnée
    public function countCreatedSince($sinceDate) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE date_creation >= :since";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':since', $sinceDate);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    // Récupérer les dernières communautés créées
    public function getLatest($limit = 5) {
        $query = "SELECT c.id, c.nom, c.categorie, c.date_creation, c.createur_id,
                         u.profile_picture_url AS profile_picture_url,
                         COALESCE(
                             NULLIF(u.username, ''),
                             NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
                             CONCAT('Utilisateur #', c.createur_id)
                         ) AS createur_display_name
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON u.id = c.createur_id
                  ORDER BY c.date_creation DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>