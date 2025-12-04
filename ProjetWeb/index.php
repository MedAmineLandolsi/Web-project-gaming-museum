<?php
// Chargement automatique des contrôleurs
spl_autoload_register(function($className) {
    if (file_exists('Controller/' . $className . '.php')) {
        require_once 'Controller/' . $className . '.php';
    } elseif (file_exists('Model/' . $className . '.php')) {
        require_once 'Model/' . $className . '.php';
    }
});

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
            case 'edit':
                $frontController->editReclamation($id);
                break;
            case 'update':
                $frontController->updateReclamation($id);
                break;
            case 'details':
                $frontController->showDetails($id);
                break;
            default:
                $frontController->index();
                break;
        }
        break;
        
    case 'back':
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