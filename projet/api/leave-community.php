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

    // Récupérer les données (peut être POST ou JSON)
    $communaute_id = null;
    if (!empty($_POST['communaute_id'])) {
        $communaute_id = (int)$_POST['communaute_id'];
    } else {
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($input['communaute_id'])) {
            $communaute_id = (int)$input['communaute_id'];
        }
    }

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

    // Empêcher le créateur de se retirer de sa propre communauté
    if ($communauteModel->createur_id == $user_id) {
        send_json(['success' => false, 'message' => 'Vous êtes le créateur de la communauté'], 403);
    }

    // Vérifier si l'utilisateur est membre
    if (!$communauteModel->hasJoined($user_id, $communaute_id)) {
        send_json(['success' => false, 'message' => "Vous n'êtes pas membre de cette communauté"], 409);
    }

    // Quitter la communauté
    $stmt = $db->prepare("DELETE FROM membre_communaute WHERE membre_id = ? AND communaute_id = ?");
    $stmt->execute([$user_id, $communaute_id]);

    if ($stmt->rowCount() > 0) {
        send_json(['success' => true, 'message' => 'Vous avez quitté la communauté avec succès']);
    } else {
        send_json(['success' => false, 'message' => "Erreur lors de la suppression de l'adhésion"], 500);
    }

} catch (PDOException $e) {
    error_log("Erreur PDO leave-community: " . $e->getMessage());
    send_json(['success' => false, 'message' => 'Erreur de base de données'], 500);
} catch (Exception $e) {
    error_log("Erreur leave-community: " . $e->getMessage());
    send_json(['success' => false, 'message' => 'Erreur serveur'], 500);
} finally {
    ob_end_flush();
}
?>