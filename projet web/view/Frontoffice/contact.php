<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Musée de Gaming</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .contact-section {
            margin-top: 100px;
            padding: 2rem 0;
        }

        .contact-hero {
            background: url('../assets/images/contact-banner.jpg') center/cover;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
        }

        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .section-title {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-info {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid var(--border);
        }

        .contact-form {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid var(--border);
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background: rgba(0, 255, 136, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .info-content h3 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .info-content p {
            color: var(--gray);
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--light);
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--light);
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--dark);
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-primary:hover {
            background: #00cc66;
            transform: translateY(-2px);
        }

        .map-container {
            margin-top: 3rem;
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid var(--border);
        }

        .map-placeholder {
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray);
            border: 2px dashed var(--border);
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light);
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-link:hover {
            background: var(--primary);
            color: var(--dark);
            transform: translateY(-2px);
        }

        .success-message {
            background: rgba(46, 213, 115, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: none;
        }

        .error-message {
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid var(--error);
            color: var(--error);
            padding: 0.5rem;
            border-radius: 5px;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            display: none;
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }
            
            .contact-hero {
                height: 200px;
            }
            
            .contact-container {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .article-guidelines {
            background: rgba(0, 255, 136, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid var(--primary);
            margin: 1rem 0;
        }

        .article-guidelines h4 {
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .article-guidelines ul {
            color: var(--gray);
            line-height: 1.6;
        }

        .article-guidelines li {
            margin-bottom: 0.5rem;
        }

        .char-count {
            text-align: right;
            color: var(--gray);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .field-error {
            border-color: #ff4757 !important;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">🎮 Musée de Gaming</div>
                <ul class="nav-links">
                    <li><a href="../index.php">Accueil</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="games.php">Jeux</a></li>
                    <li><a href="about.php">À propos</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
                    <li><a href="../backoffice/dashboard.php" class="admin-btn">Espace Admin</a></li>
                </ul>
                <div class="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <!-- Hero -->
            <div class="contact-hero">
                <div class="hero-content">
                    <h1 class="section-title">Contactez-Nous</h1>
                    <p class="section-subtitle">Nous sommes à votre écoute pour toute question ou suggestion</p>
                </div>
            </div>

            <!-- Conteneur Principal -->
            <div class="contact-container">
                <!-- Informations de Contact -->
                <div class="contact-info">
                    <h2 style="color: var(--primary); margin-bottom: 2rem;">Informations de Contact</h2>
                    
                    <div class="info-item">
                        <div class="info-icon">📍</div>
                        <div class="info-content">
                            <h3>Adresse</h3>
                            <p>
                                ESPRIT<br>
                                Technopole Ghazela<br>
                                Ariana, Tunisie
                            </p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">📞</div>
                        <div class="info-content">
                            <h3>Téléphone</h3>
                            <p>+216 21 693 411</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">✉️</div>
                        <div class="info-content">
                            <h3>Email</h3>
                            <p>A-mpact@gmail.com</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">🕒</div>
                        <div class="info-content">
                            <h3>Horaires d'Ouverture</h3>
                            <p>
                                Lundi - Vendredi: 9h00 - 18h00<br>
                                Samedi: 10h00 - 17h00<br>
                                Dimanche: Fermé
                            </p>
                        </div>
                    </div>

                    <!-- Réseaux Sociaux -->
                    <div class="social-links">
                        <a href="#" class="social-link" title="Twitter">🐦</a>
                        <a href="#" class="social-link" title="Facebook">📘</a>
                        <a href="#" class="social-link" title="Instagram">📷</a>
                        <a href="#" class="social-link" title="YouTube">📺</a>
                        <a href="#" class="social-link" title="Discord">💬</a>
                    </div>
                </div>

                <!-- Formulaire de Contact -->
                <div class="contact-form">
                    <h2 style="color: var(--primary); margin-bottom: 2rem;">Envoyez-nous un Message</h2>
                    
                    <div class="success-message" id="successMessage">
                        ✅ Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.
                    </div>

                    <form id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Prénom *</label>
                                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Votre prénom">
                                <div class="error-message" id="firstnameError"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nom *</label>
                                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Votre nom">
                                <div class="error-message" id="lastnameError"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="text" class="form-control" name="email" id="email" placeholder="votre@email.com">
                            <div class="error-message" id="emailError"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sujet *</label>
                            <select class="form-control" name="subject" id="subjectSelect">
                                <option value="">Choisir un sujet</option>
                                <option value="general">Question générale</option>
                                <option value="partnership">Partenariat</option>
                                <option value="technical">Problème technique</option>
                                <option value="suggestion">Suggestion</option>
                                <option value="article">📝 Proposer un article</option>
                                <option value="press">Presse</option>
                                <option value="other">Autre</option>
                            </select>
                            <div class="error-message" id="subjectError"></div>
                        </div>

                        <!-- Section Article (cachée par défaut) -->
                        <div id="articleSection" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Titre de votre article *</label>
                                <input type="text" class="form-control" name="article_title" id="article_title" placeholder="Ex: Test complet de Cyberpunk 2077">
                                <div class="error-message" id="articleTitleError"></div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Catégorie</label>
                                <select class="form-control" name="article_category" id="article_category">
                                    <option value="review">Test de jeu</option>
                                    <option value="tutorial">Tutoriel</option>
                                    <option value="opinion">Opinion</option>
                                    <option value="news">Actualité</option>
                                    <option value="retro">Rétrogaming</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Votre article *</label>
                                <textarea class="form-control" name="article_content" id="article_content" rows="12" placeholder="Rédigez votre article ici... (minimum 300 caractères)"></textarea>
                                <div class="char-count" id="articleCount">0 caractères</div>
                                <div class="error-message" id="articleContentError"></div>
                            </div>
                            
                            <div class="article-guidelines">
                                <h4>📋 Recommandations pour votre article :</h4>
                                <ul>
                                    <li>✅ Minimum 300 caractères</li>
                                    <li>✅ Contenu original et personnel</li>
                                    <li>✅ Respectueux et constructif</li>
                                    <li>✅ Pertinent pour la communauté gaming</li>
                                    <li>⏱️ Réponse sous 2-3 jours</li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Message *</label>
                            <textarea class="form-control" name="message" id="message" placeholder="Décrivez-nous votre demande..."></textarea>
                            <div class="error-message" id="messageError"></div>
                        </div>

                        <button type="submit" class="btn-primary">
                            Envoyer le Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Carte -->
            <div class="map-container">
                <h2 style="color: var(--primary); margin-bottom: 1rem;">Notre Localisation</h2>
                <div class="map-placeholder">
                    🗺️ Carte interactive - Localisation du Musée de Gaming
                </div>
            </div>

            <!-- FAQ -->
            <div class="contact-info" style="margin-top: 3rem;">
                <h2 style="color: var(--primary); margin-bottom: 2rem;">Questions Fréquentes</h2>
                
                <div class="info-item">
                    <div class="info-icon">❓</div>
                    <div class="info-content">
                        <h3>Puis-je proposer un article pour le blog ?</h3>
                        <p>
                            Absolument ! Nous sommes toujours ouverts aux contributions de la communauté. 
                            Envoyez-nous votre proposition via le formulaire de contact en sélectionnant 
                            "Proposer un article" comme sujet.
                        </p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">❓</div>
                    <div class="info-content">
                        <h3>Comment devenir partenaire ?</h3>
                        <p>
                            Nous collaborons avec des développeurs, éditeurs et autres acteurs du secteur. 
                            Contactez-nous via le formulaire en sélectionnant "Partenariat" pour discuter 
                            des opportunités de collaboration.
                        </p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">❓</div>
                    <div class="info-content">
                        <h3>Proposez-vous des visites guidées ?</h3>
                        <p>
                            Oui ! Nous organisons des visites guidées sur rendez-vous pour les groupes 
                            et les écoles. Contactez-nous pour planifier votre visite.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">🎮 Musée de Gaming</div>
                    <p>Votre destination ultime pour la culture gaming</p>
                </div>
                <div class="footer-section">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="games.php">Jeux</a></li>
                        <li><a href="../backoffice/dashboard.php">Admin</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>email@musee-gaming.fr<br>+33 1 23 45 67 89</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Musée de Gaming. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>

    <script>
        // Fonction pour valider les emails
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Fonction pour afficher/masquer les erreurs
        function showError(fieldId, message) {
            const errorElement = document.getElementById(fieldId + 'Error');
            const fieldElement = document.getElementById(fieldId);
            
            if (errorElement && fieldElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                fieldElement.classList.add('field-error');
            }
        }

        function hideError(fieldId) {
            const errorElement = document.getElementById(fieldId + 'Error');
            const fieldElement = document.getElementById(fieldId);
            
            if (errorElement && fieldElement) {
                errorElement.style.display = 'none';
                fieldElement.classList.remove('field-error');
            }
        }

        // Afficher/masquer la section article
        function toggleArticleSection() {
            const subject = document.getElementById('subjectSelect').value;
            const articleSection = document.getElementById('articleSection');
            
            if (subject === 'article') {
                articleSection.style.display = 'block';
            } else {
                articleSection.style.display = 'none';
                // Réinitialiser les erreurs des champs article
                hideError('article_title');
                hideError('article_content');
            }
        }

        // Compteur de caractères pour l'article
        function setupArticleCounter() {
            const articleContent = document.getElementById('article_content');
            if (articleContent) {
                articleContent.addEventListener('input', function() {
                    const count = this.value.length;
                    document.getElementById('articleCount').textContent = count + ' caractères';
                    
                    // Changer la couleur si trop court
                    if (count < 300) {
                        document.getElementById('articleCount').style.color = '#ff4757';
                    } else {
                        document.getElementById('articleCount').style.color = '#2ed573';
                    }
                });
            }
        }

        // Validation du formulaire
        function validateContactForm() {
            let isValid = true;

            // Récupérer les valeurs
            const firstname = document.getElementById('firstname').value.trim();
            const lastname = document.getElementById('lastname').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subjectSelect').value;
            const message = document.getElementById('message').value.trim();
            const isArticle = subject === 'article';
            const articleTitle = isArticle ? document.getElementById('article_title').value.trim() : '';
            const articleContent = isArticle ? document.getElementById('article_content').value.trim() : '';

            // Réinitialiser toutes les erreurs
            hideError('firstname');
            hideError('lastname');
            hideError('email');
            hideError('subject');
            hideError('message');
            hideError('article_title');
            hideError('article_content');

            // Validation prénom
            if (!firstname) {
                showError('firstname', 'Le prénom est obligatoire');
                isValid = false;
            }

            // Validation nom
            if (!lastname) {
                showError('lastname', 'Le nom est obligatoire');
                isValid = false;
            }

            // Validation email
            if (!email) {
                showError('email', 'L\'email est obligatoire');
                isValid = false;
            } else if (!isValidEmail(email)) {
                showError('email', 'Veuillez entrer un email valide');
                isValid = false;
            }

            // Validation sujet
            if (!subject) {
                showError('subject', 'Veuillez sélectionner un sujet');
                isValid = false;
            }

            // Validation message
            if (!message) {
                showError('message', 'Le message est obligatoire');
                isValid = false;
            } else if (message.length < 10) {
                showError('message', 'Le message doit contenir au moins 10 caractères');
                isValid = false;
            }

            // Validation article (si applicable)
            if (isArticle) {
                if (!articleTitle) {
                    showError('article_title', 'Le titre de l\'article est obligatoire');
                    isValid = false;
                }

                if (!articleContent) {
                    showError('article_content', 'Le contenu de l\'article est obligatoire');
                    isValid = false;
                } else if (articleContent.length < 300) {
                    showError('article_content', 'L\'article doit contenir au moins 300 caractères');
                    isValid = false;
                }
            }

            return isValid;
        }

        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Configuration du compteur d'article
            setupArticleCounter();

            // Écouteur pour le changement de sujet
            document.getElementById('subjectSelect').addEventListener('change', toggleArticleSection);

            // Écouteur pour la soumission du formulaire
            document.getElementById('contactForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (validateContactForm()) {
                    // Simulation d'envoi du formulaire
                    const formData = new FormData(this);
                    const formObject = Object.fromEntries(formData);
                    
                    console.log('Message envoyé:', formObject);
                    
                    // Afficher le message de succès
                    document.getElementById('successMessage').style.display = 'block';
                    
                    // Message de confirmation spécial pour les articles
                    if (formObject.subject === 'article') {
                        alert('📨 Merci ! Votre article a été soumis. Nous le lirons et vous recontacterons s\'il est retenu.');
                    }
                    
                    // Reset du formulaire
                    this.reset();
                    toggleArticleSection(); // Réinitialiser l'affichage de la section article
                    
                    // Cacher le message après 5 secondes
                    setTimeout(() => {
                        document.getElementById('successMessage').style.display = 'none';
                    }, 5000);
                }
            });

            // Écouteurs pour cacher les erreurs lors de la saisie
            const fields = ['firstname', 'lastname', 'email', 'message', 'article_title', 'article_content'];
            fields.forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    element.addEventListener('input', function() {
                        hideError(field);
                    });
                }
            });

            // Écouteur pour le sujet
            document.getElementById('subjectSelect').addEventListener('change', function() {
                hideError('subject');
            });
        });
    </script>
</body>
</html>