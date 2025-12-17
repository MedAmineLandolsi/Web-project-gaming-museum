<?php
session_start(); // AJOUTER CETTE LIGNE AU DÉBUT

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Vérifier si c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // OPTION 1: Si l'utilisateur est connecté, utiliser son ID
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        try {
            // Récupérer les participations de l'utilisateur connecté
            $query = "
                SELECT 
                    e.*,
                    p.date_inscription,
                    p.nom_participant,
                    p.email,
                    p.telephone,
                    u.username as user_name
                FROM participation p
                INNER JOIN evenement e ON p.id_evenement = e.id_evenement
                LEFT JOIN users u ON p.User_ID = u.id
                WHERE p.User_ID = ?
                ORDER BY e.date_debut DESC
            ";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$user_id]);
            
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'events' => $events,
                'count' => count($events),
                'user_info' => [
                    'name' => $_SESSION['username'] ?? '',
                    'email' => $_SESSION['email'] ?? '',
                    'id' => $_SESSION['user_id']
                ]
            ]);
            
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ]);
        }
        
    } 
    // OPTION 2: Si non connecté, vérifier par email (compatibilité ancienne méthode)
    else {
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
            // Récupérer les participations par email
            $query = "
                SELECT 
                    e.*,
                    p.date_inscription,
                    p.nom_participant,
                    p.email,
                    p.telephone,
                    u.username as user_name
                FROM participation p
                INNER JOIN evenement e ON p.id_evenement = e.id_evenement
                LEFT JOIN users u ON p.User_ID = u.id
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
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ]);
        }
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}
?>