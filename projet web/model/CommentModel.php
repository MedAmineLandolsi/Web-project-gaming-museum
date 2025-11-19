<?php
class CommentModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // CREATE - Créer un nouveau commentaire
    public function create($data) {
        $sql = "INSERT INTO commentaires (utilisateur_id, article_id, contenu) 
                VALUES (:utilisateur_id, :article_id, :contenu)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':utilisateur_id' => $data['utilisateur_id'],
            ':article_id' => $data['article_id'],
            ':contenu' => $data['contenu']
        ]);
        
        return $this->db->lastInsertId();
    }
    
    // READ - Récupérer tous les commentaires
    public function getAll() {
        $sql = "SELECT c.*, u.nom as utilisateur_nom, a.titre as article_titre 
                FROM commentaires c 
                LEFT JOIN utilisateurs u ON c.utilisateur_id = u.utilisateur_id 
                LEFT JOIN articles a ON c.article_id = a.article_id 
                ORDER BY c.date_commentaire DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // READ - Récupérer les commentaires d'un article
    public function getByArticle($articleId) {
        $sql = "SELECT c.*, u.nom as utilisateur_nom 
                FROM commentaires c 
                LEFT JOIN utilisateurs u ON c.utilisateur_id = u.utilisateur_id 
                WHERE c.article_id = :article_id 
                ORDER BY c.date_commentaire DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':article_id' => $articleId]);
        return $stmt->fetchAll();
    }
    
    // READ - Récupérer un commentaire par ID
    public function getById($id) {
        $sql = "SELECT c.*, u.nom as utilisateur_nom, a.titre as article_titre 
                FROM commentaires c 
                LEFT JOIN utilisateurs u ON c.utilisateur_id = u.utilisateur_id 
                LEFT JOIN articles a ON c.article_id = a.article_id 
                WHERE c.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    // READ - Récupérer les commentaires d'un utilisateur
    public function getByUser($userId) {
        $sql = "SELECT c.*, a.titre as article_titre 
                FROM commentaires c 
                LEFT JOIN articles a ON c.article_id = a.article_id 
                WHERE c.utilisateur_id = :user_id 
                ORDER BY c.date_commentaire DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    // UPDATE - Mettre à jour un commentaire
    public function update($id, $contenu) {
        $sql = "UPDATE commentaires SET contenu = :contenu WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':contenu' => $contenu,
            ':id' => $id
        ]);
    }
    
    // DELETE - Supprimer un commentaire
    public function delete($id) {
        $sql = "DELETE FROM commentaires WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    // DELETE - Supprimer tous les commentaires d'un article
    public function deleteByArticle($articleId) {
        $sql = "DELETE FROM commentaires WHERE article_id = :article_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':article_id' => $articleId]);
    }
    
    // Compter les commentaires
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM commentaires";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    // Récupérer les commentaires récents
    public function getRecent($limit = 10) {
        $sql = "SELECT c.*, u.nom as utilisateur_nom, a.titre as article_titre 
                FROM commentaires c 
                LEFT JOIN utilisateurs u ON c.utilisateur_id = u.utilisateur_id 
                LEFT JOIN articles a ON c.article_id = a.article_id 
                ORDER BY c.date_commentaire DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>