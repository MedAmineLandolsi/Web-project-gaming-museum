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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Réclamation </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/ProjetWeb/assets/css/front.css">
</head>
<body>
    <div class="header">
        <nav class="nav">
            <div class="logo"><img src="/ProjetWeb/assets/img/logo-lv.png" alt="Logo LV">GamingSupport</div>
            <ul class="nav-links">
                <li><a href="index.php?action=front">Accueil</a></li>
                <li><a href="index.php?action=back">Espace Admin</a></li>
            </ul>
        </nav>
    </div>
    <div class="container">
        <div class="main-content">
            <h1 class="page-title">✏️ Modifier la Réclamation</h1>
            <div class="form-container">
                <?php if ($message): ?>
                    <div class="notification success show" style="position:static;margin-bottom:1rem;display:block;"> <?= htmlspecialchars($message) ?> </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="notification error show" style="position:static;margin-bottom:1rem;display:block;"> <?= htmlspecialchars($error) ?> </div>
                <?php endif; ?>
                <form method="POST" action="index.php?action=front&method=update&id=<?= $reclamation['id'] ?>">
                    <div class="form-group">
                        <label class="form-label" for="nomClient">Nom *</label>
                        <input type="text" id="nomClient" name="nomClient" value="<?= htmlspecialchars($nomClient) ?>" required>
                        <?php if (isset($form_errors['nomClient'])): ?>
                            <div class="error-message" style="display:block;"> <?= htmlspecialchars($form_errors['nomClient']) ?> </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="emailClient">Email *</label>
                        <input type="email" id="emailClient" name="emailClient" value="<?= htmlspecialchars($emailClient) ?>" required>
                        <?php if (isset($form_errors['emailClient'])): ?>
                            <div class="error-message" style="display:block;"> <?= htmlspecialchars($form_errors['emailClient']) ?> </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="typeReclamation">Type de Réclamation *</label>
                        <select id="typeReclamation" name="typeReclamation" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="problème de commande" <?= $typeReclamation === 'problème de commande' ? 'selected' : '' ?>>Problème de commande</option>
                            <option value="produit défectueux" <?= $typeReclamation === 'produit défectueux' ? 'selected' : '' ?>>Produit défectueux</option>
                            <option value="retard de livraison" <?= $typeReclamation === 'retard de livraison' ? 'selected' : '' ?>>Retard de livraison</option>
                            <option value="service client" <?= $typeReclamation === 'service client' ? 'selected' : '' ?>>Service client</option>
                            <option value="autre" <?= $typeReclamation === 'autre' ? 'selected' : '' ?>>Autre</option>
                        </select>
                        <?php if (isset($form_errors['typeReclamation'])): ?>
                            <div class="error-message" style="display:block;"> <?= htmlspecialchars($form_errors['typeReclamation']) ?> </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="titre">Sujet *</label>
                        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($titre) ?>" required>
                        <?php if (isset($form_errors['titre'])): ?>
                            <div class="error-message" style="display:block;"> <?= htmlspecialchars($form_errors['titre']) ?> </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description *</label>
                        <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($description) ?></textarea>
                        <?php if (isset($form_errors['description'])): ?>
                            <div class="error-message" style="display:block;"> <?= htmlspecialchars($form_errors['description']) ?> </div>
                        <?php endif; ?>
                    </div>
                    <div style="display: flex; gap: 1.3rem; justify-content:flex-end; margin-top:2.2rem;">
                        <button type="submit" class="btn btn-primary"><span class="btn-icon">✏️</span>Valider la modification</button>
                        <a href="index.php?action=front&method=details&id=<?= $reclamation['id'] ?>" class="btn btn-secondary"><span class="btn-icon">↩️</span>Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="footer-bar-animation"></div>
</body>
</html>