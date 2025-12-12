<?php
require "../../../Crud-Jeux/controller/JeuxController.php";

require "../../controller/CommandeController.php";
require "../../model/Commande.php";

$games = (new JeuxController())->listJeux();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $cmd = new Commande(
        $_POST['produit_id'],
        $_POST['total'],
        $_POST['quantity'],
        date("Y-m-d")
    );

    (new CommandeController())->addCommande($cmd);
    header("Location: commande.php");
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
                    <a href="addcommande.php">
                        <span class="nav-icon">➕</span>
                        <span class="nav-text">ADD COMMANDE</span>
                    </a>
                </li>
                <li class="nav-item">
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
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">◄ COMMANDE ►</h1>
            </div>
            
        </header>
        <section class="dashboard-card recent-games">
    <div class="card-header">
        <h3 class="card-title">◄ ADD COMMANDE ►</h3>
    </div>

    <div class="card-content">
        
        <form method="POST">
            
            <table class="data-table vertical-form">
                <tbody>
                    <tr>
                        <th>GAME</th>
                        <td><select name="produit_id" required>
                                <?php foreach ($games as $g) { ?>
                                    <option value="<?= $g['id']; ?>"><?= $g['nom']; ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>
                    <tr>
                        <th>Quantité</th>
                        <td><input type="number" name="quantity" placeholder="Quantité" class="search-input"></td>
                    </tr>

                    <tr>
                        <th>Total</th>
                        <td><input type="number" name="total" placeholder="total" class="search-input"></td>
                    </tr>

                    

                    <th>
                        
                        
                            
                            <div class="action-grid"><button type="submit" class="action-btn action-primary">
                                <span class="action-icon">💾</span>
                                <span class="action-text"> Valider</span>
                            </button>
                            </div>
                       
                        
                    </th>

                </tbody>
            </table>
        </form>
        
        <section class="quick-actions">
            
            <a href="commande.php" style="text-decoration: none;"><div class="action-grid">
                <button class="action-btn action-primary">
                    <span class="action-icon">🔙</span>
                    <span class="action-text">RETOUR</span>
                </button>
                
            </div></a>
        </section>
    </div>
</section>


    </main>

</body>