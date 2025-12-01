<?php
session_start();
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    
    // Vérifier l'utilisateur
    $query = "SELECT * FROM utilisateurs WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier le mot de passe
        if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_email'] = $user['email'];
            
            header('Location: mes-articles.php');
            exit();
        } else {
            $error_message = "❌ Mot de passe incorrect.";
        }
    } else {
        $error_message = "❌ Aucun compte trouvé avec cet email.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - BLOG GAMING</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            overflow-x: hidden;
        }

        .header {
            background: linear-gradient(180deg, var(--darker-bg) 0%, rgba(10, 10, 10, 0.95) 100%);
            border-bottom: 2px solid var(--primary-green);
            padding: 1.2rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 30px rgba(0, 255, 65, 0.2);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-green);
            text-shadow: 
                0 0 10px var(--primary-green),
                0 0 20px var(--primary-green),
                0 0 30px var(--primary-green);
            letter-spacing: 2px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: var(--text-white);
            text-decoration: none;
            font-size: 0.7rem;
            transition: all 0.3s;
            padding: 0.5rem 0;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-purple));
            transition: width 0.3s;
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .admin-btn {
            background: linear-gradient(135deg, var(--secondary-purple), var(--accent-pink));
            color: var(--text-white) !important;
            padding: 0.9rem 1.8rem;
            border-radius: 0;
            font-weight: bold;
            border: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.4);
            transition: all 0.3s;
        }

        .admin-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 0 30px rgba(189, 0, 255, 0.8);
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--accent-pink), #ff1a75);
            color: var(--text-white) !important;
            padding: 0.9rem 1.8rem;
            border-radius: 0;
            font-weight: bold;
            border: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.4);
            transition: all 0.3s;
        }

        .logout-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 0 30px rgba(255, 0, 110, 0.8);
        }

        .connexion-section {
            margin-top: 120px;
            padding: 3rem 0;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 0;
            border: 2px solid var(--border-color);
            backdrop-filter: blur(20px);
        }

        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            color: var(--primary-green);
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 1.25rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 0;
            color: var(--text-white);
            font-size: 1rem;
            font-family: 'VT323', monospace;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(0, 255, 65, 0.1);
            background: rgba(255, 255, 255, 0.08);
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            padding: 1.25rem 2.5rem;
            border: none;
            border-radius: 0;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 24px rgba(0, 255, 65, 0.3);
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #00cc33, var(--primary-green));
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 255, 65, 0.4);
        }

        .inscription-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
        }

        .inscription-link a {
            color: var(--primary-green);
            text-decoration: none;
        }

        .inscription-link a:hover {
            color: var(--secondary-purple);
        }

        .error-message {
            background: rgba(255, 0, 110, 0.1);
            border: 2px solid rgba(255, 0, 110, 0.3);
            color: var(--accent-pink);
            padding: 1.5rem 2rem;
            border-radius: 0;
            margin-bottom: 2.5rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            font-family: 'Press Start 2P', cursive;
        }

        .validation-error {
            color: var(--accent-pink);
            font-size: 0.7rem;
            margin-top: 0.5rem;
            display: block;
            font-family: 'VT323', monospace;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 2rem;
                margin: 1rem;
            }
            
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: var(--darker-bg);
                padding: 1.5rem;
                border-top: 2px solid var(--primary-green);
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .connexion-section {
                margin-top: 100px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">🎮 LV BLOG GAMING</div>
                <ul class="nav-links">
                    <li><a href="index.php">ACCUEIL</a></li>
                    <li><a href="blog.php">ARTICLES</a></li>
                    <li><a href="about.php">À PROPOS</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="submit-article.php">✍️ ÉCRIRE UN ARTICLE</a></li>
                        <li><a href="mes-articles.php">MES ARTICLES</a></li>
                        <li><a href="deconnexion.php" class="logout-btn">DÉCONNEXION (<?php echo $_SESSION['user_prenom']; ?>)</a></li>
                    <?php else: ?>
                        <li><a href="submit-article.php">✍️ ÉCRIRE UN ARTICLE</a></li>
                        <li><a href="connexion.php" class="active">SE CONNECTER</a></li>
                        <li><a href="inscription.php">S'INSCRIRE</a></li>
                        <li><a href="../Backoffice/login.php" class="admin-btn">ESPACE ADMIN</a></li>
                    <?php endif; ?>
                </ul>
                <div class="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <section class="connexion-section">
        <div class="container">
            <div class="form-container">
                <h1 style="text-align: center; margin-bottom: 1rem; color: var(--primary-green); font-size: 1.8rem; font-weight: 800;">
                    🔐 CONNEXION
                </h1>
                <p style="text-align: center; color: var(--secondary-purple); margin-bottom: 3rem; font-size: 1.125rem; font-family: 'VT323', monospace;">
                    Accédez à votre espace personnel
                </p>

                <?php if (isset($error_message)): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="loginForm">
                    <div class="form-group">
                        <label class="form-label" for="email">EMAIL *</label>
                        <input type="email" class="form-control" name="email" id="email"
                               placeholder="votre@email.com">
                        <span class="validation-error" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="mot_de_passe">MOT DE PASSE *</label>
                        <input type="password" class="form-control" name="mot_de_passe" id="mot_de_passe"
                               placeholder="Votre mot de passe">
                        <span class="validation-error" id="passwordError"></span>
                    </div>

                    <button type="submit" class="submit-btn">
                        🚀 SE CONNECTER
                    </button>
                </form>

                <div class="inscription-link">
                    Pas de compte ? <a href="inscription.php">Inscrivez-vous ici</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.querySelector('.mobile-menu');
            const navLinks = document.querySelector('.nav-links');
            const loginForm = document.getElementById('loginForm');
            
            if (mobileMenu && navLinks) {
                mobileMenu.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                });
            }

            // Validation JavaScript
            if (loginForm) {
                loginForm.addEventListener('submit', function(event) {
                    let isValid = true;
                    
                    // Réinitialiser les messages d'erreur
                    document.getElementById('emailError').textContent = '';
                    document.getElementById('passwordError').textContent = '';
                    
                    // Validation email
                    const email = document.getElementById('email').value;
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    
                    if (!email) {
                        document.getElementById('emailError').textContent = 'L\'email est obligatoire';
                        isValid = false;
                    } else if (!emailRegex.test(email)) {
                        document.getElementById('emailError').textContent = 'Format d\'email invalide';
                        isValid = false;
                    }
                    
                    // Validation mot de passe
                    const password = document.getElementById('mot_de_passe').value;
                    
                    if (!password) {
                        document.getElementById('passwordError').textContent = 'Le mot de passe est obligatoire';
                        isValid = false;
                    } else if (password.length < 6) {
                        document.getElementById('passwordError').textContent = 'Le mot de passe doit contenir au moins 6 caractères';
                        isValid = false;
                    }
                    
                    if (!isValid) {
                        event.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>