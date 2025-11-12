<?php
require_once "controllers/JeuxController.php";
$controller = new JeuxController();

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'add': $controller->create(); break;
    case 'edit': $controller->edit($id); break;
    case 'delete': $controller->delete($id); break;
    default: $controller->index(); break;
}
?>