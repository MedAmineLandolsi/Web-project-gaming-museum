<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Article.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $article = new Article($db);
    
    $article->Titre = htmlspecialchars($_POST['title']);
    $article->Contenu = htmlspecialchars($_POST['content']);
    $article->Categorie = $_POST['category'];
    $article->Auteur_ID = $_SESSION['user_id'];
    $article->Date_Publication = date('Y-m-d H:i:s');
    $article->Statut = 'pending';
    
    if ($article->creer()) {
        $_SESSION['success_message'] = "✅ ARTICLE SOUMIS AVEC SUCCÈS ! IL SERA EXAMINÉ PAR NOTRE ÉQUIPE.";
        header('Location: submit-article.php');
        exit();
    } else {
        $error_message = "❌ ERREUR LORS DE LA SOUMISSION DE L'ARTICLE.";
    }
}

$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ÉCRIRE UN ARTICLE - BLOG GAMING</title>
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

        /* Header */
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

        /* Submit Section */
        .submit-section {
            margin-top: 120px;
            padding: 3rem 0;
        }

        .form-container {
            max-width: 800px;
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

        textarea.form-control {
            resize: vertical;
            min-height: 250px;
            font-family: 'VT323', monospace;
            line-height: 1.7;
            font-size: 1.1rem;
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

        .submit-btn:disabled {
            background: var(--text-gray);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .form-help {
            color: var(--text-gray);
            font-size: 0.7rem;
            margin-top: 0.5rem;
            display: block;
            font-weight: 500;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .char-count {
            text-align: right;
            color: var(--text-gray);
            font-size: 0.7rem;
            margin-top: 0.5rem;
            font-weight: 500;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .char-count.warning {
            color: var(--accent-pink);
        }

        .char-count.error {
            color: var(--accent-pink);
        }

        /* Messages */
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

        .field-error {
            background: rgba(255, 0, 110, 0.1);
            border: 2px solid rgba(255, 0, 110, 0.3);
            color: var(--accent-pink);
            padding: 1rem;
            border-radius: 0;
            margin-top: 0.75rem;
            font-size: 0.7rem;
            font-weight: 600;
            display: none;
            font-family: 'Press Start 2P', cursive;
        }

        .form-control.error {
            border-color: var(--accent-pink);
            box-shadow: 0 0 0 3px rgba(255, 0, 110, 0.1);
        }

        .user-info {
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

        /* Footer */
        .footer {
            background: var(--darker-bg);
            padding: 4rem 0 2rem;
            margin-top: 4rem;
            border-top: 3px solid var(--primary-green);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-section h4 {
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            text-shadow: 0 0 10px var(--primary-green);
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.75rem;
        }

        .footer-section a {
            color: var(--text-gray);
            text-decoration: none;
            transition: color 0.3s;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        .footer-section a:hover {
            color: var(--primary-green);
            transform: translateX(5px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 3rem;
            border-top: 1px solid var(--border-color);
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 4px;
        }

        .mobile-menu span {
            width: 25px;
            height: 3px;
            background: var(--primary-green);
            transition: 0.3s;
        }

        /* Responsive */
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
            
            .mobile-menu {
                display: flex;
            }
            
            .submit-section {
                margin-top: 100px;
            }
        }
    </style>
</head>
<body>
    <!-- Particules d'arrière-plan -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">🎮 LV BLOG GAMING</div>
                <ul class="nav-links">
                    <li><a href="index.php">ACCUEIL</a></li>
                    <li><a href="blog.php">ARTICLES</a></li>
                    <li><a href="about.php">À PROPOS</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="submit-article.php" class="active">✍️ ÉCRIRE UN ARTICLE</a></li>
                        <li><a href="mes-articles.php">MES ARTICLES</a></li>
                        <li><a href="deconnexion.php" class="logout-btn">DÉCONNEXION (<?php echo $_SESSION['user_prenom']; ?>)</a></li>
                    <?php else: ?>
                        <li><a href="submit-article.php" class="active">✍️ ÉCRIRE UN ARTICLE</a></li>
                        <li><a href="connexion.php">SE CONNECTER</a></li>
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

    <section class="submit-section">
        <div class="container">
            <div class="form-container">
                <h1 style="text-align: center; margin-bottom: 1rem; color: var(--primary-green); font-size: 1.8rem; font-weight: 800;">
                    ✍️ PROPOSER UN ARTICLE
                </h1>
                
                <div class="user-info">
                    🔐 Connecté en tant que : <strong><?php echo $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']; ?></strong>
                </div>

                <p style="text-align: center; color: var(--secondary-purple); margin-bottom: 3rem; font-size: 1.125rem; font-family: 'VT323', monospace;">
                    Partagez votre passion pour le gaming avec notre communauté !<br>
                    Votre article sera examiné par notre équipe avant publication.
                </p>

                <?php if ($success_message): ?>
                    <div class="success-message">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form id="submitArticleForm" method="POST" action="submit-article.php">
                    <div class="form-group">
                        <label class="form-label" for="title">TITRE DE L'ARTICLE *</label>
                        <input type="text" class="form-control" name="title" id="title"
                               placeholder="Ex: Mon avis sur Cyberpunk 2077" required
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                               oninput="updateCharCount('title', 100)">
                        <div class="char-count" id="titleCount">0/100 caractères</div>
                        <div class="field-error" id="titleError"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category">CATÉGORIE *</label>
                        <select class="form-control" name="category" id="category" required>
                            <option value="">CHOISIR UNE CATÉGORIE</option>
                            <option value="review" <?php echo (isset($_POST['category']) && $_POST['category'] == 'review') ? 'selected' : ''; ?>>🎮 TEST & REVIEW</option>
                            <option value="news" <?php echo (isset($_POST['category']) && $_POST['category'] == 'news') ? 'selected' : ''; ?>>📰 ACTUALITÉ GAMING</option>
                            <option value="tutorial" <?php echo (isset($_POST['category']) && $_POST['category'] == 'tutorial') ? 'selected' : ''; ?>>📚 TUTORIEL & GUIDE</option>
                            <option value="trends" <?php echo (isset($_POST['category']) && $_POST['category'] == 'trends') ? 'selected' : ''; ?>>📈 TENDANCES & ANALYSES</option>
                        </select>
                        <div class="field-error" id="categoryError"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="content">CONTENU DE L'ARTICLE *</label>
                        <textarea class="form-control" name="content" id="content"
                                  placeholder="Rédigez votre article ici... Partagez votre expérience, vos analyses, vos astuces..." 
                                  required
                                  oninput="updateCharCount('content', 5000)"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                        <div class="char-count" id="contentCount">0/5000 caractères</div>
                        <span class="form-help">Minimum 200 caractères recommandés</span>
                        <div class="field-error" id="contentError"></div>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        📨 SOUMETTRE L'ARTICLE POUR MODÉRATION
                    </button>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo" style="font-size: 1.2rem; margin-bottom: 1rem;">🎮 BLOG GAMING</div>
                    <p style="color: var(--text-gray); font-family: 'VT323', monospace; font-size: 1rem;">Votre destination gaming ultime</p>
                </div>
                <div class="footer-section">
                    <h4>NAVIGATION</h4>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="blog.php">Articles</a></li>
                        <li><a href="about.php">À propos</a></li>
                        <li><a href="submit-article.php">Écrire un article</a></li>
                        <li><a href="mes-articles.php">Mes articles</a></li>
                        <li><a href="deconnexion.php">Déconnexion</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>CONTACT</h4>
                    <p style="color: var(--text-gray); font-family: 'VT323', monospace; font-size: 1rem;">LV@blog-gaming.fr<br>+216 21 121 732</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 BLOG GAMING. TOUS DROITS RÉSERVÉS.</p>
            </div>
        </div>
    </footer>

    <script>
        function validateRequired(value, fieldName) {
            if (!value || value.trim() === '') {
                return `LE CHAMP ${fieldName} EST OBLIGATOIRE`;
            }
            return '';
        }

        function validateLength(value, fieldName, minLength, maxLength) {
            if (!value) return '';
            
            if (minLength && value.length < minLength) {
                return `${fieldName} DOIT CONTENIR AU MOINS ${minLength} CARACTÈRES`;
            }
            if (maxLength && value.length > maxLength) {
                return `${fieldName} NE DOIT PAS DÉPASSER ${maxLength} CARACTÈRES`;
            }
            return '';
        }

        function validateCategory(category) {
            if (!category) {
                return 'VEUILLEZ SÉLECTIONNER UNE CATÉGORIE';
            }
            return '';
        }

        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + 'Error');
            
            if (message) {
                field.classList.add('error');
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            } else {
                field.classList.remove('error');
                errorElement.style.display = 'none';
            }
        }

        function validateForm() {
            let isValid = true;

            const title = document.getElementById('title').value;
            const titleError = validateRequired(title, 'TITRE') || validateLength(title, 'LE TITRE', 10, 100);
            showError('title', titleError);
            if (titleError) isValid = false;

            const category = document.getElementById('category').value;
            const categoryError = validateCategory(category);
            showError('category', categoryError);
            if (categoryError) isValid = false;

            const content = document.getElementById('content').value;
            const contentError = validateRequired(content, 'CONTENU') || validateLength(content, 'LE CONTENU', 50, 5000);
            showError('content', contentError);
            if (contentError) isValid = false;

            return isValid;
        }

        function updateCharCount(fieldName, maxLength) {
            const field = document.getElementById(fieldName);
            const countElement = document.getElementById(fieldName + 'Count');
            const length = field.value.length;
            
            countElement.textContent = `${length}/${maxLength} CARACTÈRES`;
            
            countElement.className = `char-count ${length > maxLength * 0.9 ? 'warning' : ''} ${length > maxLength ? 'error' : ''}`;
            
            if (fieldName === 'title') {
                const error = validateLength(field.value, 'LE TITRE', 10, maxLength);
                showError(fieldName, error);
            } else if (fieldName === 'content') {
                const error = validateLength(field.value, 'LE CONTENU', 50, maxLength);
                showError(fieldName, error);
            }
        }

        document.getElementById('category').addEventListener('change', function() {
            const error = validateCategory(this.value);
            showError('category', error);
        });

        document.getElementById('submitArticleForm').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                alert('❌ VEUILLEZ CORRIGER LES ERREURS DANS LE FORMULAIRE');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ ENVOI EN COURS...';
        });

        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.querySelector('.mobile-menu');
            const navLinksContainer = document.querySelector('.nav-links');
            
            if (mobileMenu && navLinksContainer) {
                mobileMenu.addEventListener('click', function() {
                    navLinksContainer.classList.toggle('active');
                });
            }

            // Animation des particules
            const particles = document.querySelectorAll('.particle');
            particles.forEach((particle, index) => {
                particle.style.animationDelay = `${index * 2}s`;
                particle.style.animationDuration = `${15 + Math.random() * 10}s`;
            });

            const currentPage = window.location.pathname.split('/').pop();
            const navLinksAll = document.querySelectorAll('.nav-links a');
            
            navLinksAll.forEach(link => {
                const linkPage = link.getAttribute('href');
                if (linkPage === currentPage) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>