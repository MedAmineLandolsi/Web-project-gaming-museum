<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Article.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$articleModel = new Article($db);

// Récupérer l'ID de l'article depuis l'URL
if (!isset($_GET['id'])) {
    header('Location: mes-articles.php');
    exit();
}

$article_id = $_GET['id'];
$auteur_id = $_SESSION['user_id'];

// Récupérer l'article à modifier
$articleModel->Article_ID = $article_id;
$article = $articleModel->lireUn();

// Vérifier que l'article existe et appartient à l'utilisateur connecté
if (!$article || $articleModel->Auteur_ID != $auteur_id) {
    header('Location: mes-articles.php');
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleModel->Titre = htmlspecialchars($_POST['title']);
    $articleModel->Contenu = htmlspecialchars($_POST['content']);
    $articleModel->Categorie = $_POST['category'];
    $articleModel->Article_ID = $article_id;
    $articleModel->Auteur_ID = $auteur_id;
    
    if ($articleModel->modifier()) {
        $_SESSION['success_message'] = "✅ ARTICLE MODIFIÉ AVEC SUCCÈS ! IL SERA REEXAMINÉ PAR NOTRE ÉQUIPE.";
        header('Location: mes-articles.php');
        exit();
    } else {
        $error_message = "❌ ERREUR LORS DE LA MODIFICATION DE L'ARTICLE.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'article - BLOG GAMING</title>
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
            
            .submit-section {
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
                    <li><a href="submit-article.php">✍️ ÉCRIRE UN ARTICLE</a></li>
                    <li><a href="mes-articles.php" class="active">MES ARTICLES</a></li>
                    <li><a href="deconnexion.php" class="logout-btn">DÉCONNEXION (<?php echo $_SESSION['user_prenom']; ?>)</a></li>
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
                    ✏️ MODIFIER L'ARTICLE
                </h1>

                <?php if (isset($error_message)): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="title">TITRE DE L'ARTICLE *</label>
                        <input type="text" class="form-control" name="title" id="title" required
                               value="<?php echo htmlspecialchars($articleModel->Titre); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category">CATÉGORIE *</label>
                        <select class="form-control" name="category" id="category" required>
                            <option value="review" <?php echo $articleModel->Categorie == 'review' ? 'selected' : ''; ?>>🎮 TEST & REVIEW</option>
                            <option value="news" <?php echo $articleModel->Categorie == 'news' ? 'selected' : ''; ?>>📰 ACTUALITÉ GAMING</option>
                            <option value="tutorial" <?php echo $articleModel->Categorie == 'tutorial' ? 'selected' : ''; ?>>📚 TUTORIEL & GUIDE</option>
                            <option value="trends" <?php echo $articleModel->Categorie == 'trends' ? 'selected' : ''; ?>>📈 TENDANCES & ANALYSES</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="content">CONTENU DE L'ARTICLE *</label>
                        <textarea class="form-control" name="content" id="content" required><?php echo htmlspecialchars($articleModel->Contenu); ?></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        💾 ENREGISTRER LES MODIFICATIONS
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.querySelector('.mobile-menu');
            const navLinks = document.querySelector('.nav-links');
            
            if (mobileMenu && navLinks) {
                mobileMenu.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>