<?php
// routes.php - Fichier de routes pour les nouvelles fonctionnalités

// Récupérer l'action et les paramètres
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/projet/';
$path = str_replace($base_path, '', $request_uri);
$segments = explode('/', $path);

// Initialiser les contrôleurs
require_once 'config.php';
$database = new Database();
$db = $database->connect();

require_once 'controllers/MembreController.php';
require_once 'controllers/CommunauteController.php';
require_once 'controllers/PublicationController.php';

$membreController = new MembreController($db);
$communauteController = new CommunauteController($db);
$publicationController = new PublicationController($db);

// Routes pour les nouvelles fonctionnalités
if (isset($segments[0])) {
    switch($segments[0]) {
        case 'sync-users':
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
                $membreController->syncWithUsers();
            }
            break;
            
        case 'dashboard':
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $membreController->dashboard($segments[1]);
            }
            break;
            
        case 'members':
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $communauteController->showMembers($segments[1]);
            }
            break;
            
        case 'evenements':
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $communauteController->showEvenements($segments[1]);
            }
            break;
            
        case 'join-by-user':
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $communauteController->joinByUser($segments[1]);
            }
            break;
            
        case 'user-publications':
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $publicationController->showByUser($segments[1]);
            }
            break;
            
        case 'commentaires':
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $publicationController->showCommentaires($segments[1]);
            }
            break;
            
        case 'articles-related':
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $publicationController->showArticlesRelated($segments[1]);
            }
            break;
    }
}
?>