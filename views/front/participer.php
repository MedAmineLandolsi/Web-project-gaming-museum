<?php
session_start(); // AJOUTER CETTE LIGNE AU DÉBUT

// Sécuriser : définir des valeurs par défaut pour éviter les warnings
if (!isset($message)) $message = '';
if (!isset($message_type)) $message_type = '';
if (!isset($evenement)) $evenement = null;
if (!isset($placesRestantes)) $placesRestantes = null;

// Vérifier si l'utilisateur est déjà inscrit à cet événement
if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    include_once __DIR__ . '/../../config/database.php';
    include_once __DIR__ . '/../../models/Participation.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $participationModel = new Participation($db);
    $participationModel->user_id = $_SESSION['user_id'];
    $participationModel->id_evenement = $_GET['id'];
    
    if ($participationModel->userAlreadyRegistered()) {
        $message = '⚠️ VOUS ÊTES DÉJÀ INSCRIT À CET ÉVÉNEMENT';
        $message_type = 'warning';
    }
}

// ⭐⭐⭐ TRAITEMENT DU FORMULAIRE ⭐⭐⭐
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_participant']) && isset($_POST['email'])) {
    
    include_once __DIR__ . '/../../config/database.php';
    include_once __DIR__ . '/../../models/Evenement.php';
    include_once __DIR__ . '/../../models/Participation.php';
    include_once __DIR__ . '/../../controllers/ParticipationController.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $participationController = new ParticipationController($db);
    
    $id_evenement = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id_evenement > 0) {
        // Préparer les données pour la création
        $data = [
            'id_evenement' => $id_evenement,
            'nom_participant' => $_POST['nom_participant'],
            'email' => $_POST['email'],
            'telephone' => $_POST['telephone'] ?? '',
        ];
        
        // Si l'utilisateur est connecté, utiliser ses infos
        if (isset($_SESSION['user_id'])) {
            if (empty($data['nom_participant']) && isset($_SESSION['username'])) {
                $data['nom_participant'] = $_SESSION['username'];
            }
            if (empty($data['email']) && isset($_SESSION['email'])) {
                $data['email'] = $_SESSION['email'];
            }
        }
        
        $result = $participationController->create($data);
        
        switch($result) {
            case 'success':
                $message = '🎉 INSCRIPTION RÉUSSIE !';
                $message_type = 'success';
                break;
            case 'email_exists':
                $message = '⚠️ CET EMAIL EST DÉJÀ INSCRIT À CET ÉVÉNEMENT.';
                $message_type = 'error';
                break;
            case 'no_places':
                $message = '🚫 DÉSOLÉ, PLUS DE PLACES DISPONIBLES.';
                $message_type = 'error';
                break;
            case 'already_registered':
                $message = '⚠️ VOUS ÊTES DÉJÀ INSCRIT À CET ÉVÉNEMENT.';
                $message_type = 'warning';
                break;
            default:
                $message = '❌ ERREUR LORS DE L\'INSCRIPTION.';
                $message_type = 'error';
        }
    } else {
        $message = '❌ ERREUR : ID D\'ÉVÉNEMENT MANQUANT.';
        $message_type = 'error';
    }
}

// Si la vue peut être chargée directement, tenter de récupérer l'ID depuis l'URL
if ($evenement === null && isset($_GET['id'])) {
    include_once __DIR__ . '/../../config/database.php';
    include_once __DIR__ .'/../../controllers/EvenementController.php';

    $database = new Database();
    $db = $database->getConnection();
    $evenementController = new EvenementController($db);
    $evenement = $evenementController->show($_GET['id']);
}

// Calcul sûr des places restantes
if ($evenement !== null) {
    if (method_exists($evenement, 'countParticipations')) {
        $participationsCount = $evenement->countParticipations();
    } else {
        $participationsCount = isset($participationsCount) ? (int)$participationsCount : 0;
    }
    $placesRestantes = (isset($evenement->places_max) ? (int)$evenement->places_max : 0) - $participationsCount;
    if ($placesRestantes < 0) $placesRestantes = 0;
} else {
    $placesRestantes = 0;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INSCRIPTION À L'ÉVÉNEMENT - RetroGame Hub</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
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
            min-height: 100vh;
        }

        /* Particules d'arrière-plan */
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
        .particle:nth-child(9) { left: 90%; animation-delay: 1.5s; animation-duration: 18.5s; }
        .particle:nth-child(10) { left: 95%; animation-delay: 3.5s; animation-duration: 17.5s; }

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

        /* Effet de scanline */
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
            z-index: 2;
        }

        @keyframes scan {
            0% { transform: translateY(0); }
            100% { transform: translateY(10px); }
        }

        /* Container principal */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Section Inscription */
        .inscription-container {
            margin-top: 120px;
            padding: 2rem 0;
            min-height: 70vh;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 
                0 0 10px var(--primary-green),
                0 0 20px var(--primary-green),
                0 0 40px var(--primary-green);
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
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Carte d'événement */
        .feature-card {
            background-color: var(--card-bg);
            border: 2px solid var(--border-color);
            padding: 2rem;
            border-radius: 0;
            transition: all 0.4s;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 255, 65, 0.4);
            border-color: var(--primary-green);
        }
        
        .event-details {
            margin-bottom: 2rem;
            border-left: 3px solid var(--primary-green);
        }
        
        .event-details h3 {
            color: var(--secondary-purple);
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            text-shadow: 0 0 10px var(--secondary-purple);
        }
        
        .event-details h4 {
            color: var(--primary-green);
            margin-bottom: 1rem;
            font-size: 1rem;
            text-shadow: 0 0 10px var(--primary-green);
        }
        
        .event-details p {
            margin-bottom: 0.5rem;
            color: var(--text-gray);
            font-size: 0.6rem;
            font-family: 'VT323', monospace;
        }
        
        .remaining-places {
            color: var(--primary-green);
            font-weight: normal;
            text-shadow: 0 0 10px var(--primary-green);
        }
        
        /* Formulaire */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-green);
            font-size: 0.7rem;
            text-shadow: 0 0 5px var(--primary-green);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border-radius: 0;
            background: var(--darker-bg);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            font-size: 0.8rem;
            transition: all 0.3s;
            font-family: 'VT323', monospace;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
        }
        
        .form-group input.error,
        .form-group select.error,
        .form-group textarea.error {
            border-color: var(--accent-pink);
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.3);
        }
        
        .error-message {
            display: none;
            color: var(--accent-pink);
            font-size: 0.6rem;
            margin-top: 0.5rem;
            text-shadow: 0 0 5px var(--accent-pink);
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }
        
        .error-message.show {
            display: block;
        }
        
        /* Grille pour les champs côte à côte */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        /* Cases à cocher */
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-top: 0.3rem;
        }
        
        .checkbox-group label {
            margin-bottom: 0;
            font-size: 0.6rem;
            line-height: 1.4;
        }
        
        .checkbox-group a {
            color: var(--secondary-purple);
            text-decoration: none;
        }
        
        .checkbox-group a:hover {
            color: var(--primary-green);
            text-shadow: 0 0 5px var(--primary-green);
        }
        
        /* Sections du formulaire */
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .form-section-title {
            color: var(--secondary-purple);
            font-size: 0.8rem;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px var(--secondary-purple);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* AJOUTER : Style pour les infos utilisateur connecté */
        .user-info-note {
            background: rgba(0, 255, 65, 0.1);
            border: 1px solid var(--primary-green);
            color: var(--primary-green);
            padding: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 0;
            font-size: 0.6rem;
            text-align: center;
        }

        /* Boutons */
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            border: none;
            border-radius: 0;
            text-decoration: none;
            font-weight: normal;
            transition: all 0.3s;
            cursor: pointer;
            font-size: 0.7rem;
            text-align: center;
            font-family: 'Press Start 2P', cursive;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #00cc33, var(--primary-green));
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.8);
        }

        .btn-secondary {
            background: transparent;
            color: var(--secondary-purple);
            border: 2px solid var(--secondary-purple);
            box-shadow: 0 0 15px rgba(189, 0, 255, 0.3);
        }

        .btn-secondary:hover {
            background: var(--secondary-purple);
            color: var(--darker-bg);
            transform: translateY(-3px);
            box-shadow: 0 0 25px rgba(189, 0, 255, 0.6);
        }
        
        .btn-full {
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Messages d'alerte */
        .message-alert {
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            border-radius: 0;
            border: 2px solid;
            font-size: 0.8rem;
            text-shadow: 0 0 5px currentColor;
        }
        
        .message-success {
            background: rgba(0, 255, 65, 0.1);
            color: var(--primary-green);
            border-color: var(--primary-green);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
        }
        
        .message-error {
            background: rgba(255, 0, 110, 0.1);
            color: var(--accent-pink);
            border-color: var(--accent-pink);
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.3);
        }

        .message-warning {
            background: rgba(255, 204, 0, 0.1);
            color: #ffcc00;
            border-color: #ffcc00;
            box-shadow: 0 0 20px rgba(255, 204, 0, 0.3);
        }
        
        .event-full {
            text-align: center;
            padding: 2rem;
        }
        
        .event-full h3 {
            color: var(--accent-pink);
            margin-bottom: 1rem;
            text-shadow: 0 0 10px var(--accent-pink);
        }

        .event-full p {
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: var(--text-gray);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .inscription-container {
                margin-top: 140px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .feature-card {
                padding: 1.5rem;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 0.8rem;
            }

            .btn {
                padding: 0.8rem 1.5rem;
                font-size: 0.6rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.2rem;
            }

            .event-details h4 {
                font-size: 0.8rem;
            }

            .form-group label {
                font-size: 0.6rem;
            }
            
            .checkbox-group label {
                font-size: 0.5rem;
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
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>

<!-- Effet de scanline -->
<div class="scanline"></div>

<!-- Navigation (Header du premier code) -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-left">
            <div class="logo-container">
                <div class="logo-placeholder">🎮</div>
                <div class="site-title">RETROGAME HUB</div>
            </div>
        </div>
        <div class="nav-center">
            <ul class="nav-menu">
                <li><a href="../../index.php">ACCUEIL</a></li>
                <li><a href="evenements.php">ÉVÉNEMENTS</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="?history=1">MES INSCRIPTIONS</a></li>
                <?php endif; ?>
                <li><a href="../../index.php#timeline">HISTOIRE</a></li>
                <li><a href="../../index.php#newsletter">NEWSLETTER</a></li>
                <li><a href="../../index.php#contact">CONTACT</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- Si l'utilisateur est connecté -->
                <a href="../../admin/index.php" class="btn-admin">
                    <span class="btn-icon">⚙️</span>
                    <?php echo $_SESSION['role'] == 'admin' ? 'ADMIN' : 'MON COMPTE'; ?>
                </a>
                <a href="../../logout.php" class="btn-auth">
                    <span class="btn-icon">🚪</span>
                    DÉCONNEXION
                </a>
            <?php else: ?>
                <!-- Si l'utilisateur n'est pas connecté -->
                <a href="../../admin/index.php" class="btn-admin">
                    <span class="btn-icon">⚙️</span>
                    ADMIN
                </a>
                <a href="../../login.php" class="btn-auth">
                    <span class="btn-icon">👤</span>
                    CONNEXION
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container inscription-container">
    <div class="form-container">
        <h1 class="page-title">
            🎯 S'INSCRIRE À L'ÉVÉNEMENT
        </h1>

        <?php if (!empty($message)): ?>
            <div class="message-alert <?php echo $message_type == 'success' ? 'message-success' : ($message_type == 'warning' ? 'message-warning' : 'message-error'); ?>">
                <strong><?php echo htmlspecialchars($message); ?></strong>
            </div>
        <?php endif; ?>

        <?php if ($evenement === null): ?>
            <!-- Afficher un message utilisateur si l'événement n'existe pas -->
            <div class="feature-card text-center">
                <h3 style="color: var(--accent-pink); text-shadow: 0 0 10px var(--accent-pink);">⚠ ÉVÉNEMENT INTROUVABLE</h3>
                <p style="font-family: 'VT323', monospace; font-size: 1.2rem; color: var(--text-gray); margin: 1rem 0;">
                    Désolé, l'événement demandé n'existe pas ou l'ID est invalide.
                </p>
                <a href="evenements.php" class="btn btn-secondary">← RETOUR AUX ÉVÉNEMENTS</a>
            </div>
        <?php else: ?>
            <div class="feature-card event-details">
                <h3>🎮 <?php echo htmlspecialchars($evenement->jeu ?? '—'); ?></h3>
                <h4>
                    <?php echo htmlspecialchars($evenement->nom ?? '—'); ?>
                </h4>

                <p><strong>📅 DATE :</strong>
                    <?php
                        $date = isset($evenement->date_debut) ? strtotime($evenement->date_debut) : false;
                        echo $date ? date('d/m/Y H:i', $date) : '—';
                    ?>
                </p>

                <p><strong>📍 LIEU :</strong> <?php echo htmlspecialchars($evenement->lieu ?? '—'); ?></p>

                <p>
                    <strong>👥 PLACES RESTANTES :</strong>
                    <span class="remaining-places">
                        <?php echo (int)$placesRestantes; ?> / <?php echo (int)($evenement->places_max ?? 0); ?>
                    </span>
                </p>

                <?php if (isset($evenement->organisateur_id)): ?>
                    <p><strong>👤 ORGANISÉ PAR :</strong> <?php echo htmlspecialchars($evenement->organisateur_nom ?? 'Admin'); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($placesRestantes > 0): ?>
                <form id="participationForm" class="feature-card" method="POST" novalidate>
                    
                    <!-- Afficher les infos de l'utilisateur connecté -->
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="user-info-note">
                            ✅ Vous êtes connecté en tant que <strong><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></strong>. 
                            Certaines informations seront automatiquement pré-remplies.
                        </div>
                    <?php endif; ?>

                    <!-- SECTION INFORMATIONS PERSONNELLES -->
                    <div class="form-section">
                        <h3 class="form-section-title">👤 INFORMATIONS PERSONNELLES</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom_participant">NOM COMPLET *</label>
                                <input type="text" id="nom_participant" name="nom_participant"
                                       value="<?php echo isset($_POST['nom_participant']) ? htmlspecialchars($_POST['nom_participant']) : (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''); ?>"
                                       placeholder="ENTREZ VOTRE NOM COMPLET">
                                <span class="error-message" id="nom_error"></span>
                            </div>

                            <div class="form-group">
                                <label for="email">EMAIL *</label>
                                <input type="text" id="email" name="email"
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''); ?>"
                                       placeholder="VOTRE@EMAIL.COM">
                                <span class="error-message" id="email_error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="telephone">TÉLÉPHONE *</label>
                            <input type="text" id="telephone" name="telephone"
                                   value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>"
                                   placeholder="+33 X XX XX XX XX">
                            <span class="error-message" id="telephone_error"></span>
                        </div>
                    </div>

                    <!-- SECTION PROFIL GAMER (simplifiée) -->
                    <div class="form-section">
                        <h3 class="form-section-title">🎮 PROFIL GAMER</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="pseudo_gamer">PSEUDO GAMER</label>
                                <input type="text" id="pseudo_gamer" name="pseudo_gamer"
                                       value="<?php echo isset($_POST['pseudo_gamer']) ? htmlspecialchars($_POST['pseudo_gamer']) : ''; ?>"
                                       placeholder="VOTRE PSEUDO IN-GAME (OPTIONNEL)">
                            </div>

                            <div class="form-group">
                                <label for="plateforme">PLATEFORME PRÉFÉRÉE</label>
                                <select id="plateforme" name="plateforme">
                                    <option value="">CHOISISSEZ VOTRE PLATEFORME (OPTIONNEL)</option>
                                    <option value="pc" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'pc') ? 'selected' : ''; ?>>🎮 PC</option>
                                    <option value="playstation" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'playstation') ? 'selected' : ''; ?>>🎮 PLAYSTATION</option>
                                    <option value="xbox" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'xbox') ? 'selected' : ''; ?>>🎮 XBOX</option>
                                    <option value="nintendo" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'nintendo') ? 'selected' : ''; ?>>🎮 NINTENDO</option>
                                    <option value="mobile" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'mobile') ? 'selected' : ''; ?>>📱 MOBILE</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION LOGISTIQUE (simplifiée) -->
                    <div class="form-section">
                        <h3 class="form-section-title">📋 INFORMATIONS COMPLÉMENTAIRES</h3>
                        
                        <div class="form-group">
                            <label for="commentaires">COMMENTAIRES OU QUESTIONS</label>
                            <textarea id="commentaires" name="commentaires" rows="4" 
                                      placeholder="AVEC QUI SOUHAITEZ-VOUS JOUER ? AUTRES INFORMATIONS UTILES... (OPTIONNEL)"><?php echo isset($_POST['commentaires']) ? htmlspecialchars($_POST['commentaires']) : ''; ?></textarea>
                        </div>
                    </div>

                    <!-- SECTION CONDITIONS -->
                    <div class="form-section">
                        <h3 class="form-section-title">⚖️ CONDITIONS ET CONSENTEMENTS</h3>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="accepte_reglement" name="accepte_reglement" value="1" 
                                   <?php echo (isset($_POST['accepte_reglement']) && $_POST['accepte_reglement'] == '1') ? 'checked' : ''; ?>>
                            <label for="accepte_reglement">
                                J'accepte le <a href="reglement.php" target="_blank">règlement de l'événement</a> et les 
                                <a href="conditions.php" target="_blank">conditions générales d'utilisation</a> *
                            </label>
                            <span class="error-message" id="reglement_error"></span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full" id="submitBtn">
                        🎯 VALIDER MON INSCRIPTION
                    </button>

                    <p style="text-align: center; margin-top: 1rem; font-size: 0.6rem; color: var(--text-gray);">
                        * Champs obligatoires
                    </p>
                </form>
            <?php else: ?>
                <div class="feature-card event-full">
                    <h3>🚫 ÉVÉNEMENT COMPLET</h3>
                    <p>Toutes les places ont été réservées.</p>
                    <a href="evenements.php" class="btn btn-secondary">← VOIR D'AUTRES ÉVÉNEMENTS</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Footer (du premier code) -->
<footer class="footer">
    <div class="footer-top">
        <div class="footer-content">
            <div class="footer-about">
                <div class="footer-logo">
                    <div class="footer-logo-placeholder">🎮</div>
                    <h3>RETROGAME HUB</h3>
                </div>
                <p class="footer-tagline">
                    Votre destination ultime pour les événements gaming et la communauté rétro.
                </p>
                <div class="social-links">
                    <a href="#" class="social-icon">📘</a>
                    <a href="#" class="social-icon">🐦</a>
                    <a href="#" class="social-icon">📷</a>
                    <a href="#" class="social-icon">🎬</a>
                </div>
            </div>

            <div class="footer-links">
                <h4 class="footer-title">NAVIGATION</h4>
                <ul class="footer-links">
                    <li><a href="../../index.php">Accueil</a></li>
                    <li><a href="evenements.php">Événements</a></li>
                    <li><a href="../../index.php#features">À propos</a></li>
                    <li><a href="../../index.php#contact">Contact</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4 class="footer-title">ÉVÉNEMENTS</h4>
                <ul class="footer-links">
                    <li><a href="evenements.php?type=tournoi">Tournois</a></li>
                    <li><a href="evenements.php?type=lan">LAN Parties</a></li>
                    <li><a href="evenements.php?type=workshop">Workshops</a></li>
                    <li><a href="evenements.php?type=expo">Expositions</a></li>
                </ul>
            </div>

            <div class="footer-info">
                <h4 class="footer-title">CONTACT</h4>
                <div class="info-item">
                    <span class="info-icon">📍</span>
                    <div class="info-content">
                        <strong>Adresse</strong><br>
                        123 Rue du Gaming<br>
                        75000 Paris
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">📞</span>
                    <div class="info-content">
                        <strong>Téléphone</strong><br>
                        +33 1 23 45 67 89
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">✉️</span>
                    <div class="info-content">
                        <strong>Email</strong><br>
                        contact@retrogamehub.fr
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pixel-divider"></div>

    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <div class="copyright">
                &copy; 2024 RetroGame Hub. Tous droits réservés.
            </div>
            <div class="footer-bottom-links">
                <a href="#">Mentions légales</a>
                <span>|</span>
                <a href="#">Politique de confidentialité</a>
                <span>|</span>
                <a href="#">CGU</a>
            </div>
            <div class="made-with">
                Fait avec <span class="heart">❤️</span> pour la communauté gaming
            </div>
        </div>
</footer>

<!-- Scroll to Top Button -->
<button class="scroll-top" onclick="scrollToTop()">↑</button>

<script>
    // Scroll to Top
    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Show scroll button
    window.addEventListener('scroll', function() {
        const scrollButton = document.querySelector('.scroll-top');
        if (window.scrollY > 300) {
            scrollButton.classList.add('visible');
        } else {
            scrollButton.classList.remove('visible');
        }
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    class FormValidator {
        constructor() {
            this.form = document.getElementById('participationForm');
            if (!this.form) return;

            this.fields = {
                nom_participant: {
                    element: document.getElementById('nom_participant'),
                    error: document.getElementById('nom_error'),
                    validate: (value) => this.validateName(value)
                },
                email: {
                    element: document.getElementById('email'),
                    error: document.getElementById('email_error'),
                    validate: (value) => this.validateEmail(value)
                },
                telephone: {
                    element: document.getElementById('telephone'),
                    error: document.getElementById('telephone_error'),
                    validate: (value) => this.validatePhone(value)
                },
                accepte_reglement: {
                    element: document.getElementById('accepte_reglement'),
                    error: document.getElementById('reglement_error'),
                    validate: (checked) => this.validateCheckbox(checked, 'règlement')
                }
            };

            this.submitBtn = document.getElementById('submitBtn');
            this.init();
        }

        init() {
            // Validation en temps réel
            Object.values(this.fields).forEach(field => {
                if (field.element) {
                    if (field.element.type === 'checkbox') {
                        field.element.addEventListener('change', () => this.validateField(field));
                    } else {
                        field.element.addEventListener('blur', () => this.validateField(field));
                        field.element.addEventListener('input', () => this.clearError(field));
                    }
                }
            });

            // Validation à la soumission
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        validateName(name) {
            name = name.trim();
            if (!name) {
                return 'LE NOM EST OBLIGATOIRE';
            }
            if (name.length < 2) {
                return 'LE NOM DOIT CONTENIR AU MOINS 2 CARACTÈRES';
            }
            return null;
        }

        validateEmail(email) {
            email = email.trim();
            if (!email) {
                return 'L\'EMAIL EST OBLIGATOIRE';
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                return 'FORMAT D\'EMAIL INVALIDE';
            }
            return null;
        }

        validatePhone(phone) {
            phone = phone.trim();
            if (!phone) {
                return 'LE TÉLÉPHONE EST OBLIGATOIRE';
            }
            if (!/^[\+]?[0-9\s\-\(\)\.]{10,}$/.test(phone)) {
                return 'FORMAT DE TÉLÉPHONE INVALIDE (MIN. 10 CHIFFRES)';
            }
            return null;
        }

        validateCheckbox(checked, fieldName) {
            if (!checked) {
                return `VOUS DEVEZ ACCEPTER LE ${fieldName.toUpperCase()}`;
            }
            return null;
        }

        validateField(field) {
            let value;
            if (field.element.type === 'checkbox') {
                value = field.element.checked;
            } else {
                value = field.element.value;
            }
            
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
                    this.submitBtn.textContent = '🔄 INSCRIPTION EN COURS...';
                    this.submitBtn.style.background = 'linear-gradient(135deg, var(--secondary-purple), var(--accent-pink))';
                }
                
                // Soumettre le formulaire
                this.form.submit();
            } else {
                // Faire défiler jusqu'à la première erreur
                const firstError = document.querySelector('.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Afficher un message général d'erreur
                alert('⚠ VEUILLEZ CORRIGER LES ERREURS DANS LE FORMULAIRE AVANT DE SOUMETTRE.');
            }
        }
    }

    // Initialiser la validation lorsque le DOM est chargé
    document.addEventListener('DOMContentLoaded', () => {
        new FormValidator();
    });
</script>
</body>
</html>