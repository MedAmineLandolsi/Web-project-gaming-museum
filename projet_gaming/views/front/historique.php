<?php
session_start();
include_once '../../config/database.php';
include_once '../../models/Participation.php';

$database = new Database();
$db = $database->getConnection();

// Initialiser la variable pour les événements
$historiqueEvents = [];

// Récupérer l'historique selon si l'utilisateur est connecté ou non
if(isset($_SESSION['user_id'])) {
    // Si connecté, utiliser l'ID utilisateur
    $participation = new Participation($db);
    $stmt = $participation->readByUser($_SESSION['user_id']);
    $historiqueEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif(isset($_POST['email'])) {
    // Si non connecté mais email fourni via formulaire
    $participation = new Participation($db);
    $participation->email = $_POST['email'];
    
    // Pour récupérer par email, on doit chercher dans la base
    // Note: Vous devrez peut-être créer une nouvelle méthode pour cela
    // Pour l'instant, on utilise une requête directe
    $query = "SELECT p.*, e.nom as evenement_nom, e.date_debut, e.date_fin, 
                     e.lieu, e.prix, e.jeu, e.description, e.places_max,
                     e.organisateur_id
              FROM participation p 
              LEFT JOIN evenement e ON p.id_evenement = e.id_evenement 
              WHERE p.email = :email 
              ORDER BY p.date_inscription DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->execute();
    $historiqueEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Pour le formulaire si pas déjà en POST
$email = $_POST['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Historique </title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Particules d'arrière-plan -->
    <div class="particles">
        <?php for($i = 0; $i < 10; $i++): ?>
        <div class="particle"></div>
        <?php endfor; ?>
    </div>

    <!-- Navigation -->
   
    <!-- Hero Section Historique -->
    <section class="hero-history">
        <div class="grid-background"></div>
        <div class="scanline"></div>
        <div class="crt-effect"></div>
        <div class="hero-content">
            <div class="glitch-wrapper">
                <h1 class="hero-title">MON HISTORIQUE</h1>
            </div>
            <p class="hero-subtitle">
                <span class="typing-text">SUIVEZ VOTRE PARCOURS GAMING</span>
            </p>
        </div>
    </section>

    <!-- Formulaire de recherche (pour utilisateurs non connectés) -->
    <?php if(!isset($_SESSION['user_id'])): ?>
    <section class="history-search-section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-bracket">[</span>
                VÉRIFIEZ VOTRE HISTORIQUE
                <span class="title-bracket">]</span>
            </h2>
            <p class="section-subtitle">ENTREZ VOTRE EMAIL POUR VOIR VOS PARTICIPATIONS</p>
        </div>

        <div class="history-search-container">
            <div class="history-search-card">
                <div class="history-search-header">
                    <h3>📧 CONSULTEZ VOTRE HISTORIQUE</h3>
                    <p class="history-search-description">
                        Entrez l'adresse email que vous avez utilisée pour vos inscriptions
                    </p>
                </div>
                
                <form method="POST" action="historique.php" class="history-search-form" novalidate>
                    <div class="form-group">
                        <label for="userEmail" class="form-label">
                            <span class="form-icon">✉️</span>
                            VOTRE ADRESSE EMAIL
                        </label>
                        <input 
                            type="email" 
                            id="userEmail" 
                            name="email"
                            class="form-input"
                            placeholder="votre@email.com"
                            value="<?php echo htmlspecialchars($email); ?>"
                            required
                        >
                        <div class="form-hint">
                            Nous vérifierons votre historique sans envoyer d'email
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-history-search">
                        <span class="btn-icon">🔍</span>
                        CONSULTER L'HISTORIQUE
                    </button>
                    
                    <div class="login-suggestion">
                        <p>💡 <strong>Astuce :</strong> Créez un compte pour accéder plus facilement à votre historique !</p>
                        <a href="../../register.php" class="btn-register-suggestion">
                            CRÉER UN COMPTE GRATUITEMENT
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Résultats de l'historique -->
    <section class="history-results-section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-bracket">[</span>
                VOS ÉVÉNEMENTS
                <span class="title-bracket">]</span>
            </h2>
            <p class="section-subtitle">
                <?php 
                if(isset($_SESSION['user_id'])) {
                    echo "BIENVENUE " . strtoupper(htmlspecialchars($_SESSION['username'] ?? '')) . " !";
                } elseif($email) {
                    echo "RÉSULTATS POUR : " . htmlspecialchars($email);
                } else {
                    echo "VOS PARTICIPATIONS";
                }
                ?>
            </p>
        </div>

        <?php if(!empty($historiqueEvents)): ?>
        <!-- Statistiques -->
        <div class="history-stats-container">
            <div class="history-stats">
                <div class="history-stat">
                    <span class="stat-number"><?php echo count($historiqueEvents); ?></span>
                    <span class="stat-label">ÉVÉNEMENTS</span>
                </div>
                <div class="history-stat">
                    <span class="stat-number">
                        <?php 
                        $completed = array_filter($historiqueEvents, function($e) {
                            return isset($e['date_fin']) && strtotime($e['date_fin']) < time();
                        });
                        echo count($completed);
                        ?>
                    </span>
                    <span class="stat-label">TERMINÉS</span>
                </div>
                <div class="history-stat">
                    <span class="stat-number">
                        <?php 
                        $upcoming = array_filter($historiqueEvents, function($e) {
                            return isset($e['date_debut']) && strtotime($e['date_debut']) > time();
                        });
                        echo count($upcoming);
                        ?>
                    </span>
                    <span class="stat-label">À VENIR</span>
                </div>
                <div class="history-stat">
                    <span class="stat-number">
                        <?php 
                        $totalSpent = 0;
                        foreach($historiqueEvents as $event) {
                            if(isset($event['prix']) && is_numeric($event['prix'])) {
                                $totalSpent += $event['prix'];
                            }
                        }
                        echo $totalSpent;
                        ?>€
                    </span>
                    <span class="stat-label">DÉPENSÉ</span>
                </div>
            </div>
        </div>

        <!-- Liste des événements -->
        <div class="history-events-container">
            <div class="history-events-grid">
                <?php foreach($historiqueEvents as $event): 
                    $isPast = false;
                    if(isset($event['date_fin'])) {
                        $isPast = strtotime($event['date_fin']) < time();
                    } elseif(isset($event['date_debut'])) {
                        $isPast = strtotime($event['date_debut']) < time();
                    }
                ?>
                <div class="history-event-card <?php echo $isPast ? 'past-event' : 'upcoming-event'; ?>">
                    <div class="history-event-header">
                        <div class="history-event-badge <?php echo $isPast ? 'badge-past' : 'badge-upcoming'; ?>">
                            <?php echo $isPast ? 'TERMINÉ' : 'À VENIR'; ?>
                        </div>
                        <div class="history-event-date">
                            <?php 
                            if(isset($event['date_inscription'])) {
                                echo date('d M Y', strtotime($event['date_inscription']));
                            } elseif(isset($event['date_debut'])) {
                                echo date('d M Y', strtotime($event['date_debut']));
                            } else {
                                echo 'Date inconnue';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="history-event-content">
                        <h4><?php echo htmlspecialchars($event['evenement_nom'] ?? 'Événement sans nom'); ?></h4>
                        
                        <?php if(isset($event['nom_participant'])): ?>
                        <div class="event-organizer-small">👤 Inscrit en tant que: <?php echo htmlspecialchars($event['nom_participant']); ?></div>
                        <?php endif; ?>
                        
                        <div class="history-event-info">
                            <?php if(isset($event['jeu'])): ?>
                            <div class="info-item">
                                <span class="info-icon">🎮</span>
                                <span><?php echo htmlspecialchars($event['jeu']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(isset($event['lieu'])): ?>
                            <div class="info-item">
                                <span class="info-icon">📍</span>
                                <span><?php echo htmlspecialchars($event['lieu']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(isset($event['prix'])): ?>
                            <div class="info-item">
                                <span class="info-icon">💰</span>
                                <span><?php echo $event['prix']; ?>€</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if(isset($event['description'])): ?>
                        <p class="history-event-description">
                            <?php echo htmlspecialchars(substr($event['description'], 0, 120)); ?>...
                        </p>
                        <?php endif; ?>
                        
                        <div class="history-event-meta">
                            <?php if(isset($event['date_debut'])): ?>
                            <span class="meta-item">📅 <?php echo date('d/m/Y H:i', strtotime($event['date_debut'])); ?></span>
                            <?php endif; ?>
                            
                            <?php if(isset($event['date_inscription'])): ?>
                            <span class="meta-item">📝 Inscrit le: <?php echo date('d/m/Y', strtotime($event['date_inscription'])); ?></span>
                            <?php endif; ?>
                            
                            <?php if(isset($event['email'])): ?>
                            <span class="meta-item">📧 <?php echo htmlspecialchars($event['email']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php elseif($email || isset($_SESSION['user_id'])): ?>
        <!-- Aucun résultat -->
        <div class="no-history-results">
            <div class="no-history-icon">📭</div>
            <h3>Aucune participation trouvée</h3>
            <p>
                <?php if(isset($_SESSION['user_id'])): ?>
                    Vous n'avez participé à aucun événement pour le moment.
                <?php else: ?>
                    Aucun événement trouvé pour l'adresse email <?php echo htmlspecialchars($email); ?>.
                <?php endif; ?>
            </p>
            <a href="evenements.php" class="btn-event">
                <span class="btn-icon">🎮</span>
                DÉCOUVRIR LES ÉVÉNEMENTS
            </a>
        </div>
        <?php endif; ?>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <!-- Même footer que dans index.php -->
        <div class="footer-top">
            <div class="footer-content">
                <!-- ... contenu du footer ... -->
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    &copy; 2024 ludology vault . Tous droits réservés.
                </div>
                <div class="footer-bottom-links">
                    <a href="#">Mentions légales</a>
                    <span>|</span>
                    <a href="#">Politique de confidentialité</a>
                    <span>|</span>
                    <a href="#">CGU</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" onclick="scrollToTop()">↑</button>

    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        window.addEventListener('scroll', function() {
            const scrollButton = document.querySelector('.scroll-top');
            if (window.scrollY > 300) {
                scrollButton.classList.add('visible');
            } else {
                scrollButton.classList.remove('visible');
            }
        });

        // Validation du formulaire email
        const emailInput = document.getElementById('userEmail');
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                if (this.value && !this.value.includes('@')) {
                    this.classList.add('format-invalid');
                } else {
                    this.classList.remove('format-invalid');
                }
            });
        }
    </script>
    
    <style>
        /* Styles spécifiques à la page historique */
        .hero-history {
            position: relative;
            height: 40vh;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 100%);
            overflow: hidden;
            border-bottom: 2px solid #00ffff;
        }
        
        .history-search-section {
            padding: 4rem 2rem;
            background: rgba(10, 10, 26, 0.8);
        }
        
        .history-search-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .history-search-card {
            background: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(0, 255, 255, 0.3);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.1);
        }
        
        .history-search-header h3 {
            color: #00ffff;
            font-family: 'VT323', monospace;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .history-search-description {
            color: #ccc;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .btn-history-search {
            background: linear-gradient(45deg, #00ccff, #0066ff);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 4px;
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-history-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 102, 255, 0.4);
        }
        
        .login-suggestion {
            margin-top: 1.5rem;
            padding: 1rem;
            background: rgba(0, 255, 255, 0.1);
            border-radius: 8px;
            text-align: center;
        }
        
        .login-suggestion p {
            color: #00ffff;
            margin-bottom: 1rem;
        }
        
        .btn-register-suggestion {
            display: inline-block;
            background: rgba(0, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-family: 'VT323', monospace;
            transition: all 0.3s ease;
        }
        
        .btn-register-suggestion:hover {
            background: rgba(0, 255, 255, 0.4);
            transform: translateY(-2px);
        }
        
        .history-stats-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .history-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            background: rgba(0, 0, 0, 0.4);
            padding: 2rem;
            border-radius: 12px;
            border: 2px solid rgba(0, 255, 255, 0.2);
        }
        
        .history-stat {
            text-align: center;
            padding: 1rem;
        }
        
        .stat-number {
            display: block;
            font-size: 2.5rem;
            font-family: 'VT323', monospace;
            color: #00ffff;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #ccc;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .history-events-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .history-events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .history-event-card {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .history-event-card:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 255, 255, 0.3);
            box-shadow: 0 10px 20px rgba(0, 255, 255, 0.1);
        }
        
        .upcoming-event {
            border-color: rgba(0, 255, 0, 0.3);
        }
        
        .past-event {
            border-color: rgba(255, 255, 0, 0.3);
        }
        
        .history-event-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .history-event-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }
        
        .badge-upcoming {
            background: rgba(0, 255, 0, 0.2);
            color: #00ff00;
        }
        
        .badge-past {
            background: rgba(255, 255, 0, 0.2);
            color: #ffff00;
        }
        
        .history-event-content {
            padding: 1.5rem;
        }
        
        .history-event-content h4 {
            color: white;
            margin-bottom: 1rem;
            font-family: 'VT323', monospace;
            font-size: 1.3rem;
        }
        
        .history-event-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #ccc;
        }
        
        .info-icon {
            color: #00ffff;
        }
        
        .history-event-description {
            color: #aaa;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .history-event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .meta-item {
            color: #888;
            font-size: 0.9rem;
        }
        
        .no-history-results {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            margin: 2rem auto;
            max-width: 600px;
        }
        
        .no-history-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.8;
        }
        
        .no-history-results h3 {
            color: #00ffff;
            margin-bottom: 1rem;
            font-family: 'VT323', monospace;
            font-size: 1.8rem;
        }
        
        .no-history-results p {
            color: #ccc;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .hero-history {
                height: 30vh;
                min-height: 250px;
            }
            
            .history-events-grid {
                grid-template-columns: 1fr;
            }
            
            .history-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</body>
</html>