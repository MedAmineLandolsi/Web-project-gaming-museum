<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Musée de Gaming</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }

        .login-card {
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 15px;
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            text-align: center;
        }

        .login-logo {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .login-title {
            color: var(--primary);
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
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
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: var(--dark);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .login-btn:hover {
            background: #00cc66;
            transform: translateY(-2px);
        }

        .login-info {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--gray);
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

        @media (max-width: 480px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-card {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">🎮</div>
            <h1 class="login-title">Connexion Admin</h1>
            
            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="username" placeholder="Entrez votre nom d'utilisateur">
                    <div class="error-message" id="usernameError">Le nom d'utilisateur est requis</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" placeholder="Entrez votre mot de passe">
                    <div class="error-message" id="passwordError">Le mot de passe est requis</div>
                </div>
                
                <button type="button" class="login-btn" onclick="validateAndLogin()">Se connecter</button>
            </form>
            
            <div class="login-info">
                <strong>Compte de test :</strong><br>
                Identifiant: admin<br>
                Mot de passe: admin
            </div>
        </div>
    </div>

    <script src="../assets/js/state-manager.js"></script>
    <script>
        function validateLoginForm() {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            document.querySelectorAll('.error-message').forEach(error => {
                error.style.display = 'none';
            });
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error');
            });

            let isValid = true;

            if (!username) {
                document.getElementById('usernameError').style.display = 'block';
                document.getElementById('username').parentElement.classList.add('error');
                isValid = false;
            }

            if (!password) {
                document.getElementById('passwordError').style.display = 'block';
                document.getElementById('password').parentElement.classList.add('error');
                isValid = false;
            }

            return isValid;
        }

        function validateAndLogin() {
            if (!validateLoginForm()) {
                return;
            }
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (AppState.login(username, password)) {
                showNotification('Connexion réussie !', 'success');
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1000);
            } else {
                showNotification('Identifiants incorrects', 'error');
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 2rem;
                background: ${type === 'success' ? '#00ff88' : '#ff4757'};
                color: #1a1a2e;
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

        document.getElementById('loginForm').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                validateAndLogin();
            }
        });

        document.getElementById('username').addEventListener('input', function() {
            if (this.value.trim()) {
                this.parentElement.classList.remove('error');
                document.getElementById('usernameError').style.display = 'none';
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            if (this.value.trim()) {
                this.parentElement.classList.remove('error');
                document.getElementById('passwordError').style.display = 'none';
            }
        });
    </script>
</body>
</html>