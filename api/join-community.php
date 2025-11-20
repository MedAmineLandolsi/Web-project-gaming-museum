<?php
session_start();
require_once '../config/database.php';
require_once '../models/Communaute.php';

header('Content-Type: application/json');

// Debug
error_log("API join-community appelée");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    // Récupérer les données
    $input = json_decode(file_get_contents('php://input'), true);
    error_log("Données reçues: " . print_r($input, true));
    
    $communaute_id = $input['communaute_id'] ?? null;
    $user_id = $_SESSION['user_id'];
    
    if (!$communaute_id) {
        echo json_encode(['success' => false, 'message' => 'ID de communauté manquant']);
        exit;
    }
    
    $database = new Database();
    $db = $database->connect();
    $communauteModel = new Communaute($db);
    
    // Vérifier si la communauté existe
    $communauteModel->id = $communaute_id;
    if (!$communauteModel->read_single()) {
        echo json_encode(['success' => false, 'message' => 'Communauté non trouvée']);
        exit;
    }
    
    // Vérifier si déjà membre
    if ($communauteModel->hasJoined($user_id, $communaute_id)) {
        echo json_encode(['success' => false, 'message' => 'Vous êtes déjà membre']);
        exit;
    }
    
    // Rejoindre
    if ($communauteModel->join($user_id, $communaute_id)) {
        echo json_encode(['success' => true, 'message' => 'Vous avez rejoint la communauté !']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur technique']);
    }
    
} catch (Exception $e) {
    error_log("Erreur API: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>