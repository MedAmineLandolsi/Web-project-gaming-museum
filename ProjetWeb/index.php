<?php
// Session (utilisée pour protéger le backoffice)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/i18n.php';
if (isset($_GET['lang'])) {
    projetweb_set_lang((string) $_GET['lang']);
}

// Chargement automatique des contrôleurs
spl_autoload_register(function($className) {
    if (file_exists('Controller/' . $className . '.php')) {
        require_once 'Controller/' . $className . '.php';
    } elseif (file_exists('Model/' . $className . '.php')) {
        require_once 'Model/' . $className . '.php';
    }
});

function projetweb_is_admin_connected(): bool {
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        return true;
    }

    $loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    if (!$loggedIn) {
        return false;
    }

    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        return true;
    }

    $role = strtolower(trim((string)($_SESSION['role'] ?? '')));
    return $role === 'admin';
}

// Récupération des paramètres
$action = isset($_GET['action']) ? $_GET['action'] : 'front';
$method = isset($_GET['method']) ? $_GET['method'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Instanciation des contrôleurs
$frontController = new FrontController();
$backController = new BackController();

// Routing
switch ($action) {
    case 'front':
        switch ($method) {
            case 'add':
                $frontController->addReclamation();
                break;
            case 'logout':
                $frontController->logout();
                break;
            case 'edit':
                $frontController->editReclamation($id);
                break;
            case 'update':
                $frontController->updateReclamation($id);
                break;
            case 'details':
                $frontController->showDetails($id);
                break;
            case 'historique':
                $frontController->historique();
                break;
            default:
                $frontController->index();
                break;
        }
        break;
        
    case 'back':
        if (!projetweb_is_admin_connected()) {
            // Si l'admin n'est pas connecté, ne pas afficher le dashboard
            header('Location: ../gaming_museum/view/frontoffice/login.php');
            exit;
        }
        switch ($method) {
            case 'addReponse':
                $backController->addReponse();
                break;
            case 'edit':
                $backController->editReclamation($id);
                break;
            case 'update':
                $backController->updateReclamation($id);
                break;
            case 'delete':
                $backController->deleteReclamation($id);
                break;
            case 'export':
                $backController->exportData();
                break;
            case 'clear':
                $backController->clearAll();
                break;
            case 'details':
                $backController->showDetails($id);
                break;
            default:
                $backController->index();
                break;
        }
        break;
        
    default:
        $frontController->index();
        break;
}
?>