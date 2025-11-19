<?php
// CONNEXION DIRECTE DANS BLOG.PHP
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM articles ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- VOTRE CODE EXISTANT -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Gaming - Musée de Gaming</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .blog-section {
            margin-top: 100px;
            padding: 2rem 0;
        }

        .blog-hero {
            background: url('../assets/images/blog-banner.jpg') center/cover;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
        }

        .blog-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .section-title {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: var(--gray);
        }

        .blog-filters {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid var(--primary);
            color: var(--light);
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--primary);
            color: var(--dark);
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .blog-card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
        }

        .blog-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 255, 136, 0.2);
        }

        .blog-image {
            height: 200px;
            overflow: hidden;
        }

        .blog-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .blog-card:hover .blog-image img {
            transform: scale(1.1);
        }

        .blog-content {
            padding: 1.5rem;
        }

        .blog-category {
            background: var(--primary);
            color: var(--dark);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .blog-title {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .blog-title a {
            color: var(--light);
            text-decoration: none;
            transition: color 0.3s;
        }

        .blog-title a:hover {
            color: var(--primary);
        }

        .blog-excerpt {
            color: var(--gray);
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .blog-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: var(--gray);
            border-top: 1px solid var(--border);
            padding-top: 1rem;
        }

        .no-articles {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
            grid-column: 1 / -1;
        }

        /* Styles pour le formulaire d'article */
        .write-article-section {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
            margin: 3rem 0;
            border: 1px solid var(--border);
        }

        .write-article-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            color: var(--light);
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.8rem;
            color: var(--light);
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-group textarea {
            min-height: 200px;
            resize: vertical;
            font-family: inherit;
        }

        .submit-btn {
            background: var(--primary);
            color: var(--dark);
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            align-self: flex-start;
        }

        .submit-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .toggle-form-btn {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .toggle-form-btn:hover {
            background: var(--primary);
            color: var(--dark);
        }

        .error-message {
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid #ff4757;
            color: #ff4757;
            padding: 0.5rem;
            border-radius: 5px;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            display: none;
        }

        .field-error {
            border-color: #ff4757 !important;
        }

        @media (max-width: 768px) {
            .blog-grid {
                grid-template-columns: 1fr;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .blog-hero {
                height: 200px;
            }

            .write-article-section {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">🎮 Musée de Gaming</div>
                <ul class="nav-links">
                    <li><a href="../index.php">Accueil</a></li>
                    <li><a href="blog.php" class="active">Blog</a></li>
                    <li><a href="games.php">Jeux</a></li>
                    <li><a href="about.php">À propos</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="../backoffice/dashboard.php" class="admin-btn">Espace Admin</a></li>
                </ul>
                <div class="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Blog Content -->
    <section class="blog-section">
        <div class="container">
            <!-- Hero -->
            <div class="blog-hero">
                <div class="hero-content">
                    <h1 class="section-title">Blog Gaming</h1>
                    <p class="section-subtitle">Découvrez les dernières actualités, tests et tendances du monde du gaming</p>
                </div>
            </div>

            <!-- Section Écrire un article (optionnel) -->
            <div style="text-align: center; margin: 2rem 0;">
                <a href="../admin/create-article.php" class="toggle-form-btn">
                    ✍️ Écrire un article (Admin)
                </a>
            </div>

            <!-- Filters -->
            <div class="blog-filters">
                <button class="filter-btn active" data-filter="all">Tous les articles</button>
                <button class="filter-btn" data-filter="news">Actualités</button>
                <button class="filter-btn" data-filter="review">Tests & Reviews</button>
            </div>

            <!-- Blog Grid AVEC DONNÉES RÉELLES -->
            <div class="blog-grid" id="articlesGrid">
                <?php if(!empty($articles)): ?>
                    <?php foreach($articles as $article): ?>
                    <article class="blog-card" data-category="news">
                        <div class="blog-image">
                            <img src="../assets/images/<?= htmlspecialchars($article['image_url'] ?? 'placeholder.jpg') ?>" 
                                 alt="<?= htmlspecialchars($article['title']) ?>"
                                 onerror="this.src='../assets/images/placeholder.jpg'">
                        </div>
                        <div class="blog-content">
                            <span class="blog-category">Article</span>
                            <h3 class="blog-title">
                                <a href="blog-single.php?id=<?= $article['id'] ?>">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </h3>
                            <p class="blog-excerpt">
                                <?= substr(htmlspecialchars($article['content']), 0, 150) ?>...
                            </p>
                            <div class="blog-meta">
                                <span class="blog-date">
                                    <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                                </span>
                                <span class="blog-author">
                                    Par <?= htmlspecialchars($article['author']) ?>
                                </span>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-articles">
                        <h3>Aucun article trouvé</h3>
                        <p>Revenez plus tard pour découvrir de nouveaux contenus !</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">🎮 Musée de Gaming</div>
                    <p>Votre destination ultime pour la culture gaming</p>
                </div>
                <div class="footer-section">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="games.php">Jeux</a></li>
                        <li><a href="../backoffice/dashboard.php">Admin</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>email@musee-gaming.fr<br>+33 1 23 45 67 89</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Musée de Gaming. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
    <script>
        // Filtrage des articles avec JavaScript
        function initFilters() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const blogCards = document.querySelectorAll('.blog-card');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Retirer la classe active de tous les boutons
                    filterBtns.forEach(b => b.classList.remove('active'));
                    // Ajouter la classe active au bouton cliqué
                    this.classList.add('active');

                    const filter = this.getAttribute('data-filter');

                    blogCards.forEach(card => {
                        if (filter === 'all' || card.getAttribute('data-category') === filter) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initFilters();
        });
    </script>
</body>
</html>