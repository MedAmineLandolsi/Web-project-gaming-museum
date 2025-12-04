<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Détails Réclamation - Gaming Support</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/ProjetWeb/assets/css/front.css">
</head>
<body>
    <div class="header">
        <nav class="nav">
            <div class="logo"><img src="/ProjetWeb/assets/img/logo-lv.png" alt="Logo LV"></div>
            <ul class="nav-links">
                <li><a href="index.php?action=front">Retour</a></li>
                <li><a href="index.php?action=back">Espace Admin</a></li>
                <li><a href="index.php?action=front&method=edit&id=<?= $reclamation['id'] ?>" class="admin-btn">Modifier</a></li>
            </ul>
        </nav>
    </div>
    <div class="container">
        <div class="main-content">
            <?php if ($reclamation): ?>
            <h1 class="page-title">📋 Détails de la Réclamation</h1>
            <div class="card">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Date et Heure</span>
                        <div class="info-value"><?= isset($reclamation['date_creation']) ? date('d/m/Y', strtotime($reclamation['date_creation'])) . ' à ' . date('H:i:s', strtotime($reclamation['date_creation'])) : 'Date/heure non disponible' ?></div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Client</span>
                        <div class="info-value"><?= htmlspecialchars($reclamation['nomClient']) ?></div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <div class="info-value"><?= htmlspecialchars($reclamation['emailClient']) ?></div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Type</span>
                        <div class="info-value"><?= htmlspecialchars($reclamation['typeReclamation']) ?></div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Statut</span>
                        <div class="info-value">
                            <span class="status <?= $reponse ? 'status-resolved' : 'status-pending' ?>">
                                <?= $reponse ? 'Répondu' : 'En attente' ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <span class="info-label">Titre</span>
                    <div class="info-value" style="font-size: 1.2rem; font-weight: bold;">
                        <?= htmlspecialchars($reclamation['titre']) ?>
                    </div>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <span class="info-label">Description</span>
                    <div class="info-value" style="line-height: 1.8;">
                        <?= nl2br(htmlspecialchars($reclamation['description'])) ?>
                    </div>
                </div>
            </div>
            <div class="card <?= $reponse ? 'response-section' : '' ?>" style="margin-top:2.3rem;">
                <?php if ($reponse): ?>
                <div class="admin-info">
                    Réponse envoyée le <strong><?= htmlspecialchars($reponse['dateReponse']) ?></strong> 
                    à <strong><?= htmlspecialchars($reponse['heureReponse']) ?></strong>
                    par <strong><?= htmlspecialchars($reponse['adminName'] ?? 'Administrateur') ?></strong>
                </div>
                <div class="info-item">
                    <span class="info-label">Message</span>
                    <div class="info-value" style="line-height: 1.8; font-size: 1.1rem;">
                        <?= nl2br(htmlspecialchars($reponse['message'])) ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="no-response">
                    <div class="icon">🕐</div>
                    <h3 style="color: var(--accent-pink); margin-bottom: 1rem;">En attente de réponse</h3>
                    <p>Votre réclamation est en cours de traitement par notre équipe.</p>
                    <p>Nous vous répondrons dans les plus brefs délais.</p>
                </div>
                <?php endif; ?>
            </div>
            <div style="text-align: center; margin-top: 2.8rem;">
                <a href="index.php?action=front" class="btn btn-primary"><span class="btn-icon">🏠</span>Retour à l'accueil</a>
                <a href="index.php?action=front&method=edit&id=<?= $reclamation['id'] ?>" class="btn btn-secondary"><span class="btn-icon">✏️</span>Modifier</a>
            </div>
            <?php else: ?>
            <div class="aucun-resultat">❌ Réclamation non trouvée<br>La réclamation que vous recherchez n'existe pas ou a été supprimée.</div>
            <div style="text-align: center; margin-top:2.2rem;">
                <a href="index.php?action=front" class="btn btn-primary"><span class="btn-icon">🏠</span>Retour à l'accueil</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer-bar-animation"></div>
</body>
</html>