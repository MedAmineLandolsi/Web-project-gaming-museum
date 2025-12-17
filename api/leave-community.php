<?php
// Fonctionnalité supprimée : adhésion aux communautés.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

http_response_code(410);
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Fonctionnalité supprimée : l\'adhésion aux communautés n\'est plus disponible.'
]);
exit;
?>