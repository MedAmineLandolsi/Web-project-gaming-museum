<?php
session_start();
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    
    // Vérifier l'utilisateur dans la table "users"
    $query = "SELECT * FROM users WHERE email = ? AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier le mot de passe
        if (password_verify($mot_de_passe, $user['password'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_first_name'] = $user['first_name'];
            $_SESSION['user_last_name'] = $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_status'] = $user['status'];
            
            // Rediriger selon le rôle
            if ($user['role'] === 'admin') {
                header('Location: ../Backoffice/dashboard.php');
                exit();
            } else {
                header('Location: mes-articles.php');
                exit();
            }
        } else {
            $error_message = "❌ Mot de passe incorrect.";
        }
    } else {
        $error_message = "❌ Aucun compte trouvé avec cet email.";
    }
}

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: ../Backoffice/dashboard.php');
    } else {
        header('Location: mes-articles.php');
    }
    exit();
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
            gap: 2rem;
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
            white-space: nowrap;
        }

        /* Navigation Déroulante - TOUJOURS VISIBLE */
        .nav-dropdown-container {
            flex: 1;
            max-width: 300px;
            position: relative;
        }

        .nav-dropdown {
            width: 100%;
            padding: 12px 16px;
            font-size: 0.8rem;
            font-family: 'Press Start 2P', cursive;
            background: var(--card-bg);
            color: var(--primary-green);
            border: 2px solid var(--primary-green);
            border-radius: 0;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2300FF41' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 14px;
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.3);
            transition: all 0.3s;
        }

        .nav-dropdown option {
            background: var(--darker-bg);
            color: var(--text-white);
            padding: 12px;
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
        }

        .nav-dropdown:focus {
            outline: none;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
            border-color: var(--secondary-purple);
        }

        .nav-dropdown:hover {
            border-color: var(--secondary-purple);
            transform: translateY(-1px);
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

        .success-message {
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid rgba(0, 255, 65, 0.3);
            color: var(--primary-green);
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
        
        .forgot-password {
            text-align: right;
            margin-top: 0.5rem;
        }
        
        .forgot-password a {
            color: var(--secondary-purple);
            font-size: 0.8rem;
            text-decoration: none;
            font-family: 'VT323', monospace;
        }
        
        .forgot-password a:hover {
            color: var(--primary-green);
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 2rem;
                margin: 1rem;
            }
            
            .nav {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .nav-dropdown-container {
                max-width: 100%;
                width: 100%;
            }
            
            .nav-dropdown {
                width: 100%;
                font-size: 0.7rem;
                padding: 10px 12px;
            }
            
            .connexion-section {
                margin-top: 100px;
            }
            
            .logo {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">🎮 LV BLOG GAMING</div>
                
                <!-- Navigation Déroulante (TOUJOURS VISIBLE) -->
                <div class="nav-dropdown-container">
                    <select class="nav-dropdown" onchange="if(this.value) window.location.href=this.value">
                        <option value="">-- MENU PRINCIPAL --</option>
                        <option value="index.php">🏠 ACCUEIL</option>
                        <option value="blog.php">📰 ARTICLES</option>
                        <option value="about.php">ℹ️ À PROPOS</option>
                        <option value="submit-article.php">✍️ ÉCRIRE UN ARTICLE</option>
                        <option value="connexion.php" selected>🔐 SE CONNECTER</option>
                        <option value="inscription.php">📝 S'INSCRIRE</option>
                        <option value="http://localhost/projet-web/gaming_museum/view/frontoffice/index.php">🎮 MUSÉE GAMING</option>
                        <option value="../Backoffice/login.php">👑 ESPACE ADMIN</option>
                    </select>
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

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="success-message">
                        <?php echo $_SESSION['success_message']; ?>
                        <?php unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="loginForm">
                    <div class="form-group">
                        <label class="form-label" for="email">EMAIL *</label>
                        <input type="email" class="form-control" name="email" id="email"
                               placeholder="votre@email.com" required>
                        <span class="validation-error" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="mot_de_passe">MOT DE PASSE *</label>
                        <input type="password" class="form-control" name="mot_de_passe" id="mot_de_passe"
                               placeholder="Votre mot de passe" required>
                        <span class="validation-error" id="passwordError"></span>
                        <div class="forgot-password">
                            <a href="mot-de-passe-oublie.php">Mot de passe oublié ?</a>
                        </div>
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
            const loginForm = document.getElementById('loginForm');
            
            // Définir la valeur sélectionnée dans la liste déroulante
            const navDropdown = document.querySelector('.nav-dropdown');
            if (navDropdown) {
                const currentPage = window.location.pathname.split('/').pop();
                
                // S'assurer que "SE CONNECTER" est sélectionné sur cette page
                if (currentPage === 'connexion.php') {
                    navDropdown.value = 'connexion.php';
                }
                
                // Si la page actuelle n'est pas trouvée dans les options, sélectionner la première option
                if (!navDropdown.value) {
                    navDropdown.selectedIndex = 0;
                }
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

                // Validation en temps réel pour améliorer l'UX
                const fields = ['email', 'mot_de_passe'];
                fields.forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.addEventListener('blur', function() {
                            validateField(field, this.value);
                        });
                    }
                });

                function validateField(fieldName, value) {
                    const errorElement = document.getElementById(fieldName + 'Error');
                    
                    switch(fieldName) {
                        case 'email':
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (!value) {
                                errorElement.textContent = 'L\'email est obligatoire';
                            } else if (!emailRegex.test(value)) {
                                errorElement.textContent = 'Format d\'email invalide';
                            } else {
                                errorElement.textContent = '';
                            }
                            break;
                            
                        case 'mot_de_passe':
                            if (!value) {
                                errorElement.textContent = 'Le mot de passe est obligatoire';
                            } else if (value.length < 6) {
                                errorElement.textContent = 'Le mot de passe doit contenir au moins 6 caractères';
                            } else {
                                errorElement.textContent = '';
                            }
                            break;
                    }
                }
            }
        });
    </script>
</body>
</html>