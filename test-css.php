<?php
/**
 * Fichier de test pour vérifier que les CSS sont bien chargés
 * Accédez à : http://localhost/ProjetWeb/test-css.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CSS - Gaming Support</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/ProjetWeb/assets/css/admin.css">
    <style>
        body {
            padding: 2rem;
        }
        .test-box {
            background: var(--card-bg);
            border: 2px solid var(--primary-green);
            padding: 2rem;
            margin: 2rem 0;
        }
        .test-title {
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }
        .status-ok {
            color: var(--success-green);
        }
        .status-error {
            color: var(--danger-red);
        }
    </style>
</head>
<body>
    <h1 class="test-title">Test de chargement des CSS</h1>
    
    <div class="test-box">
        <h2>Vérification des fichiers CSS</h2>
        <ul>
            <li>
                admin.css: 
                <?php if (file_exists(__DIR__ . '/assets/css/admin.css')): ?>
                    <span class="status-ok">✓ Trouvé</span>
                <?php else: ?>
                    <span class="status-error">✗ Non trouvé</span>
                <?php endif; ?>
            </li>
            <li>
                front.css: 
                <?php if (file_exists(__DIR__ . '/assets/css/front.css')): ?>
                    <span class="status-ok">✓ Trouvé</span>
                <?php else: ?>
                    <span class="status-error">✗ Non trouvé</span>
                <?php endif; ?>
            </li>
            <li>
                admin.js: 
                <?php if (file_exists(__DIR__ . '/assets/js/admin.js')): ?>
                    <span class="status-ok">✓ Trouvé</span>
                <?php else: ?>
                    <span class="status-error">✗ Non trouvé</span>
                <?php endif; ?>
            </li>
            <li>
                front.js: 
                <?php if (file_exists(__DIR__ . '/assets/js/front.js')): ?>
                    <span class="status-ok">✓ Trouvé</span>
                <?php else: ?>
                    <span class="status-error">✗ Non trouvé</span>
                <?php endif; ?>
            </li>
        </ul>
    </div>
    
    <div class="test-box">
        <h2>Test visuel</h2>
        <p>Si vous voyez ce texte avec un style cyberpunk (fond sombre, texte vert néon), le CSS fonctionne !</p>
        <button class="btn-primary">Bouton de test</button>
    </div>
    
    <div class="test-box">
        <h2>Chemins à utiliser</h2>
        <p><strong>Chemin absolu (recommandé):</strong> <code>/ProjetWeb/assets/css/admin.css</code></p>
        <p><strong>Chemin relatif depuis View/back/:</strong> <code>../../assets/css/admin.css</code></p>
        <p><strong>Racine du projet:</strong> <code><?= __DIR__ ?></code></p>
    </div>
    
    <a href="index.php?action=back" class="btn-primary">Retour à l'admin</a>
    <a href="index.php?action=front" class="btn-secondary">Retour au front</a>
</body>
</html>

