<?php
// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les messages de session
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
$form_errors = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];

// Nettoyer les sessions après utilisation
unset($_SESSION['message'], $_SESSION['error'], $_SESSION['form_errors'], $_SESSION['form_data']);

// Utiliser les données du formulaire si disponibles, sinon celles de la réclamation
$nomClient = $form_data['nomClient'] ?? ($reclamation['nomClient'] ?? '');
$emailClient = $form_data['emailClient'] ?? ($reclamation['emailClient'] ?? '');
$typeReclamation = $form_data['typeReclamation'] ?? ($reclamation['typeReclamation'] ?? '');
$titre = $form_data['titre'] ?? ($reclamation['titre'] ?? '');
$description = $form_data['description'] ?? ($reclamation['description'] ?? '');
$statut = $form_data['statut'] ?? ($reclamation['statut'] ?? 'en_attente');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Réclamation - Backoffice GamingSupport</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }
        
        header {
            background: #2c3e50;
            color: white;
            padding: 20px 0;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .main-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .form-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-section h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e74c3c;
            font-size: 1.8em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        textarea {
            height: 150px;
            resize: vertical;
            font-family: inherit;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #e74c3c;
            color: white;
        }
        
        .btn-primary:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            font-weight: bold;
        }
        
        .required {
            color: #e74c3c;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
                margin: 10px auto;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>GamingSupport - Backoffice</h1>
            <p>Modifier une réclamation</p>
        </header>
        
        <div class="main-content">
            <section class="form-section">
                <h2>Modifier la Réclamation</h2>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php?action=back&method=update&id=<?= $reclamation['id'] ?>">
                    <div class="form-group">
                        <label for="nomClient">Nom <span class="required">*</span></label>
                        <input type="text" id="nomClient" name="nomClient" value="<?= htmlspecialchars($nomClient) ?>" required>
                        <?php if (isset($form_errors['nomClient'])): ?>
                            <div class="error"><?= htmlspecialchars($form_errors['nomClient']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="emailClient">Email <span class="required">*</span></label>
                        <input type="email" id="emailClient" name="emailClient" value="<?= htmlspecialchars($emailClient) ?>" required>
                        <?php if (isset($form_errors['emailClient'])): ?>
                            <div class="error"><?= htmlspecialchars($form_errors['emailClient']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="typeReclamation">Type de Réclamation <span class="required">*</span></label>
                        <select id="typeReclamation" name="typeReclamation" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="technique" <?= $typeReclamation === 'technique' ? 'selected' : '' ?>>Problème technique</option>
                            <option value="commercial" <?= $typeReclamation === 'commercial' ? 'selected' : '' ?>>Question commerciale</option>
                            <option value="administratif" <?= $typeReclamation === 'administratif' ? 'selected' : '' ?>>Problème administratif</option>
                            <option value="produit" <?= $typeReclamation === 'produit' ? 'selected' : '' ?>>Produit défectueux</option>
                            <option value="autre" <?= $typeReclamation === 'autre' ? 'selected' : '' ?>>Autre</option>
                        </select>
                        <?php if (isset($form_errors['typeReclamation'])): ?>
                            <div class="error"><?= htmlspecialchars($form_errors['typeReclamation']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="titre">Sujet <span class="required">*</span></label>
                        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($titre) ?>" required>
                        <?php if (isset($form_errors['titre'])): ?>
                            <div class="error"><?= htmlspecialchars($form_errors['titre']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description <span class="required">*</span></label>
                        <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($description) ?></textarea>
                        <?php if (isset($form_errors['description'])): ?>
                            <div class="error"><?= htmlspecialchars($form_errors['description']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="statut">Statut</label>
                        <select id="statut" name="statut">
                            <option value="en_attente" <?= $statut === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                            <option value="en_cours" <?= $statut === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                            <option value="resolu" <?= $statut === 'resolu' ? 'selected' : '' ?>>Résolu</option>
                            <option value="ferme" <?= $statut === 'ferme' ? 'selected' : '' ?>>Fermé</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Modifier la réclamation</button>
                        <a href="index.php?action=back&method=details&id=<?= $reclamation['id'] ?>" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</body>
</html>