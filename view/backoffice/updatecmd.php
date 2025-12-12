<?php
require "../../../Crud-Jeux/controller/JeuxController.php";

require "../../controller/CommandeController.php";
require "../../model/Commande.php";

$commandeC = new CommandeController();
$jeuxC = new JeuxController();

// Fetch commande by ID
if (!isset($_GET['id'])) {
    die("ID commande manquant.");
}

$cmd = $commandeC->getCommande($_GET['id']);

if (!$cmd) {
    die("Commande introuvable.");
}

// Fetch all games to select Produit_id
$games = $jeuxC->listjeux();

// Update form submit
if (isset($_POST['update'])) {

    $data = [
        'ID'         => $_POST['ID'],
        'Produit_id' => $_POST['Produit_id'],
        'quantity'   => $_POST['quantity'],
        'Total'      => $_POST['Total'],
        'Date'       => $_POST['Date']
    ];

    $commandeC->updateCommande($data);

    header("Location: commande.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Commande</title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
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
                <li class="nav-item">
                    <a href="addcommande.php">
                        <span class="nav-icon">➕</span>
                        <span class="nav-text">ADD COMMANDE</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="updatecmd.php">
                        <span class="nav-icon">✏️</span>
                        <span class="nav-text">MODIFY COMMANDE</span>
                        
                    </a>
                </li>
                <li class="nav-item">
                    <a href="commande.php">
                        <span class="nav-icon">🗑️</span>
                        <span class="nav-text">DELETE COMMANDE</span>
                        
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
    <header class="top-bar">
        <h1 class="page-title">◄ MODIFIER UNE COMMANDE ►</h1>
    </header>

    <section class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">Modifier Commande #<?= $cmd['ID'] ?></h3>
        </div>

        <div class="card-content">

            <form method="POST">

                <input type="hidden" name="ID" value="<?= $cmd['ID']; ?>">

                <!-- Select Jeu -->
                <label>Jeu</label>
                <select name="Produit_id" required class="input">
                    <?php foreach ($games as $g) { ?>
                        <option value="<?= $g['id']; ?>" 
                            <?= ($g['id'] == $cmd['Produit_id']) ? "selected" : ""; ?>>
                            <?= htmlspecialchars($g['nom']); ?>
                        </option>
                    <?php } ?>
                </select>

                <!-- Quantity -->
                <label>Quantité</label>
                <input type="number" name="quantity" class="input" 
                       value="<?= $cmd['quantity']; ?>" required>

                <!-- Total -->
                <label>Total (€)</label>
                <input type="text" name="Total" class="input" 
                       value="<?= $cmd['Total']; ?>" required>

                <!-- Date -->
                <label>Date</label>
                <input type="date" name="Date" class="input"
                       value="<?= $cmd['Date']; ?>" required>

                <br><br>
                <button type="submit" name="update" class="action-btn action-primary">
                    ✔ Mettre à jour
                </button>

                <a href="commande.php">
                    <button type="button" class="action-btn action-secondary">
                        ← Retour
                    </button>
                </a>

            </form>

        </div>

    </section>

</main>

</body>
</html>
