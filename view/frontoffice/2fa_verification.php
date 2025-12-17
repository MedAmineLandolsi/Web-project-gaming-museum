<?php
session_start();

// Redirect if not in 2FA flow
if (!isset($_SESSION['2fa_user_id']) || !isset($_SESSION['2fa_pending'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification 2FA - Ludology Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-green: #00FF41;
            --secondary-purple: #BD00FF;
            --accent-pink: #FF006E;
            --dark-bg: #0a0a0a;
            --darker-bg: #050505;
            --card-bg: #1a1a1a;
            --text-white: #ffffff;
            --text-gray: #888888;
            --text-light-gray: #aaaaaa;
            --border-color: #333333;
        }

        body {
            font-family: 'Press Start 2P', cursive;
            background-color: var(--dark-bg);
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 65, 0.02) 2px,
                    rgba(0, 255, 65, 0.02) 4px
                );
            color: var(--text-white);
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background-color: var(--primary-green);
            opacity: 0.3;
            animation: float 20s infinite;
            box-shadow: 0 0 10px var(--primary-green);
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 15s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; animation-duration: 18s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; animation-duration: 20s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 1s; animation-duration: 17s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 3s; animation-duration: 16s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; animation-duration: 19s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 2.5s; animation-duration: 21s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 4.5s; animation-duration: 15.5s; }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 0.3;
            }
            90% {
                opacity: 0.3;
            }
            50% {
                transform: translateY(-100vh) translateX(50px);
            }
        }

        .grid-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 255, 65, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 65, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            opacity: 0.3;
            z-index: 0;
        }

        @keyframes gridMove {
            0% { transform: perspective(500px) rotateX(60deg) translateY(0); }
            100% { transform: perspective(500px) rotateX(60deg) translateY(50px); }
        }

        .scanline {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                0deg,
                rgba(0, 0, 0, 0.15),
                rgba(0, 0, 0, 0.15) 1px,
                transparent 1px,
                transparent 2px
            );
            pointer-events: none;
            animation: scan 8s linear infinite;
            z-index: 1;
        }

        @keyframes scan {
            0% { transform: translateY(0); }
            100% { transform: translateY(10px); }
        }

        .section {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
            z-index: 2;
        }

        .container {
            width: 100%;
            max-width: 600px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .page-header-title {
            font-size: 1.2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .card-single {
            width: 100%;
            background: linear-gradient(135deg, rgba(10, 10, 10, 0.95), rgba(26, 26, 26, 0.95));
            backdrop-filter: blur(20px);
            border: 3px solid var(--primary-green);
            box-shadow: 
                0 20px 60px rgba(0, 255, 65, 0.4), 
                inset 0 0 100px rgba(0, 255, 65, 0.05),
                0 0 40px rgba(189, 0, 255, 0.2);
            overflow: hidden;
            position: relative;
        }

        .card-single::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 65, 0.03) 2px,
                    rgba(0, 255, 65, 0.03) 4px
                );
            pointer-events: none;
        }

        .center-wrap {
            width: 100%;
            padding: 3rem;
        }

        .form-title {
            font-size: 1.5rem;
            color: var(--primary-green);
            text-shadow: 0 0 20px var(--primary-green);
            margin-bottom: 1.5rem;
            text-align: center;
            letter-spacing: 4px;
            position: relative;
            padding-bottom: 1.2rem;
        }

        .form-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 3px;
            background: linear-gradient(90deg, 
                transparent, 
                var(--primary-green), 
                transparent
            );
            box-shadow: 0 0 15px var(--primary-green);
        }

        .form-description {
            font-size: 0.55rem;
            color: var(--text-light-gray);
            text-align: center;
            margin-bottom: 2rem;
            font-family: 'VT323', monospace;
            font-size: 1rem;
            line-height: 1.6;
        }

        .code-inputs {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .code-input {
            width: 60px;
            height: 70px;
            text-align: center;
            font-size: 2rem;
            font-family: 'Press Start 2P', cursive;
            color: var(--primary-green);
            background-color: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(0, 255, 65, 0.3);
            transition: all 300ms ease;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        .code-input:focus {
            border-color: var(--primary-green);
            box-shadow: 
                0 0 20px rgba(0, 255, 65, 0.4), 
                inset 0 2px 8px rgba(0, 0, 0, 0.6);
            background-color: rgba(0, 0, 0, 0.6);
            outline: none;
        }

        .btn {
            width: 100%;
            height: 48px;
            font-size: 0.7rem;
            font-weight: 600;
            font-family: 'Press Start 2P', cursive;
            text-transform: uppercase;
            transition: all 300ms ease;
            padding: 0 30px;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 2px solid var(--primary-green);
            background: transparent;
            color: var(--primary-green);
            cursor: pointer;
            box-shadow: 
                0 0 20px rgba(0, 255, 65, 0.3),
                inset 0 0 20px rgba(0, 255, 65, 0.05);
            margin-top: 1.2rem;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            background: var(--primary-green);
            color: var(--darker-bg);
            box-shadow: 
                0 0 35px rgba(0, 255, 65, 0.7),
                0 0 50px rgba(255, 0, 110, 0.3);
            transform: translateY(-3px);
        }

        .btn span {
            position: relative;
            z-index: 1;
        }

        .timer {
            text-align: center;
            font-size: 0.6rem;
            color: var(--secondary-purple);
            margin-top: 1rem;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .resend-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.5rem;
            transition: all 0.3s;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .resend-link:hover {
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .error-message {
            display: none;
            color: #FF0055;
            font-size: 0.5rem;
            margin-top: 1rem;
            text-align: center;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        .success-message {
            display: none;
            color: var(--primary-green);
            font-size: 0.5rem;
            margin-top: 1rem;
            text-align: center;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="grid-background"></div>
    <div class="scanline"></div>

    <div class="section">
        <div class="container">
            <div class="page-header">
                <h2 class="page-header-title">◄ VERIFICATION 2FA ►</h2>
            </div>
            
            <div class="card-single">
                <div class="center-wrap">
                    <h3 class="form-title">🔐 CODE</h3>
                    <p class="form-description">
                        Un code à 6 chiffres a été envoyé à votre email.<br>
                        Entrez-le ci-dessous pour continuer.
                    </p>

                    <div id="successMessage" class="success-message"></div>
                    <div id="errorMessage" class="error-message"></div>

                    <form id="verify2FAForm">
                        <div class="code-inputs">
                            <input type="text" maxlength="1" class="code-input" id="code1" autocomplete="off" required>
                            <input type="text" maxlength="1" class="code-input" id="code2" autocomplete="off" required>
                            <input type="text" maxlength="1" class="code-input" id="code3" autocomplete="off" required>
                            <input type="text" maxlength="1" class="code-input" id="code4" autocomplete="off" required>
                            <input type="text" maxlength="1" class="code-input" id="code5" autocomplete="off" required>
                            <input type="text" maxlength="1" class="code-input" id="code6" autocomplete="off" required>
                        </div>

                        <div class="timer" id="timer">Code expire dans: <span id="countdown">5:00</span></div>

                        <button type="submit" class="btn"><span>VERIFIER</span></button>

                        <a href="#" class="resend-link" id="resendLink">Renvoyer le code</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus and move to next input
        const inputs = document.querySelectorAll('.code-input');
        
        inputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Only allow numbers
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });

        // Focus first input on load
        inputs[0].focus();

        // Countdown timer (5 minutes)
        let timeLeft = 300;
        const countdownElement = document.getElementById('countdown');
        
        const timerInterval = setInterval(() => {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                showError('Le code a expiré. Veuillez vous reconnecter.');
                document.getElementById('verify2FAForm').style.display = 'none';
            }
        }, 1000);

        function showError(message) {
            const errorElement = document.getElementById('errorMessage');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            document.getElementById('successMessage').style.display = 'none';
        }

        function showSuccess(message) {
            const successElement = document.getElementById('successMessage');
            successElement.textContent = message;
            successElement.style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
        }

        document.getElementById('verify2FAForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const code = Array.from(inputs).map(input => input.value).join('');
            
            if (code.length !== 6) {
                showError('Veuillez entrer le code complet');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'verify2FA');
            formData.append('code', code);
            
            fetch('../../controller/user_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Vérification réussie! Redirection...');
                    setTimeout(() => {
                        if (data.role === 'admin') {
                            window.location.href = '../backoffice/dashboard.php';
                        } else {
                            window.location.href = 'index.php';
                        }
                    }, 1500);
                } else {
                    showError(data.message);
                    // Clear inputs
                    inputs.forEach(input => input.value = '');
                    inputs[0].focus();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Une erreur est survenue. Veuillez réessayer.');
            });
        });

        document.getElementById('resendLink').addEventListener('click', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'resend2FA');
            
            fetch('../../controller/user_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Un nouveau code a été envoyé!');
                    // Reset timer
                    timeLeft = 300;
                    inputs.forEach(input => input.value = '');
                    inputs[0].focus();
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Erreur lors du renvoi du code.');
            });
        });
    </script>
</body>
</html>
