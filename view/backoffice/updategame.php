<?php
include '../../controller/JeuxController.php';
include '../../model/Jeux.php'; 

$error = "";
$gameController = new JeuxController();

// 1 — Check if ID is provided
if (!isset($_GET['id'])) {
    die("ID du jeu non fourni !");
}

$id = $_GET['id'];

// 2 — Fetch game
$game = $gameController->getGameById($id);

if (!$game) {
    die("Jeu introuvable !");
}

// 3 — If form submitted → update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Keep same stock (updategame() does NOT update stock)
    $updatedGame = new Jeux(
        $id,
        $_POST['nom'],
        $_POST['description'],
        $_POST['prix'],
        $game['stock'],
        $_POST['categorie']
    );

    $gameController->updategame($updatedGame, $id);

    header("Location: updategame.php?id=$id&success=1");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Game</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
</head>
<body>

    <!-- SIDEBAR -->
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
                <li class="nav-item">
                    <a href="adddgame.php">
                        <span class="nav-icon">➕</span>
                        <span class="nav-text">ADD GAME</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="updategame.php?id=<?= $id ?>">
                        <span class="nav-icon">✏️</span>
                        <span class="nav-text">MODIFY GAME</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="delete.php">
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


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- Top Bar -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">◄ MODIFY GAME ►</h1>
            </div>
        </header>


        <section class="dashboard-card recent-games">
            <div class="card-header">
                <h3 class="card-title">◄ MODIFY GAME ►</h3>
            </div>

            <div class="card-content">

                <?php if (!empty($_GET['success'])): ?>
                    <div class="success-message">✔ Jeu mis à jour avec succès !</div>
                <?php endif; ?>

                <form method="POST">
                    <table class="data-table vertical-form">
                        <tbody>

                            <tr>
                                <th>NOM</th>
                                <td><input type="text" name="nom" value="<?= htmlspecialchars($game['nom']) ?>" class="search-input" required></td>
                            </tr>

                            <tr>
                                <th>DESCRIPTION</th>
                                <td><textarea name="description" class="search-input" required><?= htmlspecialchars($game['description']) ?></textarea></td>
                            </tr>

                            <tr>
                                <th>PRIX (€)</th>
                                <td><input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($game['prix']) ?>" class="search-input" required></td>
                            </tr>

                            <tr>
                                <th>CATEGORIE</th>
                                <td><input type="text" name="categorie" value="<?= htmlspecialchars($game['categorie']) ?>" class="search-input" required></td>
                            </tr>

                            <tr>
                                <th>
                                    <button type="submit" class="action-btn action-primary">
                                        <span class="action-icon">💾</span>
                                        <span class="action-text"> Enregistrer</span>
                                    </button>
                                </th>
                            </tr>

                        </tbody>
                    </table>
                </form>
            </div>
        </section>

    </main>

</body>
</html>
