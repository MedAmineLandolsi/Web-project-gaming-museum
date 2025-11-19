<?php
require_once '../../connection.php';
require_once '../../Model/User.php';
require_once '../../Controller/UserController.php';

$controller = new UserController();
$controller->register();
?>