<?php
session_start(); // AJOUTER CETTE LIGNE AU DÉBUT

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../../login.php');
    exit();
}

include_once '../../config/database.php';
include_once '../../models/Evenement.php';
include_once '../../controllers/EvenementController.php';
include_once '../../models/Participation.php';
include_once '../../controllers/ParticipationController.php';

$database = new Database();
$db = $database->getConnection();

$evenementController = new EvenementController($db);

$participationController = new ParticipationController($db);
$participations = $participationController->index();
$evenements = $evenementController->index();

$message = '';
$message_type = '';

if($_POST) {
    // AJOUTER l'organisateur_id (l'utilisateur connecté)
    $_POST['organisateur_id'] = $_SESSION['user_id'];
    
    if($evenementController->create($_POST)) {
        $message = '🎉 ÉVÉNEMENT CRÉÉ AVEC SUCCÈS !';
        $message_type = 'success';
    } else {
        $message = '❌ ERREUR LORS DE LA CRÉATION.';
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Événement - Administration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/projet-web/ProjetWeb/assets/css/admin-style.css">
    <style>
        .form-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 900px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-green);
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .required-field::after {
            content: " *";
            color: var(--accent-pink);
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1rem;
            background: var(--darker-bg);
            border: 1px solid var(--border-color);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            border-radius: 10px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(0, 255, 65, 0.15);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .error-message {
            display: block;
            min-height: 1.2rem;
            margin-top: 0.35rem;
            color: var(--danger-red);
            font-family: 'VT323', monospace;
            font-size: 1.05rem;
        }

        .form-hint {
            margin-top: 0.25rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1.0rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.25rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 12px;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .alert-success {
            background: rgba(0, 255, 65, 0.12);
            border: 1px solid var(--primary-green);
            color: var(--primary-green);
        }

        .alert-error {
            background: rgba(255, 0, 85, 0.12);
            border: 1px solid var(--danger-red);
            color: var(--danger-red);
        }

        .message-alert {
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 12px;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .message-success {
            background: rgba(0, 255, 65, 0.12);
            border: 1px solid var(--primary-green);
            color: var(--primary-green);
        }

        .message-error {
            background: rgba(255, 0, 85, 0.12);
            border: 1px solid var(--danger-red);
            color: var(--danger-red);
        }

        .organizer-info {
            padding: 0.9rem 1.1rem;
            margin-bottom: 1rem;
            border-radius: 12px;
            background: rgba(189, 0, 255, 0.08);
            border: 1px solid rgba(189, 0, 255, 0.35);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1.15rem;
        }

        .image-preview {
            margin-top: 0.75rem;
        }

        .image-preview img {
            max-width: 240px;
            max-height: 180px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <div class="logo-box">⚙</div>
                <div class="admin-title">
                    <h2>SYSTÈME ADMIN</h2>
                    <div class="admin-badge">PANEL DE CONTROLE</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="../index.php">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                        <span class="nav-count"><?php echo (int)count($evenements); ?></span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="../evenements.php">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">ÉVÉNEMENTS</span>
                        <span class="nav-count"><?php echo (int)count($evenements); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../participations.php">
                        <span class="nav-icon">🧾</span>
                        <span class="nav-text">PARTICIPATIONS</span>
                        <span class="nav-count"><?php echo (int)count($participations); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../../index.php" style="color: #00FF41; font-weight: bold;">
                        <span class="nav-icon">🌐</span>
                        <span class="nav-text">FRONT OFFICE</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">AD</div>
                <div class="admin-info">
                    <div class="admin-name">ADMINISTRATEUR</div>
                    <div class="admin-role">GAMING EVENTS</div>
                </div>
            </div>
            <a href="../../index.php" class="btn-logout" style="background: #00FF41; color: #000; border-color: #00FF41;">
                <span>←</span> RETOUR AU SITE
            </a>
            <a class="btn-logout" href="../../logout.php" onclick="return confirm('Déconnexion ?')">
                <span>→</span> DÉCONNEXION
            </a>
        </div>
    </aside>

    <main class="main-content" id="dashboard">
        <div class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title">Créer un événement</h1>
            </div>
            <div class="top-bar-right">
                <a href="../../index.php" class="btn-view-site">🌐 VOIR LE SITE</a>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">➕ Nouvel événement</h3>
                <a class="btn-view-all-small" href="../evenements.php">← Retour</a>
            </div>
            <div class="card-content">
                <div class="form-wrapper">
                    <?php if (!empty($message)): ?>
                        <div class="message-alert <?php echo $message_type == 'success' ? 'message-success' : 'message-error'; ?>">
                            <strong><?php echo htmlspecialchars($message); ?></strong>
                        </div>
                    <?php endif; ?>

                    <div class="organizer-info">
                        👤 Cet événement sera créé sous votre nom : <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong>
                    </div>

                    <form method="POST" id="createEventForm" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="organisateur_id" value="<?php echo $_SESSION['user_id']; ?>">

                        <div class="form-group">
                            <label for="nom" class="required-field">NOM DE L'ÉVÉNEMENT</label>
                            <input type="text" id="nom" name="nom" class="form-control" placeholder="Entrez le nom de l'événement" required>
                            <span class="error-message" id="nom_error"></span>
                            <div class="form-hint">2 à 100 caractères</div>
                        </div>

                        <div class="form-group">
                            <label for="description">DESCRIPTION</label>
                            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Décrivez l'événement..."></textarea>
                            <span class="error-message" id="description_error"></span>
                            <div class="form-hint">Maximum 1000 caractères</div>
                        </div>

                        <div class="form-group">
                            <label for="jeu" class="required-field">JEU</label>
                            <input type="text" id="jeu" name="jeu" class="form-control" placeholder="Nom du jeu" required>
                            <span class="error-message" id="jeu_error"></span>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="date_debut" class="required-field">DATE ET HEURE DE DÉBUT</label>
                                <input type="datetime-local" id="date_debut" name="date_debut" class="form-control" required>
                                <span class="error-message" id="date_debut_error"></span>
                            </div>

                            <div class="form-group">
                                <label for="date_fin" class="required-field">DATE ET HEURE DE FIN</label>
                                <input type="datetime-local" id="date_fin" name="date_fin" class="form-control" required>
                                <span class="error-message" id="date_fin_error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="lieu" class="required-field">LIEU</label>
                            <input type="text" id="lieu" name="lieu" class="form-control" placeholder="Lieu de l'événement" required>
                            <span class="error-message" id="lieu_error"></span>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="places_max" class="required-field">NOMBRE DE PLACES MAXIMUM</label>
                                <input type="number" id="places_max" name="places_max" class="form-control" placeholder="50" value="50" min="1" max="1000" required>
                                <span class="error-message" id="places_max_error"></span>
                                <div class="form-hint">1 à 1000 places</div>
                            </div>

                            <div class="form-group">
                                <label for="prix">PRIX (€)</label>
                                <input type="number" id="prix" name="prix" class="form-control" step="0.01" value="0" placeholder="0.00" min="0" max="10000">
                                <span class="error-message" id="prix_error"></span>
                                <div class="form-hint">Gratuit si 0€</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image">IMAGE DE L'ÉVÉNEMENT</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                            <div class="form-hint">Taille max: 2MB, Formats: JPG, PNG, GIF</div>
                            <div class="image-preview" id="imagePreview"></div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-view-site" id="submitBtn">CRÉER L'ÉVÉNEMENT</button>
                            <a href="../evenements.php" class="btn-view-all-small">ANNULER</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
    class CreateEventValidator {
        constructor() {
            this.form = document.getElementById('createEventForm');
            if (!this.form) return;

            this.fields = {
                nom: {
                    element: document.getElementById('nom'),
                    error: document.getElementById('nom_error'),
                    validate: (value) => this.validateRequired(value, 'Le nom de l\'événement est obligatoire')
                },
                jeu: {
                    element: document.getElementById('jeu'),
                    error: document.getElementById('jeu_error'),
                    validate: (value) => this.validateRequired(value, 'Le nom du jeu est obligatoire')
                },
                date_debut: {
                    element: document.getElementById('date_debut'),
                    error: document.getElementById('date_debut_error'),
                    validate: (value) => this.validateDateDebut(value)
                },
                date_fin: {
                    element: document.getElementById('date_fin'),
                    error: document.getElementById('date_fin_error'),
                    validate: (value) => this.validateDateFin(value)
                },
                lieu: {
                    element: document.getElementById('lieu'),
                    error: document.getElementById('lieu_error'),
                    validate: (value) => this.validateRequired(value, 'Le lieu est obligatoire')
                },
                places_max: {
                    element: document.getElementById('places_max'),
                    error: document.getElementById('places_max_error'),
                    validate: (value) => this.validatePlacesMax(value)
                },
                prix: {
                    element: document.getElementById('prix'),
                    error: document.getElementById('prix_error'),
                    validate: (value) => this.validatePrix(value)
                },
                description: {
                    element: document.getElementById('description'),
                    error: document.getElementById('description_error'),
                    validate: (value) => this.validateDescription(value)
                }
            };

            this.imageInput = document.getElementById('image');
            this.imagePreview = document.getElementById('imagePreview');
            this.submitBtn = document.getElementById('submitBtn');
            this.init();
        }

        init() {
            // Définir la date minimale pour les dates (aujourd'hui)
            this.setMinDateForDateFields();
            
            // Validation en temps réel
            Object.values(this.fields).forEach(field => {
                if (field.element) {
                    field.element.addEventListener('blur', () => this.validateField(field));
                    field.element.addEventListener('input', () => this.clearError(field));
                    
                    // Validation spéciale pour les dates
                    if (field.element.id === 'date_debut' || field.element.id === 'date_fin') {
                        field.element.addEventListener('change', () => this.validateDates());
                    }
                }
            });

            // Gestion de l'aperçu de l'image
            if (this.imageInput) {
                this.imageInput.addEventListener('change', () => this.previewImage());
            }

            // Validation à la soumission
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
            
            // Initialiser la valeur par défaut pour les places
            if (this.fields.places_max.element && !this.fields.places_max.element.value) {
                this.fields.places_max.element.value = '50';
            }
        }

        setMinDateForDateFields() {
            const now = new Date();
            // Formater la date au format YYYY-MM-DDTHH:MM
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            
            if (this.fields.date_debut.element) {
                this.fields.date_debut.element.min = minDateTime;
            }
            if (this.fields.date_fin.element) {
                this.fields.date_fin.element.min = minDateTime;
            }
        }

        previewImage() {
            const file = this.imageInput.files[0];
            if (file) {
                // Vérifier la taille du fichier (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('❌ L\'image est trop volumineuse (max 2MB)');
                    this.imageInput.value = '';
                    this.imagePreview.innerHTML = '';
                    return;
                }

                // Vérifier le type de fichier
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('❌ Format d\'image non supporté (JPG, PNG, GIF uniquement)');
                    this.imageInput.value = '';
                    this.imagePreview.innerHTML = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview.innerHTML = `
                        <img src="${e.target.result}" alt="Aperçu de l'image">
                        <p style="margin-top: 0.5rem; font-size: 0.6rem; color: var(--primary-green);">
                            ✅ Image sélectionnée: ${file.name}
                        </p>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                this.imagePreview.innerHTML = '';
            }
        }

        validateRequired(value, message) {
            value = value.trim();
            if (!value) {
                return message;
            }
            if (value.length < 2) {
                return 'Ce champ doit contenir au moins 2 caractères';
            }
            if (value.length > 100) {
                return 'Ce champ ne peut pas dépasser 100 caractères';
            }
            return null;
        }

        validateDateDebut(value) {
            if (!value) {
                return 'La date de début est obligatoire';
            }
            
            const selectedDate = new Date(value);
            const now = new Date();
            
            if (selectedDate < now) {
                return 'La date de début ne peut pas être dans le passé';
            }
            
            return null;
        }

        validateDateFin(value) {
            if (!value) {
                return 'La date de fin est obligatoire';
            }
            
            const dateDebut = this.fields.date_debut.element.value;
            if (dateDebut) {
                const debut = new Date(dateDebut);
                const fin = new Date(value);
                
                if (fin <= debut) {
                    return 'La date de fin doit être après la date de début';
                }
                
                // Vérifier que l'événement dure au moins 30 minutes
                const duration = fin.getTime() - debut.getTime();
                const minDuration = 30 * 60 * 1000; // 30 minutes en millisecondes
                
                if (duration < minDuration) {
                    return 'L\'événement doit durer au moins 30 minutes';
                }
                
                // Vérifier que l'événement ne dure pas plus de 7 jours
                const maxDuration = 7 * 24 * 60 * 60 * 1000; // 7 jours en millisecondes
                if (duration > maxDuration) {
                    return 'L\'événement ne peut pas durer plus de 7 jours';
                }
            }
            
            return null;
        }

        validateDates() {
            // Valider les deux dates ensemble
            this.validateField(this.fields.date_debut);
            this.validateField(this.fields.date_fin);
        }

        validatePlacesMax(value) {
            if (!value) {
                return 'Le nombre de places est obligatoire';
            }
            
            const places = parseInt(value);
            if (isNaN(places) || places < 1) {
                return 'Le nombre de places doit être au moins 1';
            }
            
            if (places > 1000) {
                return 'Le nombre de places ne peut pas dépasser 1000';
            }
            
            return null;
        }

        validatePrix(value) {
            if (value) {
                const prix = parseFloat(value);
                if (isNaN(prix) || prix < 0) {
                    return 'Le prix doit être un nombre positif';
                }
                
                if (prix > 10000) {
                    return 'Le prix ne peut pas dépasser 10 000€';
                }
                
                // Vérifier le format avec 2 décimales maximum
                if (!/^\d+(\.\d{1,2})?$/.test(value)) {
                    return 'Le prix doit avoir au maximum 2 décimales';
                }
            }
            return null;
        }

        validateDescription(value) {
            value = value.trim();
            if (value && value.length > 1000) {
                return 'La description ne peut pas dépasser 1000 caractères';
            }
            return null;
        }

        validateField(field) {
            const value = field.element.value;
            const error = field.validate(value);
            
            if (error) {
                this.showError(field, error);
                return false;
            } else {
                this.clearError(field);
                return true;
            }
        }

        showError(field, message) {
            field.element.classList.add('error');
            field.error.textContent = message;
            field.error.classList.add('show');
        }

        clearError(field) {
            field.element.classList.remove('error');
            field.error.textContent = '';
            field.error.classList.remove('show');
        }

        validateAll() {
            let isValid = true;
            
            // Valider tous les champs
            Object.values(this.fields).forEach(field => {
                if (!this.validateField(field)) {
                    isValid = false;
                }
            });

            return isValid;
        }

        handleSubmit(e) {
            e.preventDefault();
            
            if (this.validateAll()) {
                // Désactiver le bouton pour éviter les doubles soumissions
                if (this.submitBtn) {
                    this.submitBtn.disabled = true;
                    this.submitBtn.textContent = 'CRÉATION EN COURS...';
                }
                
                // Soumettre le formulaire
                this.form.submit();
            } else {
                // Faire défiler jusqu'à la première erreur
                const firstError = document.querySelector('.error');
                if (firstError) {
                    firstError.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
                
                // Message d'erreur stylisé
                const errorMessage = document.createElement('div');
                errorMessage.className = 'message-alert message-error';
                errorMessage.innerHTML = '<strong>Veuillez corriger les erreurs dans le formulaire avant de soumettre.</strong>';
                errorMessage.style.marginTop = '1rem';
                
                // Ajouter le message d'erreur
                const existingAlert = this.form.querySelector('.message-alert');
                if (existingAlert) {
                    existingAlert.remove();
                }
                this.form.prepend(errorMessage);
            }
        }
    }

    // Initialiser la validation lorsque le DOM est chargé
    document.addEventListener('DOMContentLoaded', () => {
        new CreateEventValidator();
    });
    </script>

    <?php include_once '../views/back/footer.php'; ?>
</body>
</html>