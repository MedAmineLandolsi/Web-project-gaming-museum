<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

$database = new Database();
$db = $database->connect();

echo "<h1>Debug joinByUser</h1>";

// Simuler la session
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'jean.dupont@email.com';

// Vérifier les tables
echo "<h2>1. Vérification des tables</h2>";

// Vérifier la table users
$query = "SELECT * FROM users WHERE id = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h3>User ID 1 :</h3>";
echo "<pre>" . print_r($user, true) . "</pre>";

// Vérifier la table membre
$query = "SELECT * FROM membre WHERE email = :email";
$stmt = $db->prepare($query);
$stmt->bindParam(':email', $user['email']);
$stmt->execute();
$membre = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h3>Membre correspondant (par email) :</h3>";
echo "<pre>" . print_r($membre, true) . "</pre>";

// Vérifier la table membre_communaute
$query = "SELECT * FROM membre_communaute WHERE membre_id = :membre_id AND communaute_id = 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':membre_id', $membre['id']);
$stmt->execute();
$already_joined = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h3>Déjà membre de la communauté 1 ? :</h3>";
if ($already_joined) {
    echo "<p style='color: orange;'>OUI - membre_id: " . $already_joined['membre_id'] . "</p>";
} else {
    echo "<p style='color: green;'>NON - peut rejoindre</p>";
}

// Tester la requête de joinByUser
echo "<h2>2. Test de la requête joinByUser</h2>";

try {
    // Étape 1: Récupérer le membre_id correspondant au user_id
    $query_membre = "SELECT m.id 
                    FROM membre m
                    INNER JOIN users u ON m.email = u.email
                    WHERE u.id = :user_id
                    LIMIT 1";
    
    $stmt_membre = $db->prepare($query_membre);
    $stmt_membre->bindParam(':user_id', $_SESSION['user_id']);
    $stmt_membre->execute();
    
    $membre_result = $stmt_membre->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>Résultat de la requête membre :</h3>";
    echo "<pre>" . print_r($membre_result, true) . "</pre>";
    
    if ($membre_result) {
        echo "<p style='color: green;'>✓ Membre trouvé: ID " . $membre_result['id'] . "</p>";
        
        // Étape 2: Vérifier si déjà membre
        $check_query = "SELECT id FROM membre_communaute 
                       WHERE membre_id = :membre_id AND communaute_id = :communaute_id";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':membre_id', $membre_result['id']);
        $check_stmt->bindParam(':communaute_id', 1);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            echo "<p style='color: orange;'>⚠ Déjà membre de la communauté 1</p>";
        } else {
            echo "<p style='color: green;'>✓ Pas encore membre - peut rejoindre</p>";
            
            // Étape 3: Tenter le join
            $join_query = "INSERT INTO membre_communaute (membre_id, communaute_id, date_join) 
                          VALUES (:membre_id, :communaute_id, NOW())";
            
            $join_stmt = $db->prepare($join_query);
            $join_stmt->bindParam(':membre_id', $membre_result['id']);
            $join_stmt->bindParam(':communaute_id', 1);
            
            if ($join_stmt->execute()) {
                echo "<p style='color: green;'>✅ JOIN réussi !</p>";
                
                // Afficher le résultat
                $show_query = "SELECT * FROM membre_communaute WHERE membre_id = :membre_id";
                $show_stmt = $db->prepare($show_query);
                $show_stmt->bindParam(':membre_id', $membre_result['id']);
                $show_stmt->execute();
                $results = $show_stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<h3>Communautés rejointes :</h3>";
                echo "<pre>" . print_r($results, true) . "</pre>";
            } else {
                echo "<p style='color: red;'>❌ Erreur lors du INSERT</p>";
                echo "<p>Erreur: " . print_r($join_stmt->errorInfo(), true) . "</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Aucun membre trouvé pour ce user</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Exception PDO: " . $e->getMessage() . "</p>";
}

echo "<h2>3. Solution rapide</h2>";
echo "<p><a href='/projet/fix_join.php' class='btn btn-primary'>Corriger automatiquement</a></p>";
?>