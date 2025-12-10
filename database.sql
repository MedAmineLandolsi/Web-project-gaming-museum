-- Script de création de la base de données Gaming Support
-- Exécutez ce script dans phpMyAdmin ou via la ligne de commande MySQL

-- Créer la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS `gaming_support` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Utiliser la base de données
USE `gaming_support`;

-- Table des réclamations
CREATE TABLE IF NOT EXISTS `reclamation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomClient` varchar(255) NOT NULL,
  `emailClient` varchar(255) NOT NULL,
  `typeReclamation` varchar(100) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `statut` varchar(50) DEFAULT 'en_attente',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_date_creation` (`date_creation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des réponses
CREATE TABLE IF NOT EXISTS `reponse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reclamationId` int(11) NOT NULL,
  `message` text NOT NULL,
  `adminName` varchar(255) DEFAULT 'Administrateur',
  `date_reponse` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_reclamation` (`reclamationId`),
  KEY `idx_reclamationId` (`reclamationId`),
  CONSTRAINT `fk_reponse_reclamation` FOREIGN KEY (`reclamationId`) REFERENCES `reclamation` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer quelques données de test (optionnel)
-- INSERT INTO `reclamation` (`nomClient`, `emailClient`, `typeReclamation`, `titre`, `description`, `statut`) VALUES
-- ('Jean Dupont', 'jean.dupont@example.com', 'problème de commande', 'Commande non reçue', 'Ma commande n\'est toujours pas arrivée après 2 semaines.', 'en_attente'),
-- ('Marie Martin', 'marie.martin@example.com', 'produit défectueux', 'Produit cassé', 'Le produit est arrivé cassé.', 'repondu');

