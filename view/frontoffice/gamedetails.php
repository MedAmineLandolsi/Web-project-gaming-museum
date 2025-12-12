<?php
include '../../controller/JeuxController.php';

if (!isset($_GET['id'])) {
    die("Game ID is missing.");
}

$gameC = new JeuxController();
$game = $gameC->getGameById($_GET['id']);

if (!$game) {
    die("Game not found.");
}

session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($game['nom']); ?> - Détails</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
</head>
<style>
    /* ----- LAYOUT DÉTAILS JEU ----- */

.main-content {
    max-width: 1100px;
    margin: 120px auto 60px;
    padding: 0 20px;
    position: relative;
    z-index: 1;
}

.page-header {
    text-align: left;
    margin-bottom: 30px;
    font-family: "Press Start 2P", system-ui, sans-serif;
}

.breadcrumb {
    font-size: 10px;
    color: #0aff9d;
    opacity: 0.8;
    margin-bottom: 8px;
}

.breadcrumb span {
    color: #fff;
}

.page-title {
    font-size: 20px;
    margin: 0 0 8px;
    text-transform: uppercase;
}

.page-subtitle {
    font-family: "VT323", monospace;
    font-size: 16px;
    color: #b3ffd8;
}

/* Conteneur général */

.details-container {
    display: grid;
    grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.4fr);
    gap: 32px;
    align-items: flex-start;
}

/* ----- COLONNE GAUCHE ----- */

.details-left {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* Carte cover */

.game-cover-card {
    position: relative;
    border-radius: 18px;
    padding: 2px;
    background: linear-gradient(135deg, #24ff00, #00ffd5, #ff00ff);
    box-shadow: 0 0 18px rgba(0, 255, 153, 0.4);
    overflow: hidden;
}

.cover-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 20% 0%, rgba(0, 255, 205, 0.3), transparent 55%);
    opacity: 0.6;
    pointer-events: none;
}

.cover-inner {
    position: relative;
    padding: 28px 22px;
    background: #050508;
    text-align: left;
    font-family: "Press Start 2P", system-ui, sans-serif;
}

.cover-id {
    font-size: 10px;
    color: #0aff9d;
    letter-spacing: 2px;
}

.cover-title {
    margin: 16px 0 12px;
    font-size: 14px;
    line-height: 1.5;
    text-transform: uppercase;
}

.cover-category {
    display: inline-block;
    padding: 4px 10px;
    font-size: 9px;
    border-radius: 999px;
    background: rgba(0, 255, 170, 0.08);
    border: 1px solid #00ffa6;
    text-transform: uppercase;
}

/* Badges rapides */

.game-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-family: "VT323", monospace;
}

.badge {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 13px;
    text-transform: uppercase;
    border: 1px solid rgba(0, 255, 170, 0.4);
    background: rgba(4, 255, 169, 0.06);
}

.badge-price {
    border-color: #ff00e6;
    background: rgba(255, 0, 230, 0.07);
}

.badge-stock {
    border-color: #ffc800;
    background: rgba(255, 200, 0, 0.07);
}

/* ----- COLONNE DROITE ----- */

.details-right {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.details-header .game-title {
    font-family: "Press Start 2P", system-ui, sans-serif;
    font-size: 16px;
    margin-bottom: 10px;
}

.game-tagline {
    font-family: "VT323", monospace;
    font-size: 15px;
    color: #c7ffe8;
}

/* Panneau description type terminal */

.game-description-panel {
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid rgba(0, 255, 170, 0.5);
    background: #050508;
    box-shadow: 0 0 16px rgba(0, 255, 170, 0.25);
}

.game-description-panel .panel-header {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: linear-gradient(90deg, #101018, #111827);
    border-bottom: 1px solid rgba(0, 255, 170, 0.3);
}

.panel-header .dot {
    width: 9px;
    height: 9px;
    border-radius: 999px;
}

.panel-header .red { background: #ff5f57; }
.panel-header .yellow { background: #febc2e; }
.panel-header .green { background: #28c840; }

.panel-title {
    margin-left: 10px;
    font-family: "Press Start 2P", system-ui, sans-serif;
    font-size: 9px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #a7ffd4;
}

.panel-body {
    padding: 16px 18px 18px;
    font-family: "VT323", monospace;
    font-size: 16px;
    line-height: 1.5;
    color: #f4fff9;
    max-height: 220px;
    overflow-y: auto;
}

/* Meta cards */

.game-meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
}

.meta-card {
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid rgba(0, 255, 170, 0.35);
    background: radial-gradient(circle at top left, rgba(0, 255, 170, 0.12), #050508);
    font-family: "VT323", monospace;
}

.meta-label {
    font-size: 12px;
    text-transform: uppercase;
    opacity: 0.75;
}

.meta-value {
    display: block;
    font-size: 17px;
    margin-top: 4px;
}

/* Boutons */

.details-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 10px;
}

.btn-auth.btn-primary {
    background: linear-gradient(135deg, #ff00e6, #ff0095);
    border-color: #ff66f0;
}

.btn-auth.btn-secondary {
    background: transparent;
    border-color: #0aff9d;
}

.btn-auth.btn-secondary:hover {
    background: rgba(10, 255, 157, 0.12);
}

/* Responsive */

@media (max-width: 900px) {
    .details-container {
        grid-template-columns: 1fr;
    }

    .main-content {
        margin-top: 100px;
    }
}

</style>
<body>

<!-- Background particles (on garde ton style) -->
<div class="particles">
    <div class="particle"></div><div class="particle"></div><div class="particle"></div>
    <div class="particle"></div><div class="particle"></div><div class="particle"></div>
    <div class="particle"></div><div class="particle"></div><div class="particle"></div>
    <div class="particle"></div>
</div>

<!-- Navigation bar -->
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
                <li><a href="index.php">HOME</a></li>
                <li><a href="games.php" class="active">JEUX</a></li>
                <li><a href="blog.html">BLOG</a></li>
                <li><a href="events.html">EVENTS</a></li>
                <li><a href="reclamation.html">RÉCLAMATION</a></li>
            </ul>
        </div>

        <div class="nav-right">
            <a href="../../../Crud-Commande/view/frontoffice/cart.php" style="text-decoration: none;">
                <button class="btn-auth">
                    <span class="btn-icon">🛒</span>
                </button>
            </a>
        </div>

        <div class="nav-right">
            <a href="../backoffice/dashboard.php" style="text-decoration: none;">
                <button class="btn-auth">
                    <span class="btn-icon">▶</span> SIGN IN / SIGN UP
                </button>
            </a>
        </div>
    </div>
</nav>

<main class="main-content">

    <!-- Header page -->
    <header class="page-header">
        <div class="breadcrumb">JEUX / <span><?= htmlspecialchars($game['nom']); ?></span></div>
        <h1 class="page-title">🎮 Détails du Jeu</h1>
        <p class="page-subtitle">Plonge dans l’univers de <strong><?= htmlspecialchars($game['nom']); ?></strong>.</p>
    </header>

    <section class="details-container">

        <!-- COLONNE GAUCHE -->
        <div class="details-left">
            <!-- Carte “cover” du jeu -->
            <article class="game-cover-card">
                <div class="cover-glow"></div>
                <div class="cover-inner">
                    <span class="cover-id">#<?= str_pad($game['id'], 3, "0", STR_PAD_LEFT); ?></span>
                    <h2 class="cover-title"><?= htmlspecialchars($game['nom']); ?></h2>
                    <span class="cover-category"><?= htmlspecialchars($game['categorie']); ?></span>
                </div>
            </article>

            <!-- Infos rapides -->
            <div class="game-badges">
                <span class="badge badge-genre"><?= htmlspecialchars($game['categorie']); ?></span>
                <span class="badge badge-price"><?= number_format($game['prix'], 2); ?> €</span>
                <span class="badge badge-stock">
                    Stock : <?= htmlspecialchars($game['stock']); ?>
                </span>
            </div>
        </div>

        <!-- COLONNE DROITE -->
        <div class="details-right">

            <header class="details-header">
                <h2 class="game-title"><?= htmlspecialchars($game['nom']); ?></h2>
                <p class="game-tagline">Une expérience à ne pas manquer pour les fans de jeux vidéo.</p>
            </header>

            <!-- Description dans un panneau type “terminal” -->
            <section class="game-description-panel">
                <div class="panel-header">
                    <span class="dot red"></span>
                    <span class="dot yellow"></span>
                    <span class="dot green"></span>
                    <span class="panel-title">SYNOPSIS</span>
                </div>
                <div class="panel-body">
                    <p class="game-description">
                        <?= nl2br(htmlspecialchars($game['description'])); ?>
                    </p>
                </div>
            </section>

            <!-- Cartes d’infos -->
            <section class="game-meta-grid">
                <div class="meta-card">
                    <span class="meta-label">Catégorie</span>
                    <span class="meta-value"><?= htmlspecialchars($game['categorie']); ?></span>
                </div>

                <div class="meta-card">
                    <span class="meta-label">Prix</span>
                    <span class="meta-value"><?= number_format($game['prix'], 2); ?> €</span>
                </div>

                <div class="meta-card">
                    <span class="meta-label">Stock disponible</span>
                    <span class="meta-value"><?= htmlspecialchars($game['stock']); ?></span>
                </div>

                <div class="meta-card">
                    <span class="meta-label">Référence</span>
                    <span class="meta-value">LV-<?= str_pad($game['id'], 4, "0", STR_PAD_LEFT); ?></span>
                </div>
            </section>

            <!-- CTA -->
            <div class="details-actions">
                <a href="#" id="download-pdf" style="text-decoration: none;">
    <button class="btn-auth btn-primary">
        <span class="btn-icon">📄</span> Télécharger en PDF
    </button>
</a>
                <a href="add_to_cart.php?id=<?= $game['id']; ?>" style="text-decoration: none;">
                    <button class="btn-auth btn-primary">
                        <span class="btn-icon">🛒</span> Ajouter au panier
                    </button>
                </a>

                <a href="games.php" style="text-decoration: none;">
                    <button class="btn-auth btn-secondary">
                        <span class="btn-icon">◀</span> Retour à la liste des jeux
                    </button>
                </a>
                <!-- Ajouter ce bouton avant ou après la description du jeu -->

            </div>

        </div>

    </section>

</main>

</body>
<!-- Inclus jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    document.getElementById("download-pdf").addEventListener("click", function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Custom Fonts (use fonts from Google Fonts)
    doc.addFont('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap', 'Press Start 2P', 'normal');
    doc.addFont('https://fonts.googleapis.com/css2?family=VT323&display=swap', 'VT323', 'normal');
    
    const gameTitle = "<?= htmlspecialchars($game['nom']); ?>";
    const gameDescription = "<?= nl2br(htmlspecialchars($game['description'])); ?>";
    const gameCategory = "Catégorie: <?= htmlspecialchars($game['categorie']); ?>";
    const gamePrice = "Prix: <?= number_format($game['prix'], 2); ?> €";
    const gameStock = "Stock: <?= htmlspecialchars($game['stock']); ?>";

    // Set title with larger font and a neon look
    doc.setFont("Press Start 2P");
    doc.setFontSize(20);
    doc.setTextColor(255, 0, 255);  // Neon purple
    doc.text(gameTitle, 10, 20);  // Title position

    // Set Description with smaller font and a neon green color
    doc.setFont("VT323");
    doc.setFontSize(12);
    doc.setTextColor(0, 255, 170);  // Neon green
    doc.text(gameDescription, 10, 40);

    // Set other information (category, price, stock) with slight styling
    doc.setFont("VT323");
    doc.setFontSize(12);
    doc.setTextColor(0, 255, 170);  // Neon green
    doc.text(gameCategory, 10, 60);
    doc.text(gamePrice, 10, 70);
    doc.text(gameStock, 10, 80);

    // Add some styling elements like borders or lines
    doc.setDrawColor(255, 0, 255);  // Neon purple for border lines
    doc.rect(5, 5, 200, 290);  // Rectangle border around content

    // Optional: Add an image (e.g., game cover or logo)
    // Example: doc.addImage('path_to_image.jpg', 'JPEG', 10, 90, 50, 50);

    // Footer line
    doc.setDrawColor(0, 255, 170);  // Neon green line at the bottom
    doc.line(10, 280, 200, 280);  // Horizontal line

    // Save the generated PDF
    doc.save(`${gameTitle}.pdf`);
});
 
</script>
</html>
