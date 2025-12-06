<?php
session_start();

// Enregistrer l'action de déconnexion (optionnel)
if (isset($_SESSION['admin_username'])) {
    $log_message = "Admin " . $_SESSION['admin_username'] . " s'est déconnecté";
    error_log(date('Y-m-d H:i:s') . " - " . $log_message . "\n", 3, "../../logs/admin_actions.log");
}

// Stocker un message de succès avant de détruire la session
$logout_message = "Vous avez été déconnecté avec succès.";

// Détruire toutes les données de session
$_SESSION = array();

// Si vous voulez supprimer également le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Rediriger vers la page de login avec un message
header('Location: login.php?logout=success');
exit();
?>