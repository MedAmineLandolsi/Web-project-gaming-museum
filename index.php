<?php
// Front controller loader
// Redirige toutes les requêtes vers le frontoffice index.php
// pour que les règles de réécriture dans .htaccess fonctionnent.

// Activer l'affichage des erreurs en développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Redirection de secours : si quelqu'un tente d'accéder directement
// à /projet/view/frontoffice/... on le renvoie vers la racine /projet/
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($requestUri, '/view/frontoffice') !== false) {
	header('Location: /projet/');
	exit;
}

// Inclure le front controller
require __DIR__ . '/view/frontoffice/index.php';
