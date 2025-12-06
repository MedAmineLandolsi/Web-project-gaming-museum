<?php
session_start();

// Vérifier si un utilisateur est connecté (nouvelle structure)
$is_user_logged_in = isset($_SESSION['user_id']);
$user_info = [];

if ($is_user_logged_in) {
    // Récupérer les informations utilisateur avant déconnexion (pour log)
    $user_info = [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'first_name' => $_SESSION['user_first_name'] ?? $_SESSION['user_prenom'] ?? null,
        'last_name' => $_SESSION['user_last_name'] ?? $_SESSION['user_nom'] ?? null
    ];
    
    // Journalisation (optionnel)
    $log_message = date('Y-m-d H:i:s') . " - Utilisateur déconnecté - ";
    $log_message .= "ID: " . $user_info['id'] . ", ";
    $log_message .= "Nom: " . ($user_info['first_name'] ?? '') . " " . ($user_info['last_name'] ?? '');
    error_log($log_message . "\n", 3, __DIR__ . "/../../logs/user_sessions.log");
}

// Stocker un message de succès temporairement
// Nous allons utiliser un cookie pour conserver le message entre la déconnexion et la redirection
$success_message = "✅ Vous avez été déconnecté avec succès !";
setcookie("logout_message", $success_message, time() + 30, "/"); // 30 secondes

// Préparer la redirection avec un paramètre de déconnexion
$redirect_url = "index.php?logout=success";

// Si l'utilisateur venait d'une page spécifique, on pourrait le rediriger vers cette page
// $redirect_url = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : 'index.php?logout=success';

// Détruire TOUTES les variables de session utilisateur (nouvelle structure)
$user_session_vars = [
    // Nouvelle structure (gaming_museum)
    'user_id',
    'user_first_name',
    'user_last_name',
    'username',
    'user_email',
    'user_role',
    
    // Ancienne structure (rétrocompatibilité)
    'user_nom',
    'user_prenom',
    'user_nom_complet'
];

foreach ($user_session_vars as $var) {
    if (isset($_SESSION[$var])) {
        unset($_SESSION[$var]);
    }
}

// Détruire la session complètement
session_destroy();

// Détruire le cookie de session si nécessaire
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirection
header('Location: ' . $redirect_url);
exit();
?>