<?php
// seed_data.php - Script pour insérer des données de démonstration
session_start();
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->connect();

    echo "<h2>Insertion des données de démonstration...</h2>";

    // === INSERTION DES MEMBRES ===
    $membres = [
        [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@email.com',
            'mot_de_passe' => 'password123',
            'statut' => 'actif',
            'avatar' => 'https://i.pravatar.cc/150?img=1',
            'bio' => 'Développeur passionné par les nouvelles technologies.'
        ],
        [
            'nom' => 'Martin',
            'prenom' => 'Marie',
            'email' => 'marie.martin@email.com',
            'mot_de_passe' => 'password123',
            'statut' => 'actif',
            'avatar' => 'https://i.pravatar.cc/150?img=2',
            'bio' => 'Designer graphique et amatrice de photographie.'
        ],
        [
            'nom' => 'Bernard',
            'prenom' => 'Pierre',
            'email' => 'pierre.bernard@email.com',
            'mot_de_passe' => 'password123',
            'statut' => 'actif',
            'avatar' => 'https://i.pravatar.cc/150?img=3',
            'bio' => 'Étudiant en informatique et passionné de jeux vidéo.'
        ],
        [
            'nom' => 'Petit',
            'prenom' => 'Sophie',
            'email' => 'sophie.petit@email.com',
            'mot_de_passe' => 'password123',
            'statut' => 'actif',
            'avatar' => 'https://i.pravatar.cc/150?img=4',
            'bio' => 'Professeure de musique et chanteuse amateur.'
        ]
    ];

    foreach ($membres as $membre) {
        $query = "INSERT INTO membre (nom, prenom, email, mot_de_passe, statut, avatar, bio) 
                  VALUES (:nom, :prenom, :email, :mot_de_passe, :statut, :avatar, :bio)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nom', $membre['nom']);
        $stmt->bindParam(':prenom', $membre['prenom']);
        $stmt->bindParam(':email', $membre['email']);
        $stmt->bindParam(':mot_de_passe', password_hash($membre['mot_de_passe'], PASSWORD_DEFAULT));
        $stmt->bindParam(':statut', $membre['statut']);
        $stmt->bindParam(':avatar', $membre['avatar']);
        $stmt->bindParam(':bio', $membre['bio']);
        
        if($stmt->execute()) {
            echo "<p style='color: green;'>✓ Membre créé: " . $membre['prenom'] . " " . $membre['nom'] . "</p>";
        }
    }

    // === INSERTION DES COMMUNAUTÉS ===
    $communautes = [
        [
            'nom' => 'Développeurs Web France',
            'categorie' => 'Technologie',
            'description' => 'Communauté des développeurs web en France. Partagez vos projets, posez vos questions et collaborez avec d\'autres passionnés du développement web.',
            'createur_id' => 1,
            'avatar' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=150',
            'visibilite' => 'publique',
            'regles' => 'Respectez les autres membres. Pas de spam. Partagez du contenu pertinent au développement web.'
        ],
        [
            'nom' => 'Artistes Numériques',
            'categorie' => 'Art',
            'description' => 'Espace dédié aux artistes numériques. Partagez vos créations, participez à des défis artistiques et échangez sur les techniques de design.',
            'createur_id' => 2,
            'avatar' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=150',
            'visibilite' => 'publique',
            'regles' => 'Respect du droit d\'auteur. Critique constructive uniquement. Partagez vos processus créatifs.'
        ],
        [
            'nom' => 'Gamers Francophones',
            'categorie' => 'Jeux',
            'description' => 'Rejoignez la communauté des gamers francophones ! Discussions sur les jeux vidéo, organisation de parties, partage d\'astuces et de reviews.',
            'createur_id' => 3,
            'avatar' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=150',
            'visibilite' => 'publique',
            'regles' => 'Pas de spoilers. Respect des autres joueurs. Contenu approprié pour tous les âges.'
        ],
        [
            'nom' => 'Musiciens Amateurs',
            'categorie' => 'Musique',
            'description' => 'Communauté pour les musiciens de tous niveaux. Partagez vos compositions, demandez des conseils et trouvez des partenaires pour jouer ensemble.',
            'createur_id' => 4,
            'avatar' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=150',
            'visibilite' => 'publique',
            'regles' => 'Partagez vos propres créations. Soyez encourageant avec les débutants. Pas de contenu protégé par le droit d\'auteur.'
        ],
        [
            'nom' => 'Photographes en Herbe',
            'categorie' => 'Photographie',
            'description' => 'Espace pour les passionnés de photographie. Partagez vos plus beaux clichés, recevez des conseils et participez à nos défis photo mensuels.',
            'createur_id' => 1,
            'avatar' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=150',
            'visibilite' => 'publique',
            'regles' => 'Photos originales uniquement. Credit des modèles si nécessaire. Critique constructive.'
        ]
    ];

    foreach ($communautes as $communaute) {
        $query = "INSERT INTO communaute (nom, categorie, description, createur_id, avatar, visibilite, regles) 
                  VALUES (:nom, :categorie, :description, :createur_id, :avatar, :visibilite, :regles)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nom', $communaute['nom']);
        $stmt->bindParam(':categorie', $communaute['categorie']);
        $stmt->bindParam(':description', $communaute['description']);
        $stmt->bindParam(':createur_id', $communaute['createur_id']);
        $stmt->bindParam(':avatar', $communaute['avatar']);
        $stmt->bindParam(':visibilite', $communaute['visibilite']);
        $stmt->bindParam(':regles', $communaute['regles']);
        
        if($stmt->execute()) {
            echo "<p style='color: blue;'>✓ Communauté créée: " . $communaute['nom'] . "</p>";
        }
    }

    // === INSERTION DES PUBLICATIONS ===
    $publications = [
        [
            'communaute_id' => 1,
            'auteur_id' => 1,
            'contenu' => 'Salut à tous ! Je débute en React et je cherche des ressources pour apprendre. Avez-vous des tutoriels ou cours à recommander ? Merci d\'avance ! 🚀',
            'images' => '["https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=400"]',
            'likes' => 5,
            'commentaires' => 3
        ],
        [
            'communaute_id' => 1,
            'auteur_id' => 2,
            'contenu' => 'Je viens de terminer mon premier projet avec Node.js et Express. C\'était un vrai challenge mais très satisfaisant ! Qui d\'autre utilise cette stack ?',
            'images' => null,
            'likes' => 8,
            'commentaires' => 7
        ],
        [
            'communaute_id' => 2,
            'auteur_id' => 2,
            'contenu' => 'Nouvelle illustration numérique terminée ! Inspirée par l\'art cyberpunk. Qu\'en pensez-vous ? ✨',
            'images' => '["https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400", "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400"]',
            'likes' => 15,
            'commentaires' => 12
        ],
        [
            'communaute_id' => 3,
            'auteur_id' => 3,
            'contenu' => 'Qui a testé le nouveau Zelda ? Je suis complètement addict, les paysages sont magnifiques ! 🎮',
            'images' => '["https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400"]',
            'likes' => 23,
            'commentaires' => 18
        ],
        [
            'communaute_id' => 4,
            'auteur_id' => 4,
            'contenu' => 'Je viens de composer ma première chanson originale au piano. C\'est un grand pas pour moi après 2 ans d\'apprentissage ! 🎹',
            'images' => null,
            'likes' => 12,
            'commentaires' => 9
        ],
        [
            'communaute_id' => 5,
            'auteur_id' => 1,
            'contenu' => 'Photo prise ce matin au lever du soleil. La lumière était parfaite ! 📸',
            'images' => '["https://images.unsplash.com/photo-1501854140801-50d01698950b?w=400"]',
            'likes' => 31,
            'commentaires' => 14
        ]
    ];

    foreach ($publications as $publication) {
        $query = "INSERT INTO publication (communaute_id, auteur_id, contenu, images, likes, commentaires) 
                  VALUES (:communaute_id, :auteur_id, :contenu, :images, :likes, :commentaires)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':communaute_id', $publication['communaute_id']);
        $stmt->bindParam(':auteur_id', $publication['auteur_id']);
        $stmt->bindParam(':contenu', $publication['contenu']);
        $stmt->bindParam(':images', $publication['images']);
        $stmt->bindParam(':likes', $publication['likes']);
        $stmt->bindParam(':commentaires', $publication['commentaires']);
        
        if($stmt->execute()) {
            echo "<p style='color: orange;'>✓ Publication créée dans la communauté " . $publication['communaute_id'] . "</p>";
        }
    }

    echo "<h3 style='color: green;'>✅ Données de démonstration insérées avec succès !</h3>";
    echo "<p><a href='/projet/'>Voir les communautés</a> | <a href='/projet/admin/'>Accéder à l'administration</a></p>";

} catch(PDOException $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?>