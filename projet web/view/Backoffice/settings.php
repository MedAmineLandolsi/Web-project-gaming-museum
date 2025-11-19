<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Admin</title>
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
            --card-bg: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.1);
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
            border-bottom: 1px solid var(--border);
        }

        .page-title {
            font-size: 2rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .settings-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .tab-btn {
            background: none;
            border: none;
            color: var(--gray);
            padding: 1rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 2px solid transparent;
            font-size: 1rem;
        }

        .tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-btn:hover {
            color: var(--light);
        }

        .card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            margin-bottom: 2rem;
        }

        .card-title {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            color: var(--light);
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
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

        select.form-control {
            cursor: pointer;
        }

        .form-help {
            color: var(--gray);
            font-size: 0.9rem;
            margin-top: 0.5rem;
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

        .btn-success:hover {
            background: #26c46a;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--light);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .toggle-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .toggle-label {
            color: var(--light);
            font-weight: bold;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.1);
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: var(--light);
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--primary);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .danger-zone {
            border: 2px solid var(--danger);
            background: rgba(255, 71, 87, 0.05);
        }

        .danger-zone .card-title {
            color: var(--danger);
        }

        .danger-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .system-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .info-label {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: var(--light);
            font-weight: bold;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .error-message {
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: none;
        }

        .form-group.error .form-control {
            border-color: #ff6b6b;
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
            
            .settings-tabs {
                flex-direction: column;
            }
            
            .danger-actions {
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
                    <a href="settings.php" class="nav-link active">
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
            <h1 class="page-title">Paramètres du Site</h1>
        </div>

        <!-- Tabs -->
        <div class="settings-tabs">
            <button class="tab-btn active" onclick="showTab('general')">⚙️ Général</button>
            <button class="tab-btn" onclick="showTab('content')">📝 Contenu</button>
            <button class="tab-btn" onclick="showTab('users')">👥 Utilisateurs</button>
            <button class="tab-btn" onclick="showTab('system')">🔧 Système</button>
            <button class="tab-btn" onclick="showTab('danger')">⚠️ Zone de Danger</button>
        </div>

        <!-- Tab Contents -->
        <div id="general" class="tab-content active">
            <!-- Paramètres Généraux -->
            <div class="card">
                <h2 class="card-title">⚙️ Paramètres Généraux</h2>
                
                <form id="generalSettings">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nom du Site</label>
                            <input type="text" class="form-control" name="site_name" 
                                   value="Musée de Gaming">
                            <div class="error-message" id="siteNameError">Le nom du site est requis</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Slogan</label>
                            <input type="text" class="form-control" name="site_slogan" 
                                   value="Votre destination gaming ultime">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email de Contact</label>
                            <input type="text" class="form-control" name="contact_email" 
                                   value="contact@musee-gaming.fr">
                            <div class="error-message" id="emailError">Format d'email invalide</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input type="text" class="form-control" name="contact_phone" 
                                   value="+33 1 23 45 67 89">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description du Site</label>
                        <textarea class="form-control" name="site_description" rows="4">
Le Musée de Gaming est votre destination ultime pour découvrir l'univers fascinant du jeu vidéo à travers nos collections, articles et analyses.
                        </textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mots-clés (SEO)</label>
                        <input type="text" class="form-control" name="site_keywords" 
                               value="gaming, jeux vidéo, musée, actualités, tests, reviews">
                        <div class="form-help">Séparés par des virgules</div>
                    </div>

                    <button type="button" class="btn btn-success" onclick="validateAndSaveGeneralSettings()">
                        💾 Sauvegarder les paramètres
                    </button>
                </form>
            </div>

            <!-- Paramètres d'Apparence -->
            <div class="card">
                <h2 class="card-title">🎨 Apparence</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Thème</label>
                        <select class="form-control" name="theme">
                            <option value="dark">Sombre</option>
                            <option value="light">Clair</option>
                            <option value="auto">Automatique</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Couleur Primaire</label>
                        <input type="text" class="form-control" name="primary_color" 
                               value="#00ff88" placeholder="#00ff88">
                    </div>
                </div>

                <div class="toggle-group">
                    <span class="toggle-label">Mode Sombre Forcé</span>
                    <label class="toggle-switch">
                        <input type="checkbox" name="force_dark_mode">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="toggle-group">
                    <span class="toggle-label">Animations</span>
                    <label class="toggle-switch">
                        <input type="checkbox" name="animations" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <button type="button" class="btn btn-success" onclick="saveAppearanceSettings()">
                    💾 Appliquer l'apparence
                </button>
            </div>
        </div>

        <!-- Les autres onglets (content, users, system, danger) restent identiques -->
        <!-- ... -->

    </main>

    <script src="../assets/js/state-manager.js"></script>
    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        function loadSystemInfo() {
            const articles = AppState.getAllArticles();
            const comments = AppState.state.comments;
            const games = AppState.state.games;
            const users = AppState.state.users || [];
            
            document.getElementById('articlesCount').textContent = articles.length;
            document.getElementById('commentsCount').textContent = comments.length;
            document.getElementById('gamesCount').textContent = games.length;
            document.getElementById('usersCount').textContent = users.length;
            
            const lastUpdate = new Date().toLocaleDateString('fr-FR');
            document.getElementById('lastUpdate').textContent = lastUpdate;
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function validateNumber(value, min, max) {
            const num = parseInt(value);
            return !isNaN(num) && num >= min && num <= max;
        }

        function validateAndSaveGeneralSettings() {
            const form = document.getElementById('generalSettings');
            const siteName = form.querySelector('input[name="site_name"]').value.trim();
            const contactEmail = form.querySelector('input[name="contact_email"]').value.trim();
            
            document.querySelectorAll('.error-message').forEach(error => {
                error.style.display = 'none';
            });
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error');
            });

            let isValid = true;

            if (!siteName) {
                document.getElementById('siteNameError').style.display = 'block';
                form.querySelector('input[name="site_name"]').parentElement.classList.add('error');
                isValid = false;
            }

            if (contactEmail && !validateEmail(contactEmail)) {
                document.getElementById('emailError').style.display = 'block';
                form.querySelector('input[name="contact_email"]').parentElement.classList.add('error');
                isValid = false;
            }

            if (isValid) {
                saveGeneralSettings();
            } else {
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'danger');
            }
        }

        function saveGeneralSettings() {
            const form = document.getElementById('generalSettings');
            const formData = new FormData(form);
            const settings = Object.fromEntries(formData);
            
            console.log('Paramètres généraux sauvegardés:', settings);
            showNotification('Paramètres généraux sauvegardés avec succès!', 'success');
        }

        function saveAppearanceSettings() {
            console.log('Paramètres d\'apparence sauvegardés');
            showNotification('Paramètres d\'apparence appliqués!', 'success');
        }

        function logout() {
            AppState.logout();
            window.location.href = 'login.php';
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 2rem;
                background: ${type === 'success' ? 'var(--success)' : 'var(--danger)'};
                color: var(--dark);
                border-radius: 8px;
                font-weight: bold;
                z-index: 10000;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        if (!AppState.isAdminUser()) {
            window.location.href = 'login.php';
        } else {
            loadSystemInfo();
        }
    </script>
</body>
</html>