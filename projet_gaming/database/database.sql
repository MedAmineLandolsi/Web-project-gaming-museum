CREATE DATABASE gaming_events;
USE gaming_events;

CREATE TABLE evenement (
    id_evenement INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    lieu VARCHAR(255) NOT NULL,
    jeu VARCHAR(255) NOT NULL,
    places_max INT NOT NULL,
    prix DECIMAL(10,2) DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE participation (
    id_participation INT AUTO_INCREMENT PRIMARY KEY,
    id_evenement INT NOT NULL,
    nom_participant VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_evenement) REFERENCES evenement(id_evenement) ON DELETE CASCADE
);

-- Données de test
INSERT INTO evenement (nom, description, date_debut, date_fin, lieu, jeu, places_max, prix) VALUES
('Tournoi Fortnite', 'Tournoi competitive Fortnite avec cash prize', '2024-02-15 14:00:00', '2024-02-15 18:00:00', 'Arena Game', 'Fortnite', 50, 20.00),
('LAN Party CS2', 'Session gaming CS2 en réseau local', '2024-02-20 10:00:00', '2024-02-20 22:00:00', 'Cyber Café NextGen', 'Counter-Strike 2', 30, 15.00),
('Championnat Valorant', 'Compétition officielle Valorant', '2024-02-25 16:00:00', '2024-02-25 20:00:00', 'Stadium E-sport', 'Valorant', 40, 25.00);