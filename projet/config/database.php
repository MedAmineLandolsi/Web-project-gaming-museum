<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'projet_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            // First, connect without specifying database to check/create it
            $temp_conn = new PDO(
                "mysql:host=" . $this->host,
                $this->username,
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            
            // Check if database exists, create if not
            $this->createDatabaseIfNotExists($temp_conn);
            
            // Now connect to the specific database
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            $this->conn->exec('set names utf8');

            // S'assurer que les dernières tables existent (utile après des mises à jour)
            $this->createTables();
            
        } catch(PDOException $e) {
            throw new Exception('Connection Error: ' . $e->getMessage());
        }

        return $this->conn;
    }

    public static function getConnection() {
        $instance = new self(); // Crée une instance de la classe
        return $instance->connect(); // Appelle la méthode connect()
    }

    private function createDatabaseIfNotExists($conn) {
        try {
            // Check if database exists
            $stmt = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $this->db_name . "'");
            
            if ($stmt->rowCount() == 0) {
                // Create database if it doesn't exist
                $conn->exec("CREATE DATABASE " . $this->db_name . " CHARACTER SET utf8 COLLATE utf8_general_ci");
                
                // Create tables after creating database
                $this->createTables();
                
                // Insert demo data
                $this->insertDemoData();
            }
            
        } catch(PDOException $e) {
            throw new Exception("Error creating database: " . $e->getMessage());
        }
    }

    private function createTables() {
        try {
            // Connect to the specific database now that it exists
            $db_with_dbname = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            
            // Table Membre
            $sql_membre = "CREATE TABLE IF NOT EXISTS membre (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(50) NOT NULL,
                prenom VARCHAR(50) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                statut ENUM('actif', 'inactif', 'suspendu') DEFAULT 'actif',
                mot_de_passe VARCHAR(255) NOT NULL,
                avatar VARCHAR(255),
                bio TEXT
            )";
            
            $db_with_dbname->exec($sql_membre);
            
            // Table Communaute
            $sql_communaute = "CREATE TABLE IF NOT EXISTS communaute (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL UNIQUE,
                categorie VARCHAR(50) NOT NULL,
                description TEXT NOT NULL,
                createur_id INT,
                date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                avatar VARCHAR(255),
                visibilite ENUM('publique', 'privee', 'cachee') DEFAULT 'publique',
                regles TEXT,
                FOREIGN KEY (createur_id) REFERENCES membre(id) ON DELETE SET NULL
            )";
            
            $db_with_dbname->exec($sql_communaute);
            
            // Table Publication
            $sql_publication = "CREATE TABLE IF NOT EXISTS publication (
                id INT AUTO_INCREMENT PRIMARY KEY,
                communaute_id INT,
                auteur_id INT,
                contenu TEXT NOT NULL,
                images JSON,
                likes INT DEFAULT 0,
                commentaires INT DEFAULT 0,
                date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (communaute_id) REFERENCES communaute(id) ON DELETE CASCADE,
                FOREIGN KEY (auteur_id) REFERENCES membre(id) ON DELETE CASCADE
            )";
            
            $db_with_dbname->exec($sql_publication);

            // Table Membre_Communaute (liaison)
            $sql_membre_communaute = "CREATE TABLE IF NOT EXISTS membre_communaute (
                id INT AUTO_INCREMENT PRIMARY KEY,
                membre_id INT NOT NULL,
                communaute_id INT NOT NULL,
                date_join TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                role ENUM('membre', 'moderateur', 'admin') DEFAULT 'membre',
                FOREIGN KEY (membre_id) REFERENCES membre(id) ON DELETE CASCADE,
                FOREIGN KEY (communaute_id) REFERENCES communaute(id) ON DELETE CASCADE,
                UNIQUE KEY unique_membre_communaute (membre_id, communaute_id)
            )";
            
            $db_with_dbname->exec($sql_membre_communaute);
            
            // Table Interactions
            $sql_interactions = "CREATE TABLE IF NOT EXISTS interactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                item_id INT NOT NULL,
                interaction_type VARCHAR(50) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES membre(id),
                FOREIGN KEY (item_id) REFERENCES publication(id)
            )";
            
            $db_with_dbname->exec($sql_interactions);
            
        } catch(PDOException $e) {
            throw new Exception("Error creating tables: " . $e->getMessage());
        }
    }

    private function insertDemoData() {
        try {
            // Connect to the specific database now that it exists
            $db_with_dbname = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );

            // Vérifier si des données existent déjà
            $stmt = $db_with_dbname->query("SELECT COUNT(*) FROM membre");
            $count_membres = $stmt->fetchColumn();

            if ($count_membres == 0) {
                echo "<div style='background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px;'>
                        <strong>Initialisation de la base de données</strong><br>
                        Insertion des données de démonstration...
                      </div>";

                // === INSERTION DES MEMBRES ===
                $membres = [
                    [
                        'nom' => 'Dupont',
                        'prenom' => 'Jean',
                        'email' => 'jean.dupont@email.com',
                        'mot_de_passe' => password_hash('password123', PASSWORD_DEFAULT),
                        'statut' => 'actif',
                        'avatar' => 'https://i.pravatar.cc/150?img=1',
                        'bio' => 'Développeur passionné par les nouvelles technologies et le web.'
                    ],
                    [
                        'nom' => 'Martin',
                        'prenom' => 'Marie',
                        'email' => 'marie.martin@email.com',
                        'mot_de_passe' => password_hash('password123', PASSWORD_DEFAULT),
                        'statut' => 'actif',
                        'avatar' => 'https://i.pravatar.cc/150?img=2',
                        'bio' => 'Designer graphique et amatrice de photographie et d\'art digital.'
                    ],
                    [
                        'nom' => 'Bernard',
                        'prenom' => 'Pierre',
                        'email' => 'pierre.bernard@email.com',
                        'mot_de_passe' => password_hash('password123', PASSWORD_DEFAULT),
                        'statut' => 'actif',
                        'avatar' => 'https://i.pravatar.cc/150?img=3',
                        'bio' => 'Étudiant en informatique et passionné de jeux vidéo et d\'e-sport.'
                    ],
                    [
                        'nom' => 'Petit',
                        'prenom' => 'Sophie',
                        'email' => 'sophie.petit@email.com',
                        'mot_de_passe' => password_hash('password123', PASSWORD_DEFAULT),
                        'statut' => 'actif',
                        'avatar' => 'https://i.pravatar.cc/150?img=4',
                        'bio' => 'Professeure de musique et chanteuse amateur. J\'adore partager ma passion.'
                    ]
                ];

                foreach ($membres as $membre) {
                    $query = "INSERT INTO membre (nom, prenom, email, mot_de_passe, statut, avatar, bio) 
                              VALUES (:nom, :prenom, :email, :mot_de_passe, :statut, :avatar, :bio)";
                    
                    $stmt = $db_with_dbname->prepare($query);
                    $stmt->execute($membre);
                }

                // === INSERTION DES COMMUNAUTÉS ===
                $communautes = [
                    [
                        'nom' => 'Développeurs Web France',
                        'categorie' => 'Technologie',
                        'description' => 'Communauté des développeurs web en France. Partagez vos projets, posez vos questions et collaborez avec d\'autres passionnés du développement web, frameworks et technologies modernes.',
                        'createur_id' => 1,
                        'avatar' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=300&h=300&fit=crop',
                        'visibilite' => 'publique',
                        'regles' => 'Respectez les autres membres. Pas de spam. Partagez du contenu pertinent au développement web. Les questions de débutants sont les bienvenues.'
                    ],
                    [
                        'nom' => 'Artistes Numériques',
                        'categorie' => 'Art',
                        'description' => 'Espace dédié aux artistes numériques. Partagez vos créations, participez à des défis artistiques et échangez sur les techniques de design, illustration et animation.',
                        'createur_id' => 2,
                        'avatar' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=300&h=300&fit=crop',
                        'visibilite' => 'publique',
                        'regles' => 'Respect du droit d\'auteur. Critique constructive uniquement. Partagez vos processus créatifs. Mentionnez les logiciels utilisés.'
                    ],
                    [
                        'nom' => 'Gamers Francophones',
                        'categorie' => 'Jeux',
                        'description' => 'Rejoignez la communauté des gamers francophones ! Discussions sur les jeux vidéo, organisation de parties, partage d\'astuces, reviews et actualités gaming.',
                        'createur_id' => 3,
                        'avatar' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=300&h=300&fit=crop',
                        'visibilite' => 'publique',
                        'regles' => 'Pas de spoilers sans avertissement. Respect des autres joueurs. Contenu approprié pour tous les âges. Pas de triche.'
                    ],
                    [
                        'nom' => 'Musiciens Amateurs',
                        'categorie' => 'Musique',
                        'description' => 'Communauté pour les musiciens de tous niveaux. Partagez vos compositions, demandez des conseils, trouvez des partenaires pour jouer ensemble et discutez instruments.',
                        'createur_id' => 4,
                        'avatar' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=300&h=300&fit=crop',
                        'visibilite' => 'publique',
                        'regles' => 'Partagez vos propres créations. Soyez encourageant avec les débutants. Pas de contenu protégé par le droit d\'auteur. Indiquez votre instrument principal.'
                    ],
                    [
                        'nom' => 'Photographes en Herbe',
                        'categorie' => 'Photographie',
                        'description' => 'Espace pour les passionnés de photographie. Partagez vos plus beaux clichés, recevez des conseils, participez à nos défis photo mensuels et échangez techniques.',
                        'createur_id' => 1,
                        'avatar' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=300&h=300&fit=crop',
                        'visibilite' => 'publique',
                        'regles' => 'Photos originales uniquement. Credit des modèles si nécessaire. Critique constructive. Partagez vos paramètres et équipements.'
                    ]
                ];

                foreach ($communautes as $communaute) {
                    $query = "INSERT INTO communaute (nom, categorie, description, createur_id, avatar, visibilite, regles) 
                              VALUES (:nom, :categorie, :description, :createur_id, :avatar, :visibilite, :regles)";
                    
                    $stmt = $db_with_dbname->prepare($query);
                    $stmt->execute($communaute);
                }

                echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; margin: 10px 0; border-radius: 5px;'>
                        <strong>✅ Données de démonstration insérées avec succès !</strong><br>
                        - 4 membres créés<br>
                        - 5 communautés actives<br>
                        - 6 publications avec interactions<br>
                        - Table membre_communaute créée<br>
                        Vous pouvez maintenant explorer l'application.
                      </div>";
            }

        } catch(PDOException $e) {
            throw new Exception("Erreur lors de l'insertion des données: " . $e->getMessage());
        }
    }
}
?>