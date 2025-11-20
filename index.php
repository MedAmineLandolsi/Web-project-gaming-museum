<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// === SIMULATION D'UTILISATEUR CONNECTÉ ===
// En production, remplacer par un vrai système d'authentification
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Jean Dupont
    $_SESSION['user_nom'] = 'Dupont';
    $_SESSION['user_prenom'] = 'Jean';
    $_SESSION['user_email'] = 'jean.dupont@email.com';
    $_SESSION['user_avatar'] = 'https://i.pravatar.cc/150?img=1';
    $_SESSION['is_admin'] = true; // Simulation admin pour tests
}

// Inclure les fichiers nécessaires
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Membre.php';
require_once __DIR__ . '/models/Communaute.php';
require_once __DIR__ . '/models/Publication.php';
require_once __DIR__ . '/controllers/MembreController.php';
require_once __DIR__ . '/controllers/CommunauteController.php';
require_once __DIR__ . '/controllers/PublicationController.php';

// Initialiser la base de données
$database = new Database();
$db = $database->connect();

if (!$db) {
    die("
        <div style='text-align: center; padding: 50px; font-family: Arial, sans-serif;'>
            <h1 style='color: #dc3545;'>Erreur de connexion à la base de données</h1>
            <p>Vérifiez la configuration dans config/database.php</p>
            <p>Assurez-vous que MySQL est démarré et que les identifiants sont corrects.</p>
        </div>
    ");
}

// Initialiser les contrôleurs
$membreController = new MembreController($db);
$communauteController = new CommunauteController($db);
$publicationController = new PublicationController($db);

// Router
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$base_path = '/projet';
$path = str_replace($base_path, '', $request);
$path = explode('?', $path)[0];

// Routes principales
switch ($path) {
    case '':
    case '/':
        $communauteController->indexFront();
        break;

    // === ROUTES FRONT OFFICE ===
    
    // Membres Front
    case '/membres':
        $membreController->indexFront();
        break;
        
    case (preg_match('/^\/membres\/(\d+)$/', $path, $matches) ? true : false):
        $membreController->showFront($matches[1]);
        break;

    // Communautés Front
    case '/communautes':
        $communauteController->indexFront();
        break;
        
    case (preg_match('/^\/communautes\/(\d+)$/', $path, $matches) ? true : false):
        $communauteController->showFront($matches[1]);
        break;
        
    case '/communautes/create':
        if ($method == 'GET') {
            $communauteController->createFront();
        } elseif ($method == 'POST') {
            $communauteController->storeFront($_POST);
        }
        break;

    // Publications Front
    case '/publications':
        $publicationController->indexFront();
        break;
        
    case (preg_match('/^\/publications\/(\d+)$/', $path, $matches) ? true : false):
        $publicationController->showFront($matches[1]);
        break;
        
    case '/publications/create':
        if ($method == 'GET') {
            $publicationController->createFront();
        } elseif ($method == 'POST') {
            $publicationController->storeFront($_POST);
        }
        break;

    // === ROUTES BACK OFFICE ===

    // Admin Dashboard
    case '/admin':
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        $title = "Tableau de bord administrateur";
        ob_start();
        include 'views/back/dashboard.php';
        $content = ob_get_clean();
        include 'views/back/layout.php';
        break;

    // Membres Back
    case '/admin/membres':
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $membreController->indexBack();
        }
        break;
        
    case '/admin/membres/create':
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $membreController->create();
        } elseif ($method == 'POST') {
            $membreController->store($_POST);
        }
        break;
        
    case (preg_match('/^\/admin\/membres\/(\d+)$/', $path, $matches) ? true : false):
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $membreController->showBack($matches[1]);
        }
        break;
        
    case (preg_match('/^\/admin\/membres\/(\d+)\/edit$/', $path, $matches) ? true : false):
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $membreController->edit($matches[1]);
        } elseif ($method == 'POST') {
            $membreController->update($matches[1], $_POST);
        }
        break;
        
    case (preg_match('/^\/admin\/membres\/(\d+)\/delete$/', $path, $matches) ? true : false):
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'POST') {
            $membreController->delete($matches[1]);
        }
        break;

    // Communautés Back
    case '/admin/communautes':
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $communauteController->indexBack();
        }
        break;
        
    case '/admin/communautes/create':
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $communauteController->create();
        } elseif ($method == 'POST') {
            $communauteController->store($_POST);
        }
        break;
        
    case (preg_match('/^\/admin\/communautes\/(\d+)$/', $path, $matches) ? true : false):
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $communauteController->showBack($matches[1]);
        }
        break;
        
    case (preg_match('/^\/admin\/communautes\/(\d+)\/edit$/', $path, $matches) ? true : false):
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $communauteController->edit($matches[1]);
        } elseif ($method == 'POST') {
            $communauteController->update($matches[1], $_POST);
        }
        break;
        
    case (preg_match('/^\/admin\/communautes\/(\d+)\/delete$/', $path, $matches) ? true : false):
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'POST') {
            $communauteController->delete($matches[1]);
        }
        break;

    // Publications Back
    case '/admin/publications':
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $publicationController->indexBack();
        }
        break;
        
    case '/admin/publications/create':
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $publicationController->create();
        } elseif ($method == 'POST') {
            $publicationController->store($_POST);
        }
        break;
        
    case (preg_match('/^\/admin\/publications\/(\d+)$/', $path, $matches) ? true : false):
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $publicationController->showBack($matches[1]);
        }
        break;
        
    case (preg_match('/^\/admin\/publications\/(\d+)\/edit$/', $path, $matches) ? true : false):
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'GET') {
            $publicationController->edit($matches[1]);
        } elseif ($method == 'POST') {
            $publicationController->update($matches[1], $_POST);
        }
        break;
        
    case (preg_match('/^\/admin\/publications\/(\d+)\/delete$/', $path, $matches) ? true : false):
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /projet/');
            exit;
        }
        if ($method == 'POST') {
            $publicationController->delete($matches[1]);
        }
        break;

// === ROUTES API ===

case '/api/join-community':
    if ($method == 'POST') {
        include 'api/join-community.php';
    }
    break;

    // === ROUTES UTILITAIRES ===

    // Route pour réinitialiser les données (débogage)
    case '/reset-demo':
        echo "<h1>Réinitialisation des données de démonstration</h1>";
        echo "<p>Cette fonctionnalité n'est pas implémentée. Supprimez manuellement la base de données 'projet_db' pour réinitialiser.</p>";
        echo "<a href='/projet/'>Retour à l'accueil</a>";
        break;

    // Route pour réparer la base de données
    case '/fix-database':
        include 'fix_database.php';
        break;

    default:
        http_response_code(404);
        $title = "Page non trouvée";
        ob_start();
        echo "<div class='container text-center py-5'>
                <h1>404 - Page non trouvée</h1>
                <p>La page '$path' n'existe pas.</p>
                <div class='mt-4'>
                    <a href='/projet/' class='btn btn-primary me-2'>Accueil</a>
                    <a href='/projet/communautes' class='btn btn-outline-primary'>Communautés</a>
                    <a href='/projet/membres' class='btn btn-outline-primary'>Membres</a>
                </div>
              </div>";
        $content = ob_get_clean();
        include 'views/front/layout.php';
        break;
}

// Nettoyer les messages flash après affichage
if (isset($_SESSION['form_errors'])) unset($_SESSION['form_errors']);
if (isset($_SESSION['old_input'])) unset($_SESSION['old_input']);
if (isset($_SESSION['success_message'])) unset($_SESSION['success_message']);
?>