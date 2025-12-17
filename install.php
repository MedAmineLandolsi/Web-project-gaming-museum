<?php
/**
 * Script d'installation automatique de la base de données
 * Accédez à ce fichier via : http://localhost/projet-web/ProjetWeb/install.php
 */

require_once 'config/database.php';

// Fonction pour exécuter le script SQL
function installDatabase() {
    try {
        // Connexion sans spécifier la base de données pour pouvoir la créer
        $conn = new PDO(
            "mysql:host=localhost;charset=utf8mb4",
            'root',
            ''
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Lire le fichier SQL
        $sql = file_get_contents(__DIR__ . '/database.sql');
        
        // Exécuter le script SQL
        $conn->exec($sql);
        
        return ['success' => true, 'message' => 'Base de données créée avec succès !'];
        
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
    }
}

// Si le formulaire est soumis
if (isset($_POST['install'])) {
    $result = installDatabase();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Gaming Support</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 3rem;
            border-radius: 15px;
            border: 2px solid #00ff88;
            max-width: 600px;
            width: 100%;
            backdrop-filter: blur(10px);
        }
        h1 {
            color: #00ff88;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .success {
            background: rgba(0, 255, 136, 0.2);
            border: 2px solid #00ff88;
            color: #00ff88;
        }
        .error {
            background: rgba(255, 71, 87, 0.2);
            border: 2px solid #ff4757;
            color: #ff4757;
        }
        .info {
            background: rgba(0, 204, 255, 0.2);
            border: 2px solid #00ccff;
            color: #00ccff;
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-radius: 10px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #00ff88, #00cc66);
            color: #1a1a2e;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 255, 136, 0.3);
        }
        .steps {
            margin-top: 2rem;
        }
        .steps h2 {
            color: #00ccff;
            margin-bottom: 1rem;
        }
        .steps ol {
            margin-left: 1.5rem;
            line-height: 2;
        }
        .steps li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Installation Gaming Support</h1>
        
        <?php if (isset($result)): ?>
            <div class="message <?= $result['success'] ? 'success' : 'error' ?>">
                <?= htmlspecialchars($result['message']) ?>
            </div>
            
            <?php if ($result['success']): ?>
                <a href="index.php" class="btn">Accéder à l'application</a>
            <?php endif; ?>
        <?php else: ?>
            <div class="info">
                <strong>⚠️ Attention :</strong> Ce script va créer la base de données et les tables nécessaires.
                Assurez-vous que MySQL est démarré dans XAMPP.
            </div>
            
            <form method="POST">
                <button type="submit" name="install" class="btn">Installer la base de données</button>
            </form>
            
            <div class="steps">
                <h2>Installation manuelle (alternative)</h2>
                <ol>
                    <li>Ouvrez phpMyAdmin : <a href="http://localhost/phpmyadmin" style="color: #00ff88;">http://localhost/phpmyadmin</a></li>
                    <li>Créez une nouvelle base de données nommée : <strong>gaming_support</strong></li>
                    <li>Importez le fichier <code>database.sql</code> dans cette base de données</li>
                    <li>Ou exécutez le contenu du fichier <code>database.sql</code> dans l'onglet SQL</li>
                </ol>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

