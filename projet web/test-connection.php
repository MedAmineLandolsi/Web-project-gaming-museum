<?php
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

if($conn) {
    echo "✅ Connexion à la base de données réussie!<br>";
    
    // Tester la lecture des articles
    $stmt = $conn->query("SELECT * FROM articles");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📊 Nombre d'articles trouvés : " . count($articles) . "<br><br>";
    
    foreach($articles as $article) {
        echo "📝 " . $article['title'] . " par " . $article['author'] . "<br>";
    }
    
} else {
    echo "❌ Erreur de connexion à la base de données";
}
?>