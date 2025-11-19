<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos - Musée de Gaming</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .about-section {
            margin-top: 100px;
            padding: 2rem 0;
        }

        .about-hero {
            background: url('../assets/images/about-banner.jpg') center/cover;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
        }

        .about-hero::before {
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

        .about-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 3rem;
            color: var(--light);
        }

        .about-text p {
            margin-bottom: 1.5rem;
        }

        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .mission-card, .vision-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid var(--border);
            text-align: center;
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .card-title {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .team-section {
            margin: 4rem 0;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .team-member {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            border: 1px solid var(--border);
            transition: transform 0.3s;
        }

        .team-member:hover {
            transform: translateY(-5px);
        }

        .member-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--dark);
        }

        .member-name {
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        .member-role {
            color: var(--secondary);
            margin-bottom: 1rem;
            font-weight: bold;
        }

        .stats-section {
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 15px;
            margin: 3rem 0;
            border: 1px solid var(--border);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-item .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            display: block;
        }

        .stat-item .label {
            color: var(--gray);
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }
            
            .about-hero {
                height: 300px;
            }
            
            .mission-vision {
                grid-template-columns: 1fr;
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
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="games.php">Jeux</a></li>
                    <li><a href="about.php" class="active">À propos</a></li>
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

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <!-- Hero -->
            <div class="about-hero">
                <div class="hero-content">
                    <h1 class="section-title">Notre Histoire</h1>
                    <p class="section-subtitle">Découvrez la passion qui anime le Musée de Gaming</p>
                </div>
            </div>

            <!-- Contenu Principal -->
            <div class="about-content">
                <div class="about-text">
                    <p>
                        Fondé en 2025, le <strong>Musée de Gaming</strong> est bien plus qu'un simple site web. 
                        C'est une passion, une communauté, et un hommage à l'art du jeu vidéo. 
                        Notre mission est de préserver, documenter et célébrer la riche histoire 
                        du gaming tout en accompagnant son évolution future.
                    </p>

                    <p>
                        Nous croyons que les jeux vidéo sont une forme d'art à part entière, 
                        mêlant narration, design, musique et technologie. Chaque jeu raconte 
                        une histoire, et nous sommes là pour vous aider à découvrir ces récits 
                        fascinants.
                    </p>
                </div>

                <!-- Mission et Vision -->
                <div class="mission-vision">
                    <div class="mission-card">
                        <div class="card-icon">🎯</div>
                        <h3 class="card-title">Notre Mission</h3>
                        <p>
                            Documenter et partager la culture gaming sous toutes ses formes, 
                            en offrant une plateforme éducative et engageante pour tous les 
                            passionnés de jeux vidéo.
                        </p>
                    </div>

                    <div class="vision-card">
                        <div class="card-icon">🔮</div>
                        <h3 class="card-title">Notre Vision</h3>
                        <p>
                            Devenir la référence francophone pour la préservation et la 
                            célébration de la culture vidéoludique, en connectant les 
                            générations de joueurs à travers le monde.
                        </p>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="stats-section">
                    <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary);">
                        Notre Impact en Chiffres
                    </h2>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="number">500+</span>
                            <span class="label">Jeux Documentés</span>
                        </div>
                        <div class="stat-item">
                            <span class="number">10K+</span>
                            <span class="label">Visiteurs Mensuels</span>
                        </div>
                        <div class="stat-item">
                            <span class="number">200+</span>
                            <span class="label">Articles Publiés</span>
                        </div>
                        <div class="stat-item">
                            <span class="number">5</span>
                            <span class="label">Années d'Expertise</span>
                        </div>
                    </div>
                </div>

                <!-- Équipe -->
                <div class="team-section">
                    <h2 style="text-align: center; margin-bottom: 1rem; color: var(--primary);">
                        Notre Équipe
                    </h2>
                    <p style="text-align: center; color: var(--gray); margin-bottom: 2rem;">
                        Passionnés de gaming depuis toujours
                    </p>

                    <div class="team-grid">
                        <div class="team-member">
                            <div class="member-avatar">👨‍💻</div>
                            <h4 class="member-name">Ilef Karoui</h4>
                            <div class="member-role">Ingénieur & Responsable Blog</div>
                            <p>Responsable du module Gestion Blog qui propose un espace de blog où les utilisateurs peuvent publier des articles sur les jeux, les tendances gaming, les résultats de tournois, etc.</p>
                        </div>
                    </div>
                </div>
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
                    <p>A-mpact@gmail.com<br>+216 21 121 732</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Musée de Gaming. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>