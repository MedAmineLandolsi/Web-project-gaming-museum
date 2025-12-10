<?php
session_start();

// Inclure la configuration de la base de données
include_once 'config/database.php';

// Redirection si déjà connecté
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $database = new Database();
    $db = $database->getConnection();
    
    // Récupération des données
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    
    // Tableau pour les erreurs
    $errors = [];
    
    // Validation des champs
    if (empty($username)) $errors['username'] = 'Nom d\'utilisateur requis';
    if (empty($email)) $errors['email'] = 'Email requis';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email invalide';
    if (empty($password)) $errors['password'] = 'Mot de passe requis';
    if (strlen($password) < 6) $errors['password'] = 'Le mot de passe doit contenir au moins 6 caractères';
    if ($password !== $confirm_password) $errors['confirm_password'] = 'Les mots de passe ne correspondent pas';
    if (empty($first_name)) $errors['first_name'] = 'Prénom requis';
    if (empty($last_name)) $errors['last_name'] = 'Nom requis';
    
    // Vérifier si l'email ou le username existe déjà
    if (empty($errors)) {
        try {
            // Vérifier l'email
            $query = "SELECT id FROM users WHERE email = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $errors['email'] = 'Cet email est déjà utilisé';
            }
            
            // Vérifier le username
            $query = "SELECT id FROM users WHERE username = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$username]);
            if ($stmt->rowCount() > 0) {
                $errors['username'] = 'Ce nom d\'utilisateur est déjà pris';
            }
        } catch (PDOException $e) {
            $errors['general'] = 'Erreur de base de données: ' . $e->getMessage();
        }
    }
    
    // Si pas d'erreurs, créer l'utilisateur
    if (empty($errors)) {
        try {
            // Hasher le mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insérer l'utilisateur
            $query = "INSERT INTO users (username, email, password, first_name, last_name, phone_number, date_of_birth, role, status, created_at, updated_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, 'user', 'active', NOW(), NOW())";
            
            $stmt = $db->prepare($query);
            $stmt->execute([
                $username,
                $email,
                $hashed_password,
                $first_name,
                $last_name,
                $phone_number,
                $date_of_birth
            ]);
            
            // Récupérer l'ID du nouvel utilisateur
            $user_id = $db->lastInsertId();
            
            // Connecter automatiquement l'utilisateur
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'user';
            $_SESSION['logged_in'] = true;
            
            // Réponse JSON
            echo json_encode([
                'success' => true,
                'message' => 'Inscription réussie ! Redirection...',
                'role' => 'user'
            ]);
            exit();
            
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()
            ]);
            exit();
        }
    } else {
        // Retourner les erreurs
        echo json_encode([
            'success' => false,
            'message' => 'Veuillez corriger les erreurs',
            'errors' => $errors
        ]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - RetroGame Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <style>
        /* Reprendre exactement le même CSS que login.php */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-green: #00FF41;
            --secondary-purple: #BD00FF;
            --accent-pink: #FF006E;
            --dark-bg: #0a0a0a;
            --darker-bg: #050505;
            --card-bg: #1a1a1a;
            --text-white: #ffffff;
            --text-gray: #888888;
            --text-light-gray: #aaaaaa;
            --border-color: #333333;
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
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background-color: var(--primary-green);
            opacity: 0.3;
            animation: float 20s infinite;
            box-shadow: 0 0 10px var(--primary-green);
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 15s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; animation-duration: 18s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; animation-duration: 20s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 1s; animation-duration: 17s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 3s; animation-duration: 16s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; animation-duration: 19s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 2.5s; animation-duration: 21s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 4.5s; animation-duration: 15.5s; }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 0.3;
            }
            90% {
                opacity: 0.3;
            }
            50% {
                transform: translateY(-100vh) translateX(50px);
            }
        }

        .grid-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 255, 65, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 65, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            opacity: 0.3;
            z-index: 0;
        }

        @keyframes gridMove {
            0% { transform: perspective(500px) rotateX(60deg) translateY(0); }
            100% { transform: perspective(500px) rotateX(60deg) translateY(50px); }
        }

        .scanline {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                0deg,
                rgba(0, 0, 0, 0.15),
                rgba(0, 0, 0, 0.15) 1px,
                transparent 1px,
                transparent 2px
            );
            pointer-events: none;
            animation: scan 8s linear infinite;
            z-index: 1;
        }

        @keyframes scan {
            0% { transform: translateY(0); }
            100% { transform: translateY(10px); }
        }

        .section {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
            z-index: 2;
        }

        .container {
            width: 100%;
            max-width: 1200px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .page-header-title {
            font-size: 1.2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
        }

        .page-header-title span,
        .page-header-title a {
            padding: 0.5rem 1.5rem;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--text-gray);
            opacity: 0.5;
            cursor: pointer;
        }

        .page-header-title span.active {
            opacity: 1;
            color: var(--secondary-purple);
            text-shadow: 0 0 20px var(--secondary-purple);
        }

        .page-header-title #loginToggle:hover {
            opacity: 0.8;
            color: var(--primary-green);
        }

        .page-header-title .separator {
            opacity: 0.3;
            color: var(--primary-green);
            cursor: default;
        }

        .toggle-switch {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
        }

        [type="checkbox"]:checked,
        [type="checkbox"]:not(:checked) {
            position: absolute;
            left: -9999px;
        }

        .checkbox:checked + label,
        .checkbox:not(:checked) + label {
            position: relative;
            display: block;
            text-align: center;
            width: 100px;
            height: 24px;
            border-radius: 12px;
            padding: 0;
            margin: 0 auto;
            cursor: pointer;
            background-color: rgba(189, 0, 255, 0.2);
            border: 2px solid var(--secondary-purple);
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.3);
            transition: all 0.3s;
        }

        .checkbox:checked + label:before,
        .checkbox:not(:checked) + label:before {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: var(--secondary-purple);
            border: 3px solid var(--darker-bg);
            font-family: 'Press Start 2P';
            content: '<';
            font-size: 16px;
            color: var(--darker-bg);
            z-index: 20;
            top: -12px;
            left: calc(100% - 36px);
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 0 25px rgba(189, 0, 255, 0.6);
        }

        .card-wrap {
            position: relative;
            width: 700px;
            max-width: 100%;
            margin: 0 auto;
        }

        .card-single {
            width: 100%;
            background: linear-gradient(135deg, rgba(10, 10, 10, 0.95), rgba(26, 26, 26, 0.95));
            backdrop-filter: blur(20px);
            border: 3px solid var(--secondary-purple);
            box-shadow: 
                0 20px 60px rgba(189, 0, 255, 0.4), 
                inset 0 0 100px rgba(189, 0, 255, 0.05),
                0 0 40px rgba(255, 0, 110, 0.2);
            overflow: hidden;
            position: relative;
        }

        .card-single::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(189, 0, 255, 0.03) 2px,
                    rgba(189, 0, 255, 0.03) 4px
                );
            pointer-events: none;
        }

        .card-single::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, 
                var(--secondary-purple), 
                var(--primary-green), 
                var(--accent-pink), 
                var(--secondary-purple)
            );
            background-size: 400%;
            border-radius: inherit;
            z-index: -1;
            opacity: 0.5;
            filter: blur(8px);
            animation: borderGlow 8s linear infinite;
        }

        @keyframes borderGlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .center-wrap {
            width: 100%;
            padding: 2.5rem 3.5rem 3rem 3.5rem;
            display: block;
        }

        .form-title {
            font-size: 1.8rem;
            color: var(--secondary-purple);
            text-shadow: 0 0 20px var(--secondary-purple);
            margin-bottom: 2rem;
            text-align: center;
            letter-spacing: 4px;
            position: relative;
            padding-right: 1rem;
            padding-bottom: 1.2rem;
            padding-top: 0.5rem;
        }

        .form-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 3px;
            background: linear-gradient(90deg, 
                transparent, 
                var(--secondary-purple), 
                transparent
            );
            box-shadow: 0 0 15px var(--secondary-purple);
        }

        .login-link {
            font-size: 0.6rem;
            color: var(--text-light-gray);
        }

        .login-link span {
            color: var(--primary-green);
            font-weight: bold;
            transition: all 0.3s;
        }

        .login-link:hover span {
            color: var(--accent-pink);
            text-shadow: 0 0 10px var(--accent-pink);
        }

        .form-group {
            position: relative;
            display: block;
            margin: 0 0 0.8rem 0;
            padding: 0;
        }

        .form-style {
            padding: 0.9rem 1.2rem;
            padding-left: 3.2rem;
            height: 46px;
            width: 100%;
            font-weight: 400;
            font-size: 0.65rem;
            font-family: 'Press Start 2P', cursive;
            letter-spacing: 1px;
            outline: none;
            color: var(--text-white);
            background-color: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(189, 0, 255, 0.3);
            transition: all 300ms ease;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        .form-style:focus,
        .form-style:active {
            border-color: var(--secondary-purple);
            box-shadow: 
                0 0 20px rgba(189, 0, 255, 0.4), 
                0 0 30px rgba(255, 0, 110, 0.2),
                inset 0 2px 8px rgba(0, 0, 0, 0.6);
            background-color: rgba(0, 0, 0, 0.6);
        }

        .form-style::placeholder {
            color: var(--text-gray);
            opacity: 0.5;
            font-size: 0.55rem;
        }

        .input-icon {
            position: absolute;
            top: 0;
            left: 1rem;
            height: 46px;
            font-size: 18px;
            line-height: 46px;
            text-align: left;
            color: var(--secondary-purple);
            transition: all 300ms ease;
            pointer-events: none;
        }

        .form-group:focus-within .input-icon {
            color: var(--secondary-purple);
            text-shadow: 0 0 10px var(--secondary-purple);
            filter: drop-shadow(0 0 5px var(--primary-green));
        }

        .btn {
            width: 100%;
            height: 48px;
            font-size: 0.7rem;
            font-weight: 600;
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
            transition: all 300ms ease;
            padding: 0 30px;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 2px solid var(--secondary-purple);
            background: transparent;
            color: var(--secondary-purple);
            cursor: pointer;
            box-shadow: 
                0 0 20px rgba(189, 0, 255, 0.3),
                inset 0 0 20px rgba(189, 0, 255, 0.05);
            margin-top: 1.2rem;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: radial-gradient(circle, var(--secondary-purple), var(--primary-green));
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn span {
            position: relative;
            z-index: 1;
        }

        .btn:active,
        .btn:focus {
            background: var(--secondary-purple);
            color: var(--darker-bg);
            box-shadow: 
                0 0 30px rgba(189, 0, 255, 0.6),
                0 0 50px rgba(255, 0, 110, 0.3);
            transform: translateY(-2px);
        }

        .btn:hover {
            background: var(--secondary-purple);
            color: var(--darker-bg);
            box-shadow: 
                0 0 35px rgba(189, 0, 255, 0.7),
                0 0 50px rgba(0, 255, 65, 0.3);
            transform: translateY(-3px);
        }

        .link {
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.5rem;
            transition: all 0.3s;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .link:hover {
            color: var(--secondary-purple);
            text-shadow: 0 0 10px var(--secondary-purple);
        }

        .text-center {
            text-align: center;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-2 {
            margin-top: 0.8rem;
        }

        .pb-3 {
            padding-bottom: 1rem;
        }

        .error-message {
            display: none;
            color: #FF0055;
            font-size: 0.45rem;
            margin-top: 0.3rem;
            font-family: 'VT323', monospace;
            font-size: 0.8rem;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .col-6 {
            flex: 0 0 calc(50% - 0.5rem);
        }

        @media (max-width: 768px) {
            .col-6 {
                flex: 0 0 100%;
            }
            
            .card-wrap {
                width: 100%;
            }

            .center-wrap {
                padding: 2.5rem 2rem;
            }

            .form-title {
                font-size: 1.2rem;
                margin-bottom: 1.5rem;
            }

            .form-style {
                font-size: 0.6rem;
                height: 44px;
                padding-left: 3rem;
            }

            .input-icon {
                height: 44px;
                line-height: 44px;
                font-size: 16px;
            }

            .btn {
                font-size: 0.65rem;
                height: 46px;
            }
        }

        @media (max-width: 480px) {
            .center-wrap {
                padding: 2rem 1.5rem;
            }

            .form-title {
                font-size: 1rem;
                margin-bottom: 1.2rem;
            }

            .form-style {
                font-size: 0.55rem;
                height: 42px;
            }

            .input-icon {
                height: 42px;
                line-height: 42px;
            }

            .btn {
                font-size: 0.6rem;
                height: 44px;
            }

            .form-group {
                margin: 0 0 0.7rem 0;
            }

            .login-link {
                font-size: 0.55rem;
            }
        }
    </style>
</head>
<body>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="grid-background"></div>
    <div class="scanline"></div>

    <div class="section">
        <div class="container">
            <div class="page-header">
                <h2 class="page-header-title">
                    <a href="login.php" style="text-decoration: none;">CONNEXION</a>
                    <span class="separator">&lt;&gt;</span>
                    <span class="active">INSCRIPTION</span>
                </h2>
                <div class="toggle-switch">
                    <input class="checkbox" type="checkbox" id="reg-log" name="reg-log" checked/>
                    <label for="reg-log"></label>
                </div>
            </div>
            
            <div class="card-wrap">
                <div class="card-single">
                    <div class="center-wrap">
                        <div class="section text-center">
                            <h3 class="form-title">INSCRIPTION</h3>
                            <form id="registerForm" method="POST">
                                <input type="hidden" name="action" value="register">
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" name="username" class="form-style" placeholder="Nom d'utilisateur" id="registerUsername" autocomplete="off" required>
                                            <i class="input-icon uil uil-user"></i>
                                            <span class="error-message"></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-style" placeholder="Email" id="registerEmail" autocomplete="off" required>
                                            <i class="input-icon uil uil-envelope"></i>
                                            <span class="error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" name="first_name" class="form-style" placeholder="Prénom" id="registerFirstName" autocomplete="off" required>
                                            <i class="input-icon uil uil-user-circle"></i>
                                            <span class="error-message"></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" name="last_name" class="form-style" placeholder="Nom" id="registerLastName" autocomplete="off" required>
                                            <i class="input-icon uil uil-user-square"></i>
                                            <span class="error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-style" placeholder="Mot de passe" id="registerPassword" autocomplete="off" required>
                                            <i class="input-icon uil uil-lock"></i>
                                            <span class="error-message"></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="password" name="confirm_password" class="form-style" placeholder="Confirmer mot de passe" id="registerConfirmPassword" autocomplete="off" required>
                                            <i class="input-icon uil uil-lock-access"></i>
                                            <span class="error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="tel" name="phone_number" class="form-style" placeholder="Téléphone" id="registerPhone" autocomplete="off">
                                            <i class="input-icon uil uil-phone"></i>
                                            <span class="error-message"></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="date" name="date_of_birth" class="form-style" placeholder="Date de naissance" id="registerBirthDate" autocomplete="off">
                                            <i class="input-icon uil uil-calendar-alt"></i>
                                            <span class="error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn mt-4"><span>S'INSCRIRE</span></button>
                                <p class="mb-0 mt-3 text-center">
                                    <a href="login.php" class="link login-link">Déjà inscrit ? <span>SE CONNECTER &gt;</span></a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const checkbox = document.getElementById('reg-log');
        const loginToggle = document.getElementById('loginToggle');

        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                window.location.href = 'login.php';
            }
        });

        function showError(input, message) {
            const formGroup = input.closest('.form-group');
            let errorElement = formGroup.querySelector('.error-message');
            
            if (!errorElement) {
                errorElement = document.createElement('span');
                errorElement.className = 'error-message';
                formGroup.appendChild(errorElement);
            }
            
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            input.style.borderColor = '#FF0055';
        }

        function clearError(input) {
            const formGroup = input.closest('.form-group');
            const errorElement = formGroup.querySelector('.error-message');
            
            if (errorElement) {
                errorElement.style.display = 'none';
            }
            input.style.borderColor = 'rgba(189, 0, 255, 0.3)';
        }

        function validateRegisterForm() {
            const username = document.getElementById('registerUsername');
            const email = document.getElementById('registerEmail');
            const password = document.getElementById('registerPassword');
            const confirmPassword = document.getElementById('registerConfirmPassword');
            const firstName = document.getElementById('registerFirstName');
            const lastName = document.getElementById('registerLastName');
            
            let isValid = true;
            
            // Validation username
            if (!username.value.trim()) {
                showError(username, 'Nom d\'utilisateur requis');
                isValid = false;
            } else if (username.value.trim().length < 3) {
                showError(username, 'Minimum 3 caractères');
                isValid = false;
            } else {
                clearError(username);
            }
            
            // Validation email
            if (!email.value.trim()) {
                showError(email, 'Email requis');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                showError(email, 'Email invalide');
                isValid = false;
            } else {
                clearError(email);
            }
            
            // Validation password
            if (!password.value) {
                showError(password, 'Mot de passe requis');
                isValid = false;
            } else if (password.value.length < 6) {
                showError(password, 'Minimum 6 caractères');
                isValid = false;
            } else {
                clearError(password);
            }
            
            // Validation confirm password
            if (!confirmPassword.value) {
                showError(confirmPassword, 'Confirmation requise');
                isValid = false;
            } else if (password.value !== confirmPassword.value) {
                showError(confirmPassword, 'Les mots de passe ne correspondent pas');
                isValid = false;
            } else {
                clearError(confirmPassword);
            }
            
            // Validation first name
            if (!firstName.value.trim()) {
                showError(firstName, 'Prénom requis');
                isValid = false;
            } else {
                clearError(firstName);
            }
            
            // Validation last name
            if (!lastName.value.trim()) {
                showError(lastName, 'Nom requis');
                isValid = false;
            } else {
                clearError(lastName);
            }
            
            return isValid;
        }

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateRegisterForm()) {
                const formData = new FormData(this);
                
                // Afficher un indicateur de chargement
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.querySelector('span').textContent;
                submitBtn.querySelector('span').textContent = 'INSCRIPTION EN COURS...';
                submitBtn.disabled = true;
                
                fetch('register.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Réinitialiser les erreurs
                        document.querySelectorAll('.error-message').forEach(el => {
                            el.style.display = 'none';
                        });
                        
                        // Redirection après inscription réussie
                        if (data.role === 'admin') {
                            window.location.href = 'admin/index.php';
                        } else {
                            window.location.href = 'index.php';
                        }
                    } else {
                        // Afficher les erreurs spécifiques
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = document.getElementById(`register${field.charAt(0).toUpperCase() + field.slice(1)}`);
                                if (input) {
                                    showError(input, data.errors[field]);
                                }
                            });
                        } else {
                            // Erreur générale
                            alert(data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue. Veuillez réessayer.');
                })
                .finally(() => {
                    // Restaurer le bouton
                    submitBtn.querySelector('span').textContent = originalText;
                    submitBtn.disabled = false;
                });
            }
        });

        // Redirection vers login.php si on clique sur le bouton de connexion
        document.querySelector('a[href="login.php"]').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'login.php';
        });

        // Clear errors on input
        document.querySelectorAll('.form-style').forEach(input => {
            input.addEventListener('input', function() {
                clearError(this);
            });
        });
    </script>
</body>
</html>