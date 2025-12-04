<?php
/**
 * Helper pour générer les chemins des assets
 */
function asset($path) {
    // Déterminer le chemin de base
    $basePath = '/ProjetWeb';
    
    // Si on est dans un sous-dossier, ajuster
    $scriptPath = $_SERVER['SCRIPT_NAME'];
    if (strpos($scriptPath, '/View/') !== false) {
        $basePath = '../../';
    }
    
    return $basePath . '/' . ltrim($path, '/');
}

function url($path = '') {
    $baseUrl = '/ProjetWeb';
    return $baseUrl . '/' . ltrim($path, '/');
}
?>

