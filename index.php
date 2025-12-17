<?php
// Point d'entrée principal du workspace
// Permet d'accéder aux différents sous-projets depuis la racine.
// Par défaut, on garde le comportement existant : gaming_museum.

$app = isset($_GET['app']) ? strtolower(trim((string) $_GET['app'])) : '';

$routes = [
	// comportement historique
	'gaming_museum' => 'gaming_museum/view/frontoffice/index.php',
	'museum' => 'gaming_museum/view/frontoffice/index.php',
	'gm' => 'gaming_museum/view/frontoffice/index.php',

	// dossier intégré
	'projetweb' => 'ProjetWeb/index.php',
	'pw' => 'ProjetWeb/index.php',

	// autres dossiers présents (optionnel)
	'projet' => 'projet/index.php',
	'projet_gaming' => 'projet_gaming/index.php',
	'projetweb12' => 'projetweb12/index.php',
];

$target = $routes[$app] ?? $routes['gaming_museum'];
header('Location: ' . $target);
exit;
