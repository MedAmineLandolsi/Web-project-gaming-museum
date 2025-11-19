<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Édition Article - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary: #00ff88;
            --secondary: #00ccff;
            --dark: #1a1a2e;
            --darker: #16213e;
            --light: #ffffff;
            --gray: #cccccc;
            --danger: #ff4757;
            --warning: #ffa502;
            --success: #2ed573;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, var(--dark) 0%, var(--darker) 100%);
            color: var(--light);
            display: flex;
            min-height: 100vh;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            background: var(--dark);
            padding: 2rem 1rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .admin-logo {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            color: var(--gray);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(0, 255, 136, 0.1);
            color: var(--primary);
            border-left: 4px solid var(--primary);
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .page-title {
            font-size: 2rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-success {
            background: var(--success);
            color: var(--dark);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--dark);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--light);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary);
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: var(--light);
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(0, 255, 136, 0.2);
        }

        .form-control::placeholder {
            color: var(--gray);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 200px;
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
        }

        select.form-control {
            cursor: pointer;
        }

        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-upload-btn {
            background: rgba(0, 255, 136, 0.1);
            color: var(--primary);
            padding: 0.8rem 1.5rem;
            border: 2px dashed var(--primary);
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            display: block;
            transition: all 0.3s;
        }

        .file-upload-btn:hover {
            background: rgba(0, 255, 136, 0.2);
        }

        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .current-image {
            margin-top: 1rem;
            text-align: center;
        }

        .current-image img {
            max-width: 300px;
            border-radius: 10px;
            border: 2px solid var(--primary);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .preview-section {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            border-left: 4px solid var(--secondary);
        }

        .preview-title {
            color: var(--secondary);
            margin-bottom: 1rem;
        }

        .char-count {
            text-align: right;
            color: var(--gray);
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .char-count.warning {
            color: var(--warning);
        }

        .char-count.error {
            color: var(--danger);
        }

        .error-message {
            color: var(--danger);
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .form-control.error {
            border-color: var(--danger);
            box-shadow: 0 0 0 2px rgba(255, 71, 87, 0.2);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="admin-logo">⚙️ Admin Gaming</div>
        <nav>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i>📊</i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="blog-admin.php" class="nav-link">
                        <i>📝</i> Articles Blog
                    </a>
                </li>
                <li class="nav-item">
                    <a href="comments-admin.php" class="nav-link">
                        <i>💬</i> Commentaires
                    </a>
                </li>
                <li class="nav-item">
                    <a href="games-admin.php" class="nav-link">
                        <i>🎮</i> Jeux
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users-admin.php" class="nav-link">
                        <i>👥</i> Utilisateurs
                    </a>
                </li>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link">
                        <i>⚙️</i> Paramètres
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../index.php" class="nav-link">
                        <i>🏠</i> Site Public
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" onclick="logout()" class="nav-link">
                        <i>🚪</i> Déconnexion
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <div class="admin-header">
            <h1 class="page-title" id="pageTitle">Nouvel Article</h1>
            <a href="blog-admin.php" class="btn btn-secondary">
                ← Retour à la liste
            </a>
        </div>

        <!-- Formulaire -->
        <div class="card">
            <form id="articleForm">
                <input type="hidden" id="articleId" name="id">

                <!-- Titre -->
                <div class="form-group">
                    <label for="title" class="form-label">Titre de l'article *</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           placeholder="Entrez un titre accrocheur...">
                    <div class="char-count" id="titleCount">0/100 caractères</div>
                    <div class="error-message" id="titleError"></div>
                </div>

                <!-- Catégorie et Statut -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="category" class="form-label">Catégorie *</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">Choisir une catégorie</option>
                            <option value="review">Test & Review</option>
                            <option value="news">Actualité</option>
                            <option value="tutorial">Tutoriel</option>
                            <option value="trends">Tendances</option>
                            <option value="interview">Interview</option>
                        </select>
                        <div class="error-message" id="categoryError"></div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Statut</label>
                        <select id="status" name="status" class="form-control">
                            <option value="draft">Brouillon</option>
                            <option value="published">Publié</option>
                        </select>
                    </div>
                </div>

                <!-- Image Principale -->
                <div class="form-group">
                    <label class="form-label">Image Principale</label>
                    <div class="file-upload">
                        <div class="file-upload-btn" id="fileUploadBtn">
                            📷 Cliquer pour uploader une image
                            <input type="file" class="file-upload-input" id="imageInput" 
                                   onchange="handleImageUpload(this)">
                        </div>
                    </div>
                    <div class="current-image" id="currentImageContainer" style="display: none;">
                        <p>Image actuelle :</p>
                        <img src="" alt="Current image" id="currentImage">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeImage()" 
                                style="margin-top: 0.5rem;">
                            🗑️ Supprimer l'image
                        </button>
                    </div>
                    <div class="error-message" id="imageError"></div>
                </div>

                <!-- Contenu -->
                <div class="form-group">
                    <label for="content" class="form-label">Contenu de l'article *</label>
                    <textarea id="content" name="content" class="form-control" 
                              placeholder="Rédigez votre article ici..." 
                              rows="15"></textarea>
                    <div class="char-count" id="contentCount">0/10000 caractères</div>
                    <div class="error-message" id="contentError"></div>
                </div>

                <!-- Tags -->
                <div class="form-group">
                    <label for="tags" class="form-label">
                        Tags (séparés par des virgules)
                    </label>
                    <input type="text" id="tags" name="tags" class="form-control" 
                           placeholder="ex: gaming, review, playstation, cyberpunk">
                    <small style="color: var(--gray); margin-top: 0.5rem; display: block;">
                        Les tags aident à améliorer la découvrabilité de votre article
                    </small>
                </div>

                <!-- Aperçu en temps réel -->
                <div class="preview-section">
                    <h3 class="preview-title">Aperçu en direct</h3>
                    <div id="titlePreview" style="color: var(--primary); font-size: 1.2rem; margin-bottom: 0.5rem;">
                        Votre titre apparaîtra ici...
                    </div>
                    <div id="contentPreview" style="color: var(--gray); max-height: 150px; overflow: hidden;">
                        Le contenu de votre article apparaîtra ici...
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="saveDraft()">
                        💾 Sauvegarder le brouillon
                    </button>
                    <button type="submit" class="btn btn-success">
                        🚀 Publier l'article
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="../assets/js/state-manager.js"></script>
    <script>
        const FormValidator = {
            validateText: function(value, minLength = 1, maxLength = null) {
                if (!value || value.trim().length < minLength) {
                    return { isValid: false, message: `Doit contenir au moins ${minLength} caractère(s)` };
                }
                if (maxLength && value.length > maxLength) {
                    return { isValid: false, message: `Ne doit pas dépasser ${maxLength} caractères` };
                }
                return { isValid: true };
            },

            validateSelect: function(value) {
                if (!value) {
                    return { isValid: false, message: 'Ce champ est obligatoire' };
                }
                return { isValid: true };
            },

            validateImage: function(file) {
                if (!file) return { isValid: true };
                
                if (file.size > 5 * 1024 * 1024) {
                    return { isValid: false, message: 'L\'image est trop volumineuse. Taille maximum: 5MB' };
                }
                
                if (!file.type.startsWith('image/')) {
                    return { isValid: false, message: 'Veuillez sélectionner un fichier image valide' };
                }
                
                return { isValid: true };
            },

            showError: function(input, message) {
                const errorElement = document.getElementById(input.id + 'Error');
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                    input.classList.add('error');
                }
            },

            hideError: function(input) {
                const errorElement = document.getElementById(input.id + 'Error');
                if (errorElement) {
                    errorElement.style.display = 'none';
                    input.classList.remove('error');
                }
            },

            validateForm: function(formData) {
                let isValid = true;

                const titleValidation = this.validateText(formData.title, 5, 100);
                if (!titleValidation.isValid) {
                    this.showError(document.getElementById('title'), titleValidation.message);
                    isValid = false;
                } else {
                    this.hideError(document.getElementById('title'));
                }

                const categoryValidation = this.validateSelect(formData.category);
                if (!categoryValidation.isValid) {
                    this.showError(document.getElementById('category'), categoryValidation.message);
                    isValid = false;
                } else {
                    this.hideError(document.getElementById('category'));
                }

                const contentValidation = this.validateText(formData.content, 50, 10000);
                if (!contentValidation.isValid) {
                    this.showError(document.getElementById('content'), contentValidation.message);
                    isValid = false;
                } else {
                    this.hideError(document.getElementById('content'));
                }

                return isValid;
            }
        };

        let currentImageFile = null;

        function getArticleIdFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('id');
        }

        function loadArticleData() {
            const articleId = getArticleIdFromUrl();
            
            if (articleId) {
                document.getElementById('pageTitle').textContent = 'Modifier l\'Article';
                document.querySelector('h1').textContent = 'Modifier l\'Article';
                
                const article = AppState.getArticleById(articleId);
                if (article) {
                    document.getElementById('articleId').value = article.id;
                    document.getElementById('title').value = article.title;
                    document.getElementById('category').value = article.category;
                    document.getElementById('status').value = article.status;
                    document.getElementById('content').value = article.content;
                    document.getElementById('tags').value = article.tags || '';
                    
                    if (article.image) {
                        document.getElementById('currentImage').src = `../assets/images/${article.image}`;
                        document.getElementById('currentImageContainer').style.display = 'block';
                    }
                    
                    updatePreview();
                    updateCharCounts();
                } else {
                    alert('Article non trouvé');
                    window.location.href = 'blog-admin.php';
                }
            }
        }

        function updatePreview() {
            const title = document.getElementById('title').value;
            const content = document.getElementById('content').value;
            
            document.getElementById('titlePreview').textContent = title || 'Votre titre apparaîtra ici...';
            document.getElementById('contentPreview').textContent = 
                content ? content.substring(0, 200) + '...' : 'Le contenu de votre article apparaîtra ici...';
        }

        function updateCharCounts() {
            const title = document.getElementById('title').value;
            const content = document.getElementById('content').value;
            const titleCount = document.getElementById('titleCount');
            const contentCount = document.getElementById('contentCount');
            
            titleCount.textContent = `${title.length}/100 caractères`;
            contentCount.textContent = `${content.length}/10000 caractères`;
            
            titleCount.className = `char-count ${title.length > 90 ? 'warning' : ''} ${title.length > 100 ? 'error' : ''}`;
            contentCount.className = `char-count ${content.length > 9000 ? 'warning' : ''} ${content.length > 10000 ? 'error' : ''}`;
        }

        function handleImageUpload(input) {
            const file = input.files[0];
            FormValidator.hideError(document.getElementById('imageInput'));
            
            if (file) {
                const imageValidation = FormValidator.validateImage(file);
                if (!imageValidation.isValid) {
                    FormValidator.showError(document.getElementById('imageInput'), imageValidation.message);
                    return;
                }
                
                currentImageFile = file;
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('currentImage').src = e.target.result;
                    document.getElementById('currentImageContainer').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            currentImageFile = null;
            document.getElementById('imageInput').value = '';
            document.getElementById('currentImageContainer').style.display = 'none';
            FormValidator.hideError(document.getElementById('imageInput'));
        }

        function saveDraft() {
            document.getElementById('status').value = 'draft';
            document.getElementById('articleForm').dispatchEvent(new Event('submit'));
        }

        function setupRealTimeValidation() {
            const inputs = ['title', 'category', 'content'];
            
            inputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('blur', function() {
                        const formData = {
                            title: document.getElementById('title').value,
                            category: document.getElementById('category').value,
                            content: document.getElementById('content').value
                        };
                        
                        if (inputId === 'title') {
                            const validation = FormValidator.validateText(formData.title, 5, 100);
                            if (!validation.isValid) {
                                FormValidator.showError(input, validation.message);
                            } else {
                                FormValidator.hideError(input);
                            }
                        } else if (inputId === 'category') {
                            const validation = FormValidator.validateSelect(formData.category);
                            if (!validation.isValid) {
                                FormValidator.showError(input, validation.message);
                            } else {
                                FormValidator.hideError(input);
                            }
                        } else if (inputId === 'content') {
                            const validation = FormValidator.validateText(formData.content, 50, 10000);
                            if (!validation.isValid) {
                                FormValidator.showError(input, validation.message);
                            } else {
                                FormValidator.hideError(input);
                            }
                        }
                    });
                }
            });
        }

        document.getElementById('title').addEventListener('input', function() {
            updatePreview();
            updateCharCounts();
            FormValidator.hideError(this);
        });

        document.getElementById('content').addEventListener('input', function() {
            updatePreview();
            updateCharCounts();
            FormValidator.hideError(this);
        });

        document.getElementById('category').addEventListener('change', function() {
            FormValidator.hideError(this);
        });

        document.getElementById('articleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!AppState.isAdminUser()) {
                alert('Accès non autorisé');
                return;
            }

            const formData = {
                title: document.getElementById('title').value,
                content: document.getElementById('content').value,
                category: document.getElementById('category').value,
                tags: document.getElementById('tags').value,
                status: document.getElementById('status').value,
                author: 'Admin'
            };

            if (!FormValidator.validateForm(formData)) {
                alert('Veuillez corriger les erreurs dans le formulaire');
                return;
            }

            const articleId = document.getElementById('articleId').value;
            let successMessage = '';

            if (articleId) {
                AppState.updateArticle(articleId, formData);
                successMessage = 'Article modifié avec succès !';
            } else {
                AppState.addArticle(formData);
                successMessage = 'Article créé avec succès !';
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '✅ Succès !';
            submitBtn.style.background = 'var(--success)';
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.style.background = '';
                alert(successMessage);
                window.location.href = 'blog-admin.php';
            }, 1000);
        });

        function logout() {
            AppState.logout();
            window.location.href = 'login.php';
        }

        if (!AppState.isAdminUser()) {
            window.location.href = 'login.php';
        } else {
            loadArticleData();
            updateCharCounts();
            setupRealTimeValidation();
        }
    </script>
</body>
</html>