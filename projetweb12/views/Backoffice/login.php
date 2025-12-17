<?php
session_start();
include_once '../../config/database.php';

// Si déjà connecté, rediriger vers l'admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: blog-admin.php');
    exit();
}

// Traitement du formulaire de connexion
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        // Connexion à la base de données
        $database = new Database();
        $db = $database->getConnection();
        
        // Rechercher l'utilisateur par username ou email
        $query = "SELECT * FROM users 
                  WHERE (username = :identifiant OR email = :identifiant) 
                  AND status = 'active' 
                  LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':identifiant', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier le mot de passe
            if (password_verify($password, $user['password'])) {
                // Vérifier si l'utilisateur est admin
                if ($user['role'] === 'admin') {
                    // Connexion réussie
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_email'] = $user['email'];
                    $_SESSION['admin_first_name'] = $user['first_name'];
                    $_SESSION['admin_last_name'] = $user['last_name'];
                    $_SESSION['admin_role'] = $user['role'];
                    
                    // Mettre à jour le timestamp de dernière connexion (optionnel)
                    $update_query = "UPDATE users SET updated_at = NOW() WHERE id = :id";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->bindParam(':id', $user['id']);
                    $update_stmt->execute();
                    
                    // Redirection vers le tableau de bord
                    $_SESSION['success_message'] = '✅ CONNEXION RÉUSSIE !';
                    header('Location: blog-admin.php');
                    exit();
                } else {
                    $error_message = '❌ ACCÈS REFUSÉ : VOUS N\'AVEZ PAS LES DROITS ADMIN.';
                }
            } else {
                $error_message = '❌ IDENTIFIANTS INCORRECTS.';
            }
        } else {
            $error_message = '❌ UTILISATEUR NON TROUVÉ.';
        }
        
    } catch (PDOException $e) {
        $error_message = '❌ ERREUR DE CONNEXION À LA BASE DE DONNÉES.';
        // Pour le débogage, vous pouvez décommenter la ligne suivante :
        // $error_message = '❌ ERREUR : ' . $e->getMessage();
    }
    
    // Si on arrive ici avec des identifiants par défaut (pour la transition)
    // Cette partie peut être supprimée une fois que tous les admins sont dans la base
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = 'admin';
        $_SESSION['admin_role'] = 'admin';
        $_SESSION['warning'] = 'ATTENTION : Vous utilisez des identifiants par défaut.';
        header('Location: blog-admin.php');
        exit();
    }
}

// Récupérer les identifiants par défaut d'admin depuis la base
$default_admin_info = '';
try {
    $database = new Database();
    $db = $database->getConnection();
    $query = "SELECT username, email, role FROM users WHERE role = 'admin' AND status = 'active' LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($admins)) {
        $default_admin_info = "<strong>Admins disponibles :</strong><br>";
        foreach ($admins as $admin) {
            $default_admin_info .= "• " . htmlspecialchars($admin['username']) . 
                                 " (" . htmlspecialchars($admin['email']) . ")<br>";
        }
    }
} catch (Exception $e) {
    // Ne rien afficher en cas d'erreur
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONNEXION ADMIN</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-green: #00FF41;
            --secondary-purple: #BD00FF;
            --accent-pink: #FF006E;
            --warning-orange: #FF9500;
            --dark-bg: #0a0a0a;
            --darker-bg: #050505;
            --card-bg: #1a1a1a;
            --text-white: #ffffff;
            --text-gray: #888888;
            --text-light-gray: #aaaaaa;
            --border-color: #333333;
            --success-green: #00FF41;
            --danger-red: #FF0055;
        }

        body {
            font-family: 'Press Start 2P', cursive;
            background-color: var(--dark-bg);
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 65, 0.02) 2px,
                    rgba(0, 255, 65, 0.02) 4px
                );
            color: var(--text-white);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-container {
            width: 100%;
            max-width: 500px;
            padding: 2rem;
        }

        .login-box {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            padding: 3rem 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 50px rgba(0, 255, 65, 0.3);
        }

        .login-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-title {
            font-size: 1.5rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--text-gray);
            font-size: 0.6rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-green);
            font-size: 0.6rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            font-size: 1rem;
            font-family: 'VT323', monospace;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary-green);
            color: var(--dark-bg);
            border: 2px solid var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            text-transform: uppercase;
        }

        .btn:hover {
            background: transparent;
            color: var(--primary-green);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
        }

        .login-help {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-gray);
            font-size: 0.5rem;
            line-height: 1.5;
        }
        
        .admin-info {
            background: rgba(0, 255, 65, 0.1);
            border: 1px solid var(--primary-green);
            padding: 0.8rem;
            margin-top: 1rem;
            font-size: 0.5rem;
            text-align: left;
            font-family: 'VT323', monospace;
        }

        .error-message {
            background: rgba(255, 0, 85, 0.1);
            border: 2px solid var(--danger-red);
            color: var(--danger-red);
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.6rem;
        }

        .cyber-border {
            position: absolute;
            width: 20px;
            height: 20px;
        }

        .cyber-border-tl {
            top: 0;
            left: 0;
            border-top: 2px solid var(--primary-green);
            border-left: 2px solid var(--primary-green);
        }

        .cyber-border-tr {
            top: 0;
            right: 0;
            border-top: 2px solid var(--primary-green);
            border-right: 2px solid var(--primary-green);
        }

        .cyber-border-bl {
            bottom: 0;
            left: 0;
            border-bottom: 2px solid var(--primary-green);
            border-left: 2px solid var(--primary-green);
        }

        .cyber-border-br {
            bottom: 0;
            right: 0;
            border-bottom: 2px solid var(--primary-green);
            border-right: 2px solid var(--primary-green);
        }
        
        .warning-box {
            background: rgba(255, 149, 0, 0.1);
            border: 1px solid var(--warning-orange);
            color: var(--warning-orange);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.6rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-box {
                padding: 2rem 1rem;
            }
            
            .login-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="cyber-border cyber-border-tl"></div>
            <div class="cyber-border cyber-border-tr"></div>
            <div class="cyber-border cyber-border-bl"></div>
            <div class="cyber-border cyber-border-br"></div>

            <div class="login-header">
                <h1 class="login-title">CONNEXION ADMIN</h1>
                <p class="login-subtitle">PANEL GAMING BLOG</p>
            </div>

            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['expired'])): ?>
                <div class="warning-box">
                    ⚠️ VOTRE SESSION A EXPIRÉ. VEUILLEZ VOUS RECONNECTER.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['logout'])): ?>
                <div class="warning-box">
                    ✅ VOUS AVEZ ÉTÉ DÉCONNECTÉ AVEC SUCCÈS.
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">NOM D'UTILISATEUR OU EMAIL</label>
                    <input type="text" name="username" class="form-control" 
                           placeholder="Entrez votre username ou email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">MOT DE PASSE</label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn">SE CONNECTER</button>
            </form>

            <div class="login-help">
                <p>Utilisez vos identifiants de la table "users" avec rôle "admin".</p>
                
                <?php if (!empty($default_admin_info)): ?>
                    <div class="admin-info">
                        <?php echo $default_admin_info; ?>
                    </div>
                <?php else: ?>
                    <p>Aucun administrateur trouvé dans la base.</p>
                <?php endif; ?>
                
                <p style="margin-top: 1rem;">
                    <a href="../Frontoffice/index.php" style="color: var(--text-gray); text-decoration: none;">
                        ← Retour au site public
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const usernameInput = document.querySelector('input[name="username"]');
            const passwordInput = document.querySelector('input[name="password"]');
            
            form.addEventListener('submit', function(e) {
                let valid = true;
                
                if (!usernameInput.value.trim()) {
                    showError(usernameInput, 'Le nom d\'utilisateur est requis');
                    valid = false;
                } else {
                    clearError(usernameInput);
                }
                
                if (!passwordInput.value.trim()) {
                    showError(passwordInput, 'Le mot de passe est requis');
                    valid = false;
                } else if (passwordInput.value.length < 6) {
                    showError(passwordInput, 'Le mot de passe doit contenir au moins 6 caractères');
                    valid = false;
                } else {
                    clearError(passwordInput);
                }
                
                if (!valid) {
                    e.preventDefault();
                }
            });
            
            function showError(input, message) {
                input.style.borderColor = 'var(--danger-red)';
                input.style.boxShadow = '0 0 10px rgba(255, 0, 85, 0.3)';
                
                // Créer ou mettre à jour le message d'erreur
                let errorElement = input.nextElementSibling;
                if (!errorElement || !errorElement.classList.contains('error-message')) {
                    errorElement = document.createElement('div');
                    errorElement.className = 'error-message';
                    errorElement.style.fontSize = '0.5rem';
                    errorElement.style.marginTop = '0.5rem';
                    input.parentNode.appendChild(errorElement);
                }
                errorElement.textContent = message;
            }
            
            function clearError(input) {
                input.style.borderColor = 'var(--primary-green)';
                input.style.boxShadow = '';
                
                // Supprimer le message d'erreur
                let errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.remove();
                }
            }
            
            // Auto-focus sur le champ username
            usernameInput.focus();
        });
    </script>
</body>
</html>