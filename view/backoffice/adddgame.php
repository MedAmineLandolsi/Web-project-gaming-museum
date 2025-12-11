<?php

include '../../controller/JeuxController.php';
include '../../model/Jeux.php'; 

$error = "";
$gameController = new JeuxController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        isset($_POST["nom"], $_POST["description"], $_POST["prix"], $_POST["stock"], $_POST["categorie"]) &&
        !empty($_POST["nom"]) &&
        !empty($_POST["description"]) &&
        !empty($_POST["prix"]) &&
        !empty($_POST["stock"]) &&
        !empty($_POST["categorie"])
    ) {

        // Create game object
        $game = new jeux(
            null,
            $_POST['nom'],
            $_POST['description'],
            floatval($_POST['prix']),
            intval($_POST['stock']),
            $_POST['categorie']
        );

        // Add game to database
        $gameController->addgame($game);

        // Redirect to list page
        header('Location: addgame.php');
        exit;
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Game</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .input-error {
    border: 2px solid red !important;
    background: #ffe6e6;
}

    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <div class="logo-box">[LOGO]</div>
                <div class="admin-title">
                    <h2>LUDOLOGY VAULT</h2>
                    <span class="admin-badge">ADMIN PANEL</span>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item active">
                    <a href="adddgame.php">
                        <span class="nav-icon">➕</span>
                        <span class="nav-text">ADD GAME</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="updategame.php">
                        <span class="nav-icon">✏️</span>
                        <span class="nav-text">MODIFY GAME</span>
                        
                    </a>
                </li>
                <li class="nav-item">
                    <a href="commande.php">
                        <span class="nav-icon">🗑️</span>
                        <span class="nav-text">DELETE GAME</span>
                        
                    </a>
                </li>
                
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">AD</div>
                <div class="admin-info">
                    <span class="admin-name">Admin User</span>
                    <span class="admin-role">Super Admin</span>
                </div>
            </div>
            <button class="btn-logout">
                <span>🚪</span> DÉCONNEXION
            </button>
        </div>
    </aside>
    <main class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">◄ ADD GAME ►</h1>
            </div>
            
        </header>
        <section class="dashboard-card recent-games">
    <div class="card-header">
        <h3 class="card-title">◄ ADD GAME ►</h3>
    </div>

    <div class="card-content">
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <table class="data-table vertical-form">
                <tbody>

                    <tr>
                        <th>NOM</th>
                        <td><input type="text" name="nom" placeholder="Nom du jeu" class="search-input"></td>
                    </tr>

                    <tr>
                        <th>DESCRIPTION</th>
                        <td><textarea name="description" placeholder="Description" class="search-input"></textarea></td>
                    </tr>

                    <tr>
                        <th>PRIX (€)</th>
                        <td><input type="number" step="0.01" name="prix" placeholder="Prix" class="search-input"></td>
                    </tr>

                    <tr>
                        <th>STOCK</th>
                        <td><input type="number" name="stock" placeholder="Stock" class="search-input"></td>
                    </tr>

                    <tr>
                        <th>CATEGORIE</th>
                        <td><input type="text" name="categorie" placeholder="Catégorie" class="search-input"></td>
                    </tr>

                    <th>
                        
                        
                            
                            <div class="action-grid"><button type="submit" class="action-btn action-primary">
                                <span class="action-icon">💾</span>
                                <span class="action-text"> Enregistrer</span>
                            </button>
                            </div>
                       
                        
                    </th>

                </tbody>
            </table>
        </form>
        
        <section class="quick-actions">
            
            <a href="addgame.php" style="text-decoration: none;"><div class="action-grid">
                <button class="action-btn action-primary">
                    <span class="action-icon">🔙</span>
                    <span class="action-text">RETOUR</span>
                </button>
                
            </div></a>
        </section>
    </div>
</section>


    </main>
    <script src="script.js"></script>
</body>