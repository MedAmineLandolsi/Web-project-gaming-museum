-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 23 nov. 2025 à 08:53
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `blog_gaming0`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `Article_ID` int(11) NOT NULL,
  `Titre` varchar(255) NOT NULL,
  `Contenu` text NOT NULL,
  `Categorie` varchar(50) NOT NULL,
  `Auteur_ID` int(11) NOT NULL,
  `Date_Publication` datetime NOT NULL,
  `Statut` enum('published','draft','pending') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`Article_ID`, `Titre`, `Contenu`, `Categorie`, `Auteur_ID`, `Date_Publication`, `Statut`) VALUES
(1, 'Cyberpunk 2077 : Le Retour', 'Après des débuts difficiles, Cyberpunk 2077 a su se réinventer...', 'review', 1, '2025-11-23 07:36:00', 'published'),
(3, 'Guide Débutant sur Elden Ring', 'Vous débutez sur Elden Ring ? Ce guide complet...cggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg', 'tutorial', 1, '2025-11-23 07:36:00', 'draft'),
(4, 'Nouvelle Xbox en Développement', 'Microsoft travaillerait sur la prochaine génération Z', 'news', 1, '2025-11-23 07:36:00', 'published'),
(5, 'Cyberpunk 2077 : Le Retour', 'Après des débuts difficiles, Cyberpunk 2077 a su se réinventer...', 'review', 1, '2025-11-23 08:41:19', 'published'),
(6, 'Les Tendances Gaming 2024', 'Cette année sera marquée par lIA générative...', 'trends', 1, '2025-11-23 08:41:19', 'published'),
(7, 'Guide Débutant sur Elden Ring', 'Vous débutez sur Elden Ring ? Ce guide complet...', 'tutorial', 1, '2025-11-23 08:41:19', 'published');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `ID` int(11) NOT NULL,
  `Article_ID` int(11) NOT NULL,
  `Auteur` varchar(100) NOT NULL,
  `Contenu` text NOT NULL,
  `Date_Commentaire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commentaires`
--

INSERT INTO `commentaires` (`ID`, `Article_ID`, `Auteur`, `Contenu`, `Date_Commentaire`) VALUES
(1, 7, 'bolbol', 'bonjourrrrrrrrrrrrrrrr', '2025-11-23 08:45:25');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`Article_ID`);

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Article_ID` (`Article_ID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `Article_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`Article_ID`) REFERENCES `articles` (`Article_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
