<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Article.php';

$database = new Database();
$db = $database->getConnection();

$articleModel = new Article($db);
$recentArticles = $articleModel->lireDerniers(10)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos - Blog Gaming</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            background: linear-gradient(180deg, var(--darker-bg) 0%, rgba(10, 10, 10, 0.95) 100%);
            border-bottom: 2px solid var(--primary-green);
            padding: 1.2rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 30px rgba(0, 255, 65, 0.2);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-green);
            text-shadow: 
                0 0 10px var(--primary-green),
                0 0 20px var(--primary-green),
                0 0 30px var(--primary-green);
            letter-spacing: 2px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: var(--text-white);
            text-decoration: none;
            font-size: 0.7rem;
            transition: all 0.3s;
            padding: 0.5rem 0;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-purple));
            transition: width 0.3s;
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .admin-btn {
            background: linear-gradient(135deg, var(--secondary-purple), var(--accent-pink));
            color: var(--text-white) !important;
            padding: 0.9rem 1.8rem;
            border-radius: 0;
            font-weight: bold;
            border: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.4);
            transition: all 0.3s;
        }

        .admin-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 0 30px rgba(189, 0, 255, 0.8);
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--accent-pink), #ff1a75);
            color: var(--text-white) !important;
            padding: 0.9rem 1.8rem;
            border-radius: 0;
            font-weight: bold;
            border: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.4);
            transition: all 0.3s;
        }

        .logout-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 0 30px rgba(255, 0, 110, 0.8);
        }

        /* About Section */
        .about-section {
            margin-top: 100px;
            padding: 4rem 0;
        }

        .about-hero {
            background: radial-gradient(ellipse at center, rgba(0, 255, 65, 0.1) 0%, transparent 70%),
                        linear-gradient(180deg, var(--darker-bg) 0%, var(--dark-bg) 100%);
            padding: 6rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 4rem;
            border-radius: 0;
            border: 2px solid var(--primary-green);
            position: relative;
            overflow: hidden;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-green);
            text-shadow: 
                0 0 10px var(--primary-green),
                0 0 20px var(--primary-green),
                0 0 40px var(--primary-green);
            margin-bottom: 1.5rem;
            line-height: 1.1;
            animation: glitch 5s infinite;
        }

        @keyframes glitch {
            0%, 90%, 100% { 
                transform: translate(0);
                text-shadow: 
                    0 0 10px var(--primary-green),
                    0 0 20px var(--primary-green);
            }
            92% { 
                transform: translate(-3px, 3px);
                text-shadow: 
                    3px -3px 0 var(--secondary-purple),
                    -3px 3px 0 var(--accent-pink);
            }
            94% { 
                transform: translate(3px, -3px);
                text-shadow: 
                    -3px 3px 0 var(--secondary-purple),
                    3px -3px 0 var(--accent-pink);
            }
        }

        .section-subtitle {
            color: var(--secondary-purple);
            font-size: 1.5rem;
            font-weight: 500;
            font-family: 'VT323', monospace;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .about-text {
            font-size: 1.125rem;
            line-height: 1.8;
            margin-bottom: 4rem;
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .about-text p {
            margin-bottom: 2rem;
        }

        /* Mission & Vision */
        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin: 4rem 0;
        }

        .mission-card, .vision-card {
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 0;
            border: 2px solid var(--border-color);
            text-align: center;
            transition: all 0.4s;
            backdrop-filter: blur(20px);
        }

        .mission-card:hover, .vision-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(0, 255, 65, 0.4);
            border-color: var(--primary-green);
        }

        .card-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }

        .card-title {
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .card-title + p {
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        /* Team Section */
        .team-section {
            margin: 6rem 0;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .team-member {
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 0;
            text-align: center;
            border: 2px solid var(--border-color);
            transition: all 0.4s;
            backdrop-filter: blur(20px);
        }

        .team-member:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(0, 255, 65, 0.4);
            border-color: var(--primary-green);
        }

        .member-avatar {
            width: 120px;
            height: 120px;
            border-radius: 0;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            box-shadow: 0 8px 24px rgba(0, 255, 65, 0.3);
            border: 2px solid var(--primary-green);
        }

        .member-name {
            color: var(--primary-green);
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .member-role {
            color: var(--secondary-purple);
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .member-role + p {
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Stats */
        .stats-section {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1) 0%, rgba(189, 0, 255, 0.05) 100%);
            padding: 4rem;
            border-radius: 0;
            margin: 4rem 0;
            border: 2px solid var(--primary-green);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-item .number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-green);
            text-shadow: 0 0 15px var(--primary-green);
            display: block;
            line-height: 1;
        }

        .stat-item .label {
            color: var(--text-gray);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.5rem;
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
        }

        /* Community CTA */
        .community-cta {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            padding: 4rem;
            border-radius: 0;
            text-align: center;
            margin: 4rem 0;
            box-shadow: 0 16px 40px rgba(0, 255, 65, 0.3);
            border: 2px solid var(--primary-green);
        }

        .community-cta h2 {
            color: var(--darker-bg);
            margin-bottom: 1.5rem;
            font-size: 2rem;
            font-weight: 800;
        }

        .community-cta p {
            color: var(--darker-bg);
            margin-bottom: 2.5rem;
            font-size: 1.25rem;
            font-family: 'VT323', monospace;
        }

        /* FAQ Section */
        .faq-section {
            margin: 6rem 0;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            background: var(--card-bg);
            margin-bottom: 1.5rem;
            border: 2px solid var(--border-color);
            border-radius: 0;
            overflow: hidden;
            transition: all 0.3s;
        }

        .faq-item:hover {
            border-color: var(--primary-green);
        }

        .faq-question {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.05));
            color: var(--primary-green);
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
            font-family: 'Press Start 2P', cursive;
        }

        .faq-question:hover {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.2), rgba(189, 0, 255, 0.1));
        }

        .faq-question.active {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            color: var(--darker-bg);
        }

        .faq-answer {
            padding: 0 2rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease-out;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .faq-answer.show {
            padding: 2rem;
            max-height: 500px;
        }

        .faq-toggle {
            color: var(--primary-green);
            font-size: 1.5rem;
            transition: transform 0.3s;
        }

        .faq-question.active .faq-toggle {
            color: var(--darker-bg);
            transform: rotate(45deg);
        }

        .contact-info {
            text-align: center;
            margin-top: 3rem;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05), rgba(255, 0, 110, 0.05));
            border: 2px solid var(--primary-green);
            border-radius: 0;
        }

        .contact-info h3 {
            color: var(--primary-green);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .contact-info p {
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .phone-number {
            color: var(--accent-pink) !important;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(255, 0, 110, 0.5);
        }

        /* Chatbot Widget */
        .chatbot-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
        }

        .chatbot-toggle {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(0, 255, 65, 0.4);
            border: 2px solid var(--primary-green);
            transition: all 0.3s;
            font-size: 2.5rem;
            color: var(--darker-bg);
            position: relative;
        }

        .chatbot-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 35px rgba(0, 255, 65, 0.6);
        }

        .chatbot-toggle.active {
            background: linear-gradient(135deg, var(--accent-pink), var(--secondary-purple));
            transform: rotate(90deg);
        }

        .chatbot-toggle::after {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border-radius: 50%;
            border: 2px solid var(--primary-green);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                opacity: 0.7;
            }
            70% {
                transform: scale(1.1);
                opacity: 0;
            }
            100% {
                transform: scale(0.95);
                opacity: 0;
            }
        }

        .chatbot-container {
            position: absolute;
            bottom: 90px;
            right: 0;
            width: 350px;
            background: var(--darker-bg);
            border-radius: 15px;
            border: 2px solid var(--primary-green);
            box-shadow: 0 10px 40px rgba(0, 255, 65, 0.3);
            overflow: hidden;
            display: none;
            flex-direction: column;
            backdrop-filter: blur(20px);
        }

        .chatbot-container.active {
            display: flex;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chatbot-header {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--darker-bg);
            font-weight: bold;
            font-size: 0.8rem;
        }

        .chatbot-header span {
            font-size: 1.5rem;
        }

        .chatbot-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            max-height: 400px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            padding: 12px 16px;
            border-radius: 15px;
            max-width: 85%;
            font-size: 0.8rem;
            line-height: 1.4;
            font-family: 'VT323', monospace;
            word-wrap: break-word;
        }

        .bot-message {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.05));
            color: var(--text-white);
            border: 1px solid var(--primary-green);
            align-self: flex-start;
            border-bottom-left-radius: 5px;
        }

        .user-message {
            background: linear-gradient(135deg, var(--secondary-purple), var(--accent-pink));
            color: var(--text-white);
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }

        .chatbot-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 20px;
            background: rgba(26, 26, 26, 0.8);
        }

        .chatbot-option {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            padding: 12px 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.7rem;
            text-align: left;
            font-family: 'Press Start 2P', cursive;
        }

        .chatbot-option:hover {
            background: rgba(0, 255, 65, 0.1);
            border-color: var(--primary-green);
            transform: translateX(5px);
        }

        .chatbot-input {
            padding: 15px;
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 10px;
        }

        .chatbot-input input {
            flex: 1;
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            padding: 10px 15px;
            font-family: 'VT323', monospace;
            font-size: 1rem;
            border-radius: 5px;
        }

        .chatbot-input input:focus {
            outline: none;
            border-color: var(--primary-green);
        }

        .chatbot-input button {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            border: none;
            color: var(--darker-bg);
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            transition: all 0.3s;
        }

        .chatbot-input button:hover {
            transform: scale(1.05);
        }

        .close-chatbot {
            margin-left: auto;
            background: none;
            border: none;
            color: var(--darker-bg);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .close-chatbot:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 1.25rem 2.5rem;
            background: var(--darker-bg);
            color: var(--text-white);
            text-decoration: none;
            border-radius: 0;
            font-weight: 700;
            transition: all 0.3s;
            border: 2px solid var(--darker-bg);
            cursor: pointer;
            font-size: 0.8rem;
            font-family: 'Press Start 2P', cursive;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            background: transparent;
            color: var(--darker-bg);
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 255, 65, 0.4);
        }

        /* Footer */
        .footer {
            background: var(--darker-bg);
            padding: 4rem 0 2rem;
            margin-top: 6rem;
            border-top: 3px solid var(--primary-green);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-section h4 {
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            text-shadow: 0 0 10px var(--primary-green);
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.75rem;
        }

        .footer-section a {
            color: var(--text-gray);
            text-decoration: none;
            transition: color 0.3s;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        .footer-section a:hover {
            color: var(--primary-green);
            transform: translateX(5px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 3rem;
            border-top: 1px solid var(--border-color);
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 4px;
        }

        .mobile-menu span {
            width: 25px;
            height: 3px;
            background: var(--primary-green);
            transition: 0.3s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .section-title {
                font-size: 1.8rem;
            }
            
            .about-hero {
                padding: 4rem 1.5rem;
            }
            
            .mission-vision {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: var(--darker-bg);
                padding: 1.5rem;
                border-top: 2px solid var(--primary-green);
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .mobile-menu {
                display: flex;
            }
            
            .community-cta {
                padding: 3rem 1.5rem;
            }
            
            .community-cta h2 {
                font-size: 1.5rem;
            }
            
            .faq-question {
                font-size: 0.7rem;
                padding: 1rem 1.5rem;
            }
            
            .faq-answer.show {
                padding: 1.5rem;
            }
            
            .chatbot-container {
                width: 300px;
                right: -10px;
            }
            
            .chatbot-toggle {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
            
            .chatbot-messages {
                max-height: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Particules d'arrière-plan -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">🎮 LV BLOG GAMING</div>
                <ul class="nav-links">
                    <li><a href="index.php">ACCUEIL</a></li>
                    <li><a href="blog.php">ARTICLES</a></li>
                    <li><a href="about.php" class="active">À PROPOS</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="submit-article.php">✍️ ÉCRIRE UN ARTICLE</a></li>
                        <li><a href="mes-articles.php">MES ARTICLES</a></li>
                        <li><a href="deconnexion.php" class="logout-btn">DÉCONNEXION (<?php echo $_SESSION['user_prenom']; ?>)</a></li>
                    <?php else: ?>
                        <li><a href="submit-article.php">✍️ ÉCRIRE UN ARTICLE</a></li>
                        <li><a href="connexion.php">SE CONNECTER</a></li>
                        <li><a href="inscription.php">S'INSCRIRE</a></li>
                        <li><a href="../Backoffice/login.php" class="admin-btn">ESPACE ADMIN</a></li>
                    <?php endif; ?>
                </ul>
                <div class="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <section class="about-section">
        <div class="container">
            <div class="about-hero">
                <div class="hero-content">
                    <h1 class="section-title">NOTRE HISTOIRE</h1>
                    <p class="section-subtitle">Découvrez la passion qui anime le Blog Gaming</p>
                </div>
            </div>

            <div class="about-content">
                <div class="about-text">
                    <p>
                        Fondé en 2024, le <strong style="color: var(--primary-green);">BLOG GAMING</strong> est bien plus qu'un simple site web. 
                        C'est une passion, une communauté, et un hommage à l'art du jeu vidéo. 
                        Notre mission est de préserver, documenter et célébrer la riche histoire 
                        du gaming tout en accompagnant son évolution future.
                    </p>

                    <p>
                        Nous croyons que les jeux vidéo sont une forme d'art à part entière, 
                        mêlant narration, design, musique et technologie. Chaque jeu raconte 
                        une histoire, et nous sommes là pour vous aider à découvrir ces récits 
                        fascinants.
                    </p>

                    <p>
                        Notre plateforme est ouverte à tous les passionnés qui souhaitent 
                        partager leur expérience, leurs analyses et leur amour du gaming 
                        avec une communauté grandissante de joueurs.
                    </p>
                </div>

                <div class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="number"><?php echo count($recentArticles) > 0 ? count($recentArticles) . '+' : '10+'; ?></span>
                            <span class="label">Articles Publiés</span>
                        </div>
                        <div class="stat-item">
                            <span class="number">4</span>
                            <span class="label">Catégories</span>
                        </div>
                        <div class="stat-item">
                            <span class="number">24/7</span>
                            <span class="label">Communauté Active</span>
                        </div>
                        <div class="stat-item">
                            <span class="number">2024</span>
                            <span class="label">Fondation</span>
                        </div>
                    </div>
                </div>

                <div class="mission-vision">
                    <div class="mission-card">
                        <div class="card-icon">🎯</div>
                        <h3 class="card-title">NOTRE MISSION</h3>
                        <p>
                            Documenter et partager la culture gaming sous toutes ses formes, 
                            en offrant une plateforme éducative et engageante pour tous les 
                            passionnés de jeux vidéo.
                        </p>
                    </div>

                    <div class="vision-card">
                        <div class="card-icon">🔮</div>
                        <h3 class="card-title">NOTRE VISION</h3>
                        <p>
                            Devenir la référence francophone pour la préservation et la 
                            célébration de la culture vidéoludique, en connectant les 
                            générations de joueurs à travers le monde.
                        </p>
                    </div>
                </div>

                <div class="community-cta">
                    <h2>REJOIGNEZ NOTRE COMMUNAUTÉ !</h2>
                    <p>Partagez votre passion et contribuez à enrichir notre collection d'articles gaming</p>
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'submit-article.php' : 'inscription.php'; ?>" class="btn">
                        ✍️ COMMENCER À ÉCRIRE
                    </a>
                </div>

                <div class="team-section">
                    <h2 style="text-align: center; margin-bottom: 1rem; color: var(--primary-green); font-size: 2rem; font-weight: 800;">
                        NOTRE ÉQUIPE
                    </h2>
                    <p style="text-align: center; color: var(--secondary-purple); margin-bottom: 3rem; font-size: 1.25rem; font-family: 'VT323', monospace;">
                        Des passionnés au service de la communauté gaming
                    </p>

                    <div class="team-grid">
                        <div class="team-member">
                            <div class="member-avatar">👨‍💻</div>
                            <h4 class="member-name">ILEF KAROUI</h4>
                            <div class="member-role">INGÉNIEUR INFORMATIQUE</div>
                            <p>Spécialiste des RPG et jeux d'action, apporte son expertise technique et sa passion pour le gaming depuis plus de 10 ans.</p>
                        </div>
                    </div>
                </div>

                <div class="mission-vision">
                    <div class="mission-card">
                        <div class="card-icon">🌟</div>
                        <h3 class="card-title">NOS VALEURS</h3>
                        <p>
                            <strong style="color: var(--primary-green);">PASSION</strong> - Partager notre amour du gaming<br>
                            <strong style="color: var(--primary-green);">QUALITÉ</strong> - Du contenu soigné et vérifié<br>
                            <strong style="color: var(--primary-green);">COMMUNAUTÉ</strong> - Une plateforme ouverte à tous<br>
                            <strong style="color: var(--primary-green);">INNOVATION</strong> - Toujours à la pointe
                        </p>
                    </div>

                    <div class="vision-card">
                        <div class="card-icon">🚀</div>
                        <h3 class="card-title">NOS PROJETS</h3>
                        <p>
                            • Base de données gaming étendue<br>
                            • Interviews de développeurs<br>
                            • Événements communautaires<br>
                            • Contenu exclusif
                        </p>
                    </div>
                </div>

                <!-- NOUVELLE SECTION FAQ -->
                <div class="faq-section">
                    <h2 style="text-align: center; margin-bottom: 1rem; color: var(--primary-green); font-size: 2rem; font-weight: 800;">
                        QUESTIONS FRÉQUENTES
                    </h2>
                    <p style="text-align: center; color: var(--secondary-purple); margin-bottom: 3rem; font-size: 1.25rem; font-family: 'VT323', monospace;">
                        Les réponses à vos questions les plus courantes
                    </p>

                    <div class="faq-container">
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>🤝 JE VEUX FAIRE PARTIE DE VOTRE ÉQUIPE - COMMENT PROCÉDER ?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>Absolument ! Nous sommes toujours à la recherche de passionnés pour rejoindre notre équipe. Vous pouvez nous contacter directement par téléphone au <span class="phone-number">+216 21 121 732</span> pour discuter des opportunités de collaboration.</p>
                                <p style="margin-top: 1rem; color: var(--primary-green);">💼 <strong>Postes disponibles :</strong> Rédacteur gaming, Designer, Développeur Web, Community Manager</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question">
                                <span>📝 COMMENT PROPOSER UN ARTICLE SUR VOTRE SITE ?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>C'est très simple ! Cliquez sur le bouton "✍️ ÉCRIRE UN ARTICLE" dans le menu de navigation. Vous serez redirigé vers un formulaire où vous pourrez soumettre votre article. Nos modérateurs examineront votre soumission et vous contacteront dans les 48 heures.</p>
                                <p style="margin-top: 1rem; color: var(--primary-green);">⚠️ <strong>Important :</strong> Les articles doivent être originaux et respecter nos guidelines de contenu.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question">
                                <span>🎮 PUIS-JE PARTAGER MA PASSION DU GAMING MÊME SI JE SUIS DÉBUTANT ?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>Bien sûr ! Notre communauté accueille tous les niveaux, des débutants aux experts. Chaque perspective est précieuse. N'hésitez pas à partager vos découvertes, vos premières impressions ou vos questions. La diversité des points de vue enrichit notre communauté.</p>
                                <p style="margin-top: 1rem; color: var(--primary-green);">🌟 <strong>Notre devise :</strong> "Chaque joueur a une histoire à raconter !"</p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-info">
                        <h3>📞 BESOIN DE PLUS D'INFORMATIONS ?</h3>
                        <p>Appelez-nous directement : <span class="phone-number">+216 21 121 732</span></p>
                        <p style="margin-top: 1rem; font-size: 1rem;">📧 Ou envoyez-nous un email : <strong style="color: var(--primary-green);">LV@blog-gaming.fr</strong></p>
                    </div>
                </div>
                <!-- FIN DE LA SECTION FAQ -->
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo" style="font-size: 1.2rem; margin-bottom: 1rem;">🎮 BLOG GAMING</div>
                    <p style="color: var(--text-gray); font-family: 'VT323', monospace; font-size: 1rem;">Votre destination ultime pour la culture gaming</p>
                </div>
                <div class="footer-section">
                    <h4>NAVIGATION</h4>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="blog.php">Articles</a></li>
                        <li><a href="about.php">À propos</a></li>
                        <li><a href="submit-article.php">Écrire un article</a></li>
                        <li><a href="mes-articles.php">Mes articles</a></li>
                        <li><a href="deconnexion.php">Déconnexion</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>CONTACT</h4>
                    <p style="color: var(--text-gray); font-family: 'VT323', monospace; font-size: 1rem;">LV@blog-gaming.fr<br>+216 21 121 732</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 BLOG GAMING. TOUS DROITS RÉSERVÉS.</p>
            </div>
        </div>
    </footer>

    <!-- Chatbot Widget -->
    <div class="chatbot-widget">
        <div class="chatbot-toggle">🤖</div>
        <div class="chatbot-container">
            <div class="chatbot-header">
                <span>🤖</span>
                Assistant LV Gaming
                <button class="close-chatbot">&times;</button>
            </div>
            <div class="chatbot-messages">
                <div class="message bot-message">
                    Salut ! Je suis l'assistant du Blog Gaming LV. 👋<br>
                    Choisis une question ci-dessous pour obtenir une réponse rapide !
                </div>
            </div>
            <div class="chatbot-options">
                <button class="chatbot-option" data-question="1">
                    🤝 Je veux faire partie de votre équipe
                </button>
                <button class="chatbot-option" data-question="2">
                    📝 Comment proposer un article ?
                </button>
                <button class="chatbot-option" data-question="3">
                    🎮 Je suis débutant, puis-je participer ?
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.querySelector('.mobile-menu');
            const navLinksContainer = document.querySelector('.nav-links');
            
            if (mobileMenu && navLinksContainer) {
                mobileMenu.addEventListener('click', function() {
                    navLinksContainer.classList.toggle('active');
                });
            }

            // Animation des particules
            const particles = document.querySelectorAll('.particle');
            particles.forEach((particle, index) => {
                particle.style.animationDelay = `${index * 2}s`;
                particle.style.animationDuration = `${15 + Math.random() * 10}s`;
            });

            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-links a');
            
            navLinks.forEach(link => {
                const linkPage = link.getAttribute('href');
                if (linkPage === currentPage) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });

            // FAQ Toggle Functionality
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const answer = this.nextElementSibling;
                    const isActive = this.classList.contains('active');
                    
                    // Close all other items
                    faqQuestions.forEach(q => {
                        if (q !== this) {
                            q.classList.remove('active');
                            q.nextElementSibling.classList.remove('show');
                        }
                    });
                    
                    // Toggle current item
                    this.classList.toggle('active');
                    
                    if (!isActive) {
                        answer.classList.add('show');
                    } else {
                        answer.classList.remove('show');
                    }
                });
            });

            // Open first FAQ item by default
            if (faqQuestions.length > 0) {
                faqQuestions[0].classList.add('active');
                faqQuestions[0].nextElementSibling.classList.add('show');
            }

            // Chatbot Functionality
            const chatbotToggle = document.querySelector('.chatbot-toggle');
            const chatbotContainer = document.querySelector('.chatbot-container');
            const closeChatbot = document.querySelector('.close-chatbot');
            const chatbotOptions = document.querySelectorAll('.chatbot-option');
            const chatbotMessages = document.querySelector('.chatbot-messages');

            // Réponses du chatbot
            const chatbotResponses = {
                1: {
                    question: "🤝 Je veux faire partie de votre équipe",
                    answer: "🎮 <strong>Super !</strong> Nous sommes toujours ravis d'accueillir de nouveaux passionnés dans notre équipe.<br><br>📞 <strong>Contactez-nous directement :</strong> Appelez le <span style='color: #FF006E; font-weight: bold;'>+216 21 121 732</span><br><br>💼 <strong>Nous recherchons :</strong> Rédacteurs gaming, développeurs web, designers, community managers.<br><br>🚀 <strong>Notre équipe attend votre appel !</strong>"
                },
                2: {
                    question: "📝 Comment proposer un article ?",
                    answer: "✍️ <strong>C'est très simple !</strong> Voici la procédure :<br><br>1. Clique sur '✍️ ÉCRIRE UN ARTICLE' dans le menu<br>2. Remplis le formulaire avec ton contenu<br>3. Nos modérateurs examineront ta soumission<br>4. Tu recevras une réponse sous 48h<br><br>📋 <strong>Important :</strong> Les articles doivent être originaux et respecter nos guidelines."
                },
                3: {
                    question: "🎮 Je suis débutant, puis-je participer ?",
                    answer: "🌟 <strong>Absolument !</strong> Notre communauté accueille <strong>TOUS</strong> les niveaux !<br><br>🎯 <strong>Pourquoi participer :</strong><br>• Partage tes premières impressions<br>• Pose tes questions<br>• Découvre de nouveaux jeux<br>• Apprends avec la communauté<br><br>💚 <strong>Notre devise :</strong> 'Chaque joueur a une histoire à raconter !'<br><br>🚀 <strong>N'hésite pas à nous rejoindre !</strong>"
                }
            };

            // Ouvrir/fermer le chatbot
            chatbotToggle.addEventListener('click', function() {
                chatbotContainer.classList.toggle('active');
                chatbotToggle.classList.toggle('active');
            });

            closeChatbot.addEventListener('click', function() {
                chatbotContainer.classList.remove('active');
                chatbotToggle.classList.remove('active');
            });

            // Gérer les clics sur les options
            chatbotOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const questionId = this.getAttribute('data-question');
                    const response = chatbotResponses[questionId];
                    
                    // Ajouter le message de l'utilisateur
                    const userMessage = document.createElement('div');
                    userMessage.className = 'message user-message';
                    userMessage.innerHTML = response.question;
                    chatbotMessages.appendChild(userMessage);
                    
                    // Ajouter la réponse du bot après un court délai
                    setTimeout(() => {
                        const botMessage = document.createElement('div');
                        botMessage.className = 'message bot-message';
                        botMessage.innerHTML = response.answer;
                        chatbotMessages.appendChild(botMessage);
                        
                        // Faire défiler vers le bas
                        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
                    }, 500);
                    
                    // Faire défiler vers le bas
                    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
                });
            });

            // Fermer le chatbot en cliquant en dehors
            document.addEventListener('click', function(event) {
                const isChatbot = chatbotContainer.contains(event.target) || chatbotToggle.contains(event.target);
                const isChatbotActive = chatbotContainer.classList.contains('active');
                
                if (!isChatbot && isChatbotActive && event.target !== chatbotToggle) {
                    chatbotContainer.classList.remove('active');
                    chatbotToggle.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>