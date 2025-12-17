<?php
// Front controller (racine)
// Toutes les requêtes réécrites par .htaccess arrivent ici.

// Base URL dynamique: fonctionne si le projet est dans /projet, /projet-web/projet, etc.
if (!defined('BASE_URL')) {
	$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
	$baseUrl = rtrim(dirname($scriptName), '/');
	if ($baseUrl === '/' || $baseUrl === '.') {
		$baseUrl = '';
	}
	define('BASE_URL', $baseUrl);
}

require __DIR__ . '/view/frontoffice/index.php';
