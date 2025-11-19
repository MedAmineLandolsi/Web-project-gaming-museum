<?php
require_once '../config/database.php';

function getAllArticles() {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM articles ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getArticleById($id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM articles WHERE id = ? LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>