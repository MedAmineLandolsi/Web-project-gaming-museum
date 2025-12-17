<?php
session_start();
// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../backoffice/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ludology Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
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
            max-width: 1200px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .page-header-title {
            font-size: 1.2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
        }

        .page-header-title span,
        .page-header-title a {
            padding: 0.5rem 1.5rem;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--text-gray);
            opacity: 0.5;
            cursor: pointer;
        }

        .page-header-title span.active {
            opacity: 1;
            color: var(--primary-green);
            text-shadow: 0 0 20px var(--primary-green);
        }

        .page-header-title #signupToggle:hover {
            opacity: 0.8;
            color: var(--secondary-purple);
        }

        .page-header-title .separator {
            opacity: 0.3;
            color: var(--primary-green);
            cursor: default;
        }

        .toggle-switch {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
        }

        [type="checkbox"]:checked,
        [type="checkbox"]:not(:checked) {
            position: absolute;
            left: -9999px;
        }

        .checkbox:checked + label,
        .checkbox:not(:checked) + label {
            position: relative;
            display: block;
            text-align: center;
            width: 100px;
            height: 24px;
            border-radius: 12px;
            padding: 0;
            margin: 0 auto;
            cursor: pointer;
            background-color: rgba(0, 255, 65, 0.2);
            border: 2px solid var(--primary-green);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
            transition: all 0.3s;
        }

        .checkbox:checked + label:before,
        .checkbox:not(:checked) + label:before {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: var(--primary-green);
            border: 3px solid var(--darker-bg);
            font-family: 'Press Start 2P';
            content: '>';
            font-size: 16px;
            color: var(--darker-bg);
            z-index: 20;
            top: -12px;
            left: -12px;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 0 25px rgba(0, 255, 65, 0.6);
        }

        .card-wrap {
            position: relative;
            width: 700px;
            max-width: 100%;
            margin: 0 auto;
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

        .card-single::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, 
                var(--primary-green), 
                var(--secondary-purple), 
                var(--accent-pink), 
                var(--primary-green)
            );
            background-size: 400%;
            border-radius: inherit;
            z-index: -1;
            opacity: 0.5;
            filter: blur(8px);
            animation: borderGlow 8s linear infinite;
        }

        @keyframes borderGlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .center-wrap {
            width: 100%;
            padding: 2.5rem 3.5rem 3rem 3.5rem;
            display: block;
        }

        .form-title {
            font-size: 1.8rem;
            color: var(--primary-green);
            text-shadow: 0 0 20px var(--primary-green);
            margin-bottom: 2rem;
            text-align: center;
            letter-spacing: 4px;
            position: absolute;
            padding-right: 1rem;
            padding-bottom: 1.2rem;
            padding-top: 0.5rem;
            top: 15%;
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

        .signup-link {
            font-size: 0.6rem;
            color: var(--text-light-gray);
        }

        .signup-link span {
            color: var(--secondary-purple);
            font-weight: bold;
            transition: all 0.3s;
        }

        .signup-link:hover span {
            color: var(--accent-pink);
            text-shadow: 0 0 10px var(--accent-pink);
        }

        .form-group {
            position: relative;
            display: block;
            margin: 0 0 1rem 0;
            padding: 0;
        }

        .form-style {
            padding: 1rem 1.2rem;
            padding-left: 3.2rem;
            height: 48px;
            width: 100%;
            font-weight: 400;
            font-size: 0.65rem;
            font-family: 'Press Start 2P', cursive;
            letter-spacing: 1px;
            outline: none;
            color: var(--text-white);
            background-color: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(0, 255, 65, 0.3);
            transition: all 300ms ease;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        .form-style:focus,
        .form-style:active {
            border-color: var(--primary-green);
            box-shadow: 
                0 0 20px rgba(0, 255, 65, 0.4), 
                0 0 30px rgba(189, 0, 255, 0.2),
                inset 0 2px 8px rgba(0, 0, 0, 0.6);
            background-color: rgba(0, 0, 0, 0.6);
        }

        .form-style::placeholder {
            color: var(--text-gray);
            opacity: 0.5;
            font-size: 0.55rem;
        }

        .input-icon {
            position: absolute;
            top: 0;
            left: 1rem;
            height: 48px;
            font-size: 18px;
            line-height: 48px;
            text-align: left;
            color: var(--primary-green);
            transition: all 300ms ease;
            pointer-events: none;
        }

        .form-group:focus-within .input-icon {
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            filter: drop-shadow(0 0 5px var(--secondary-purple));
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

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: radial-gradient(circle, var(--primary-green), var(--secondary-purple));
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn span {
            position: relative;
            z-index: 1;
        }

        .btn:active,
        .btn:focus {
            background: var(--primary-green);
            color: var(--darker-bg);
            box-shadow: 
                0 0 30px rgba(0, 255, 65, 0.6),
                0 0 50px rgba(189, 0, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn:hover {
            background: var(--primary-green);
            color: var(--darker-bg);
            box-shadow: 
                0 0 35px rgba(0, 255, 65, 0.7),
                0 0 50px rgba(255, 0, 110, 0.3);
            transform: translateY(-3px);
        }

        .link {
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.5rem;
            transition: all 0.3s;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .link:hover {
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .text-center {
            text-align: center;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-2 {
            margin-top: 1rem;
        }

        .pb-3 {
            padding-bottom: 1rem;
        }

        .error-message {
            display: none;
            color: #FF0055;
            font-size: 0.45rem;
            margin-top: 0.5rem;
            font-family: 'VT323', monospace;
            font-size: 0.8rem;
        }

        .captcha-error-message {
            display: none;
            color: #FF0055;
            font-size: 0.45rem;
            margin-top: 0.5rem;
            font-family: 'VT323', monospace;
            font-size: 0.8rem;
            text-align: center;
        }

        .captcha-container {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
            margin-bottom: 0;
        }

        .g-recaptcha {
            transform: scale(0.9);
            transform-origin: center;
        }

        /* Google Sign-In Styling */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 2rem 0 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }

        .divider span {
            padding: 0 1rem;
            color: var(--text-gray);
            font-size: 0.5rem;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .google-btn-container {
            margin-bottom: 2rem;
        }

        #googleSignInContainer {
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .google-blocked-message {
            padding: 1rem;
            background: rgba(255, 215, 0, 0.1);
            border: 2px solid #FFD700;
            color: #FFD700;
            font-size: 0.5rem;
            text-align: center;
            line-height: 1.6;
            font-family: 'VT323', monospace;
            font-size: 0.85rem;
        }

        .google-status {
            font-size: 0.5rem;
            color: var(--text-gray);
            text-align: center;
            margin-top: 0.5rem;
            font-family: 'VT323', monospace;
        }

        @media (max-width: 768px) {
            .card-wrap {
                width: 100%;
            }

            .center-wrap {
                padding: 2.5rem 2rem;
            }

            .form-title {
                font-size: 1.2rem;
                margin-bottom: 1.5rem;
            }

            .form-style {
                font-size: 0.6rem;
                height: 46px;
                padding-left: 3rem;
            }

            .input-icon {
                height: 46px;
                line-height: 46px;
                font-size: 16px;
            }

            .btn {
                font-size: 0.65rem;
                height: 46px;
            }

            .g-recaptcha {
                transform: scale(0.85);
            }
        }

        @media (max-width: 480px) {
            .center-wrap {
                padding: 2rem 1.5rem;
            }

            .form-title {
                font-size: 1rem;
                margin-bottom: 1.2rem;
            }

            .form-style {
                font-size: 0.55rem;
                height: 44px;
            }

            .input-icon {
                height: 44px;
                line-height: 44px;
            }

            .btn {
                font-size: 0.55rem;
                height: 44px;
            }

            .form-group {
                margin: 0 0 0.9rem 0;
            }

            .signup-link {
                font-size: 0.55rem;
            }

            .g-recaptcha {
                transform: scale(0.75);
            }
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
                <h2 class="page-header-title">
                    <span class="active">SIGN IN</span>
                    <span class="separator">&lt;&gt;</span>
                    <span id="signupToggle">SIGN UP</span>
                </h2>
                <div class="toggle-switch">
                    <input class="checkbox" type="checkbox" id="reg-log" name="reg-log"/>
                    <label for="reg-log"></label>
                </div>
            </div>
            
            <div class="card-wrap">
                <div class="card-single">
                    <div class="center-wrap">
                        <div class="section text-center">
                            <h3 class="form-title">CONNEXION</h3>

                            <!-- Google Sign-In Container -->
                            <div class="google-btn-container">
                                <div id="googleSignInContainer">
                                    <!-- Google button will be rendered here or fallback message -->
                                </div>
                                <div class="google-status" id="googleStatus">Checking Google Sign-In...</div>
                            </div>

                            <div class="divider">
                                <span>OU</span>
                            </div>

                            <form id="loginForm">
                                <div class="form-group">
                                    <input type="text" name="username" class="form-style" placeholder="Email ou Nom d'utilisateur" id="loginUsername" autocomplete="off" required>
                                    <i class="input-icon uil uil-user"></i>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group mt-2">
                                    <input type="password" name="password" class="form-style" placeholder="Mot de passe" id="loginPassword" autocomplete="off" required>
                                    <i class="input-icon uil uil-lock-alt"></i>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group captcha-container">
                                    <div class="g-recaptcha" 
                                         data-sitekey="6LdiwxwsAAAAAJvy6JuOZrKpQMYReIozQW-uRK5T"
                                         data-callback="onCaptchaSuccess">
                                    </div>
                                </div>
                                <span class="captcha-error-message"></span>
                                <button type="submit" class="btn mt-4"><span>SE CONNECTER</span></button>
                                <p class="mb-0 mt-4 text-center">
                                    <a href="forget_password.php" class="link">&gt; Mot de passe oublié ?</a>
                                </p>
                                <p class="mb-0 mt-3 text-center">
                                    <a href="signup.php" class="link signup-link">Pas encore inscrit ? <span>S'INSCRIRE &gt;</span></a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ========================================
        // GOOGLE OAUTH WITH NETWORK ERROR HANDLING
        // ========================================

        const DEBUG = true;
        const CLIENT_ID = '939931083288-4rc1jqq22l17lcfr9gs2fuplj1k9j5ps.apps.googleusercontent.com';
        
        let googleInitialized = false;
        let initializationAttempted = false;

        function debugLog(message, data = null) {
            if (DEBUG) {
                console.log(`[Google OAuth] ${message}`, data || '');
            }
        }

        // JWT Parser
        function parseJwt(token) {
            try {
                const base64Url = token.split('.')[1];
                const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                const jsonPayload = decodeURIComponent(
                    atob(base64).split('').map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)).join('')
                );
                return JSON.parse(jsonPayload);
            } catch (error) {
                debugLog('JWT parse error', error);
                throw new Error('Failed to parse JWT token');
            }
        }

        // Show fallback when Google is blocked
        function showGoogleBlockedMessage() {
            const container = document.getElementById('googleSignInContainer');
            const status = document.getElementById('googleStatus');
            
            container.innerHTML = `
                <div class="google-blocked-message">
                    ⚠️ GOOGLE SIGN-IN UNAVAILABLE<br>
                    <span style="font-size: 0.75rem; color: #888;">
                        Network blocking detected. Please use login below.
                    </span>
                </div>
            `;
            
            status.textContent = 'Google authentication temporarily unavailable';
            status.style.color = '#FFD700';
            
            debugLog('❌ Google Sign-In blocked - showing fallback message');
        }

        // Handle successful Google Sign-In
        function handleGoogleSignIn(response) {
            debugLog('✅ Google Sign-In callback received');
            
            try {
                const payload = parseJwt(response.credential);
                debugLog('Parsed JWT:', payload);
                
                const googleData = {
                    id: payload.sub,
                    email: payload.email,
                    given_name: payload.given_name || '',
                    family_name: payload.family_name || '',
                    picture: payload.picture || ''
                };
                
                // Update UI
                const status = document.getElementById('googleStatus');
                if (status) {
                    status.textContent = 'Signing in with Google...';
                    status.style.color = 'var(--primary-green)';
                }
                
                // Send to backend
                const formData = new FormData();
                formData.append('action', 'googleLogin');
                formData.append('google_data', JSON.stringify(googleData));
                
                fetch('../../controller/user_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    debugLog('Backend response:', data);
                    
                    if (data.success) {
                        if (status) status.textContent = 'Success! Redirecting...';
                        
                        setTimeout(() => {
                            if (data.role === 'admin') {
                                window.location.href = '../backoffice/dashboard.php';
                            } else {
                                window.location.href = 'index.php';
                            }
                        }, 500);
                    } else {
                        alert(data.message || 'Login failed');
                        if (status) status.textContent = 'Ready to sign in';
                    }
                })
                .catch(error => {
                    debugLog('Backend error:', error);
                    alert('Network error. Please try again.');
                    if (status) status.textContent = 'Ready to sign in';
                });
                
            } catch (error) {
                debugLog('Error processing sign-in:', error);
                alert('Failed to process Google Sign-In');
            }
        }

        // Initialize Google Sign-In
        function initializeGoogleSignIn() {
            if (initializationAttempted) {
                debugLog('Initialization already attempted');
                return googleInitialized;
            }
            
            initializationAttempted = true;
            debugLog('Starting Google Sign-In initialization...');
            
            // Check if Google library is loaded
            if (typeof google === 'undefined' || !google.accounts || !google.accounts.id) {
                debugLog('❌ Google library not loaded');
                showGoogleBlockedMessage();
                return false;
            }
            
            debugLog('✅ Google library loaded');
            
            try {
                // Initialize
                google.accounts.id.initialize({
                    client_id: CLIENT_ID,
                    callback: handleGoogleSignIn,
                    auto_select: false,
                    cancel_on_tap_outside: true
                });
                
                debugLog('✅ google.accounts.id.initialize() completed');
                
                // Get button container
                const container = document.getElementById('googleSignInContainer');
                if (!container) {
                    debugLog('❌ Button container not found');
                    return false;
                }
                
                // Render Google's button
                google.accounts.id.renderButton(container, {
                    theme: 'filled_blue',
                    size: 'large',
                    text: 'signin_with',
                    width: Math.min(container.offsetWidth || 400, 400)
                });
                
                debugLog('✅ Button rendered successfully');
                
                // Update status
                const status = document.getElementById('googleStatus');
                if (status) {
                    status.textContent = 'Google Sign-In ready';
                    status.style.color = 'var(--primary-green)';
                }
                
                googleInitialized = true;
                return true;
                
            } catch (error) {
                debugLog('❌ Initialization error:', error);
                showGoogleBlockedMessage();
                return false;
            }
        }

        // Wait for Google library and initialize
        function waitForGoogleAndInit() {
            debugLog('=== Waiting for Google library ===');
            
            let attempts = 0;
            const maxAttempts = 20; // Reduced from 30 for faster fallback
            const interval = 200;
            
            const checkInterval = setInterval(() => {
                attempts++;
                
                if (typeof google !== 'undefined' && google.accounts && google.accounts.id) {
                    debugLog(`✅ Google library loaded after ${attempts} attempts`);
                    clearInterval(checkInterval);
                    initializeGoogleSignIn();
                } else if (attempts >= maxAttempts) {
                    debugLog(`❌ Google library failed to load after ${maxAttempts} attempts`);
                    clearInterval(checkInterval);
                    showGoogleBlockedMessage();
                }
            }, interval);
        }

        // ========================================
        // REGULAR LOGIN FORM HANDLING
        // ========================================

        const checkbox = document.getElementById('reg-log');
        const signupToggle = document.getElementById('signupToggle');

        checkbox.addEventListener('change', function() {
            if (this.checked) {
                window.location.href = 'signup.php';
            }
        });

        signupToggle.addEventListener('click', function() {
            checkbox.checked = true;
            window.location.href = 'signup.php';
        });

        function showError(input, message) {
            const formGroup = input.closest('.form-group');
            let errorElement = formGroup.querySelector('.error-message');
            
            if (!errorElement) {
                errorElement = document.createElement('span');
                errorElement.className = 'error-message';
                formGroup.appendChild(errorElement);
            }
            
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            input.style.borderColor = '#FF0055';
        }

        function clearError(input) {
            const formGroup = input.closest('.form-group');
            const errorElement = formGroup.querySelector('.error-message');
            
            if (errorElement) {
                errorElement.style.display = 'none';
            }
            input.style.borderColor = 'rgba(0, 255, 65, 0.3)';
        }

        function showCaptchaError(message) {
            const errorElement = document.querySelector('.captcha-error-message');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function clearCaptchaError() {
            const errorElement = document.querySelector('.captcha-error-message');
            if (errorElement) {
                errorElement.style.display = 'none';
            }
        }

        function onCaptchaSuccess() {
            clearCaptchaError();
        }

        function validateLoginForm() {
            const username = document.getElementById('loginUsername');
            const password = document.getElementById('loginPassword');
            
            let isValid = true;
            
            if (!username.value.trim()) {
                showError(username, 'Username or email is required');
                isValid = false;
            } else {
                clearError(username);
            }
            
            if (!password.value.trim()) {
                showError(password, 'Password is required');
                isValid = false;
            } else {
                clearError(password);
            }
            
            const recaptchaResponse = grecaptcha.getResponse();
            if (!recaptchaResponse) {
                showCaptchaError('Please verify that you are not a robot');
                isValid = false;
            } else {
                clearCaptchaError();
            }
            
            return isValid;
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateLoginForm()) {
                const formData = new FormData(this);
                formData.append('action', 'login');
                
                const recaptchaResponse = grecaptcha.getResponse();
                formData.append('g-recaptcha-response', recaptchaResponse);
                
                fetch('../../controller/user_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.require_2fa) {
                            window.location.href = '2fa_verification.php';
                        } else {
                            if (data.role === 'admin') {
                                window.location.href = '../backoffice/dashboard.php';
                            } else {
                                window.location.href = 'index.php';
                            }
                        }
                    } else {
                        if (data.message.toLowerCase().includes('captcha') || 
                            data.message.toLowerCase().includes('robot') ||
                            data.message.toLowerCase().includes('verification')) {
                            showCaptchaError(data.message);
                            grecaptcha.reset();
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        });

        // Start Google initialization
        debugLog('=== Script Loaded ===');
        waitForGoogleAndInit();
    </script>
</body>
</html>