<?php
/**
 * Helper pour générer les chemins des assets
 */
function projetweb_base_url() {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptName = str_replace('\\', '/', $scriptName);

    // Capture le préfixe jusqu'à /ProjetWeb (ex: /projet-web/ProjetWeb)
    if (preg_match('#^(.*?/ProjetWeb)(?:/|$)#i', $scriptName, $m)) {
        return rtrim($m[1], '/');
    }

    // Fallback : dossier du script courant
    $dir = str_replace('\\', '/', dirname($scriptName));
    $dir = rtrim($dir, '/');
    return $dir === '' ? '/' : $dir;
}

function asset($path) {
    return projetweb_base_url() . '/' . ltrim($path, '/');
}

function url($path = '') {
    return projetweb_base_url() . '/' . ltrim($path, '/');
}
?>

