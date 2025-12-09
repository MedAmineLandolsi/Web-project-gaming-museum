<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

$database = new Database();
$db = $database->connect();

echo "<h1>Correction du problème joinByUser</h1>";

// 1. Vérifier et créer la table membre_communaute si nécessaire
$sql = "CREATE TABLE IF NOT EXISTS membre_communaute (
    id INT AUTO_INCREMENT PRIMARY KEY,
    membre_id INT NOT NULL,
    communaute_id INT NOT NULL,
    date_join TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM('membre', 'moderateur', 'admin') DEFAULT 'membre',
    FOREIGN KEY (membre_id) REFERENCES membre(id) ON DELETE CASCADE,
    FOREIGN KEY (communaute_id) REFERENCES communaute(id) ON DELETE CASCADE,
    UNIQUE KEY unique_membre_communaute (membre_id, communaute_id)
)";

try {
    $db->exec($sql);
    echo "<p style='color: green;'>✅ Table membre_communaute vérifiée/créée</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erreur table: " . $e->getMessage() . "</p>";
}

// 2. Vérifier les contraintes de clé étrangère
echo "<h2>Vérification des relations</h2>";

// User 1 existe ?
$query = "SELECT * FROM users WHERE id = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "<p style='color: green;'>✅ User ID 1 trouvé: " . $user['email'] . "</p>";
    
    // Membre correspondant existe ?
    $query = "SELECT * FROM membre WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $user['email']);
    $stmt->execute();
    $membre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($membre) {
        echo "<p style='color: green;'>✅ Membre correspondant trouvé: ID " . $membre['id'] . "</p>";
        
        // Communauté 1 existe ?
        $query = "SELECT * FROM communaute WHERE id = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $communaute = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($communaute) {
            echo "<p style='color: green;'>✅ Communauté 1 trouvée: " . $communaute['nom'] . "</p>";
            
            // Déjà membre ?
            $query = "SELECT * FROM membre_communaute 
                     WHERE membre_id = :membre_id AND communaute_id = 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':membre_id', $membre['id']);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: orange;'>⚠ Déjà membre de la communauté 1</p>";
                
                // Afficher les membres actuels
                $query = "SELECT mc.*, m.nom, m.prenom 
                         FROM membre_communaute mc
                         INNER JOIN membre m ON mc.membre_id = m.id
                         WHERE mc.communaute_id = 1";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $membres = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<h3>Membres actuels de la communauté 1 :</h3>";
                echo "<ul>";
                foreach ($membres as $m) {
                    echo "<li>" . $m['prenom'] . " " . $m['nom'] . " (depuis " . $m['date_join'] . ")</li>";
                }
                echo "</ul>";
                
            } else {
                // Ajouter le membre
                $query = "INSERT INTO membre_communaute (membre_id, communaute_id, date_join) 
                         VALUES (:membre_id, 1, NOW())";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':membre_id', $membre['id']);
                
                if ($stmt->execute()) {
                    echo "<p style='color: green;'>✅ Ajouté à la communauté 1 avec succès !</p>";
                } else {
                    echo "<p style='color: red;'>❌ Erreur lors de l'insertion</p>";
                }
            }
            
        } else {
            echo "<p style='color: red;'>❌ Communauté 1 n'existe pas</p>";
            
            // Créer une communauté de test
            $query = "INSERT INTO communaute (nom, categorie, description, createur_id) 
                     VALUES ('Communauté Test', 'Test', 'Description test', :createur_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':createur_id', $membre['id']);
            
            if ($stmt->execute()) {
                $communaute_id = $db->lastInsertId();
                echo "<p style='color: green;'>✅ Communauté test créée (ID: $communaute_id)</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>❌ Aucun membre avec l'email: " . $user['email'] . "</p>";
        
        // Créer un membre correspondant
        $query = "INSERT INTO membre (nom, prenom, email, mot_de_passe, statut) 
                 VALUES (:nom, :prenom, :email, :password, 'actif')";
        $stmt = $db->prepare($query);
        
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $stmt->bindParam(':nom', $user['last_name']);
        $stmt->bindParam(':prenom', $user['first_name']);
        $stmt->bindParam(':email', $user['email']);
        $stmt->bindParam(':password', $password);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Membre créé pour l'user</p>";
        }
    }
    
} else {
    echo "<p style='color: red;'>❌ User ID 1 n'existe pas</p>";
}

echo "<h2>Test du join maintenant</h2>";
echo "<p><a href='/projet/join-by-user/1' class='btn btn-success'>Tester join-by-user/1</a></p>";
echo "<p><a href='/projet/communautes/1' class='btn btn-primary'>Voir la communauté 1</a></p>";
?>