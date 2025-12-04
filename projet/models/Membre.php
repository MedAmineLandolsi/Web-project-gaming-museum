<?php
class Membre {
    private $conn;
    private $table = 'membre';

    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $date_inscription;
    public $statut;
    public $mot_de_passe;
    public $avatar;
    public $bio;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Vérifier si l'email existe déjà
    public function emailExists($email, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Lire tous les membres
    public function read() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire un membre
    public function read_single() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nom = $row['nom'];
            $this->prenom = $row['prenom'];
            $this->email = $row['email'];
            $this->date_inscription = $row['date_inscription'];
            $this->statut = $row['statut'];
            $this->mot_de_passe = $row['mot_de_passe'];
            $this->avatar = $row['avatar'];
            $this->bio = $row['bio'];
            return true;
        }
        return false;
    }

    // Créer un membre
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET nom=:nom, prenom=:prenom, email=:email, mot_de_passe=:mot_de_passe, 
                      statut=:statut, avatar=:avatar, bio=:bio";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenom = htmlspecialchars(strip_tags($this->prenom));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->mot_de_passe = password_hash($this->mot_de_passe, PASSWORD_DEFAULT);
        $this->statut = htmlspecialchars(strip_tags($this->statut));
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));
        $this->bio = htmlspecialchars(strip_tags($this->bio));

        // Liaison des paramètres
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':mot_de_passe', $this->mot_de_passe);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':avatar', $this->avatar);
        $stmt->bindParam(':bio', $this->bio);

        if($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Mettre à jour un membre
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET nom=:nom, prenom=:prenom, email=:email, statut=:statut, 
                      avatar=:avatar, bio=:bio 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenom = htmlspecialchars(strip_tags($this->prenom));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->statut = htmlspecialchars(strip_tags($this->statut));
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Liaison des paramètres
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':avatar', $this->avatar);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Supprimer un membre
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

    // Compter le nombre de communautés créées par un membre
    public function countCommunautes($membre_id) {
        $query = "SELECT COUNT(*) as count FROM communaute WHERE createur_id = :membre_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':membre_id', $membre_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Compter tous les membres
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    // Compter les membres inscrits depuis une date donnée
    public function countRegisteredSince($sinceDate) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE date_inscription >= :since";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':since', $sinceDate);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    // Récupérer les derniers membres inscrits
    public function getLatest($limit = 5) {
        $query = "SELECT id, nom, prenom, email, statut, date_inscription 
                  FROM " . $this->table . " 
                  ORDER BY date_inscription DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>