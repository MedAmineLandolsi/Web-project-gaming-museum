<?php
class Publication {
    private $conn;
    private $table = 'publication';

    public $id;
    public $communaute_id;
    public $auteur_id;
    public $contenu;
    public $images;
    public $likes;
    public $commentaires;
    public $date_publication;
    public $date_modification;
    public $auteur_nom;
    public $avatar;
    public $communaute_nom;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lire toutes les publications - CORRIGÉ
    public function read() {
        $query = "SELECT p.*, m.nom, m.prenom, m.avatar, c.nom as communaute_nom 
                  FROM " . $this->table . " p 
                  LEFT JOIN membre m ON p.auteur_id = m.id 
                  LEFT JOIN communaute c ON p.communaute_id = c.id 
                  ORDER BY p.date_publication DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Décoder les images JSON pour chaque publication
        foreach ($publications as &$publication) {
            $publication['images'] = $this->decodeImages($publication['images']);
        }
        
        return $publications;
    }

    // Lire les publications d'une communauté - CORRIGÉ
    public function read_by_communaute($communaute_id) {
        $query = "SELECT p.*, m.nom, m.prenom, m.avatar 
                  FROM " . $this->table . " p 
                  LEFT JOIN membre m ON p.auteur_id = m.id 
                  WHERE p.communaute_id = :communaute_id 
                  ORDER BY p.date_publication DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':communaute_id', $communaute_id);
        $stmt->execute();
        
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Décoder les images JSON pour chaque publication
        foreach ($publications as &$publication) {
            $publication['images'] = $this->decodeImages($publication['images']);
        }
        
        return $publications;
    }

    // Lire une publication - CORRIGÉ
    public function read_single() {
        $query = "SELECT p.*, m.nom, m.prenom, m.avatar, c.nom as communaute_nom 
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
            $this->images = $this->decodeImages($row['images']);
            $this->likes = $row['likes'];
            $this->commentaires = $row['commentaires'];
            $this->date_publication = $row['date_publication'];
            $this->auteur_nom = $row['prenom'] . ' ' . $row['nom'];
            $this->avatar = $row['avatar'];
            $this->communaute_nom = $row['communaute_nom'];
            return true;
        }
        return false;
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
        $this->images = $this->images ? json_encode($this->images) : null;
        $this->likes = htmlspecialchars(strip_tags($this->likes));
        $this->commentaires = htmlspecialchars(strip_tags($this->commentaires));

        // Liaison des paramètres
        $stmt->bindParam(':communaute_id', $this->communaute_id);
        $stmt->bindParam(':auteur_id', $this->auteur_id);
        $stmt->bindParam(':contenu', $this->contenu);
        $stmt->bindParam(':images', $this->images);
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
                  SET contenu=:contenu, images=:images, likes=:likes, 
                      commentaires=:commentaires 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->contenu = htmlspecialchars(strip_tags($this->contenu));
        $this->images = $this->images ? json_encode($this->images) : null;
        $this->likes = htmlspecialchars(strip_tags($this->likes));
        $this->commentaires = htmlspecialchars(strip_tags($this->commentaires));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Liaison des paramètres
        $stmt->bindParam(':contenu', $this->contenu);
        $stmt->bindParam(':images', $this->images);
        $stmt->bindParam(':likes', $this->likes);
        $stmt->bindParam(':commentaires', $this->commentaires);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
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

    // Méthode pour décoder les images - NOUVELLE MÉTHODE
    private function decodeImages($imagesJson) {
        if (empty($imagesJson)) {
            return [];
        }
        
        $images = json_decode($imagesJson, true);
        
        // Si le décodage échoue, essayer de traiter comme une chaîne simple
        if ($images === null) {
            // Essayer de séparer par des virgules
            $images = explode(',', $imagesJson);
            $images = array_map('trim', $images);
            $images = array_filter($images); // Supprimer les éléments vides
        }
        
        // S'assurer que c'est un tableau
        if (!is_array($images)) {
            return [];
        }
        
        return $images;
    }
}
?>