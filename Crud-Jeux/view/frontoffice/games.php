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
}

// Get search/filter parameters
$search = $_GET['search'] ?? null;
if (isset($_GET['filter']) && $_GET['filter'] !== '') {
    $search = $_GET['filter'];
}

// Get sorting
$sortBy = $_GET['sortby'] ?? null;
$order  = $_GET['order'] ?? null;

// Fetch games
$list = $gamesC->listjeux($search, $sortBy, $order);
$gameCount = $gamesC->countGames();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ludology Vault - Collection de Jeux</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .pixel-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }

        .game-card {
            position: relative;
        }

        .game-desc {
            opacity: 0;
            height: 0;
            overflow: hidden;
            transform: translateY(10px);
            transition: all 0.35s ease-in-out;
            pointer-events: none;
        }

        .game-card:hover .game-desc {
            opacity: 1;
            height: auto;
            transform: translateY(0);
        }
        
        /* User menu styles (copied from index) */
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
        
        /* Header search bar */
        .header-search {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .header-search-input {
            padding: 0.5rem 1rem;
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            width: 200px;
        }
        
        .header-search-btn {
            padding: 0.5rem 1rem;
            background: var(--primary-green);
            border: 2px solid var(--primary-green);
            color: var(--darker-bg);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
            cursor: pointer;
        }
        
        .header-search-btn:hover {
            background: #00cc33;
        }
        
        /* Games header */
        .games-header {
            margin: 2rem 0;
            text-align: center;
        }
        
        .games-title {
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 1rem;
        }
        
        .games-count {
            font-size: 1rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
        
        /* Sort and filter bar */
        .sort-filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2rem 0;
            padding: 1rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
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
                    <div class="logo-placeholder">🎮</div>
                    <h1 class="site-title">LUDOLOGY VAULT</h1>
                </div>
            </div>
            
            <div class="nav-center">
                <ul class="nav-menu">
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="games.php" class="active">JEUX</a></li>
                    
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
                <a href="cart.php" style="text-decoration: none; margin-left: 1rem;">
                    <button class="btn-auth">
                        <span class="btn-icon">🛒</span> 
                    </button>
                </a>
            </div>
        </div>
    </nav>

    <!-- Games Header -->
    <section class="games-header">
        <h2 class="games-title">◄◄◄ COLLECTION COMPLÈTE DES JEUX ►►►</h2>
        <p class="games-count"><?= $gameCount ?> jeux disponibles dans notre collection</p>
    </section>

    <!-- Search & Filter Section -->
    <section class="search-section">
    <div class="search-container">
        <h3 class="search-title">◄ FILTRER ET TRIER LA COLLECTION ►</h3>
        
        <!-- Search Bar -->
        <form method="GET" action="games.php" class="search-bar">
            <input type="text" 
                   name="search"
                   placeholder="Rechercher un jeu..." 
                   class="search-input"
                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit" class="search-button">RECHERCHER</button>
        </form>

        <!-- Filters and Sorts Container -->
        <div class="filters-sorts-container">
            <!-- Quick Filters -->
            <div class="filters-section">
                <h4 class="filters-title">CATÉGORIES</h4>
                <div class="quick-filters">
                    <a href="games.php?filter=Action">
                        <button class="filter-chip <?= (($_GET['filter'] ?? '') === 'Action') ? 'active' : '' ?>">
                            Action
                        </button>
                    </a>
                    <a href="games.php?filter=Looter Shooter">
                        <button class="filter-chip <?= (($_GET['filter'] ?? '') === 'Looter Shooter') ? 'active' : '' ?>">
                            Looter Shooter
                        </button>
                    </a>
                    <a href="games.php?filter=Battle Royale">
                        <button class="filter-chip <?= (($_GET['filter'] ?? '') === 'Battle Royale') ? 'active' : '' ?>">
                            Battle Royale
                        </button>
                    </a>
                    <a href="games.php">
                        <button class="filter-chip clear-filter">
                            TOUT AFFICHER
                        </button>
                    </a>
                </div>
            </div>

            <!-- Sorting Options -->
            <div class="sorts-section">
                <h4 class="sorts-title">TRIER PAR</h4>
                <form method="GET" action="games.php" class="sort-form">
                    <?php if (isset($_GET['search'])): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['filter'])): ?>
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($_GET['filter']) ?>">
                    <?php endif; ?>
                    
                    <div class="sort-options">
                        <div class="sort-option">
                            <label>Critère:</label>
                            <select name="sortby" onchange="this.form.submit()" class="sort-select">
                                <option value="">Sélectionner</option>
                                <option value="prix" <?= (($_GET['sortby'] ?? '') === 'prix') ? 'selected' : '' ?>>Prix</option>
                                <option value="nom" <?= (($_GET['sortby'] ?? '') === 'nom') ? 'selected' : '' ?>>Nom</option>
                                <option value="date" <?= (($_GET['sortby'] ?? '') === 'date') ? 'selected' : '' ?>>Date d'ajout</option>
                            </select>
                        </div>
                        
                        <div class="sort-option">
                            <label>Ordre:</label>
                            <select name="order" onchange="this.form.submit()" class="sort-select">
                                <option value="asc" <?= (($_GET['order'] ?? '') === 'asc') ? 'selected' : '' ?>>Croissant ↑</option>
                                <option value="desc" <?= (($_GET['order'] ?? '') === 'desc') ? 'selected' : '' ?>>Décroissant ↓</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Active Filters Display -->
        <?php if (isset($_GET['search']) || isset($_GET['filter']) || isset($_GET['sortby'])): ?>
        <div class="active-filters">
            <span class="active-filters-label">Filtres actifs:</span>
            
            <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
            <span class="active-filter-tag">
                Recherche: "<?= htmlspecialchars($_GET['search']) ?>"
                <a href="games.php?<?= http_build_query(array_diff_key($_GET, ['search' => ''])) ?>" class="remove-filter">×</a>
            </span>
            <?php endif; ?>
            
            <?php if (isset($_GET['filter']) && $_GET['filter'] !== ''): ?>
            <span class="active-filter-tag">
                Catégorie: <?= htmlspecialchars($_GET['filter']) ?>
                <a href="games.php?<?= http_build_query(array_diff_key($_GET, ['filter' => ''])) ?>" class="remove-filter">×</a>
            </span>
            <?php endif; ?>
            
            <?php if (isset($_GET['sortby']) && $_GET['sortby'] !== ''): ?>
            <span class="active-filter-tag">
                Tri: <?= htmlspecialchars($_GET['sortby']) ?> (<?= ($_GET['order'] ?? 'asc') === 'asc' ? 'Croissant' : 'Décroissant' ?>)
                <a href="games.php?<?= http_build_query(array_diff_key($_GET, ['sortby' => '', 'order' => ''])) ?>" class="remove-filter">×</a>
            </span>
            <?php endif; ?>
            
            <?php if (isset($_GET['search']) || isset($_GET['filter']) || isset($_GET['sortby'])): ?>
            <a href="games.php" class="clear-all-filters">
                Effacer tous les filtres
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.search-section {
    margin: 2rem 0;
}

.search-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    background: linear-gradient(135deg, rgba(0, 255, 65, 0.05), rgba(189, 0, 255, 0.05));
    border: 2px solid var(--primary-green);
    border-radius: 8px;
}

.search-title {
    text-align: center;
    font-size: 1.2rem;
    color: var(--primary-green);
    margin-bottom: 2rem;
    text-shadow: 0 0 10px var(--primary-green);
}

.search-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.search-input {
    flex: 1;
    padding: 1rem 1.5rem;
    background: var(--darker-bg);
    border: 2px solid var(--primary-green);
    color: var(--text-white);
    font-family: 'VT323', monospace;
    font-size: 1.2rem;
    border-radius: 4px;
}

.search-input:focus {
    outline: none;
    box-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
}

.search-button {
    padding: 1rem 2rem;
    background: var(--primary-green);
    border: 2px solid var(--primary-green);
    color: var(--darker-bg);
    font-family: 'Press Start 2P', cursive;
    font-size: 0.6rem;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s;
    font-weight: bold;
}

.search-button:hover {
    background: #00cc33;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 255, 65, 0.4);
}

/* Filters and Sorts Container */
.filters-sorts-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .filters-sorts-container {
        grid-template-columns: 1fr;
    }
}

/* Filters Section */
.filters-section, .sorts-section {
    background: rgba(0, 255, 65, 0.03);
    border: 1px solid var(--border-color);
    padding: 1.5rem;
    border-radius: 6px;
}

.filters-title, .sorts-title {
    font-size: 0.8rem;
    color: var(--primary-green);
    margin-bottom: 1rem;
    font-family: 'Press Start 2P', cursive;
    text-align: center;
}

.quick-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    justify-content: center;
}

.filter-chip {
    padding: 0.8rem 1.5rem;
    background: rgba(0, 255, 65, 0.1);
    border: 2px solid var(--primary-green);
    color: var(--primary-green);
    font-family: 'Press Start 2P', cursive;
    font-size: 0.5rem;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s;
}

.filter-chip:hover {
    background: rgba(0, 255, 65, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 5px 10px rgba(0, 255, 65, 0.3);
}

.filter-chip.active {
    background: var(--primary-green);
    color: var(--darker-bg);
    font-weight: bold;
}

.filter-chip.clear-filter {
    background: var(--accent-pink);
    border-color: var(--accent-pink);
    color: white;
}

.filter-chip.clear-filter:hover {
    background: #ff2d8a;
    border-color: #ff2d8a;
}

/* Sorts Section */
.sort-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.sort-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 480px) {
    .sort-options {
        grid-template-columns: 1fr;
    }
}

.sort-option {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.sort-option label {
    font-size: 0.5rem;
    color: var(--text-gray);
    font-family: 'Press Start 2P', cursive;
}

.sort-select {
    padding: 0.8rem;
    background: var(--darker-bg);
    border: 2px solid var(--primary-green);
    color: var(--text-white);
    font-family: 'VT323', monospace;
    font-size: 1rem;
    border-radius: 4px;
    cursor: pointer;
}

.sort-select:focus {
    outline: none;
    box-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
}

/* Active Filters Display */
.active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
    padding: 1rem;
    background: rgba(0, 255, 65, 0.05);
    border-radius: 6px;
    border-left: 4px solid var(--primary-green);
}

.active-filters-label {
    font-size: 0.6rem;
    color: var(--primary-green);
    font-family: 'Press Start 2P', cursive;
    font-weight: bold;
}

.active-filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(0, 255, 65, 0.15);
    border: 1px solid var(--primary-green);
    border-radius: 20px;
    font-size: 0.6rem;
    color: var(--text-white);
    font-family: 'VT323', monospace;
}

.remove-filter {
    color: var(--accent-pink);
    text-decoration: none;
    font-weight: bold;
    font-size: 1rem;
    line-height: 1;
    padding: 0 0.2rem;
}

.remove-filter:hover {
    color: #ff2d8a;
}

.clear-all-filters {
    margin-left: auto;
    font-size: 0.5rem;
    color: var(--accent-pink);
    text-decoration: none;
    font-family: 'Press Start 2P', cursive;
    padding: 0.5rem 1rem;
    border: 1px solid var(--accent-pink);
    border-radius: 4px;
    transition: all 0.3s;
}

.clear-all-filters:hover {
    background: var(--accent-pink);
    color: white;
}

/* Results Count */
.results-count {
    text-align: center;
    margin-top: 1rem;
    font-size: 0.8rem;
    color: var(--text-gray);
    font-family: 'VT323', monospace;
}

.results-count strong {
    color: var(--primary-green);
}
</style>

<?php if (!empty($list)): ?>
<div class="results-count">
    <strong><?= count($list) ?></strong> jeux trouvés
    <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
    pour "<?= htmlspecialchars($_GET['search']) ?>"
    <?php endif; ?>
</div>
<?php endif; ?>

    <!-- Games Grid -->
    <section class="featured-section">
        <div class="games-grid">
            <?php if (!empty($list)): ?>
                <?php foreach ($list as $game): ?>
                <div class="game-card">
                    <!-- CATEGORY BADGE -->
                    <div class="game-badge">
                        <?= htmlspecialchars($game['categorie']); ?>
                    </div>

                    <!-- IMAGE / PIXEL ART AREA -->
                    <div class="game-image">
                        <div class="pixel-art">
                            <div class="pixel-placeholder">
                                <?php if (!empty($game['image'])): ?>
                                    <img 
                                        src="../../uploads/<?= htmlspecialchars($game['image']); ?>" 
                                        alt="<?= htmlspecialchars($game['nom']); ?>"
                                        class="game-img"
                                    >
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple)); display: flex; align-items: center; justify-content: center; color: white; font-family: 'VT323', monospace; font-size: 1.2rem;">
                                        <?= substr(htmlspecialchars($game['nom']), 0, 10) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="hover-overlay">
                                <a href="gamedetails.php?id=<?= $game['id']; ?>">
                                    <button class="quick-view">DÉTAILS</button>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- GAME INFO -->
                    <div class="game-info">
                        <h3><?= htmlspecialchars($game['nom']); ?></h3>

                        <div class="game-meta">
                            <span class="game-year">💰 
                                <?= htmlspecialchars($game['prix']); ?> €
                            </span>
                            <span class="game-rating">
                                Stock : <?= htmlspecialchars($game['stock']); ?>
                            </span>
                        </div>

                        <p class="game-desc">
                            <?= nl2br(htmlspecialchars(substr($game['description'], 0, 150) . '...')); ?>
                        </p>

                        <div class="game-tags">
                            <span class="tag">
                                <?= htmlspecialchars($game['categorie']); ?>
                            </span>
                        </div>
                        
                        <!-- Add to cart button -->
                        <?php if ($isLoggedIn && $game['stock'] > 0): ?>
                        <form action="cart.php" method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
                            <input type="hidden" name="action" value="add_to_cart">
                            <button type="submit" class="btn-auth" style="width: 100%; padding: 0.5rem;">
                                <span class="btn-icon">🛒</span> AJOUTER AU PANIER
                            </button>
                        </form>
                        <?php elseif (!$isLoggedIn && $game['stock'] > 0): ?>
                        <a href="login.php" style="text-decoration: none;">
                            <button class="btn-auth" style="width: 100%; padding: 0.5rem; background: var(--warning-orange);">
                                <span class="btn-icon">🔒</span> CONNECTEZ-VOUS POUR ACHETER
                            </button>
                        </a>
                        <?php elseif ($game['stock'] <= 0): ?>
                        <button class="btn-auth" style="width: 100%; padding: 0.5rem; background: var(--danger-red); cursor: not-allowed;" disabled>
                            <span class="btn-icon">⛔</span> RUPTURE DE STOCK
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: rgba(255, 255, 255, 0.05); border: 2px solid var(--border-color);">
                    <h3 style="color: var(--text-gray); margin-bottom: 1rem;">Aucun jeu trouvé</h3>
                    <p style="color: var(--text-light-gray); font-family: 'VT323', monospace; font-size: 1.2rem;">
                        Essayez une autre recherche ou <a href="games.php" style="color: var(--primary-green);">affichez tous les jeux</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-content">
                <div class="footer-section footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-placeholder">🎮</div>
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
        
        // Highlight active filter chips
        document.addEventListener('DOMContentLoaded', function() {
            const filterChips = document.querySelectorAll('.filter-chip');
            filterChips.forEach(chip => {
                chip.addEventListener('click', function() {
                    filterChips.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>