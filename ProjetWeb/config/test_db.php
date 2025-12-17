<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'database.php'; // include your Database class

// 1. Connect
$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "✅ Connected to the database successfully!<br>";
} else {
    die("❌ Connection failed!");
}

// 2. Insert data to test persistence
try {
    $stmt = $conn->prepare("INSERT INTO reclamations (title, client) VALUES (:title, :client)");
    $stmt->execute([
        ':title' => 'Test Reclamation',
        ':client' => 'John Doe'
    ]);
    echo "✅ Data inserted successfully!<br>";
} catch(PDOException $e) {
    echo "❌ Insert failed: " . $e->getMessage();
}

// 3. Retrieve data
try {
    $result = $conn->query("SELECT * FROM reclamations")->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($result);
    echo "</pre>";
} catch(PDOException $e) {
    echo "❌ Select failed: " . $e->getMessage();
}
?>
