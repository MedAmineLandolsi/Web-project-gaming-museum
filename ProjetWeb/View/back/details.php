<!-- Ajouter cette partie dans le formulaire de réponse -->
<form method="POST" action="index.php?action=back&method=addReponse" name="reponseForm" onsubmit="return validateReponse()">
    <input type="hidden" name="reclamationId" value="<?= htmlspecialchars($reclamation['id']) ?>" />
    
    <div class="form-group">
        <span class="info-label"><?= $reponse ? 'Modifier la réponse' : 'Nouvelle réponse' ?> *</span>
        <textarea name="message" id="messageReponse" placeholder="Tapez votre réponse ici..."><?= $reponse ? htmlspecialchars($reponse['message']) : '' ?></textarea>
        <div class="error-message" id="messageError">La réponse doit contenir au moins 5 caractères</div>
    </div>
    
    <div class="actions">
        <button type="submit" class="btn btn-primary">
            <?= $reponse ? '✏️ Modifier la Réponse' : '💬 Envoyer la Réponse' ?>
        </button>
        <a href="index.php?action=back&method=delete&id=<?= htmlspecialchars($reclamation['id']) ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')">
            🗑️ Supprimer
        </a>
        <a href="index.php?action=back" class="btn btn-secondary">
            ↩️ Retour
            <a href="index.php?action=back&method=edit&id=<?= $reclamation['id'] ?>" class="btn btn-warning">Modifier</a>
        </a>
    </div>
</form>

<script type="text/javascript">
// Validation de la réponse
function validateReponse() {
    var message = document.getElementById('messageReponse').value;
    
    if (message.length < 5) {
        document.getElementById('messageError').style.display = 'block';
        document.getElementById('messageReponse').className = 'input-error';
        showNotification('La réponse doit contenir au moins 5 caractères', 'error');
        return false;
    }
    
    return true;
}

document.getElementById('messageReponse').onblur = function() {
    if (this.value.length >= 5) {
        document.getElementById('messageError').style.display = 'none';
        this.className = '';
    } else {
        document.getElementById('messageError').style.display = 'block';
        this.className = 'input-error';
    }
};

function showNotification(message, type) {
    // Créer une notification si elle n'existe pas
    var notification = document.getElementById('notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        notification.className = 'notification';
        document.body.appendChild(notification);
    }
    
    notification.textContent = message;
    notification.className = 'notification ' + type;
    notification.style.display = 'block';
    
    setTimeout(function() {
        notification.style.display = 'none';
    }, 3000);
}
</script>