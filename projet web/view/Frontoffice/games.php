<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Jeux - Musée de Gaming</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .games-section {
            margin-top: 100px;
            padding: 2rem 0;
        }

        .games-hero {
            background: url('../assets/images/games-banner.jpg') center/cover;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
        }

        .games-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .section-title {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .search-container {
            max-width: 600px;
            margin: 0 auto 3rem;
        }

        .search-box {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .search-input {
            flex: 1;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid var(--border);
            border-radius: 10px;
            color: var(--light);
            font-size: 1rem;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            margin-bottom: 0.5rem;
            color: var(--primary);
            font-weight: bold;
        }

        .filter-select {
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--light);
        }

        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .game-card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid var(--border);
        }

        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
        }

        .game-image {
            height: 200px;
            overflow: hidden;
        }

        .game-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .game-content {
            padding: 1.5rem;
        }

        .game-title {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--light);
        }

        .game-platforms {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .platform-tag {
            background: rgba(0, 204, 255, 0.2);
            color: var(--secondary);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            border: 1px solid var(--secondary);
        }

        .game-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: var(--gray);
        }

        .game-rating {
            color: gold;
            font-weight: bold;
        }

        .game-description {
            color: var(--gray);
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .game-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .no-games {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
            grid-column: 1 / -1;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: var(--gray);
        }

        .error-message {
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: none;
        }

        .search-box.error .search-input {
            border-color: #ff6b6b;
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }
            
            .games-hero {
                height: 200px;
            }
            
            .search-box {
                flex-direction: column;
            }
            
            .games-grid {
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
                <div class="logo">🎮 Musée de Gaming</div>
                <ul class="nav-links">
                    <li><a href="../index.php">Accueil</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="games.php" class="active">Jeux</a></li>
                    <li><a href="about.php">À propos</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="../backoffice/dashboard.php" class="admin-btn">Espace Admin</a></li>
                </ul>
                <div class="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Games Section -->
    <section class="games-section">
        <div class="container">
            <!-- Hero -->
            <div class="games-hero">
                <div class="hero-content">
                    <h1 class="section-title">Base de Données Jeux</h1>
                    <p class="section-subtitle">Explorez notre collection complète de jeux vidéo</p>
                </div>
            </div>

            <!-- Recherche et Filtres -->
            <div class="search-container">
                <div class="search-box" id="searchBox">
                    <input type="text" class="search-input" id="searchInput" placeholder="Rechercher un jeu...">
                    <button type="button" class="btn btn-primary" onclick="validateAndSearch()">Rechercher</button>
                </div>
                <div class="error-message" id="searchError">Veuillez saisir un terme de recherche valide</div>

                <div class="filters">
                    <div class="filter-group">
                        <label class="filter-label">Plateforme</label>
                        <select class="filter-select" id="platformFilter">
                            <option value="">Toutes les plateformes</option>
                            <option value="pc">PC</option>
                            <option value="playstation">PlayStation</option>
                            <option value="xbox">Xbox</option>
                            <option value="nintendo">Nintendo</option>
                            <option value="mobile">Mobile</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Genre</label>
                        <select class="filter-select" id="genreFilter">
                            <option value="">Tous les genres</option>
                            <option value="action">Action</option>
                            <option value="aventure">Aventure</option>
                            <option value="rpg">RPG</option>
                            <option value="fps">FPS</option>
                            <option value="strategie">Stratégie</option>
                            <option value="sport">Sport</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Année</label>
                        <select class="filter-select" id="yearFilter">
                            <option value="">Toutes les années</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                            <option value="2022">2022</option>
                            <option value="2021">2021</option>
                            <option value="2020">2020</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grille de Jeux -->
            <div class="games-grid" id="gamesGrid">
                <div class="loading">Chargement des jeux...</div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">🎮 Musée de Gaming</div>
                    <p>Votre destination ultime pour la culture gaming</p>
                </div>
                <div class="footer-section">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="games.php">Jeux</a></li>
                        <li><a href="../backoffice/dashboard.php">Admin</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>email@musee-gaming.fr<br>+33 1 23 45 67 89</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Musée de Gaming. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/state-manager.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        // Données de démonstration pour les jeux
        const sampleGames = [
            {
                id: 1,
                title: "Cyberpunk 2077",
                description: "Un RPG d'action-aventure en monde ouvert se déroulant dans la métropole futuriste de Night City.",
                platforms: ["PC", "PlayStation", "Xbox"],
                genre: "RPG",
                release_year: 2020,
                rating: 4.5,
                image: "cyberpunk-game.jpg",
                developer: "CD Projekt Red"
            },
            {
                id: 2,
                title: "The Legend of Zelda: Tears of the Kingdom",
                description: "La suite épique de Breath of the Wild, repoussant les limites de l'exploration et de la créativité.",
                platforms: ["Nintendo Switch"],
                genre: "Aventure",
                release_year: 2023,
                rating: 5.0,
                image: "zelda-totk.jpg",
                developer: "Nintendo"
            },
            {
                id: 3,
                title: "Call of Duty: Modern Warfare III",
                description: "Retour des opérations spéciales dans cette suite intense du célèbre FPS.",
                platforms: ["PC", "PlayStation", "Xbox"],
                genre: "FPS",
                release_year: 2023,
                rating: 4.0,
                image: "cod-mw3.jpg",
                developer: "Infinity Ward"
            },
            {
                id: 4,
                title: "Baldur's Gate 3",
                description: "Un RPG épique basé sur D&D, acclamé par la critique pour sa narration et sa profondeur.",
                platforms: ["PC", "PlayStation", "Xbox"],
                genre: "RPG",
                release_year: 2023,
                rating: 5.0,
                image: "baldurs-gate-3.jpg",
                developer: "Larian Studios"
            },
            {
                id: 5,
                title: "Spider-Man 2",
                description: "Peter Parker et Miles Morales unissent leurs forces contre de nouveaux dangers menaçant New York.",
                platforms: ["PlayStation"],
                genre: "Action",
                release_year: 2023,
                rating: 4.8,
                image: "spiderman-2.jpg",
                developer: "Insomniac Games"
            },
            {
                id: 6,
                title: "Starfield",
                description: "Explorez la galaxie dans ce RPG spatial ambitieux de Bethesda Game Studios.",
                platforms: ["PC", "Xbox"],
                genre: "RPG",
                release_year: 2023,
                rating: 4.2,
                image: "starfield.jpg",
                developer: "Bethesda"
            }
        ];

        function loadGames() {
            const gamesGrid = document.getElementById('gamesGrid');
            
            // Initialiser les jeux dans le state s'ils n'existent pas
            if (AppState.state.games.length === 0) {
                AppState.state.games = sampleGames;
                AppState.saveState();
            }

            displayGames(AppState.state.games);
        }

        function displayGames(games) {
            const gamesGrid = document.getElementById('gamesGrid');
            
            if (games.length === 0) {
                gamesGrid.innerHTML = `
                    <div class="no-games">
                        <h3>Aucun jeu trouvé</h3>
                        <p>Essayez de modifier vos critères de recherche.</p>
                    </div>
                `;
                return;
            }

            gamesGrid.innerHTML = games.map(game => `
                <div class="game-card">
                    <div class="game-image">
                        <img src="../assets/images/${game.image}" alt="${game.title}" 
                             onerror="this.src='../assets/images/placeholder.jpg'">
                    </div>
                    <div class="game-content">
                        <h3 class="game-title">${game.title}</h3>
                        
                        <div class="game-platforms">
                            ${game.platforms.map(platform => 
                                `<span class="platform-tag">${platform}</span>`
                            ).join('')}
                        </div>
                        
                        <div class="game-meta">
                            <span class="game-year">${game.release_year}</span>
                            <span class="game-rating">⭐ ${game.rating}/5</span>
                        </div>
                        
                        <p class="game-description">${game.description}</p>
                        
                        <div class="game-meta">
                            <span class="game-developer">${game.developer}</span>
                            <span class="game-genre">${game.genre}</span>
                        </div>
                        
                        <div class="game-actions">
                            <button class="btn btn-primary btn-sm" onclick="viewGameDetails(${game.id})">
                                Détails
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="addToFavorites(${game.id})">
                                ♡ Favori
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function validateAndSearch() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput.value.trim();
            const searchBox = document.getElementById('searchBox');
            const searchError = document.getElementById('searchError');

            // Réinitialiser les erreurs
            searchBox.classList.remove('error');
            searchError.style.display = 'none';

            // Validation personnalisée
            if (searchTerm.length > 0 && searchTerm.length < 2) {
                searchBox.classList.add('error');
                searchError.style.display = 'block';
                searchError.textContent = 'Le terme de recherche doit contenir au moins 2 caractères';
                return;
            }

            // Si validation OK, effectuer la recherche
            searchGames();
        }

        function searchGames() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const platformFilter = document.getElementById('platformFilter').value;
            const genreFilter = document.getElementById('genreFilter').value;
            const yearFilter = document.getElementById('yearFilter').value;

            let filteredGames = AppState.state.games.filter(game => {
                const matchesSearch = !searchTerm || 
                                    game.title.toLowerCase().includes(searchTerm) ||
                                    game.description.toLowerCase().includes(searchTerm);
                
                const matchesPlatform = !platformFilter || 
                                      game.platforms.some(platform => 
                                          platform.toLowerCase().includes(platformFilter.toLowerCase()));
                
                const matchesGenre = !genreFilter || 
                                   game.genre.toLowerCase().includes(genreFilter.toLowerCase());
                
                const matchesYear = !yearFilter || 
                                  game.release_year.toString() === yearFilter;

                return matchesSearch && matchesPlatform && matchesGenre && matchesYear;
            });

            displayGames(filteredGames);
        }

        function viewGameDetails(gameId) {
            // Validation du gameId
            if (!gameId || isNaN(gameId)) {
                alert('ID de jeu invalide');
                return;
            }

            const game = AppState.state.games.find(g => g.id === gameId);
            if (!game) {
                alert('Jeu non trouvé');
                return;
            }

            alert(`Détails du jeu:\n\nTitre: ${game.title}\nGenre: ${game.genre}\nPlateformes: ${game.platforms.join(', ')}\nAnnée: ${game.release_year}\nNote: ${game.rating}/5\n\nCette fonctionnalité sera implémentée prochainement!`);
        }

        function addToFavorites(gameId) {
            // Validation du gameId
            if (!gameId || isNaN(gameId)) {
                alert('ID de jeu invalide');
                return;
            }

            const game = AppState.state.games.find(g => g.id === gameId);
            if (!game) {
                alert('Jeu non trouvé');
                return;
            }

            // Ajouter aux favoris (simulation)
            if (!AppState.state.favorites) {
                AppState.state.favorites = [];
            }

            const isAlreadyFavorite = AppState.state.favorites.some(fav => fav.id === gameId);
            if (!isAlreadyFavorite) {
                AppState.state.favorites.push(game);
                AppState.saveState();
                alert(`"${game.title}" a été ajouté aux favoris!`);
            } else {
                alert(`"${game.title}" est déjà dans vos favoris!`);
            }
        }

        // Validation en temps réel pour la recherche
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchBox = document.getElementById('searchBox');
            const searchError = document.getElementById('searchError');
            
            // Réinitialiser les erreurs lors de la saisie
            searchBox.classList.remove('error');
            searchError.style.display = 'none';

            // Recherche automatique après validation
            if (this.value.trim().length >= 2 || this.value.trim().length === 0) {
                searchGames();
            }
        });

        // Écouteurs d'événements pour les filtres
        document.getElementById('platformFilter').addEventListener('change', searchGames);
        document.getElementById('genreFilter').addEventListener('change', searchGames);
        document.getElementById('yearFilter').addEventListener('change', searchGames);

        // Recherche avec la touche Entrée
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                validateAndSearch();
            }
        });

        // Charger les jeux au démarrage
        document.addEventListener('DOMContentLoaded', loadGames);
    </script>
</body>
</html>