<?php
session_start();

// En dev, n'affiche pas les notices/warnings au milieu du HTML (ça casse souvent les URLs d'assets)
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
// Auth réelle: ne pas simuler d'utilisateur.

// Inclure les fichiers nécessaires
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__) . '/shared/validation.php';
require_once dirname(__DIR__, 2) . '/model/Communaute.php';
require_once dirname(__DIR__, 2) . '/model/Publication.php';
require_once dirname(__DIR__, 2) . '/controller/CommunauteController.php';
require_once dirname(__DIR__, 2) . '/controller/PublicationController.php';
require_once dirname(__DIR__, 2) . '/controller/ApiController.php';

// Initialiser la base de données
$database = new Database();
$db = $database->connect();

if (!$db) {
    die("
        <div style='text-align: center; padding: 50px; font-family: Arial, sans-serif;'>
            <h1 style='color: #dc3545;'>Erreur de connexion à la base de données</h1>
            <p>Vérifiez la configuration dans config.php</p>
            <p>Assurez-vous que MySQL est démarré et que les identifiants sont corrects.</p>
        </div>
    ");
}

// Initialiser les contrôleurs
$communauteController = new CommunauteController($db);
$publicationController = new PublicationController($db);
$apiController = new ApiController($db);

// Router
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];


// BASE_URL est défini dans /index.php (front controller racine)
if (!defined('BASE_URL')) {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $baseUrl = rtrim(dirname($scriptName), '/');
    if ($baseUrl === '/' || $baseUrl === '.') {
        $baseUrl = '';
    }
    define('BASE_URL', $baseUrl);
}
// Construire le path à partir de REQUEST_URI en enlevant le query string
$path = explode('?', $request)[0];

// Retirer le base path (ex: /projet-web/projet) s'il est présent au début
if (BASE_URL !== '' && strpos($path, BASE_URL) === 0) {
    $path = substr($path, strlen(BASE_URL));
}

// Pour compatibilité, aussi retirer /index.php si présent au début
if (strpos($path, '/index.php') === 0) {
    $path = substr($path, strlen('/index.php'));
}

// (debug blocks removed) To enable debugging, open this file and add temporary prints.

// Normaliser le path (enlever les slashes et points en fin sauf pour la racine)
// Certaines requêtes peuvent contenir un point final accidentel
// qui empêcherait la correspondance des routes. On supprime donc les '/' et '.' en fin.
$path = rtrim($path, '/.');
if (empty($path)) {
    $path = '/';
} else {
    // S'assurer que le path commence par /
    if ($path === '' || $path === null) {
        $path = '/';
    } elseif ($path[0] !== '/') {
        $path = '/' . $path;
    }
}

// Vérifier les routes dynamiques AVANT le switch
$matches = [];
$routeMatched = false;

// Join / Leave communauté
if (preg_match('/^\/communautes\/(\d+)\/join$/', $path, $matches)) {
    if ($method === 'POST') {
        $communauteController->join((int) $matches[1]);
    }
    http_response_code(405);
    exit;
} elseif (preg_match('/^\/communautes\/(\d+)\/leave$/', $path, $matches)) {
    if ($method === 'POST') {
        $communauteController->leave((int) $matches[1]);
    }
    http_response_code(405);
    exit;
}

// Routes Publications Front avec regex - ORDRE IMPORTANT : edit, update, delete AVANT show
// Note: Les URLs front utilisent /publications/edit/{id} et /publications/update/{id}
if (!$routeMatched && preg_match('/^\/publications\/edit\/(\d+)$/', $path, $matches)) {
    if ($method == 'GET') {
        $publicationController->editFront($matches[1]);
    }
    $routeMatched = true;
} elseif (!$routeMatched && preg_match('/^\/publications\/update\/(\d+)$/', $path, $matches)) {
    if ($method == 'POST') {
        $publicationController->updateFront($matches[1], $_POST);
    }
    $routeMatched = true;
} elseif (!$routeMatched && preg_match('/^\/publications\/delete\/(\d+)$/', $path, $matches)) {
    if ($method == 'POST') {
        $publicationController->deleteFront($matches[1]);
    }
    $routeMatched = true;
} elseif (!$routeMatched && preg_match('/^\/publications\/(\d+)$/', $path, $matches)) {
    $publicationController->showFront($matches[1]);
    $routeMatched = true;
}

// (Routes supprimées)

// Routes Communautés Front avec regex
if (!$routeMatched && preg_match('/^\/communautes\/(\d+)$/', $path, $matches)) {
    $communauteController->showFront($matches[1]);
    $routeMatched = true;
}

// (Routes Admin supprimées)

// Routes Admin Communautés avec regex
if (!$routeMatched && preg_match('/^\/admin\/communautes\/(\d+)$/', $path, $matches)) {
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    if ($method == 'GET') {
        $communauteController->showBack($matches[1]);
    }
    $routeMatched = true;
} elseif (!$routeMatched && preg_match('/^\/admin\/communautes\/(\d+)\/edit$/', $path, $matches)) {
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
		header('Location: ' . BASE_URL . '/');
        exit;
    }
    if ($method == 'GET') {
        $communauteController->edit($matches[1]);
    } elseif ($method == 'POST') {
        $communauteController->update($matches[1], $_POST);
    }
    $routeMatched = true;
} elseif (!$routeMatched && preg_match('/^\/admin\/communautes\/(\d+)\/delete$/', $path, $matches)) {
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
		header('Location: ' . BASE_URL . '/');
        exit;
    }
    if ($method == 'POST') {
        $communauteController->delete($matches[1]);
    }
    $routeMatched = true;
}

// Routes Admin Publications avec regex
if (!$routeMatched && preg_match('/^\/admin\/publications\/(\d+)$/', $path, $matches)) {
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
		header('Location: ' . BASE_URL . '/');
        exit;
    }
    if ($method == 'GET') {
        $publicationController->showBack($matches[1]);
    }
    $routeMatched = true;
} elseif (!$routeMatched && preg_match('/^\/admin\/publications\/(\d+)\/edit$/', $path, $matches)) {
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
		header('Location: ' . BASE_URL . '/');
        exit;
    }
    if ($method == 'GET') {
        $publicationController->edit($matches[1]);
    } elseif ($method == 'POST') {
        $publicationController->update($matches[1], $_POST);
    }
    $routeMatched = true;
} elseif (!$routeMatched && preg_match('/^\/admin\/publications\/(\d+)\/delete$/', $path, $matches)) {
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
		header('Location: ' . BASE_URL . '/');
        exit;
    }
    if ($method == 'POST') {
        $publicationController->delete($matches[1]);
    }
    $routeMatched = true;
}

// Si une route dynamique a été trouvée, on arrête ici
if ($routeMatched) {
    exit;
}

// Routes principales (routes statiques)
switch ($path) {
    case '':
    case '/':
        $communauteController->indexFront();
        break;

    // === ROUTES ASSISTANT IA ===
    case '/publications/ai-assistant':
        $publicationController->showAIAssistant();
        break;
        
    case '/ai-assistant/handle':
        $publicationController->handleAIAssistant();
        break;
    // === ROUTES FRONT OFFICE ===
    
    // (Routes supprimées)

    // Communautés Front
    case '/communautes':
        $communauteController->indexFront();
        break;
        
    case '/communautes/create':
        if ($method == 'GET') {
            $communauteController->createFront();
        } elseif ($method == 'POST') {
            $communauteController->storeFront($_POST);
        }
        break;

    // Publications Front - ROUTES CORRIGÉES
    case '/publications':
        $publicationController->indexFront();
        break;
        
    case '/publications/create':
        if ($method == 'GET') {
            $publicationController->createFront();
        } elseif ($method == 'POST') {
            $publicationController->storeFront($_POST);
        }
        break;

    // Recherche de publications
    case '/publications/search':
        if ($method == 'GET' && isset($_GET['q'])) {
            $keyword = $_GET['q'];
            $publications = $publicationController->searchPublications($keyword);
            
            $title = "Résultats de recherche pour: " . htmlspecialchars($keyword);
            ob_start();
                include 'publications/search.php';
            $content = ob_get_clean();
            include 'layout.php';
        }
        break;

    // === ROUTES BACK OFFICE ===

    // Admin Dashboard
    case '/admin':
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        // Préparer les statistiques du tableau de bord
        $communauteStatsModel = new Communaute($db);
        $publicationStatsModel = new Publication($db);

        $todayStart = date('Y-m-d 00:00:00');
        $weekStart = date('Y-m-d 00:00:00', strtotime('-6 days'));
        $monthStart = date('Y-m-01 00:00:00');

        $stats = [
            'communautes_total' => $communauteStatsModel->countAll(),
            'publications_total' => $publicationStatsModel->countAll(),
            'comments_total' => $publicationStatsModel->totalComments(),
            'communautes_new_month' => $communauteStatsModel->countCreatedSince($monthStart),
            'publications_today' => $publicationStatsModel->countSinceDate($todayStart),
        ];

        $recentInteractions = $publicationStatsModel->totalInteractionsSince($weekStart);
        $stats['engagement_rate'] = min(100, round(($recentInteractions / max(1, 1)) * 100));

        $latestCommunautes = $communauteStatsModel->getLatest(5);
        $latestPublications = $publicationStatsModel->getLatest(5);

        $sidebarStats = [
            'dashboard' => $stats['communautes_total'] + $stats['publications_total'],
            'communautes' => $stats['communautes_total'],
            'publications' => $stats['publications_total'],
        ];

        $title = "Tableau de bord administrateur";
        ob_start();
            $backofficeDir = dirname(__DIR__) . '/backoffice';
            include $backofficeDir . '/dashboard.php';
        $content = ob_get_clean();
        include $backofficeDir . '/layout.php';
        break;

    // (Routes Admin supprimées)

    // Communautés Back
    case '/admin/communautes':
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        if ($method == 'GET') {
            $communauteController->indexBack();
        }
        break;
        
    case '/admin/communautes/create':
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        if ($method == 'GET') {
            $communauteController->create();
        } elseif ($method == 'POST') {
            $communauteController->store($_POST);
        }
        break;
        

    // Publications Back
    case '/admin/publications':
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        if ($method == 'GET') {
            $publicationController->indexBack();
        }
        break;
        
    case '/admin/publications/create':
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
		header('Location: ' . BASE_URL . '/');
            exit;
        }
        if ($method == 'GET') {
            $publicationController->create();
        } elseif ($method == 'POST') {
            $publicationController->store($_POST);
        }
        break;
        

    // === ROUTES API ===

    case '/api/search':
        if ($method === 'GET') {
            $apiController->search();
        }
        http_response_code(405);
        exit;

    case '/api/ai/chat':
        if ($method === 'POST') {
            $apiController->chat();
        }
        http_response_code(405);
        exit;

    // === ROUTES UTILITAIRES ===

    // Route pour réinitialiser les données (débogage)
    case '/reset-demo':
	    $__base_for_views = defined('BASE_URL') ? BASE_URL : '';
        echo "<h1>Réinitialisation des données de démonstration</h1>";
        echo "<p>Cette fonctionnalité n'est pas implémentée. Supprimez manuellement la base de données 'projet_db' pour réinitialiser.</p>";
        echo "<a href='" . $__base_for_views . "/'>Retour à l'accueil</a>";
        break;

    // Route pour réparer la base de données
    case '/fix-database':
        include 'fix_database.php';
        break;

    // Route de déconnexion
    case '/logout':
        session_destroy();
        header('Location: ' . BASE_URL . '/');
        exit;
        break;

    default:
        http_response_code(404);
        $title = "Page non trouvée";
        ob_start();
                    $__base_for_views = defined('BASE_URL') ? BASE_URL : '';
                        echo "<div class='container text-center py-5'>
                                        <h1>404 - Page non trouvée</h1>
                                        <p>La page demandée n'existe pas ou l'URL est incorrecte.<br>Accédez au site via <b>" . $__base_for_views . "/</b> et non <b>" . $__base_for_views . "/view/frontoffice/</b>.</p>
                                        <div class='mt-4'>
                                                <a href='" . $__base_for_views . "/' class='btn btn-primary me-2'>Accueil</a>
                                                <a href='" . $__base_for_views . "/communautes' class='btn btn-outline-primary'>Communautés</a>
                                                <a href='" . $__base_for_views . "/publications' class='btn btn-outline-primary'>Publications</a>
                                        </div>
                                    </div>";
        $content = ob_get_clean();
        include __DIR__ . '/layout.php';
        break;
}

// Nettoyer les messages flash après affichage
if (isset($_SESSION['form_errors'])) unset($_SESSION['form_errors']);
if (isset($_SESSION['old_input'])) unset($_SESSION['old_input']);
if (isset($_SESSION['success_message'])) unset($_SESSION['success_message']);
?>