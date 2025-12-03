<?php
class Publication {
    private $conn;
    private $table = 'publication';

    public $id;
    public $communaute_id;
    public $auteur_id;
    public $contenu;
    public $date_publication;
    public $date_modification;
    public $images;
    public $likes;
    public $commentaires;
    public $auteur_nom;
    public $communaute_nom;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lire toutes les publications
    public function read() {
        $query = "SELECT p.*, m.nom, m.prenom, c.nom as communaute_nom 
                  FROM " . $this->table . " p 
                  LEFT JOIN membre m ON p.auteur_id = m.id 
                  LEFT JOIN communaute c ON p.communaute_id = c.id 
                  ORDER BY p.date_publication DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Décoder les images JSON
        foreach ($publications as &$publication) {
            if (!empty($publication['images'])) {
                $publication['images'] = json_decode($publication['images'], true);
            } else {
                $publication['images'] = [];
            }
        }
        
        return $publications;
    }

    // Lire toutes les publications avec tri
    public function readOrdered($order_by = 'date_publication', $order_dir = 'DESC') {
        $allowed_columns = ['date_publication', 'likes', 'commentaires'];
        $allowed_directions = ['ASC', 'DESC'];
        
        $order_by = in_array($order_by, $allowed_columns) ? $order_by : 'date_publication';
        $order_dir = in_array($order_dir, $allowed_directions) ? $order_dir : 'DESC';
        
        $query = "SELECT p.*, m.nom, m.prenom, c.nom as communaute_nom 
                  FROM " . $this->table . " p 
                  LEFT JOIN membre m ON p.auteur_id = m.id 
                  LEFT JOIN communaute c ON p.communaute_id = c.id 
                  ORDER BY p." . $order_by . " " . $order_dir;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Décoder les images JSON
        foreach ($publications as &$publication) {
            if (!empty($publication['images'])) {
                $publication['images'] = json_decode($publication['images'], true);
            } else {
                $publication['images'] = [];
            }
        }
        
        return $publications;
    }

    // Lire les publications d'une communauté avec tri
    // Lire les publications d'une communauté avec tri
public function readByCommunauteOrdered($communaute_id, $order_by = 'date_publication', $order_dir = 'DESC') {
    $allowed_columns = ['date_publication', 'likes', 'commentaires'];
    $allowed_directions = ['ASC', 'DESC'];
    
    $order_by = in_array($order_by, $allowed_columns) ? $order_by : 'date_publication';
    $order_dir = in_array($order_dir, $allowed_directions) ? $order_dir : 'DESC';
    
    $query = "SELECT p.*, m.nom, m.prenom, c.nom as communaute_nom
              FROM " . $this->table . " p 
              LEFT JOIN membre m ON p.auteur_id = m.id 
              LEFT JOIN communaute c ON p.communaute_id = c.id
              WHERE p.communaute_id = :communaute_id 
              ORDER BY p." . $order_by . " " . $order_dir;
    
    try {
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':communaute_id', $communaute_id);
        $stmt->execute();
        
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Décoder les images JSON CORRECTEMENT
        foreach ($publications as &$publication) {
            if (!empty($publication['images'])) {
                $images = json_decode($publication['images'], true);
                $publication['images'] = is_array($images) ? $images : [];
            } else {
                $publication['images'] = [];
            }
            
            // S'assurer que date_modification existe
            if (!isset($publication['date_modification'])) {
                $publication['date_modification'] = null;
            }
        }
        
        return $publications;
        
    } catch (PDOException $e) {
        error_log("Erreur readByCommunauteOrdered: " . $e->getMessage());
        return [];
    }
}
    // Lire une publication
    public function read_single() {
        $query = "SELECT p.*, m.nom, m.prenom, c.nom as communaute_nom 
                  FROM " . $this->table . " p 
                  LEFT JOIN membre m ON p.auteur_id = m.id 
                  LEFT JOIN communaute c ON p.communaute_id = c.id 
                  WHERE p.id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->communaute_id = $row['communaute_id'];
            $this->auteur_id = $row['auteur_id'];
            $this->contenu = $row['contenu'];
            $this->date_publication = $row['date_publication'];
            $this->date_modification = $row['date_modification'];
            $this->likes = $row['likes'];
            $this->commentaires = $row['commentaires'];
            $this->auteur_nom = $row['prenom'] . ' ' . $row['nom'];
            $this->communaute_nom = $row['communaute_nom'];
            
            // Décoder les images
            if (!empty($row['images'])) {
                $this->images = json_decode($row['images'], true);
            } else {
                $this->images = [];
            }
            
            return true;
        }
        return false;
    }

    // Lire les publications par auteur
    public function read_by_auteur($auteur_id) {
        $query = "SELECT p.*, m.nom, m.prenom, c.nom as communaute_nom 
                  FROM " . $this->table . " p 
                  LEFT JOIN membre m ON p.auteur_id = m.id 
                  LEFT JOIN communaute c ON p.communaute_id = c.id 
                  WHERE p.auteur_id = :auteur_id 
                  ORDER BY p.date_publication DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':auteur_id', $auteur_id);
        $stmt->execute();
        
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Décoder les images JSON
        foreach ($publications as &$publication) {
            if (!empty($publication['images'])) {
                $publication['images'] = json_decode($publication['images'], true);
            } else {
                $publication['images'] = [];
            }
        }
        
        return $publications;
    }

    // Lire les publications par communauté
    public function read_by_communaute($communaute_id) {
        $query = "SELECT p.*, m.nom, m.prenom, c.nom as communaute_nom 
                  FROM " . $this->table . " p 
                  LEFT JOIN membre m ON p.auteur_id = m.id 
                  LEFT JOIN communaute c ON p.communaute_id = c.id 
                  WHERE p.communaute_id = :communaute_id 
                  ORDER BY p.date_publication DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':communaute_id', $communaute_id);
        $stmt->execute();
        
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Décoder les images JSON
        foreach ($publications as &$publication) {
            if (!empty($publication['images'])) {
                $publication['images'] = json_decode($publication['images'], true);
            } else {
                $publication['images'] = [];
            }
        }
        
        return $publications;
    }

    // Créer une publication
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET communaute_id=:communaute_id, auteur_id=:auteur_id, 
                      contenu=:contenu, images=:images, likes=:likes, 
                      commentaires=:commentaires";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->communaute_id = htmlspecialchars(strip_tags($this->communaute_id));
        $this->auteur_id = htmlspecialchars(strip_tags($this->auteur_id));
        $this->contenu = htmlspecialchars(strip_tags($this->contenu));
        
        // Encoder les images en JSON
        $images_json = !empty($this->images) ? json_encode($this->images) : null;
        
        $this->likes = htmlspecialchars(strip_tags($this->likes));
        $this->commentaires = htmlspecialchars(strip_tags($this->commentaires));

        // Liaison des paramètres
        $stmt->bindParam(':communaute_id', $this->communaute_id);
        $stmt->bindParam(':auteur_id', $this->auteur_id);
        $stmt->bindParam(':contenu', $this->contenu);
        $stmt->bindParam(':images', $images_json);
        $stmt->bindParam(':likes', $this->likes);
        $stmt->bindParam(':commentaires', $this->commentaires);

        if($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Mettre à jour une publication
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET contenu=:contenu, images=:images, date_modification=NOW() 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->contenu = htmlspecialchars(strip_tags($this->contenu));
        
        // Encoder les images en JSON
        $images_json = !empty($this->images) ? json_encode($this->images) : null;
        
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Liaison des paramètres
        $stmt->bindParam(':contenu', $this->contenu);
        $stmt->bindParam(':images', $images_json);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Mettre à jour une publication avec gestion des images
    public function updateWithImages() {
        return $this->update();
    }

    // Supprimer une publication
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

    // Ajouter un like
    public function addLike() {
        $query = "UPDATE " . $this->table . " 
                  SET likes = likes + 1 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    // Ajouter un commentaire
    public function addComment() {
        $query = "UPDATE " . $this->table . " 
                  SET commentaires = commentaires + 1 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    // Compter toutes les publications
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    // Compter les publications depuis une date donnée
    public function countSinceDate($sinceDate) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE date_publication >= :since";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':since', $sinceDate);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    // Total des commentaires sur toutes les publications
    public function totalComments() {
        $query = "SELECT COALESCE(SUM(commentaires), 0) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    // Total des interactions (likes + commentaires) depuis une date
    public function totalInteractionsSince($sinceDate) {
        $query = "SELECT COALESCE(SUM(likes + commentaires), 0) as total 
                  FROM " . $this->table . " 
                  WHERE date_publication >= :since";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':since', $sinceDate);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    // Récupérer les dernières publications
    public function getLatest($limit = 5) {
        $query = "SELECT p.id, p.contenu, p.date_publication, p.likes, p.commentaires,
                         m.prenom, m.nom, c.nom AS communaute_nom
                  FROM " . $this->table . " p
                  LEFT JOIN membre m ON p.auteur_id = m.id
                  LEFT JOIN communaute c ON p.communaute_id = c.id
                  ORDER BY p.date_publication DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>