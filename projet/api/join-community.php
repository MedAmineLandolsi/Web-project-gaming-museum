<?php
// Démarrer le buffer de sortie pour éviter toute sortie parasite
ob_start();

// Désactiver l'affichage des erreurs pour ne pas polluer le JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour envoyer du JSON propre
function send_json(array $payload, int $status = 200) {
    ob_clean(); // Nettoyer tout ce qui aurait pu être affiché
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

try {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        send_json(['success' => false, 'message' => 'Vous devez être connecté'], 401);
    }

    // Vérifier la méthode HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        send_json(['success' => false, 'message' => 'Méthode non autorisée'], 405);
    }

    // Charger les dépendances
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/Communaute.php';

    // Récupérer les données JSON
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        send_json(['success' => false, 'message' => 'Données JSON invalides'], 400);
    }

    $communaute_id = isset($input['communaute_id']) ? (int)$input['communaute_id'] : null;
    $user_id = (int)$_SESSION['user_id'];

    if (!$communaute_id || $communaute_id <= 0) {
        send_json(['success' => false, 'message' => 'ID de communauté manquant ou invalide'], 422);
    }

    // Connexion à la base de données
    $database = new Database();
    $db = $database->connect();

    if (!$db) {
        send_json(['success' => false, 'message' => 'Erreur de connexion à la base de données'], 500);
    }

    // Initialiser le modèle
    $communauteModel = new Communaute($db);

    // Vérifier si la communauté existe
    $communauteModel->id = $communaute_id;
    if (!$communauteModel->read_single()) {
        send_json(['success' => false, 'message' => 'Communauté non trouvée'], 404);
    }

    // Vérifier si déjà membre
    if ($communauteModel->hasJoined($user_id, $communaute_id)) {
        send_json(['success' => false, 'message' => 'Vous êtes déjà membre de cette communauté'], 409);
    }

    // Rejoindre la communauté
    if ($communauteModel->join($user_id, $communaute_id)) {
        send_json(['success' => true, 'message' => 'Vous avez rejoint la communauté avec succès !']);
    }

    // Si on arrive ici, l'insertion a échoué
    send_json(['success' => false, 'message' => 'Erreur lors de l\'ajout à la communauté'], 500);

} catch (PDOException $e) {
    error_log("Erreur PDO join-community: " . $e->getMessage());
    send_json(['success' => false, 'message' => 'Erreur de base de données'], 500);
} catch (Exception $e) {
    error_log("Erreur join-community: " . $e->getMessage());
    send_json(['success' => false, 'message' => 'Erreur serveur'], 500);
} finally {
    ob_end_flush();
}
?>
