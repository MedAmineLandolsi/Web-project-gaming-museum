<?php
session_start();
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $first_name = htmlspecialchars($_POST['prenom']);
    $last_name = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $username = strtolower(str_replace(' ', '.', $first_name . '.' . $last_name));
    
    // Vérifier si l'email existe déjà dans la table "users"
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $error_message = "❌ Cet email est déjà utilisé.";
    } else {
        // Vérifier si le username existe déjà
        $username_check_query = "SELECT id FROM users WHERE username = ?";
        $username_stmt = $db->prepare($username_check_query);
        $username_stmt->bindParam(1, $username);
        $username_stmt->execute();
        
        // Si le username existe, ajouter un numéro
        $counter = 1;
        $original_username = $username;
        while ($username_stmt->rowCount() > 0) {
            $username = $original_username . $counter;
            $username_stmt->bindParam(1, $username);
            $username_stmt->execute();
            $counter++;
        }
        
        // Créer l'utilisateur dans la table "users"
        $query = "INSERT INTO users 
                  (username, email, password, first_name, last_name, phone_number, date_of_birth, profile_picture_url, role, status, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $db->prepare($query);
        
        // Valeurs par défaut pour les champs obligatoires
        $phone_number = '00000000'; // Valeur par défaut
        $date_of_birth = '2000-01-01'; // Valeur par défaut
        $profile_picture_url = ''; // Vide par défaut
        $role = 'user'; // Rôle par défaut
        $status = 'active'; // Statut par défaut
        
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $email);
        $stmt->bindParam(3, $password);
        $stmt->bindParam(4, $first_name);
        $stmt->bindParam(5, $last_name);
        $stmt->bindParam(6, $phone_number);
        $stmt->bindParam(7, $date_of_birth);
        $stmt->bindParam(8, $profile_picture_url);
        $stmt->bindParam(9, $role);
        $stmt->bindParam(10, $status);
        
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $db->lastInsertId();
            $_SESSION['user_first_name'] = $first_name;
            $_SESSION['user_last_name'] = $last_name;
            $_SESSION['user_email'] = $email;
            $_SESSION['username'] = $username;
            $_SESSION['user_role'] = $role;
            
            header('Location: mes-articles.php');
            exit();
        } else {
            $error_message = "❌ Erreur lors de l'inscription.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - BLOG GAMING</title>
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

        .inscription-section {
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

        .connexion-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
        }

        .connexion-link a {
            color: var(--primary-green);
            text-decoration: none;
        }

        .connexion-link a:hover {
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
        
        .username-notice {
            background: rgba(0, 255, 65, 0.1);
            border: 1px solid var(--primary-green);
            color: var(--primary-green);
            padding: 0.8rem;
            margin-bottom: 1.5rem;
            font-size: 0.7rem;
            font-family: 'VT323', monospace;
            text-align: center;
        }
        
        .username-display {
            background: rgba(189, 0, 255, 0.1);
            border: 1px solid var(--secondary-purple);
            color: var(--secondary-purple);
            padding: 0.8rem;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            font-family: 'VT323', monospace;
            text-align: center;
            display: none;
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
            
            .inscription-section {
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
                        <li><a href="deconnexion.php" class="logout-btn">DÉCONNEXION (<?php echo $_SESSION['user_first_name'] ?? 'Utilisateur'; ?>)</a></li>
                    <?php else: ?>
                        <li><a href="submit-article.php">✍️ ÉCRIRE UN ARTICLE</a></li>
                        <li><a href="connexion.php">SE CONNECTER</a></li>
                        <li><a href="inscription.php" class="active">S'INSCRIRE</a></li>
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

    <section class="inscription-section">
        <div class="container">
            <div class="form-container">
                <h1 style="text-align: center; margin-bottom: 1rem; color: var(--primary-green); font-size: 1.8rem; font-weight: 800;">
                    🎮 CRÉER UN COMPTE
                </h1>
                <p style="text-align: center; color: var(--secondary-purple); margin-bottom: 3rem; font-size: 1.125rem; font-family: 'VT323', monospace;">
                    Rejoignez notre communauté gaming et partagez vos articles !
                </p>
                
                <div class="username-notice">
                    <strong>Information :</strong> Un nom d'utilisateur unique sera généré automatiquement à partir de votre prénom et nom.
                </div>

                <?php if (isset($error_message)): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="inscriptionForm">
                    <div class="form-group">
                        <label class="form-label" for="nom">NOM *</label>
                        <input type="text" class="form-control" name="nom" id="nom"
                               placeholder="Votre nom" required>
                        <span class="validation-error" id="nomError"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="prenom">PRÉNOM *</label>
                        <input type="text" class="form-control" name="prenom" id="prenom"
                               placeholder="Votre prénom" required>
                        <span class="validation-error" id="prenomError"></span>
                    </div>

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
                    </div>
                    
                    <div id="usernamePreview" class="username-display">
                        Votre nom d'utilisateur sera : <strong id="usernameText"></strong>
                    </div>

                    <button type="submit" class="submit-btn">
                        🚀 CRÉER MON COMPTE
                    </button>
                </form>

                <div class="connexion-link">
                    Déjà un compte ? <a href="connexion.php">Connectez-vous ici</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.querySelector('.mobile-menu');
            const navLinks = document.querySelector('.nav-links');
            const inscriptionForm = document.getElementById('inscriptionForm');
            const usernamePreview = document.getElementById('usernamePreview');
            const usernameText = document.getElementById('usernameText');
            
            // Fonction pour générer le username
            function generateUsername() {
                const prenom = document.getElementById('prenom').value.trim();
                const nom = document.getElementById('nom').value.trim();
                
                if (prenom && nom) {
                    let username = (prenom + '.' + nom).toLowerCase();
                    // Nettoyer le username
                    username = username
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Enlever les accents
                        .replace(/[^a-z0-9.]/g, ''); // Enlever les caractères spéciaux
                    
                    usernameText.textContent = username;
                    usernamePreview.style.display = 'block';
                } else {
                    usernamePreview.style.display = 'none';
                }
            }
            
            // Écouter les changements sur les champs nom et prénom
            document.getElementById('nom').addEventListener('input', generateUsername);
            document.getElementById('prenom').addEventListener('input', generateUsername);
            
            if (mobileMenu && navLinks) {
                mobileMenu.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                });
            }

            // Validation JavaScript
            if (inscriptionForm) {
                inscriptionForm.addEventListener('submit', function(event) {
                    let isValid = true;
                    
                    // Réinitialiser les messages d'erreur
                    document.getElementById('nomError').textContent = '';
                    document.getElementById('prenomError').textContent = '';
                    document.getElementById('emailError').textContent = '';
                    document.getElementById('passwordError').textContent = '';
                    
                    // Validation nom
                    const nom = document.getElementById('nom').value;
                    const nomRegex = /^[a-zA-ZÀ-ÿ\s\-']{2,}$/;
                    
                    if (!nom) {
                        document.getElementById('nomError').textContent = 'Le nom est obligatoire';
                        isValid = false;
                    } else if (!nomRegex.test(nom)) {
                        document.getElementById('nomError').textContent = 'Le nom doit contenir au moins 2 caractères et seulement des lettres';
                        isValid = false;
                    }
                    
                    // Validation prénom
                    const prenom = document.getElementById('prenom').value;
                    const prenomRegex = /^[a-zA-ZÀ-ÿ\s\-']{2,}$/;
                    
                    if (!prenom) {
                        document.getElementById('prenomError').textContent = 'Le prénom est obligatoire';
                        isValid = false;
                    } else if (!prenomRegex.test(prenom)) {
                        document.getElementById('prenomError').textContent = 'Le prénom doit contenir au moins 2 caractères et seulement des lettres';
                        isValid = false;
                    }
                    
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
                    } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
                        document.getElementById('passwordError').textContent = 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre';
                        isValid = false;
                    }
                    
                    if (!isValid) {
                        event.preventDefault();
                    }
                });

                // Validation en temps réel pour améliorer l'UX
                const fields = ['nom', 'prenom', 'email', 'mot_de_passe'];
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
                        case 'nom':
                        case 'prenom':
                            const nameRegex = /^[a-zA-ZÀ-ÿ\s\-']{2,}$/;
                            if (!value) {
                                errorElement.textContent = `Le ${fieldName} est obligatoire`;
                            } else if (!nameRegex.test(value)) {
                                errorElement.textContent = `Le ${fieldName} doit contenir au moins 2 caractères et seulement des lettres`;
                            } else {
                                errorElement.textContent = '';
                            }
                            break;
                            
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
                            } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(value)) {
                                errorElement.textContent = 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre';
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