<?php
// Helper pour les chemins - depuis View/front/ vers la racine
$basePath = '../../';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gaming Support - Réclamations</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/ProjetWeb/assets/css/front.css">
</head>
<body>
    <div class="header">
        <nav class="nav">
            <div class="logo">
                <img src="/ProjetWeb/assets/img/logo-lv.png" alt="Logo LV">
                
            </div>
            <ul class="nav-links">
                <li><a href="#" class="active">Accueil</a></li>
                <li><a href="#">Aide</a></li>
                <li><a href="index.php?action=back" class="admin-btn">Espace Admin</a></li>
                <!-- Dans votre menu existant -->
 <a href="View/back/historique_reclamations.php">Historique des Réclamations</a>
            </ul>
        </nav>
    </div>

    <div id="notification" class="notification"></div>

    <div class="container">
        <div class="main-content">
            <h1 class="page-title">📩 Soumettre une Réclamation</h1>

            <div class="form-container">
                <form method="POST" action="index.php?action=front&method=add" name="reclamationForm" id="reclamationForm" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label class="form-label">Votre Nom *</label>
                        <input type="text" name="nomClient" id="nomClient" placeholder="Entrez votre nom complet" />
                        <div class="error-message" id="nomError">Le nom est requis et doit contenir au moins 2 caractères</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Votre Email *</label>
                        <input type="text" name="emailClient" id="emailClient" placeholder="Entrez votre email" />
                        <div class="error-message" id="emailError">Veuillez entrer une adresse email valide</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Type de Réclamation *</label>
                        <select name="typeReclamation" id="typeReclamation">
                            <option value="">Sélectionnez un type</option>
                            <option value="problème de commande">Problème de commande</option>
                            <option value="produit défectueux">Produit défectueux</option>
                            <option value="retard de livraison">Retard de livraison</option>
                            <option value="service client">Service client</option>
                            <option value="autre">Autre</option>
                        </select>
                        <div class="error-message" id="typeError">Veuillez sélectionner un type de réclamation</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="titre" id="titre" placeholder="Titre de votre réclamation" />
                        <div class="error-message" id="titreError">Le titre est requis et doit contenir au moins 5 caractères</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description *</label>
                        <textarea name="description" id="description" placeholder="Décrivez votre problème en détail..."></textarea>
                        <div class="error-message" id="descriptionError">La description est requise et doit contenir au moins 10 caractères</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><span class="btn-icon">➕</span>Envoyer la Réclamation</button>
                </form>
            </div>

            <h2 class="page-title">📋 Historique des Réclamations</h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Titre</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reclamations)): ?>
                        <tr>
                            <td colspan="6" class="aucun-resultat">Aucune réclamation pour le moment.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($reclamations as $r): 
                                $reponse = null;
                                foreach ($reponses as $rep) {
                                    if ($rep['reclamationId'] == $r['id']) {
                                        $reponse = $rep;
                                        break;
                                    }
                                }
                                $statut = $reponse ? 'Répondu' : 'En attente';
                                $statutClass = $reponse ? 'status-resolved' : 'status-pending';
                            ?>
                            <tr>
                                <td><strong><?= date('d/m/Y', strtotime($r['date_creation'])) ?></strong><br /><small style="color: var(--gray);"> <?= date('H:i:s', strtotime($r['date_creation'])) ?> </small></td>
                                <td><?= htmlspecialchars($r['nomClient']) ?></td>
                                <td><?= htmlspecialchars($r['typeReclamation']) ?></td>
                                <td><strong><?= htmlspecialchars($r['titre']) ?></strong><br /><small style="color: var(--gray);"> <?= htmlspecialchars(substr($r['description'], 0, 50)) ?><?= strlen($r['description']) > 50 ? '...' : '' ?> </small></td>
                                <td><span class="status <?= $statutClass ?>"> <?= $statut ?> </span></td>
                                <td><button class="btn btn-secondary btn-small" onclick="voirDetails(<?= $r['id'] ?>)"><span class="btn-icon">👁️</span>Voir</button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>© 2024 GamingSupport - Plateforme de Gestion des Réclamations</p>
        </div>
    </div>

    <div class="footer-bar-animation"></div>
    <script src="/ProjetWeb/assets/js/front.js"></script>
    <script type="text/javascript">
    // Fonctions de validation
    function validateEmail(email) {
        var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        return re.test(email);
    }

    function validateName(name) {
        return name.length >= 2 && /^[a-zA-ZÀ-ÿ\s\-]+$/.test(name);
    }

    function validateText(text, minLength) {
        return text.length >= minLength;
    }

    function showError(fieldId, errorId, show) {
        var field = document.getElementById(fieldId);
        var error = document.getElementById(errorId);
        
        if (show) {
            field.className = 'input-error';
            error.style.display = 'block';
        } else {
            field.className = '';
            error.style.display = 'none';
        }
    }

    // Validation en temps réel
    document.getElementById('nomClient').onblur = function() {
        var isValid = validateName(this.value);
        showError('nomClient', 'nomError', !isValid);
    };

    document.getElementById('emailClient').onblur = function() {
        var isValid = validateEmail(this.value);
        showError('emailClient', 'emailError', !isValid);
    };

    document.getElementById('typeReclamation').onchange = function() {
        var isValid = this.value !== '';
        showError('typeReclamation', 'typeError', !isValid);
    };

    document.getElementById('titre').onblur = function() {
        var isValid = validateText(this.value, 5);
        showError('titre', 'titreError', !isValid);
    };

    document.getElementById('description').onblur = function() {
        var isValid = validateText(this.value, 10);
        showError('description', 'descriptionError', !isValid);
    };

    // Validation du formulaire
    function validateForm() {
        var nom = document.getElementById('nomClient').value;
        var email = document.getElementById('emailClient').value;
        var type = document.getElementById('typeReclamation').value;
        var titre = document.getElementById('titre').value;
        var description = document.getElementById('description').value;
        
        var isValid = true;
        
        if (!validateName(nom)) {
            showError('nomClient', 'nomError', true);
            isValid = false;
        }
        
        if (!validateEmail(email)) {
            showError('emailClient', 'emailError', true);
            isValid = false;
        }
        
        if (type === '') {
            showError('typeReclamation', 'typeError', true);
            isValid = false;
        }
        
        if (!validateText(titre, 5)) {
            showError('titre', 'titreError', true);
            isValid = false;
        }
        
        if (!validateText(description, 10)) {
            showError('description', 'descriptionError', true);
            isValid = false;
        }
        
        if (!isValid) {
            showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
            return false;
        }
        
        return true;
    }

    function showNotification(message, type) {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.className = 'notification ' + type;
        notification.classList.add('show');
        
        setTimeout(function() {
            notification.classList.remove('show');
        }, 3000);
    }

    function voirDetails(id) {
        window.location.href = 'index.php?action=front&method=details&id=' + id;
    }

    // Afficher notification si paramètre présent dans l'URL
    const urlParams = window.location.search;
    if (urlParams.indexOf('success=1') !== -1) {
        showNotification('Réclamation envoyée avec succès !', 'success');
    }
    </script>
</body>
</html>