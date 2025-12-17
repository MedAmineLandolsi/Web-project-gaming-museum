<?php
// voir_reclamation.php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit;
}

// Données exemple (à remplacer par votre base de données)
$reclamations = [
    1 => [
        'date' => '20/11/2025',
        'heure' => '19:04',
        'client' => 'Nourhene',
        'email' => 'nourheneklai@gmail.com',
        'type' => 'retard de livraison',
        'titre' => 'service livraison indisponible',
        'description' => 'les services de livraisons mauvaises',
        'statut' => 'EN ATTENTE'
    ]
];

$id = $_GET['id'] ?? 1;
$reclamation = $reclamations[$id] ?? null;

if (!$reclamation) {
    echo "Réclamation non trouvée";
    exit;
}

require_once __DIR__ . '/../../config/paths.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Réclamation - Gaming Support</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <!-- UPDATE THIS PATH TO MATCH YOUR PROJECT STRUCTURE -->
    <link rel="stylesheet" href="<?= asset('assets/css/admin-style.css') ?>">
    <!-- OR if admin-style.css is in the same directory as this file, use: -->
    <!-- <link rel="stylesheet" href="admin-style.css"> -->
    <style>
        body {
            padding: 2rem;
        }

        .reclamation-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .page-header {
            background: linear-gradient(135deg, var(--sidebar-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 5px 30px rgba(0, 255, 65, 0.2);
        }

        .page-header h1 {
            font-size: 1.5rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .navigation {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .nav-link {
            padding: 0.8rem 1.5rem;
            background: transparent;
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            text-decoration: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            background: var(--primary-green);
            color: var(--darker-bg);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
            transform: translateY(-2px);
        }

        .details-card {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            padding: 2rem;
            box-shadow: 0 5px 30px rgba(0, 255, 65, 0.2);
            animation: startup 0.6s ease-out forwards;
        }

        .card-title {
            font-size: 1rem;
            color: var(--secondary-purple);
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--secondary-purple);
            text-shadow: 0 0 10px var(--secondary-purple);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .info-label {
            font-size: 0.6rem;
            color: var(--primary-green);
            text-transform: uppercase;
            font-weight: bold;
        }

        .info-value {
            font-size: 0.8rem;
            color: var(--text-white);
            font-family: 'VT323', monospace;
            padding: 0.8rem;
            background: rgba(0, 255, 65, 0.05);
            border-left: 3px solid var(--primary-green);
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(255, 149, 0, 0.2);
            border: 2px solid var(--warning-orange);
            color: var(--warning-orange);
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
            font-family: 'Press Start 2P', cursive;
        }

        @keyframes startup {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .navigation {
                flex-direction: column;
            }

            .nav-link {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="reclamation-container">
        <div class="page-header">
            <h1>📋 DÉTAILS DE LA RÉCLAMATION</h1>
        </div>

        <nav class="navigation">
            <a href="accueil.php" class="nav-link">
                🏠 Accueil
            </a>
            <a href="historique_reclamations.php" class="nav-link">
                ↩️ Retour à l'historique
            </a>
        </nav>

        <div class="details-card">
            <h2 class="card-title">⚡ INFORMATIONS DE LA RÉCLAMATION</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">📅 Date/Heure</span>
                    <div class="info-value"><?= $reclamation['date'] ?> <?= $reclamation['heure'] ?></div>
                </div>

                <div class="info-item">
                    <span class="info-label">👤 Client</span>
                    <div class="info-value"><?= $reclamation['client'] ?></div>
                </div>

                <div class="info-item">
                    <span class="info-label">📧 Email</span>
                    <div class="info-value"><?= $reclamation['email'] ?></div>
                </div>

                <div class="info-item">
                    <span class="info-label">🏷️ Type</span>
                    <div class="info-value"><?= $reclamation['type'] ?></div>
                </div>

                <div class="info-item">
                    <span class="info-label">📝 Titre</span>
                    <div class="info-value"><?= $reclamation['titre'] ?></div>
                </div>

                <div class="info-item">
                    <span class="info-label">📊 Statut</span>
                    <div class="info-value">
                        <span class="status-badge"><?= $reclamation['statut'] ?></span>
                    </div>
                </div>
            </div>

            <div style="margin-top: 2rem;">
                <div class="info-item">
                    <span class="info-label">📄 Description</span>
                    <div class="info-value" style="min-height: 100px; line-height: 1.8;">
                        <?= nl2br(htmlspecialchars($reclamation['description'])) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- UPDATE THIS PATH TO MATCH YOUR PROJECT STRUCTURE -->
    <script src="<?= asset('assets/js/admin-script.js') ?>"></script>
    <!-- OR if admin-script.js is in the same directory as this file, use: -->
    <!-- <script src="admin-script.js"></script> -->
</body>
</html>
