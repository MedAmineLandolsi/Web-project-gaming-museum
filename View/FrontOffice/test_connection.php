<?php
require_once '../../connection.php';
require_once '../../Model/User.php';
require_once '../../Controller/UserController.php';

echo "<h3>Login Debug</h3>";

if ($_POST) {
    $controller = new UserController();
    $controller->login();
} else {
    echo "No POST data received<br>";
    echo "<form method='POST'>
        Email: <input type='email' name='email' value='test@test.com'><br>
        Password: <input type='password' name='password' value='password123'><br>
        <input type='submit' value='Login'>
    </form>";
}
?>