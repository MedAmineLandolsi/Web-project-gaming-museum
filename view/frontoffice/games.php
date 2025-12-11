<?php
include '../../controller/JeuxController.php';
$gamesC = new JeuxController();

// Get search/filter
$search = $_GET['search'] ?? null;
if (isset($_GET['filter']) && $_GET['filter'] !== '') {
    $search = $_GET['filter'];
}

// Get price sort
// Get sorting column and order
$sortBy = $_GET['sortby'] ?? null; // 'prix', 'nom', or 'date'
$order  = $_GET['order'] ?? null;  // 'asc' or 'desc'

// Fetch games
$list = $gamesC->listjeux($search, $sortBy, $order);

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ludology Vault - Games List</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
.game-card {
    position: relative; /* helps isolate hover */
}

.game-desc {
    opacity: 0;
    height: 0;
    overflow: hidden;
    transform: translateY(10px);
    transition: all 0.35s ease-in-out;
    pointer-events: none;
}

.game-card:hover .game-desc {
    opacity: 1;
    height: auto;
    transform: translateY(0);
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
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <div class="logo-container">
                    <div class="logo-placeholder">[LOGO]</div>
                    <h1 class="site-title">LUDOLOGY VAULT</h1>
                </div>
            </div>
            
            <div class="nav-center">
                <ul class="nav-menu">
                    <li><a href="index.php" >HOME</a></li>
                    <li><a href="games.php" class="active">JEUX</a></li>
                    <li><a href="blog.html">BLOG</a></li>
                    <li><a href="events.html">EVENTS</a></li>
                    <li><a href="reclamation.html">RÉCLAMATION</a></li>
                </ul>
            </div>
            
            <div class="nav-right">
                <a href="../../../Crud-Commande/view/frontoffice/cart.php" style="text-decoration: none;"><button class="btn-auth">
                    <span class="btn-icon">🛒</span> 
                </button></a>
            </div>
            <div class="nav-right">
                <a href="../backoffice/dashboard.php" style="text-decoration: none;"><button class="btn-auth">
                    <span class="btn-icon">▶</span> SIGN IN / SIGN UP
                </button></a>
            </div>
        </div>
    </nav>
     <section class="search-section">
        <div class="search-container">
            <h3 class="search-title">◄ RECHERCHER DANS LA BASE DE DONNÉES ►</h3>
            <form method="GET" action="games.php" class="search-bar">
    <input type="text" 
           name="search"
           placeholder="Entrez le nom d'un jeu, console, année..." 
           class="search-input"
           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
           
    <button type="submit" class="search-button">SEARCH</button>
</form>

            <div class="quick-filters">

    <a href="games.php?filter=Action">
        <button class="filter-chip">Action</button>
    </a>

    <a href="games.php?filter=Looter Shooter">
        <button class="filter-chip">Looter Shooter</button>
    </a>

    <a href="games.php?filter=Battle Royale">
        <button class="filter-chip">Battle Royale</button>
    </a>

</div>



        </div>
        <div class="quick-sorts" style="float: right;">
    
    <form method="GET" action="games.php">
    <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <input type="hidden" name="filter" value="<?= htmlspecialchars($_GET['filter'] ?? '') ?>">

    <select name="sortby" onchange="this.form.submit()" class="filter-chip">
        <option value="">Trier par</option>
        <option value="prix" <?= (($_GET['sortby'] ?? '') === 'prix') ? 'selected' : '' ?>>Prix</option>
        <option value="nom" <?= (($_GET['sortby'] ?? '') === 'nom') ? 'selected' : '' ?>>Nom</option>
        <option value="date" <?= (($_GET['sortby'] ?? '') === 'date') ? 'selected' : '' ?>>Date</option>
    </select>

    <select name="order" onchange="this.form.submit()" class="filter-chip">
        <option value="asc" <?= (($_GET['order'] ?? '') === 'asc') ? 'selected' : '' ?>>Ascendant ↑</option>
        <option value="desc" <?= (($_GET['order'] ?? '') === 'desc') ? 'selected' : '' ?>>Descendant ↓</option>
    </select>
</form>

</div>
    </section>
    <section>
    <div class="games-grid">
        <?php if (!empty($list)) { 
            foreach ($list as $game) { 
        ?>  

        <div class="game-card">
            <!-- CATEGORY BADGE -->
            <div class="game-badge">
                <?= htmlspecialchars($game['categorie']); ?>
            </div>

            <!-- IMAGE / PIXEL ART AREA -->
            <div class="game-image">
                <div class="pixel-art">
                    <div class="pixel-placeholder">
                        <?= htmlspecialchars($game['nom']); ?>
                    </div>

                    <div class="hover-overlay">
                        <a href="gamedetails.php?id=<?= $game['id']; ?>"><button class="quick-view">Details</button></a>
                    </div>
                    
                </div>
            </div>

            <!-- INFO UNDER THE CARD (like your PAC-MAN example) -->
            <div class="game-info">
                <h3><?= htmlspecialchars($game['nom']); ?></h3>

                <div class="game-meta">
                    <!-- I use prix + stock instead of year + stars -->
                    <span class="game-year">💰 
                        <?= htmlspecialchars($game['prix']); ?> €
                    </span>
                    <span class="game-rating">
                        Stock : <?= htmlspecialchars($game['stock']); ?>
                    </span>
                </div>

                <p class="game-desc">
                    <?= nl2br(htmlspecialchars($game['description'])); ?>
                </p>

                <div class="game-tags">
                    <!-- main tag = categorie -->
                    <span class="tag">
                        <?= htmlspecialchars($game['categorie']); ?>
                    </span>
                    <!-- you can add more tags later if you have them in DB -->
                </div>
            </div>
        </div><!-- end .game-card -->

        <?php 
            } 
        } 
        ?>
    </div><!-- end .games-grid -->
</section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-content">
                <div class="footer-section footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-placeholder">[LOGO]</div>
                        <h3>LUDOLOGY VAULT</h3>
                    </div>
                    <p class="footer-tagline">Préserver l'histoire du jeu vidéo pour les générations futures</p>
                    <div class="social-links">
                        <a href="#" class="social-icon" title="Facebook">
                            <span>FB</span>
                        </a>
                        <a href="#" class="social-icon" title="Twitter">
                            <span>TW</span>
                        </a>
                        <a href="#" class="social-icon" title="Instagram">
                            <span>IG</span>
                        </a>
                        <a href="#" class="social-icon" title="YouTube">
                            <span>YT</span>
                        </a>
                        <a href="#" class="social-icon" title="Discord">
                            <span>DC</span>
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">NAVIGATION</h3>
                    <ul class="footer-links">
                        <li><a href="index.html">► Accueil</a></li>
                        <li><a href="games.html">► Collection de Jeux</a></li>
                        <li><a href="blog.html">► Blog & Actualités</a></li>
                        <li><a href="events.html">► Événements</a></li>
                        <li><a href="#">► À Propos</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">RESSOURCES</h3>
                    <ul class="footer-links">
                        <li><a href="#">► Base de Données</a></li>
                        <li><a href="#">► Archives Historiques</a></li>
                        <li><a href="#">► Guides & Tutoriels</a></li>
                        <li><a href="reclamation.html">► Support & Réclamations</a></li>
                        <li><a href="#">► FAQ</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">MUSÉE</h3>
                    <div class="footer-info">
                        <p class="info-item">
                            <span class="info-icon">🕐</span>
                            <span class="info-content">
                                <strong>Horaires:</strong><br>
                                Lun-Ven: 09:00 - 18:00<br>
                                Sam-Dim: 10:00 - 20:00
                            </span>
                        </p>
                        <p class="info-item">
                            <span class="info-icon">📧</span>
                            <span class="info-content">contact@ludologyvault.tn</span>
                        </p>
                        <p class="info-item">
                            <span class="info-icon">📞</span>
                            <span class="info-content">+216 XX XXX XXX</span>
                        </p>
                        <p class="info-item">
                            <span class="info-icon">📍</span>
                            <span class="info-content">Tunis, Tunisia</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="pixel-divider"></div>
            <div class="footer-bottom-content">
                <p class="copyright">&copy; 2024 LUDOLOGY VAULT - Tous droits réservés</p>
                <div class="footer-bottom-links">
                    <a href="#">Mentions Légales</a>
                    <span>•</span>
                    <a href="#">Politique de Confidentialité</a>
                    <span>•</span>
                    <a href="#">Conditions d'Utilisation</a>
                </div>
                <p class="made-with">Made with <span class="heart">♥</span> for gamers worldwide</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <span>▲</span>
    </button>

    <script src="script.js"></script>
</body>
</html>