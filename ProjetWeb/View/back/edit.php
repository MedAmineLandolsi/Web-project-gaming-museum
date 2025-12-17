<?php
// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) session_start();
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
$form_errors = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['message'], $_SESSION['error'], $_SESSION['form_errors'], $_SESSION['form_data']);
$nomClient = $form_data['nomClient'] ?? ($reclamation['nomClient'] ?? '');
$emailClient = $form_data['emailClient'] ?? ($reclamation['emailClient'] ?? '');
$typeReclamation = $form_data['typeReclamation'] ?? ($reclamation['typeReclamation'] ?? '');
$titre = $form_data['titre'] ?? ($reclamation['titre'] ?? '');
$description = $form_data['description'] ?? ($reclamation['description'] ?? '');
$statut = $form_data['statut'] ?? ($reclamation['statut'] ?? 'en_attente');

require_once __DIR__ . '/../../config/paths.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Réclamation - Backoffice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <!-- UPDATE THIS PATH TO MATCH YOUR PROJECT STRUCTURE -->
    <link rel="stylesheet" href="<?= asset('assets/css/admin-style.css') ?>">
    <!-- OR if admin-style.css is in the same directory as this file, use: -->
    <!-- <link rel="stylesheet" href="admin-style.css"> -->
    <style>
        /* Additional styles specific to edit page */
        .edit-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
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
            margin-bottom: 0.5rem;
        }

        .page-header p {
            font-size: 0.7rem;
            color: var(--text-gray);
        }

        .form-section {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            padding: 2rem;
            box-shadow: 0 5px 30px rgba(0, 255, 65, 0.2);
            animation: startup 0.6s ease-out forwards;
        }

        .section-title {
            color: var(--secondary-purple);
            font-size: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--secondary-purple);
            text-shadow: 0 0 10px var(--secondary-purple);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.8rem;
            font-size: 0.6rem;
            color: var(--primary-green);
            text-transform: uppercase;
            font-weight: bold;
        }

        .required {
            color: var(--danger-red);
            font-size: 0.8rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            background-color: var(--card-bg);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
            background: linear-gradient(
                90deg,
                rgba(0, 255, 65, 0.05) 0%,
                rgba(0, 255, 65, 0.1) 50%,
                rgba(0, 255, 65, 0.05) 100%
            );
            background-size: 200% 100%;
            animation: cyber-load 2s linear infinite;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
            font-family: 'VT323', monospace;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--text-gray);
        }

        .form-group select {
            cursor: pointer;
        }

        .error {
            color: var(--danger-red);
            font-size: 0.6rem;
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: rgba(255, 0, 85, 0.1);
            border-left: 3px solid var(--danger-red);
            display: block;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
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
            justify-content: center;
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

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--text-gray);
            color: var(--text-gray);
        }

        .btn-secondary:hover {
            background-color: var(--text-gray);
            color: var(--darker-bg);
        }

        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.6rem;
            border-left: 4px solid;
            animation: startup 0.6s ease-out forwards;
        }

        .alert-success {
            background: rgba(0, 255, 65, 0.1);
            color: var(--success-green);
            border-left-color: var(--success-green);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.2);
        }

        .alert-danger {
            background: rgba(255, 0, 85, 0.1);
            color: var(--danger-red);
            border-left-color: var(--danger-red);
            box-shadow: 0 0 20px rgba(255, 0, 85, 0.2);
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
            .edit-container {
                padding: 1rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .page-header h1 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="page-header">
            <h1>⚙️ GAMING SUPPORT - BACKOFFICE</h1>
            <p>Modifier une réclamation</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">✗ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h2 class="section-title">✏️ MODIFIER LA RÉCLAMATION</h2>

            <form method="POST" action="index.php?action=back&method=update&id=<?= $reclamation['id'] ?>" novalidate data-validate="reclamation">
                <div class="form-group">
                    <label for="nomClient">👤 Nom <span class="required">*</span></label>
                    <input type="text" id="nomClient" name="nomClient" value="<?= htmlspecialchars($nomClient) ?>" placeholder="Entrez le nom du client">
                    <?php if (isset($form_errors['nomClient'])): ?>
                        <div class="error-message is-visible" id="nomError">⚠️ <?= htmlspecialchars($form_errors['nomClient']) ?></div>
                    <?php endif; ?>
                    <?php if (!isset($form_errors['nomClient'])): ?>
                        <div class="error-message" id="nomError">⚠️ Le nom est obligatoire (min 2 caractères)</div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="emailClient">📧 Email <span class="required">*</span></label>
                    <input type="text" id="emailClient" name="emailClient" value="<?= htmlspecialchars($emailClient) ?>" placeholder="email@exemple.com">
                    <?php if (isset($form_errors['emailClient'])): ?>
                        <div class="error-message is-visible" id="emailError">⚠️ <?= htmlspecialchars($form_errors['emailClient']) ?></div>
                    <?php endif; ?>
                    <?php if (!isset($form_errors['emailClient'])): ?>
                        <div class="error-message" id="emailError">⚠️ Email invalide</div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="typeReclamation">🏷️ Type de Réclamation <span class="required">*</span></label>
                    <select id="typeReclamation" name="typeReclamation">
                        <option value="">→ Sélectionnez un type</option>
                        <option value="technique" <?= $typeReclamation === 'technique' ? 'selected' : '' ?>>⚙️ Problème technique</option>
                        <option value="commercial" <?= $typeReclamation === 'commercial' ? 'selected' : '' ?>>💼 Question commerciale</option>
                        <option value="administratif" <?= $typeReclamation === 'administratif' ? 'selected' : '' ?>>📋 Problème administratif</option>
                        <option value="produit" <?= $typeReclamation === 'produit' ? 'selected' : '' ?>>📦 Produit défectueux</option>
                        <option value="autre" <?= $typeReclamation === 'autre' ? 'selected' : '' ?>>❓ Autre</option>
                    </select>
                    <?php if (isset($form_errors['typeReclamation'])): ?>
                        <div class="error-message is-visible" id="typeError">⚠️ <?= htmlspecialchars($form_errors['typeReclamation']) ?></div>
                    <?php endif; ?>
                    <?php if (!isset($form_errors['typeReclamation'])): ?>
                        <div class="error-message" id="typeError">⚠️ Veuillez sélectionner un type</div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="titre">📝 Sujet <span class="required">*</span></label>
                    <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($titre) ?>" placeholder="Titre de la réclamation">
                    <?php if (isset($form_errors['titre'])): ?>
                        <div class="error-message is-visible" id="titreError">⚠️ <?= htmlspecialchars($form_errors['titre']) ?></div>
                    <?php endif; ?>
                    <?php if (!isset($form_errors['titre'])): ?>
                        <div class="error-message" id="titreError">⚠️ Le titre doit contenir au moins 5 caractères</div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="description">📄 Description <span class="required">*</span></label>
                    <textarea id="description" name="description" rows="8" placeholder="Décrivez le problème en détail..."><?= htmlspecialchars($description) ?></textarea>
                    <?php if (isset($form_errors['description'])): ?>
                        <div class="error-message is-visible" id="descriptionError">⚠️ <?= htmlspecialchars($form_errors['description']) ?></div>
                    <?php endif; ?>
                    <?php if (!isset($form_errors['description'])): ?>
                        <div class="error-message" id="descriptionError">⚠️ La description doit contenir au moins 10 caractères</div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="statut">📊 Statut</label>
                    <select id="statut" name="statut">
                        <option value="en_attente" <?= $statut === 'en_attente' ? 'selected' : '' ?>>⏳ En attente</option>
                        <option value="en_cours" <?= $statut === 'en_cours' ? 'selected' : '' ?>>🔄 En cours</option>
                        <option value="resolu" <?= $statut === 'resolu' ? 'selected' : '' ?>>✅ Résolu</option>
                        <option value="ferme" <?= $statut === 'ferme' ? 'selected' : '' ?>>🔒 Fermé</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        💾 Modifier la réclamation
                    </button>
                    <a href="index.php?action=back&method=details&id=<?= $reclamation['id'] ?>" class="btn btn-secondary">
                        ↩️ Annuler
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
