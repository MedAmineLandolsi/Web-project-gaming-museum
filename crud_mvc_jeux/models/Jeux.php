<?php
class Jeux {
    private $conn;
    private $table = "jeux";

    public $id;
    public $nom;
    public $description;
    public $prix;
    public $stock;
    public $categorie;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table;
        return $this->conn->query($query);
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (nom, description, prix, stock, categorie)
                  VALUES (:nom, :description, :prix, :stock, :categorie)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nom' => htmlspecialchars(strip_tags($this->nom)),
            ':description' => htmlspecialchars(strip_tags($this->description)),
            ':prix' => $this->prix,
            ':stock' => $this->stock,
            ':categorie' => htmlspecialchars(strip_tags($this->categorie))
        ]);
    }

    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET nom=:nom, description=:description, prix=:prix, stock=:stock, categorie=:categorie
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nom' => htmlspecialchars(strip_tags($this->nom)),
            ':description' => htmlspecialchars(strip_tags($this->description)),
            ':prix' => $this->prix,
            ':stock' => $this->stock,
            ':categorie' => htmlspecialchars(strip_tags($this->categorie)),
            ':id' => $this->id
        ]);
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $this->id]);
    }
}
?>