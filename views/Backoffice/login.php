<?php
session_start();

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
    
    // Identifiants simples
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: blog-admin.php');
        exit();
    } else {
        $error_message = '❌ IDENTIFIANTS INCORRECTS. UTILISEZ ADMIN/ADMIN123';
    }
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

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">NOM D'UTILISATEUR</label>
                    <input type="text" name="username" class="form-control" placeholder="ADMIN" required value="admin">
                </div>
                <div class="form-group">
                    <label class="form-label">MOT DE PASSE</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required value="admin123">
                </div>
                <button type="submit" class="btn">SE CONNECTER</button>
            </form>

            <div class="login-help">
                IDENTIFIANTS : ADMIN / ADMIN123
            </div>
        </div>
    </div>
</body>
</html>