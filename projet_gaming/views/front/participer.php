<?php
session_start();

// Sécuriser : définir des valeurs par défaut
if (!isset($message)) $message = '';
if (!isset($message_type)) $message_type = '';
if (!isset($evenement)) $evenement = null;
if (!isset($placesRestantes)) $placesRestantes = null;

// Vérifier si l'utilisateur est déjà inscrit
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

// Traitement du formulaire
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
            // Nouvelles données optionnelles
            'pseudo_gamer' => $_POST['pseudo_gamer'] ?? '',
            'plateforme' => $_POST['plateforme'] ?? '',
            'niveau_jeu' => $_POST['niveau_jeu'] ?? '',
            'genre_prefere' => $_POST['genre_prefere'] ?? '',
            'jeu_favori' => $_POST['jeu_favori'] ?? '',
            'equipement_personnel' => $_POST['equipement_personnel'] ?? '',
            'taille_tshirt' => $_POST['taille_tshirt'] ?? '',
            'restrictions_alimentaires' => $_POST['restrictions_alimentaires'] ?? '',
            'accessibilite' => $_POST['accessibilite'] ?? '',
            'comment_arrivez' => $_POST['comment_arrivez'] ?? '',
            'hebergement' => $_POST['hebergement'] ?? '',
            'type_participation' => $_POST['type_participation'] ?? '',
            'experience_gaming' => $_POST['experience_gaming'] ?? '',
            'reseaux_sociaux' => $_POST['reseaux_sociaux'] ?? '',
            'commentaires' => $_POST['commentaires'] ?? '',
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

// Récupérer l'événement
if ($evenement === null && isset($_GET['id'])) {
    include_once __DIR__ . '/../../config/database.php';
    include_once __DIR__ .'/../../controllers/EvenementController.php';

    $database = new Database();
    $db = $database->getConnection();
    $evenementController = new EvenementController($db);
    $evenement = $evenementController->show($_GET['id']);
}

// Calcul des places restantes
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
    <title>INSCRIPTION À L'ÉVÉNEMENT - ludology vault</title>
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
            max-width: 900px;
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
        
        .form-group label.optional {
            color: var(--text-light-gray);
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
        
        .form-section:last-child {
            border-bottom: none;
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

        .optional-field {
            opacity: 0.8;
        }

        .optional-field label {
            color: var(--text-light-gray);
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

        /* Style pour les champs optionnels */
        .optional-tag {
            display: inline-block;
            background: var(--border-color);
            color: var(--text-light-gray);
            font-size: 0.5rem;
            padding: 0.2rem 0.5rem;
            margin-left: 0.5rem;
            border-radius: 0;
        }

        /* Indicateur de progression */
        .progress-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .progress-step {
            text-align: center;
            position: relative;
            flex: 1;
        }

        .step-number {
            width: 30px;
            height: 30px;
            background: var(--border-color);
            color: var(--text-gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-size: 0.7rem;
            transition: all 0.3s;
        }

        .progress-step.active .step-number {
            background: var(--primary-green);
            color: var(--darker-bg);
            box-shadow: 0 0 10px var(--primary-green);
        }

        .step-label {
            font-size: 0.5rem;
            color: var(--text-gray);
        }

        .progress-step.active .step-label {
            color: var(--primary-green);
        }

        .progress-line {
            position: absolute;
            top: 15px;
            left: 5%;
            right: 5%;
            height: 2px;
            background: var(--border-color);
            z-index: -1;
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

<!-- Navigation Header (DU PREMIER CODE) -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-left">
            <div class="logo-container">
                <div class="logo-placeholder">🎮</div>
                <div class="site-title">ludology vault</div>
            </div>
        </div>
        <div class="nav-center">
            <ul class="nav-menu">
                <li><a href="../../index.php">ACCUEIL</a></li>
                <li><a href="evenements.php">ÉVÉNEMENTS</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="historique.php">MES INSCRIPTIONS</a></li>
                <?php endif; ?>
                <li><a href="../../index.php#timeline"></a></li>
                <li><a href="../../index.php#newsletter"></a></li>
                <li><a href="../../index.php#contact"></a></li>
            </ul>
        </div>
        <div class="nav-right">
            <!-- MODIFIER : Boutons de connexion/déconnexion -->
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- Si l'utilisateur est connecté -->
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <!-- Seulement pour les admins -->
                    <a href="../../admin/index.php" class="btn-admin">
                        <span class="btn-icon">⚙️</span>
                        ADMIN
                    </a>
                <?php endif; ?>
                <a href="../../logout.php" class="btn-auth">
                    <span class="btn-icon">⚙️</span>
                    DÉCONNEXION
                </a>
            <?php else: ?>
                <!-- Si l'utilisateur n'est pas connecté -->
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
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="user-info-note">
                            ✅ Vous êtes connecté en tant que <strong><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></strong>. 
                            Certaines informations seront automatiquement pré-remplies.
                        </div>
                    <?php endif; ?>

                    <!-- SECTION INFORMATIONS PERSONNELLES -->
                    <div class="form-section">
                        <h3 class="form-section-title">👤 INFORMATIONS PERSONNELLES (OBLIGATOIRES)</h3>
                        
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

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telephone">TÉLÉPHONE *</label>
                                <input type="text" id="telephone" name="telephone"
                                       value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>"
                                       placeholder="+33 X XX XX XX XX">
                                <span class="error-message" id="telephone_error"></span>
                            </div>

                            <div class="form-group optional-field">
                                <label for="reseaux_sociaux">RÉSEAUX SOCIAUX <span class="optional-tag">OPTIONNEL</span></label>
                                <input type="text" id="reseaux_sociaux" name="reseaux_sociaux"
                                       value="<?php echo isset($_POST['reseaux_sociaux']) ? htmlspecialchars($_POST['reseaux_sociaux']) : ''; ?>"
                                       placeholder="@pseudo_twitter, /pseudo_instagram">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION PROFIL GAMER -->
                    <div class="form-section">
                        <h3 class="form-section-title">🎮 PROFIL GAMER (OPTIONNEL)</h3>
                        
                        <div class="form-row">
                            <div class="form-group optional-field">
                                <label for="pseudo_gamer">PSEUDO GAMER</label>
                                <input type="text" id="pseudo_gamer" name="pseudo_gamer"
                                       value="<?php echo isset($_POST['pseudo_gamer']) ? htmlspecialchars($_POST['pseudo_gamer']) : ''; ?>"
                                       placeholder="VOTRE PSEUDO IN-GAME">
                            </div>

                            <div class="form-group optional-field">
                                <label for="plateforme">PLATEFORME PRÉFÉRÉE</label>
                                <select id="plateforme" name="plateforme">
                                    <option value="">CHOISISSEZ VOTRE PLATEFORME</option>
                                    <option value="pc" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'pc') ? 'selected' : ''; ?>>🎮 PC</option>
                                    <option value="playstation" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'playstation') ? 'selected' : ''; ?>>🎮 PLAYSTATION</option>
                                    <option value="xbox" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'xbox') ? 'selected' : ''; ?>>🎮 XBOX</option>
                                    <option value="nintendo" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'nintendo') ? 'selected' : ''; ?>>🎮 NINTENDO</option>
                                    <option value="mobile" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'mobile') ? 'selected' : ''; ?>>📱 MOBILE</option>
                                    <option value="arcade" <?php echo (isset($_POST['plateforme']) && $_POST['plateforme'] == 'arcade') ? 'selected' : ''; ?>>🕹️ ARCADE</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group optional-field">
                                <label for="niveau_jeu">NIVEAU DE JEU</label>
                                <select id="niveau_jeu" name="niveau_jeu">
                                    <option value="">SÉLECTIONNEZ VOTRE NIVEAU</option>
                                    <option value="debutant" <?php echo (isset($_POST['niveau_jeu']) && $_POST['niveau_jeu'] == 'debutant') ? 'selected' : ''; ?>>🥉 DÉBUTANT</option>
                                    <option value="intermediaire" <?php echo (isset($_POST['niveau_jeu']) && $_POST['niveau_jeu'] == 'intermediaire') ? 'selected' : ''; ?>>🥈 INTERMÉDIAIRE</option>
                                    <option value="avance" <?php echo (isset($_POST['niveau_jeu']) && $_POST['niveau_jeu'] == 'avance') ? 'selected' : ''; ?>>🥇 AVANCÉ</option>
                                    <option value="pro" <?php echo (isset($_POST['niveau_jeu']) && $_POST['niveau_jeu'] == 'pro') ? 'selected' : ''; ?>>🏆 PROFESSIONNEL</option>
                                </select>
                            </div>

                            <div class="form-group optional-field">
                                <label for="experience_gaming">ANNÉES D'EXPÉRIENCE GAMING</label>
                                <select id="experience_gaming" name="experience_gaming">
                                    <option value="">CHOISISSEZ VOTRE EXPÉRIENCE</option>
                                    <option value="1-3" <?php echo (isset($_POST['experience_gaming']) && $_POST['experience_gaming'] == '1-3') ? 'selected' : ''; ?>>🎯 1-3 ANS</option>
                                    <option value="3-5" <?php echo (isset($_POST['experience_gaming']) && $_POST['experience_gaming'] == '3-5') ? 'selected' : ''; ?>>🎯 3-5 ANS</option>
                                    <option value="5-10" <?php echo (isset($_POST['experience_gaming']) && $_POST['experience_gaming'] == '5-10') ? 'selected' : ''; ?>>🎯 5-10 ANS</option>
                                    <option value="10+" <?php echo (isset($_POST['experience_gaming']) && $_POST['experience_gaming'] == '10+') ? 'selected' : ''; ?>>🎯 10+ ANS</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group optional-field">
                                <label for="genre_prefere">GENRE DE JEU PRÉFÉRÉ</label>
                                <select id="genre_prefere" name="genre_prefere">
                                    <option value="">SÉLECTIONNEZ VOTRE GENRE</option>
                                    <option value="fps" <?php echo (isset($_POST['genre_prefere']) && $_POST['genre_prefere'] == 'fps') ? 'selected' : ''; ?>>🔫 FPS</option>
                                    <option value="rpg" <?php echo (isset($_POST['genre_prefere']) && $_POST['genre_prefere'] == 'rpg') ? 'selected' : ''; ?>>⚔️ RPG</option>
                                    <option value="strategie" <?php echo (isset($_POST['genre_prefere']) && $_POST['genre_prefere'] == 'strategie') ? 'selected' : ''; ?>>♟️ STRATÉGIE</option>
                                    <option value="sport" <?php echo (isset($_POST['genre_prefere']) && $_POST['genre_prefere'] == 'sport') ? 'selected' : ''; ?>>⚽ SPORT</option>
                                    <option value="course" <?php echo (isset($_POST['genre_prefere']) && $_POST['genre_prefere'] == 'course') ? 'selected' : ''; ?>>🏎️ COURSE</option>
                                    <option value="aventure" <?php echo (isset($_POST['genre_prefere']) && $_POST['genre_prefere'] == 'aventure') ? 'selected' : ''; ?>>🗺️ AVENTURE</option>
                                    <option value="combat" <?php echo (isset($_POST['genre_prefere']) && $_POST['genre_prefere'] == 'combat') ? 'selected' : ''; ?>>🥊 COMBAT</option>
                                </select>
                            </div>

                            <div class="form-group optional-field">
                                <label for="jeu_favori">JEU RÉTRO FAVORI</label>
                                <input type="text" id="jeu_favori" name="jeu_favori"
                                       value="<?php echo isset($_POST['jeu_favori']) ? htmlspecialchars($_POST['jeu_favori']) : ''; ?>"
                                       placeholder="EX: SUPER MARIO BROS, SONIC...">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION ÉQUIPEMENT -->
                    <div class="form-section">
                        <h3 class="form-section-title">💻 ÉQUIPEMENT (OPTIONNEL)</h3>
                        
                        <div class="form-group optional-field">
                            <label for="equipement_personnel">ÉQUIPEMENT PERSONNEL</label>
                            <textarea id="equipement_personnel" name="equipement_personnel" rows="3"
                                      placeholder="APPORTEZ-VOUS VOTRE ÉQUIPEMENT ? (MANETTE, CLAVIER, SOURIS...)"><?php echo isset($_POST['equipement_personnel']) ? htmlspecialchars($_POST['equipement_personnel']) : ''; ?></textarea>
                        </div>
                    </div>

                    <!-- SECTION LOGISTIQUE -->
                    <div class="form-section">
                        <h3 class="form-section-title">🚗 LOGISTIQUE (OPTIONNEL)</h3>
                        
                        <div class="form-row">
                            <div class="form-group optional-field">
                                <label for="comment_arrivez">COMMENT VENEZ-VOUS ?</label>
                                <select id="comment_arrivez" name="comment_arrivez">
                                    <option value="">MOYEN DE TRANSPORT</option>
                                    <option value="voiture" <?php echo (isset($_POST['comment_arrivez']) && $_POST['comment_arrivez'] == 'voiture') ? 'selected' : ''; ?>>🚗 VOITURE</option>
                                    <option value="transport" <?php echo (isset($_POST['comment_arrivez']) && $_POST['comment_arrivez'] == 'transport') ? 'selected' : ''; ?>>🚇 TRANSPORTS</option>
                                    <option value="velo" <?php echo (isset($_POST['comment_arrivez']) && $_POST['comment_arrivez'] == 'velo') ? 'selected' : ''; ?>>🚲 VÉLO</option>
                                    <option value="autre" <?php echo (isset($_POST['comment_arrivez']) && $_POST['comment_arrivez'] == 'autre') ? 'selected' : ''; ?>>🚀 AUTRE</option>
                                </select>
                            </div>

                            <div class="form-group optional-field">
                                <label for="hebergement">BESOIN D'HÉBERGEMENT ?</label>
                                <select id="hebergement" name="hebergement">
                                    <option value="">CHOISISSEZ UNE OPTION</option>
                                    <option value="non" <?php echo (isset($_POST['hebergement']) && $_POST['hebergement'] == 'non') ? 'selected' : ''; ?>>🚫 NON</option>
                                    <option value="hotel" <?php echo (isset($_POST['hebergement']) && $_POST['hebergement'] == 'hotel') ? 'selected' : ''; ?>>🏨 HÔTEL</option>
                                    <option value="camping" <?php echo (isset($_POST['hebergement']) && $_POST['hebergement'] == 'camping') ? 'selected' : ''; ?>>🏕️ CAMPING</option>
                                    <option value="recherche" <?php echo (isset($_POST['hebergement']) && $_POST['hebergement'] == 'recherche') ? 'selected' : ''; ?>>🔍 JE RECHERCHE</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION PRÉFÉRENCES -->
                    <div class="form-section">
                        <h3 class="form-section-title">⭐ PRÉFÉRENCES (OPTIONNEL)</h3>
                        
                        <div class="form-row">
                            <div class="form-group optional-field">
                                <label for="type_participation">TYPE DE PARTICIPATION PRÉFÉRÉ</label>
                                <select id="type_participation" name="type_participation">
                                    <option value="">CHOISISSEZ VOTRE STYLE</option>
                                    <option value="competitif" <?php echo (isset($_POST['type_participation']) && $_POST['type_participation'] == 'competitif') ? 'selected' : ''; ?>>🏆 COMPÉTITIF</option>
                                    <option value="casual" <?php echo (isset($_POST['type_participation']) && $_POST['type_participation'] == 'casual') ? 'selected' : ''; ?>>😊 CASUAL</option>
                                    <option value="observateur" <?php echo (isset($_POST['type_participation']) && $_POST['type_participation'] == 'observateur') ? 'selected' : ''; ?>>👀 OBSERVATEUR</option>
                                    <option value="social" <?php echo (isset($_POST['type_participation']) && $_POST['type_participation'] == 'social') ? 'selected' : ''; ?>>🤝 SOCIAL</option>
                                </select>
                            </div>

                            <div class="form-group optional-field">
                                <label for="taille_tshirt">TAILLE DE T-SHIRT</label>
                                <select id="taille_tshirt" name="taille_tshirt">
                                    <option value="">SI GOODIES PRÉVUS</option>
                                    <option value="XS" <?php echo (isset($_POST['taille_tshirt']) && $_POST['taille_tshirt'] == 'XS') ? 'selected' : ''; ?>>XS</option>
                                    <option value="S" <?php echo (isset($_POST['taille_tshirt']) && $_POST['taille_tshirt'] == 'S') ? 'selected' : ''; ?>>S</option>
                                    <option value="M" <?php echo (isset($_POST['taille_tshirt']) && $_POST['taille_tshirt'] == 'M') ? 'selected' : ''; ?>>M</option>
                                    <option value="L" <?php echo (isset($_POST['taille_tshirt']) && $_POST['taille_tshirt'] == 'L') ? 'selected' : ''; ?>>L</option>
                                    <option value="XL" <?php echo (isset($_POST['taille_tshirt']) && $_POST['taille_tshirt'] == 'XL') ? 'selected' : ''; ?>>XL</option>
                                    <option value="XXL" <?php echo (isset($_POST['taille_tshirt']) && $_POST['taille_tshirt'] == 'XXL') ? 'selected' : ''; ?>>XXL</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION BESOINS SPÉCIFIQUES -->
                    <div class="form-section">
                        <h3 class="form-section-title">♿ BESOINS SPÉCIFIQUES (OPTIONNEL)</h3>
                        
                        <div class="form-row">
                            <div class="form-group optional-field">
                                <label for="restrictions_alimentaires">RESTRICTIONS ALIMENTAIRES</label>
                                <textarea id="restrictions_alimentaires" name="restrictions_alimentaires" rows="2"
                                          placeholder="ALLERGIES, RÉGIMES SPÉCIAUX..."><?php echo isset($_POST['restrictions_alimentaires']) ? htmlspecialchars($_POST['restrictions_alimentaires']) : ''; ?></textarea>
                            </div>

                            <div class="form-group optional-field">
                                <label for="accessibilite">BESOINS D'ACCESSIBILITÉ</label>
                                <textarea id="accessibilite" name="accessibilite" rows="2"
                                          placeholder="BESOINS PARTICULIERS POUR LE CONFORT..."><?php echo isset($_POST['accessibilite']) ? htmlspecialchars($_POST['accessibilite']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION COMMENTAIRES -->
                    <div class="form-section">
                        <h3 class="form-section-title">💬 COMMENTAIRES (OPTIONNEL)</h3>
                        
                        <div class="form-group optional-field">
                            <label for="commentaires">COMMENTAIRES, QUESTIONS OU SUGGESTIONS</label>
                            <textarea id="commentaires" name="commentaires" rows="4"
                                      placeholder="AVEC QUI SOUHAITEZ-VOUS JOUER ? AUTRES INFORMATIONS UTILES..."><?php echo isset($_POST['commentaires']) ? htmlspecialchars($_POST['commentaires']) : ''; ?></textarea>
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

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-full" id="submitBtn">
                            🎯 VALIDER MON INSCRIPTION
                        </button>

                        <p style="margin-top: 1rem; font-size: 0.6rem; color: var(--text-gray);">
                            * Champs obligatoires | Les champs optionnels nous aident à améliorer votre expérience
                        </p>
                    </div>
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

<!-- Footer (DU PREMIER CODE) -->
<footer class="footer">
    <div class="footer-top">
        <div class="footer-content">
            <div class="footer-section footer-about">
                <div class="footer-logo">
                    <div class="footer-logo-placeholder">[LOGO]</div>
                    <h3>LUDOLOGY VAULT</h3>
                </div>
                <p class="footer-tagline">Preserver l'histoire du jeu video pour les generations futures</p>
                <div class="social-links">
                    <a href="#" class="social-icon" title="Facebook">
                        <span>FB</span>
                    </a>
                    <a href="#" class="social-icon" title="Twitter">
                        <span>TW</span>
                    </a>
                    <a href="#" class="social-icon" title="Instagram">
                        <span>IG</span>
                    </a>
                    <a href="#" class="social-icon" title="YouTube">
                        <span>YT</span>
                    </a>
                    <a href="#" class="social-icon" title="Discord">
                        <span>DC</span>
                    </a>
                </div>
            </div>

            <div class="footer-section">
                <h3 class="footer-title">NAVIGATION</h3>
                <ul class="footer-links">
                    <li><a href="../../index.php">► Accueil</a></li>
                    <li><a href="games.php">► Collection de Jeux</a></li>
                    <li><a href="blog.php">► Blog & Actualites</a></li>
                    <li><a href="evenements.php">► Evenements</a></li>
                    <li><a href="#">► A Propos</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="footer-title">RESSOURCES</h3>
                <ul class="footer-links">
                    <li><a href="#">► Base de Donnees</a></li>
                    <li><a href="#">► Archives Historiques</a></li>
                    <li><a href="#">► Guides & Tutoriels</a></li>
                    <li><a href="reclamation.php">► Support & Reclamations</a></li>
                    <li><a href="#">► FAQ</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="footer-title">MUSEE</h3>
                <div class="footer-info">
                    <p class="info-item">
                        <span class="info-icon">🕐</span>
                        <span class="info-content">
                            <strong>Horaires:</strong><br>
                            Lun-Ven: 09:00 - 18:00<br>
                            Sam-Dim: 10:00 - 20:00
                        </span>
                    </p>
                    <p class="info-item">
                        <span class="info-icon">📧</span>
                        <span class="info-content">contact@ludologyvault.tn</span>
                    </p>
                    <p class="info-item">
                        <span class="info-icon">📞</span>
                        <span class="info-content">+216 XX XXX XXX</span>
                    </p>
                    <p class="info-item">
                        <span class="info-icon">📍</span>
                        <span class="info-content">Tunis, Tunisia</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="pixel-divider"></div>
        <div class="footer-bottom-content">
            <p class="copyright">&copy; 2024 LUDOLOGY VAULT - Tous droits reserves</p>
            <div class="footer-bottom-links">
                <a href="#">Mentions Legales</a>
                <span>•</span>
                <a href="#">Politique de Confidentialite</a>
                <span>•</span>
                <a href="#">Conditions d'Utilisation</a>
            </div>
            <p class="made-with">Made with <span class="heart">♥</span> for gamers worldwide</p>
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
        
        // Animation pour les champs optionnels
        const optionalFields = document.querySelectorAll('.optional-field input, .optional-field select, .optional-field textarea');
        optionalFields.forEach(field => {
            field.addEventListener('focus', function() {
                this.parentElement.style.opacity = '1';
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            field.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.style.opacity = '0.8';
                    this.parentElement.style.transform = 'none';
                }
            });
        });
    });
</script>
</body>
</html>