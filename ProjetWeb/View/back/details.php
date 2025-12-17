<?php
// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) session_start();
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

require_once __DIR__ . '/../../config/paths.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Réclamation - Backoffice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <!-- UPDATE THIS PATH TO MATCH YOUR PROJECT STRUCTURE -->
    <link rel="stylesheet" href="<?= asset('assets/css/admin-style.css') ?>">
    <!-- OR if admin-style.css is in the same directory as this file, use: -->
    <!-- <link rel="stylesheet" href="admin-style.css"> -->
    <style>
        /* Additional styles specific to details page */
        .details-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .details-card {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 30px rgba(0, 255, 65, 0.2);
        }

        .details-header {
            border-bottom: 2px solid var(--primary-green);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .details-title {
            font-size: 1.2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 0.5rem;
        }

        .details-subtitle {
            font-size: 0.6rem;
            color: var(--text-gray);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .info-label {
            font-size: 0.6rem;
            color: var(--secondary-purple);
            text-transform: uppercase;
            font-weight: bold;
        }

        .info-value {
            font-size: 0.7rem;
            color: var(--text-white);
            font-family: 'VT323', monospace;
            padding: 0.8rem;
            background: rgba(0, 255, 65, 0.05);
            border-left: 3px solid var(--primary-green);
        }

        .description-section {
            margin: 2rem 0;
        }

        .description-content {
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            padding: 1.5rem;
            font-family: 'VT323', monospace;
            font-size: 1rem;
            color: var(--text-light-gray);
            line-height: 1.8;
            min-height: 150px;
        }

        .response-section {
            background: linear-gradient(135deg, rgba(189, 0, 255, 0.1), rgba(0, 255, 65, 0.05));
            border: 2px solid var(--secondary-purple);
            padding: 2rem;
            margin: 2rem 0;
        }

        .response-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .response-icon {
            font-size: 2rem;
        }

        .response-title {
            font-size: 0.8rem;
            color: var(--secondary-purple);
            text-shadow: 0 0 10px var(--secondary-purple);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group textarea {
            width: 100%;
            min-height: 150px;
            padding: 1rem;
            background-color: var(--card-bg);
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            resize: vertical;
            transition: all 0.3s;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary-purple);
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.3);
            background: linear-gradient(
                90deg,
                rgba(0, 255, 65, 0.05) 0%,
                rgba(0, 255, 65, 0.1) 50%,
                rgba(0, 255, 65, 0.05) 100%
            );
            background-size: 200% 100%;
            animation: cyber-load 2s linear infinite;
        }

        .form-group textarea::placeholder {
            color: var(--text-gray);
        }

        .error-message {
            display: none;
            color: var(--danger-red);
            font-size: 0.6rem;
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: rgba(255, 0, 85, 0.1);
            border-left: 3px solid var(--danger-red);
        }

        .input-error {
            border-color: var(--danger-red) !important;
            box-shadow: 0 0 20px rgba(255, 0, 85, 0.3) !important;
        }

        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem 2rem;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            font-weight: bold;
        }

        .btn-primary:hover {
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.6);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: transparent;
            border: 2px solid var(--danger-red);
            color: var(--danger-red);
        }

        .btn-danger:hover {
            background-color: var(--danger-red);
            color: var(--text-white);
            box-shadow: 0 0 20px rgba(255, 0, 85, 0.5);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--text-gray);
            color: var(--text-gray);
        }

        .btn-secondary:hover {
            background-color: var(--text-gray);
            color: var(--darker-bg);
        }

        .btn-warning {
            background: transparent;
            border: 2px solid var(--warning-orange);
            color: var(--warning-orange);
        }

        .btn-warning:hover {
            background-color: var(--warning-orange);
            color: var(--darker-bg);
            box-shadow: 0 0 20px rgba(255, 149, 0, 0.5);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0;
            font-size: 0.6rem;
            border-left: 4px solid;
            background: rgba(0, 255, 65, 0.05);
            animation: startup 0.6s ease-out forwards;
        }

        .alert-success {
            color: var(--success-green);
            border-left-color: var(--success-green);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.2);
        }

        .alert-danger {
            color: var(--danger-red);
            border-left-color: var(--danger-red);
            background: rgba(255, 0, 85, 0.05);
            box-shadow: 0 0 20px rgba(255, 0, 85, 0.2);
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-size: 0.6rem;
            border-radius: 0;
            border: 2px solid;
            text-transform: uppercase;
            font-weight: bold;
        }

        .status-pending {
            color: var(--warning-orange);
            border-color: var(--warning-orange);
            background: rgba(255, 149, 0, 0.1);
        }

        .status-in-progress {
            color: var(--secondary-purple);
            border-color: var(--secondary-purple);
            background: rgba(189, 0, 255, 0.1);
        }

        .status-resolved {
            color: var(--success-green);
            border-color: var(--success-green);
            background: rgba(0, 255, 65, 0.1);
        }

        .status-closed {
            color: var(--text-gray);
            border-color: var(--text-gray);
            background: rgba(136, 136, 136, 0.1);
        }

        @keyframes cyber-load {
            0% {
                background-position: -100% 0;
            }
            100% {
                background-position: 200% 0;
            }
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
            .info-grid {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="details-container" style="padding: 2rem;">
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="details-card">
            <div class="details-header">
                <h1 class="details-title">📋 DÉTAILS DE LA RÉCLAMATION #<?= htmlspecialchars($reclamation['id']) ?></h1>
                <p class="details-subtitle">Informations complètes et gestion de la réclamation</p>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">📅 Date de création</span>
                    <div class="info-value"><?= isset($reclamation['date_creation']) ? date('d/m/Y H:i', strtotime($reclamation['date_creation'])) : 'N/A' ?></div>
                </div>

                <div class="info-item">
                    <span class="info-label">👤 Nom du client</span>
                    <div class="info-value"><?= htmlspecialchars($reclamation['nomClient']) ?></div>
                </div>

                <div class="info-item">
                    <span class="info-label">📧 Email</span>
                    <div class="info-value"><?= htmlspecialchars($reclamation['emailClient']) ?></div>
                </div>

                <div class="info-item">
                    <span class="info-label">🏷️ Type de réclamation</span>
                    <div class="info-value"><?= htmlspecialchars($reclamation['typeReclamation']) ?></div>
                </div>

                <div class="info-item">
                    <span class="info-label">📝 Titre</span>
                    <div class="info-value"><?= htmlspecialchars($reclamation['titre']) ?></div>
                </div>

                <div class="info-item">
                    <span class="info-label">📊 Statut</span>
                    <div class="info-value">
                        <?php
                        $statutClass = 'status-pending';
                        $statutText = 'EN ATTENTE';
                        
                        if (isset($reclamation['statut'])) {
                            switch ($reclamation['statut']) {
                                case 'en_cours':
                                    $statutClass = 'status-in-progress';
                                    $statutText = 'EN COURS';
                                    break;
                                case 'resolu':
                                    $statutClass = 'status-resolved';
                                    $statutText = 'RÉSOLU';
                                    break;
                                case 'ferme':
                                    $statutClass = 'status-closed';
                                    $statutText = 'FERMÉ';
                                    break;
                            }
                        }
                        ?>
                        <span class="status-badge <?= $statutClass ?>"><?= $statutText ?></span>
                    </div>
                </div>
            </div>

            <div class="description-section">
                <div class="info-item">
                    <span class="info-label">📄 Description complète</span>
                    <div class="description-content"><?= nl2br(htmlspecialchars($reclamation['description'])) ?></div>
                </div>
            </div>
        </div>

        <!-- Response Section -->
        <div class="response-section" id="reponse">
            <div class="response-header">
                <span class="response-icon">💬</span>
                <h2 class="response-title"><?= $reponse ? 'MODIFIER LA RÉPONSE' : 'NOUVELLE RÉPONSE' ?></h2>
            </div>

            <form method="POST" action="index.php?action=back&method=addReponse" name="reponseForm" novalidate data-validate="reponse">
                <input type="hidden" name="reclamationId" value="<?= htmlspecialchars($reclamation['id']) ?>" />
                
                <div class="form-group">
                    <span class="info-label"><?= $reponse ? 'Modifier la réponse' : 'Nouvelle réponse' ?> *</span>
                    <textarea name="message" id="messageReponse" placeholder="Tapez votre réponse ici..."><?= $reponse ? htmlspecialchars($reponse['message']) : '' ?></textarea>
                    <div class="error-message" id="messageError">⚠️ La réponse doit contenir au moins 5 caractères</div>
                </div>
                
                <div class="actions">
                    <button type="submit" class="btn btn-primary">
                        <?= $reponse ? '✏️ Modifier la Réponse' : '💬 Envoyer la Réponse' ?>
                    </button>
                    <a href="index.php?action=back&method=edit&id=<?= $reclamation['id'] ?>" class="btn btn-warning">
                        ✏️ Modifier la réclamation
                    </a>
                    <a href="index.php?action=back&method=delete&id=<?= htmlspecialchars($reclamation['id']) ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')">
                        🗑️ Supprimer
                    </a>
                    <a href="index.php?action=back" class="btn btn-secondary">
                        ↩️ Retour au tableau de bord
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- UPDATE THIS PATH TO MATCH YOUR PROJECT STRUCTURE -->
    <script src="<?= asset('assets/js/admin-script.js') ?>"></script>
    <script src="<?= asset('assets/js/forms-validation.js') ?>"></script>
    <!-- OR if admin-script.js is in the same directory as this file, use: -->
    <!-- <script src="admin-script.js"></script> -->
</body>
</html>
