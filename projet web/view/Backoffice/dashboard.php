<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Musée de Gaming</title>
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
            margin: 0;
            padding: 0;
        }

        .header {
            background: var(--dark);
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            color: var(--light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            background: rgba(0, 255, 136, 0.1);
            color: var(--primary);
        }

        .dashboard {
            margin-top: 100px;
            padding: 2rem 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            border: 1px solid var(--border);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray);
            font-size: 1rem;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .action-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            border: 1px solid var(--border);
            text-decoration: none;
            color: var(--light);
            transition: all 0.3s;
        }

        .action-card:hover {
            background: rgba(0, 255, 136, 0.1);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .recent-activity {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid var(--border);
        }

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: rgba(0, 255, 136, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .error-message {
            color: #ff4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--card-bg);
            color: var(--light);
        }

        .form-control.error {
            border-color: #ff4444;
        }

        h1 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        h2 {
            color: var(--light);
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                gap: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">⚙️ Admin Gaming</div>
                <ul class="nav-links">
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="blog-admin.php">Articles</a></li>
                    <li><a href="comments-admin.php">Commentaires</a></li>
                    <li><a href="games-admin.php">Jeux</a></li>
                    <li><a href="../index.php">Site Public</a></li>
                    <li><a href="#" onclick="logout()">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Dashboard Content -->
    <section class="dashboard">
        <div class="container">
            <h1>Tableau de Bord Admin</h1>
            <p style="color: var(--gray); margin-bottom: 2rem;">Bienvenue dans l'interface d'administration du Musée de Gaming</p>

            <!-- Statistiques -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="articlesCount">0</div>
                    <div class="stat-label">Articles Publiés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="commentsCount">0</div>
                    <div class="stat-label">Commentaires</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="draftsCount">0</div>
                    <div class="stat-label">Brouillons</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="gamesCount">0</div>
                    <div class="stat-label">Jeux en Base</div>
                </div>
            </div>

            <!-- Actions Rapides -->
            <h2>Actions Rapides</h2>
            <div class="quick-actions">
                <a href="blog-edit.php" class="action-card">
                    <div class="action-icon">📝</div>
                    <div>Nouvel Article</div>
                </a>
                <a href="blog-admin.php" class="action-card">
                    <div class="action-icon">📰</div>
                    <div>Gérer Articles</div>
                </a>
                <a href="comments-admin.php" class="action-card">
                    <div class="action-icon">💬</div>
                    <div>Modérer Commentaires</div>
                </a>
                <a href="games-admin.php" class="action-card">
                    <div class="action-icon">🎮</div>
                    <div>Gérer Jeux</div>
                </a>
            </div>

            <!-- Activité Récente -->
            <div class="recent-activity">
                <h2>Activité Récente</h2>
                <ul class="activity-list" id="recentActivity">
                    <!-- Rempli dynamiquement -->
                </ul>
            </div>
        </div>
    </section>

    <script src="../assets/js/state-manager.js"></script>
    <script>
        const FormValidator = {
            validateText: function(value, minLength = 2) {
                return value && value.trim().length >= minLength;
            },

            validateEmail: function(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            },

            validateNumber: function(number, min = null, max = null) {
                const num = parseFloat(number);
                if (isNaN(num)) return false;
                if (min !== null && num < min) return false;
                if (max !== null && num > max) return false;
                return true;
            },

            validateDate: function(dateString) {
                const date = new Date(dateString);
                return date instanceof Date && !isNaN(date);
            },

            validateURL: function(url) {
                try {
                    new URL(url);
                    return true;
                } catch {
                    return false;
                }
            },

            showError: function(input, message) {
                let errorElement = input.parentNode.querySelector('.error-message');
                if (!errorElement) {
                    errorElement = document.createElement('div');
                    errorElement.className = 'error-message';
                    input.parentNode.appendChild(errorElement);
                }
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                input.classList.add('error');
            },

            hideError: function(input) {
                const errorElement = input.parentNode.querySelector('.error-message');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
                input.classList.remove('error');
            },

            validateForm: function(formData, rules) {
                let isValid = true;
                
                for (const [field, rule] of Object.entries(rules)) {
                    const value = formData[field];
                    
                    if (rule.required && !value) {
                        isValid = false;
                        continue;
                    }
                    
                    if (value) {
                        switch (rule.type) {
                            case 'text':
                                if (!this.validateText(value, rule.minLength)) {
                                    isValid = false;
                                }
                                break;
                            case 'email':
                                if (!this.validateEmail(value)) {
                                    isValid = false;
                                }
                                break;
                            case 'number':
                                if (!this.validateNumber(value, rule.min, rule.max)) {
                                    isValid = false;
                                }
                                break;
                            case 'date':
                                if (!this.validateDate(value)) {
                                    isValid = false;
                                }
                                break;
                            case 'url':
                                if (!this.validateURL(value)) {
                                    isValid = false;
                                }
                                break;
                        }
                    }
                }
                
                return isValid;
            }
        };

        function loadDashboard() {
            const articles = AppState.getAllArticles();
            const comments = AppState.state.comments;
            
            document.getElementById('articlesCount').textContent = 
                articles.filter(a => a.status === 'published').length;
            document.getElementById('commentsCount').textContent = comments.length;
            document.getElementById('draftsCount').textContent = 
                articles.filter(a => a.status === 'draft').length;
            document.getElementById('gamesCount').textContent = 
                AppState.state.games.length;

            const recentActivity = document.getElementById('recentActivity');
            const recentArticles = articles.slice(0, 5);
            
            recentActivity.innerHTML = recentArticles.map(article => `
                <li class="activity-item">
                    <div class="activity-icon">📝</div>
                    <div>
                        <strong>${article.title}</strong><br>
                        <small>${formatDate(article.created_at)} - ${article.status === 'published' ? 'Publié' : 'Brouillon'}</small>
                    </div>
                </li>
            `).join('');
        }

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('fr-FR', options);
        }

        function logout() {
            AppState.logout();
            window.location.href = 'login.php';
        }

        function setupFormValidation() {
            const forms = document.querySelectorAll('form[data-validate]');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const data = Object.fromEntries(formData);
                    
                    const rules = {
                        title: { type: 'text', required: true, minLength: 2 },
                        email: { type: 'email', required: true },
                    };
                    
                    if (FormValidator.validateForm(data, rules)) {
                        this.submit();
                    } else {
                        alert('Veuillez corriger les erreurs dans le formulaire.');
                    }
                });
            });
        }

        if (!AppState.isAdminUser()) {
            window.location.href = 'login.php';
        } else {
            loadDashboard();
        }
    </script>
</body>
</html>