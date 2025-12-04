<?php
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Vérifier si c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    
    // Valider l'email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Adresse email invalide'
        ]);
        exit;
    }
    
    try {
        // Récupérer les participations de l'utilisateur
        $query = "
            SELECT 
                e.*,
                p.date_inscription,
                p.nom_participant
            FROM participation p
            INNER JOIN evenement e ON p.id_evenement = e.id_evenement
            WHERE p.email = ?
            ORDER BY e.date_debut DESC
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'events' => $events,
            'count' => count($events)
        ]);
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur de base de données'
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}
?>