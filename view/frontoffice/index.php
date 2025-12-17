<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/JeuxController.php';
require_once '../../controller/CommandeController.php';

// Initialize controllers
$userController = new UserController();
$gamesC = new JeuxController();
$commandeC = new CommandeController();

// Check if user is logged in
$isLoggedIn = $userController->isLoggedIn();
$user = null;
$username = '';
$profilePicture = '';
$role = '';

if ($isLoggedIn) {
    $result = $userController->viewProfile($_SESSION['user_id']);
    $user = $result['user'];
    $username = $user['username'];
    $profilePicture = $user['profile_picture_url'] ?? '';
    $role = $user['role'];
    
    // Get user's orders
    $userOrders = $commandeC->getUserCommandes($_SESSION['user_id']);
    $recentOrders = array_slice($userOrders, 0, 3);
    
    // Get user order stats
    $userOrderStats = $commandeC->getUserOrderCount($_SESSION['user_id']);
} else {
    $userOrders = [];
    $recentOrders = [];
    $userOrderStats = ['order_count' => 0, 'total_spent' => 0];
}

// Get games for display
$list = $gamesC->listjeux(null, null, null);
$gameCount = $gamesC->countGames();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ludology Vault - Gaming Museum</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .user-menu {
            position: relative;
        }

        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
        }

        .user-profile-btn:hover {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.2), rgba(189, 0, 255, 0.2));
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.5);
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid var(--primary-green);
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--darker-bg);
            font-size: 0.8rem;
            font-weight: bold;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-name {
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .dropdown-icon {
            font-size: 0.8rem;
            transition: transform 0.3s;
        }

        .user-profile-btn:hover .dropdown-icon {
            transform: translateY(2px);
        }

        .user-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            min-width: 250px;
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            box-shadow: 0 10px 40px rgba(0, 255, 65, 0.4);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s;
            z-index: 1000;
            overflow: hidden;
        }

        .user-dropdown::before {
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

        .user-menu:hover .user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 1.5rem;
            border-bottom: 2px solid var(--primary-green);
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), transparent);
        }

        .dropdown-header-title {
            font-size: 0.6rem;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
        }

        .dropdown-header-subtitle {
            font-size: 0.5rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
        }

        .dropdown-menu-list {
            list-style: none;
            padding: 0.5rem 0;
        }

        .dropdown-menu-item {
            margin: 0;
        }

        .dropdown-menu-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-light-gray);
            text-decoration: none;
            font-size: 0.6rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .dropdown-menu-link:hover {
            background: rgba(0, 255, 65, 0.1);
            color: var(--primary-green);
            border-left-color: var(--primary-green);
        }

        .dropdown-menu-link.admin {
            border-top: 1px solid var(--border-color);
            color: var(--secondary-purple);
        }

        .dropdown-menu-link.admin:hover {
            background: rgba(189, 0, 255, 0.1);
            color: var(--secondary-purple);
            border-left-color: var(--secondary-purple);
        }

        .dropdown-menu-link.logout {
            border-top: 1px solid var(--border-color);
            color: var(--accent-pink);
        }

        .dropdown-menu-link.logout:hover {
            background: rgba(255, 0, 110, 0.1);
            color: var(--accent-pink);
            border-left-color: var(--accent-pink);
        }

        .dropdown-icon-left {
            font-size: 1rem;
        }
        
        /* User hero stats */
        .user-hero-stats {
            display: flex;
            gap: 1.5rem;
            margin-top: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .user-stat-box {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));
            border: 2px solid var(--primary-green);
            padding: 1rem;
            min-width: 120px;
            text-align: center;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .user-stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 255, 65, 0.3);
        }

        .user-stat-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .user-stat-number {
            font-size: 1.2rem;
            color: var(--primary-green);
            display: block;
            margin-bottom: 0.3rem;
            font-family: 'VT323', monospace;
            font-size: 1.5rem;
        }

        .user-stat-label {
            font-size: 0.5rem;
            color: var(--text-gray);
            font-family: 'Press Start 2P', cursive;
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

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <div class="logo-container">
                    <div class="logo-placeholder">[LOGO]</div>
                    <h1 class="site-title">LUDOLOGY VAULT</h1>
                </div>
            </div>
            
            <div class="nav-center">
                <ul class="nav-menu">
                    <li><a href="index.php" class="active">HOME</a></li>
                    <li><a href="games.php">JEUX</a></li>
                    
                    <?php if ($isLoggedIn): ?>
                    <li><a href="my-orders.php">MES COMMANDES</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="nav-right">
                <?php if ($isLoggedIn): ?>
                    <div class="user-menu">
                        <button class="user-profile-btn">
                            <div class="user-avatar">
                                <?php if ($profilePicture && file_exists("../../uploads/" . $profilePicture)): ?>
                                    <img src="../../uploads/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($username, 0, 2)); ?>
                                <?php endif; ?>
                            </div>
                            <span class="user-name"><?php echo htmlspecialchars($username); ?></span>
                            <span class="dropdown-icon">▼</span>
                        </button>
                        
                        <div class="user-dropdown">
                            <div class="dropdown-header">
                                <div class="dropdown-header-title">WELCOME BACK</div>
                                <div class="dropdown-header-subtitle"><?php echo htmlspecialchars($username); ?></div>
                            </div>
                            <ul class="dropdown-menu-list">
                                <li class="dropdown-menu-item">
                                    <a href="profile.php" class="dropdown-menu-link">
                                        <span class="dropdown-icon-left">👤</span>
                                        MON PROFIL
                                    </a>
                                </li>
                                <li class="dropdown-menu-item">
                                    <a href="my-orders.php" class="dropdown-menu-link">
                                        <span class="dropdown-icon-left">🛒</span>
                                        MES COMMANDES
                                    </a>
                                </li>
                                <li class="dropdown-menu-item">
                                    <a href="../../../gaming_museum/view/frontoffice/index.php" class="dropdown-menu-link">
                                        <span class="dropdown-icon-left">🎮</span>
                                        gaming museum
                                    </a>
                                </li>
                                <?php if ($role === 'admin'): ?>
                                <li class="dropdown-menu-item">
                                    <a href="../backoffice/dashboard.php" class="dropdown-menu-link admin">
                                        <span class="dropdown-icon-left">⚙</span>
                                        ADMIN DASHBOARD
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="dropdown-menu-item">
                                    <a href="#" class="dropdown-menu-link logout" id="logoutBtn">
                                        <span class="dropdown-icon-left">🚪</span>
                                        DECONNEXION
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" style="text-decoration: none;">
                        <button class="btn-auth">
                            <span class="btn-icon">▶</span> SIGN IN / SIGN UP
                        </button>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="scanline"></div>
        <div class="crt-effect"></div>
        <div class="grid-background"></div>
        
        <div class="hero-content">
            <div class="glitch-wrapper">
                <h2 class="hero-title glitch" data-text="PRÉSERVONS L'HISTOIRE">PRÉSERVONS L'HISTOIRE</h2>
            </div>
            <p class="hero-subtitle">
                <span class="typing-text">
                    <?php if ($isLoggedIn): ?>
                        Bienvenue <?php echo htmlspecialchars($username); ?> ! Explorez notre collection...
                    <?php else: ?>
                        Explorez la plus grande collection de jeux vidéo rétro...
                    <?php endif; ?>
                </span>
            </p>
            
            <div class="hero-stats">
                <div class="stat-box">
                    <div class="stat-icon">🎮</div>
                    <span class="stat-number" data-target="<?= $gameCount ?>">0</span>
                    <span class="stat-label">JEUX ARCHIVÉS</span>
                </div>
                
            </div>
            
            <?php if ($isLoggedIn): ?>
            <div class="user-hero-stats">
                <div class="user-stat-box">
                    <div class="user-stat-icon">🛒</div>
                    <span class="user-stat-number"><?= $userOrderStats['order_count'] ?? 0 ?></span>
                    <span class="user-stat-label">COMMANDES</span>
                </div>
                <div class="user-stat-box">
                    <div class="user-stat-icon">💰</div>
                    <span class="user-stat-number"><?= number_format($userOrderStats['total_spent'] ?? 0, 2) ?>€</span>
                    <span class="user-stat-label">TOTAL DÉPENSÉ</span>
                </div>
                <div class="user-stat-box">
                    <div class="user-stat-icon">⭐</div>
                    <span class="user-stat-number">
                        <?php 
                        if (($userOrderStats['order_count'] ?? 0) >= 10) {
                            echo 'VIP';
                        } elseif (($userOrderStats['order_count'] ?? 0) >= 5) {
                            echo 'Régulier';
                        } else {
                            echo 'Membre';
                        }
                        ?>
                    </span>
                    <span class="user-stat-label">STATUT</span>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="cta-buttons">
                <a href="games.php" style="text-decoration: none;">
                    <button class="btn-primary">
                        <span>EXPLORER LA COLLECTION</span>
                        <span class="btn-arrow">→</span>
                    </button>
                </a>
                <?php if ($isLoggedIn): ?>
                <a href="my-orders.php" style="text-decoration: none;">
                    <button class="btn-secondary-hero">
                        <span>MES COMMANDES</span>
                        <span class="btn-arrow">→</span>
                    </button>
                </a>
                <?php else: ?>
                <a href="login.php" style="text-decoration: none;">
                    <button class="btn-secondary-hero">
                        <span>CONNEXION</span>
                        <span class="btn-arrow">→</span>
                    </button>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Featured Games Section -->
   <!-- Museum Showcase Section -->
<section class="museum-showcase-section">
    <div class="section-header">
        <h2 class="section-title">
            <span class="title-bracket">◄◄◄</span>
            GALERIE DU MUSÉE
            <span class="title-bracket">►►►</span>
        </h2>
        <p class="section-subtitle">Pièces rares récemment acquises - Voyagez à travers les décennies du gaming</p>
    </div>
    
    <!-- Museum Timeline Slider -->
    <div class="timeline-slider">
        <div class="timeline-track">
            <?php 
            // Group games by decade or era for museum presentation
            $eras = [
                '70s-80s' => ['label' => 'ÈRE ARCADE (70s-80s)', 'icon' => '🕹️', 'color' => '#FF9E00'],
                '90s' => ['label' => 'RENAISSANCE 16-BIT (90s)', 'icon' => '🎮', 'color' => '#00FF41'],
                '2000s' => ['label' => 'ÈRE 3D (2000s)', 'icon' => '🕶️', 'color' => '#BD00FF'],
                'Modern' => ['label' => 'COLLECTION MODERNE', 'icon' => '🌟', 'color' => '#0096FF']
            ];
            
            foreach ($eras as $eraKey => $eraData):
            ?>
            <div class="era-marker" style="border-color: <?= $eraData['color'] ?>;">
                <div class="era-icon" style="color: <?= $eraData['color'] ?>;">
                    <?= $eraData['icon'] ?>
                </div>
                <div class="era-label" style="color: <?= $eraData['color'] ?>;">
                    <?= $eraData['label'] ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Museum Grid with Hover Effects -->
    <div class="museum-grid">
        <?php 
        $featuredGames = array_slice($list, 0, 6); // Show 6 featured games
        foreach ($featuredGames as $index => $game): 
            // Assign era based on category or other logic
            $era = 'Modern';
            if (stripos($game['categorie'], 'Retro') !== false) $era = '70s-80s';
            if (stripos($game['categorie'], 'Classic') !== false) $era = '90s';
            if (stripos($game['categorie'], '3D') !== false) $era = '2000s';
        ?>
        <div class="museum-exhibit" data-era="<?= $era ?>">
            <!-- Museum Exhibit Frame -->
            <div class="exhibit-frame">
                <!-- Exhibit Badge -->
                <div class="exhibit-badge" style="background: <?= $eras[$era]['color'] ?>20; border-color: <?= $eras[$era]['color'] ?>;">
                    <span class="era-icon-small"><?= $eras[$era]['icon'] ?></span>
                    <span><?= $eras[$era]['label'] ?></span>
                </div>
                
                <!-- Exhibit Spotlight -->
                <div class="spotlight"></div>
                
                <!-- Game Display with Museum Case Effect -->
                <div class="exhibit-display">
                    <div class="display-case">
                        <div class="case-glass">
                            <?php if (!empty($game['image']) && file_exists('../../uploads/' . $game['image'])): ?>
                                <img src="../../uploads/<?= htmlspecialchars($game['image']) ?>" 
                                     alt="<?= htmlspecialchars($game['nom']) ?>"
                                     class="exhibit-image"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="pixel-art-render">
                                    <div class="pixel-canvas">
                                        <?php 
                                        // Create ASCII art from game name
                                        $name = substr($game['nom'], 0, 10);
                                        echo str_repeat('■', 10) . "<br>";
                                        echo "&nbsp;&nbsp;{$name}&nbsp;&nbsp;<br>";
                                        echo str_repeat('■', 10);
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Scan line effect for retro games -->
                            <?php if ($era === '70s-80s' || $era === '90s'): ?>
                            <div class="scanlines"></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Exhibit Platform -->
                        <div class="display-platform"></div>
                    </div>
                </div>
                
                <!-- Exhibit Info Plaque -->
                <div class="exhibit-plaque">
                    <div class="plaque-content">
                        <h3 class="exhibit-title"><?= htmlspecialchars($game['nom']) ?></h3>
                        <div class="exhibit-details">
                            <span class="detail-item">
                                <span class="detail-icon">🏷️</span>
                                <span class="detail-text"><?= htmlspecialchars($game['categorie']) ?></span>
                            </span>
                            <span class="detail-item">
                                <span class="detail-icon">💰</span>
                                <span class="detail-text"><?= number_format($game['prix'], 2) ?> €</span>
                            </span>
                            <span class="detail-item">
                                <span class="detail-icon">📦</span>
                                <span class="detail-text stock-<?= $game['stock'] > 0 ? 'in' : 'out' ?>">
                                    <?= $game['stock'] > 0 ? 'Disponible' : 'Épuisé' ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Museum Label -->
                <div class="museum-label">
                    <div class="label-id">REF: LV-<?= str_pad($game['id'], 6, '0', STR_PAD_LEFT) ?></div>
                    <div class="label-year">
                        <?php 
                        // Generate fake year based on era
                        $years = [
                            '70s-80s' => mt_rand(1978, 1989),
                            '90s' => mt_rand(1990, 1999),
                            '2000s' => mt_rand(2000, 2009),
                            'Modern' => mt_rand(2010, 2024)
                        ];
                        echo $years[$era];
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Interactive Controls -->
            <div class="exhibit-controls">
                <button class="control-btn view-details" 
                        onclick="window.location.href='game-details.php?id=<?= $game['id'] ?>'">
                    <span class="control-icon">🔍</span>
                    <span class="control-text">EXAMINER</span>
                </button>
                
                <?php if ($game['stock'] > 0 && $isLoggedIn): ?>
                <form action="cart.php" method="POST" class="control-form">
                    <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
                    <input type="hidden" name="action" value="add_to_cart">
                    <button type="submit" class="control-btn acquire">
                        <span class="control-icon">🛒</span>
                        <span class="control-text">ACQUÉRIR</span>
                    </button>
                </form>
                <?php elseif (!$isLoggedIn): ?>
                <button class="control-btn login-required" onclick="window.location.href='login.php'">
                    <span class="control-icon">🔒</span>
                    <span class="control-text">CONNEXION</span>
                </button>
                <?php else: ?>
                <button class="control-btn out-of-stock" disabled>
                    <span class="control-icon">⛔</span>
                    <span class="control-text">ÉPUISÉ</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Museum Navigation -->
    <div class="museum-nav">
        <a href="games.php" class="nav-link">
            <span class="nav-icon">🏛️</span>
            <span class="nav-text">EXPLORER TOUTES LES COLLECTIONS</span>
            <span class="nav-arrow">→</span>
        </a>
        <a href="timeline.php" class="nav-link">
            <span class="nav-icon">📜</span>
            <span class="nav-text">VOIR LA FRISE CHRONOLOGIQUE</span>
            <span class="nav-arrow">→</span>
        </a>
    </div>
</section>

<style>
    /* Museum Showcase Styles */
    .museum-showcase-section {
        padding: 4rem 2rem;
        background: linear-gradient(135deg, var(--darker-bg) 0%, #0a0a1a 100%);
        position: relative;
        overflow: hidden;
    }
    
    .museum-showcase-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            var(--primary-green) 20%, 
            var(--primary-green) 80%, 
            transparent 100%);
        box-shadow: 0 0 20px var(--primary-green);
    }
    
    .timeline-slider {
        max-width: 1200px;
        margin: 2rem auto 3rem;
        padding: 1rem;
        overflow-x: auto;
        scrollbar-width: none;
    }
    
    .timeline-slider::-webkit-scrollbar {
        display: none;
    }
    
    .timeline-track {
        display: flex;
        justify-content: space-around;
        min-width: min-content;
        gap: 2rem;
    }
    
    .era-marker {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem;
        border: 2px solid;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        min-width: 200px;
    }
    
    .era-marker:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    
    .era-icon {
        font-size: 2rem;
        filter: drop-shadow(0 0 5px currentColor);
    }
    
    .era-label {
        font-family: 'Press Start 2P', cursive;
        font-size: 0.5rem;
        text-align: center;
        line-height: 1.4;
    }
    
    .museum-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 3rem;
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }
    
    .museum-exhibit {
        position: relative;
        perspective: 1000px;
    }
    
    .exhibit-frame {
        position: relative;
        background: linear-gradient(135deg, #1a1a2e 0%, #0f0f1a 100%);
        border-radius: 16px;
        padding: 1.5rem;
        border: 3px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        transition: all 0.4s ease;
        transform-style: preserve-3d;
    }
    
    .museum-exhibit:hover .exhibit-frame {
        transform: translateY(-10px) rotateX(5deg);
        box-shadow: 0 20px 60px rgba(0, 255, 65, 0.2);
        border-color: var(--primary-green);
    }
    
    .exhibit-badge {
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
        font-family: 'Press Start 2P', cursive;
        font-size: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        z-index: 2;
        backdrop-filter: blur(10px);
    }
    
    .spotlight {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: radial-gradient(
            ellipse at top,
            rgba(0, 255, 65, 0.1) 0%,
            transparent 70%
        );
        pointer-events: none;
        border-radius: 12px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .museum-exhibit:hover .spotlight {
        opacity: 1;
    }
    
    .exhibit-display {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .display-case {
        position: relative;
        background: linear-gradient(135deg, #2a2a3e 0%, #1a1a2e 100%);
        border-radius: 12px;
        padding: 1.5rem;
        border: 2px solid rgba(0, 255, 65, 0.3);
        box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.5);
    }
    
    .case-glass {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: #000;
    }
    
    .exhibit-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }
    
    .museum-exhibit:hover .exhibit-image {
        transform: scale(1.05);
    }
    
    .pixel-art-render {
        width: 100%;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000;
    }
    
    .pixel-canvas {
        color: var(--primary-green);
        font-family: 'VT323', monospace;
        font-size: 1.5rem;
        text-align: center;
        line-height: 1.2;
        text-shadow: 0 0 10px var(--primary-green);
    }
    
    .scanlines {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: repeating-linear-gradient(
            0deg,
            rgba(0, 0, 0, 0.15) 0px,
            rgba(0, 0, 0, 0.15) 1px,
            transparent 1px,
            transparent 2px
        );
        pointer-events: none;
    }
    
    .display-platform {
        position: absolute;
        bottom: -10px;
        left: 20%;
        right: 20%;
        height: 20px;
        background: linear-gradient(135deg, #3a3a4e 0%, #2a2a3e 100%);
        border-radius: 4px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    }
    
    .exhibit-plaque {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, transparent 100%);
        border-radius: 8px;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 1rem;
    }
    
    .exhibit-title {
        font-family: 'Press Start 2P', cursive;
        font-size: 0.7rem;
        color: var(--primary-green);
        margin-bottom: 0.5rem;
        text-align: center;
        line-height: 1.4;
    }
    
    .exhibit-details {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-family: 'VT323', monospace;
        font-size: 1rem;
        color: var(--text-light-gray);
    }
    
    .detail-icon {
        font-size: 0.9rem;
    }
    
    .stock-in {
        color: var(--primary-green);
    }
    
    .stock-out {
        color: var(--danger-red);
    }
    
    .museum-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 4px;
        font-family: 'VT323', monospace;
        font-size: 0.9rem;
        color: var(--text-gray);
        border: 1px dashed rgba(255, 255, 255, 0.2);
    }
    
    .exhibit-controls {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .control-btn {
        flex: 1;
        padding: 0.8rem;
        border: none;
        border-radius: 6px;
        font-family: 'Press Start 2P', cursive;
        font-size: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .view-details {
        background: rgba(0, 150, 255, 0.2);
        border: 2px solid #0096ff;
        color: #0096ff;
    }
    
    .view-details:hover {
        background: rgba(0, 150, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 150, 255, 0.3);
    }
    
    .acquire {
        background: rgba(0, 255, 65, 0.2);
        border: 2px solid var(--primary-green);
        color: var(--primary-green);
    }
    
    .acquire:hover {
        background: rgba(0, 255, 65, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 255, 65, 0.3);
    }
    
    .login-required {
        background: rgba(189, 0, 255, 0.2);
        border: 2px solid var(--secondary-purple);
        color: var(--secondary-purple);
    }
    
    .login-required:hover {
        background: rgba(189, 0, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(189, 0, 255, 0.3);
    }
    
    .out-of-stock {
        background: rgba(255, 0, 60, 0.2);
        border: 2px solid var(--danger-red);
        color: var(--danger-red);
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    .control-form {
        flex: 1;
        display: flex;
    }
    
    .museum-nav {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 3rem;
        flex-wrap: wrap;
    }
    
    .nav-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 2rem;
        background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));
        border: 2px solid var(--primary-green);
        border-radius: 12px;
        color: var(--text-white);
        text-decoration: none;
        font-family: 'Press Start 2P', cursive;
        font-size: 0.6rem;
        transition: all 0.3s ease;
    }
    
    .nav-link:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 255, 65, 0.3);
        background: linear-gradient(135deg, rgba(0, 255, 65, 0.2), rgba(189, 0, 255, 0.2));
    }
    
    .nav-icon {
        font-size: 1.5rem;
    }
    
    .nav-text {
        text-align: center;
    }
    
    .nav-arrow {
        font-size: 1.2rem;
        animation: arrowPulse 1.5s infinite;
    }
    
    @keyframes arrowPulse {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(5px); }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .museum-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 1rem;
        }
        
        .timeline-track {
            flex-direction: column;
            gap: 1rem;
        }
        
        .era-marker {
            min-width: auto;
        }
        
        .museum-nav {
            flex-direction: column;
            gap: 1rem;
        }
        
        .nav-link {
            padding: 0.8rem 1rem;
            font-size: 0.5rem;
        }
    }
</style>

<script>
    // Add interactive effects
    document.addEventListener('DOMContentLoaded', function() {
        const exhibits = document.querySelectorAll('.museum-exhibit');
        
        exhibits.forEach(exhibit => {
            exhibit.addEventListener('mouseenter', function() {
                const era = this.dataset.era;
                const eraColors = {
                    '70s-80s': '#FF9E00',
                    '90s': '#00FF41',
                    '2000s': '#BD00FF',
                    'Modern': '#0096FF'
                };
                
                const color = eraColors[era] || '#00FF41';
                this.querySelector('.exhibit-frame').style.borderColor = color;
                this.querySelector('.spotlight').style.background = 
                    `radial-gradient(ellipse at top, ${color}20 0%, transparent 70%)`;
            });
            
            exhibit.addEventListener('mouseleave', function() {
                this.querySelector('.exhibit-frame').style.borderColor = 'rgba(255, 255, 255, 0.1)';
            });
        });
        
        // Timeline slider auto-scroll
        const timelineTrack = document.querySelector('.timeline-track');
        if (timelineTrack) {
            let scrollPosition = 0;
            const scrollSpeed = 0.5;
            
            function autoScroll() {
                scrollPosition += scrollSpeed;
                timelineTrack.scrollLeft = scrollPosition;
                
                if (scrollPosition >= timelineTrack.scrollWidth - timelineTrack.clientWidth) {
                    scrollPosition = 0;
                }
                
                requestAnimationFrame(autoScroll);
            }
            
            // Start auto-scroll after page load
            setTimeout(autoScroll, 2000);
        }
    });
</script>

    <!-- Timeline Section -->
    <section class="timeline-section">
        <h2 class="section-title">
            <span class="title-bracket">◄◄◄</span>
            CHRONOLOGIE DU GAMING
            <span class="title-bracket">►►►</span>
        </h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-year">1972</div>
                <div class="timeline-content">
                    <h4>NAISSANCE DU GAMING</h4>
                    <p>Pong lance l'ère des jeux vidéo commerciaux</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">1980</div>
                <div class="timeline-content">
                    <h4>L'ÂGE D'OR DES ARCADES</h4>
                    <p>Pac-Man, Space Invaders dominent les salles d'arcade</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">1985</div>
                <div class="timeline-content">
                    <h4>RÉVOLUTION CONSOLE</h4>
                    <p>La NES sauve l'industrie du crash de 1983</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">1991</div>
                <div class="timeline-content">
                    <h4>GUERRE DES CONSOLES</h4>
                    <p>Sonic vs Mario - Sega vs Nintendo</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">1994</div>
                <div class="timeline-content">
                    <h4>ÈRE PLAYSTATION</h4>
                    <p>Sony entre dans l'arène avec la PlayStation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="newsletter-container">
            <div class="newsletter-content">
                <h2>RESTEZ CONNECTÉ AU PASSÉ</h2>
                <p>Inscrivez-vous à notre newsletter pour recevoir les dernières nouvelles, événements et ajouts à notre collection</p>
            </div>
            <div class="newsletter-form">
                <input type="email" placeholder="votre.email@exemple.com" class="newsletter-input">
                <button class="newsletter-button">S'ABONNER →</button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-content">
                <div class="footer-section footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-placeholder">[LOGO]</div>
                        <h3>LUDOLOGY VAULT</h3>
                    </div>
                    <p class="footer-tagline">Préserver l'histoire du jeu vidéo pour les générations futures</p>
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
                        <li><a href="index.php">► Accueil</a></li>
                        <li><a href="games.php">► Collection de Jeux</a></li>
                        <li><a href="blog.php">► Blog & Actualités</a></li>
                        <li><a href="events.php">► Événements</a></li>
                        <li><a href="#">► À Propos</a></li>
                        <?php if ($isLoggedIn): ?>
                        <li><a href="profile.php">► Mon Profil</a></li>
                        <li><a href="my-orders.php">► Mes Commandes</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">RESSOURCES</h3>
                    <ul class="footer-links">
                        <li><a href="#">► Base de Données</a></li>
                        <li><a href="#">► Archives Historiques</a></li>
                        <li><a href="#">► Guides & Tutoriels</a></li>
                        <li><a href="reclamation.php">► Support & Réclamations</a></li>
                        <li><a href="#">► FAQ</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">MUSÉE</h3>
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
                <p class="copyright">&copy; 2024 LUDOLOGY VAULT - Tous droits réservés</p>
                <div class="footer-bottom-links">
                    <a href="#">Mentions Légales</a>
                    <span>•</span>
                    <a href="#">Politique de Confidentialité</a>
                    <span>•</span>
                    <a href="#">Conditions d'Utilisation</a>
                </div>
                <p class="made-with">Made with <span class="heart">♥</span> for gamers worldwide</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <span>▲</span>
    </button>

    <script src="script.js"></script>
    <script>
        <?php if ($isLoggedIn): ?>
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter?')) {
                const formData = new FormData();
                formData.append('action', 'logout');
                
                fetch('../../controller/user_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = 'index.php';
                });
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>