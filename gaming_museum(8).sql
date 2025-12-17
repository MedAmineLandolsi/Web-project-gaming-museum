-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 17 déc. 2025 à 19:51
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
-- Base de données : `gaming_museum`
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
  `Auteur_ID` int(10) UNSIGNED NOT NULL,
  `Date_Publication` datetime NOT NULL,
  `Statut` enum('published','draft','pending') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`Article_ID`, `Titre`, `Contenu`, `Categorie`, `Auteur_ID`, `Date_Publication`, `Statut`, `created_at`, `updated_at`) VALUES
(1, 'Cyberpunk 2077 : Le Retour', 'Apres des debuts difficiles, Cyberpunk 2077 a su se reinventer...', 'review', 3, '2025-11-23 07:36:00', 'published', '2025-12-04 11:09:37', '2025-12-04 11:09:37'),
(3, 'Guide Debutant sur Elden Ring', 'Vous debutez sur Elden Ring ? Ce guide complet...', 'tutorial', 3, '2025-11-23 07:36:00', 'draft', '2025-12-04 11:09:37', '2025-12-04 11:09:37'),
(4, 'Nouvelle Xbox en Developpement', 'Microsoft travaillerait sur la prochaine generation Z', 'news', 3, '2025-11-23 07:36:00', 'published', '2025-12-04 11:09:37', '2025-12-04 11:09:37'),
(5, 'Cyberpunk 2077 : Le Retour', 'Apres des debuts difficiles, Cyberpunk 2077 a su se reinventer...', 'review', 3, '2025-11-23 08:41:19', 'published', '2025-12-04 11:09:37', '2025-12-04 11:09:37'),
(6, 'Les Tendances Gaming 2024', 'Cette annee sera marquee par lIA generative...', 'trends', 3, '2025-11-23 08:41:19', 'published', '2025-12-04 11:09:37', '2025-12-04 11:09:37'),
(7, 'Guide Debutant sur Elden Ring', 'Vous debutez sur Elden Ring ? Ce guide complet...', 'tutorial', 3, '2025-11-23 08:41:19', 'published', '2025-12-04 11:09:37', '2025-12-04 11:09:37'),
(8, 'Minecraft  Bien plus quun jeu un phénomène culturel', 'mincraft aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'review', 26, '2025-12-16 22:07:49', 'published', '2025-12-16 21:07:49', '2025-12-16 21:12:21');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `ID` int(11) NOT NULL,
  `Article_ID` int(11) NOT NULL,
  `User_ID` int(10) UNSIGNED NOT NULL,
  `Contenu` text NOT NULL,
  `Date_Commentaire` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commentaires`
--

INSERT INTO `commentaires` (`ID`, `Article_ID`, `User_ID`, `Contenu`, `Date_Commentaire`, `created_at`) VALUES
(1, 7, 6, 'bonjourrrrrrrrrrrrrrrr', '2025-11-23 08:45:25', '2025-12-04 11:09:37');

-- --------------------------------------------------------

--
-- Structure de la table `communaute`
--

CREATE TABLE `communaute` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `categorie` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `createur_id` int(11) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL,
  `visibilite` enum('publique','privee','cachee') DEFAULT 'publique',
  `regles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `communaute`
--

INSERT INTO `communaute` (`id`, `nom`, `categorie`, `description`, `createur_id`, `user_id`, `date_creation`, `avatar`, `visibilite`, `regles`) VALUES
(1, 'Dupont', 'Technologie', 'Communauté des développeurs web en France. Partagez vos projets, posez vos questions et collaborez avec d&#039;autres passionnés du développement web, frameworks et technologies modernes.', 1, 9, '2025-11-19 17:04:25', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=300&amp;h=300&amp;fit=crop', 'publique', 'Respectez les autres membres. Pas de spam. Partagez du contenu pertinent au développement web. Les questions de débutants sont les bienvenues.'),
(2, 'Artistes Numériques', 'Art', 'Espace dédié aux artistes numériques. Partagez vos créations, participez à des défis artistiques et échangez sur les techniques de design, illustration et animation.', 2, 10, '2025-11-19 17:04:25', 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=300&h=300&fit=crop', 'publique', 'Respect du droit d\'auteur. Critique constructive uniquement. Partagez vos processus créatifs. Mentionnez les logiciels utilisés.'),
(4, 'Musiciens Amateurs', 'Musique', 'Communauté pour les musiciens de tous niveaux. Partagez vos compositions, demandez des conseils, trouvez des partenaires pour jouer ensemble et discutez instruments.', 4, 8, '2025-11-19 17:04:25', 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=300&h=300&fit=crop', 'publique', 'Partagez vos propres créations. Soyez encourageant avec les débutants. Pas de contenu protégé par le droit d\'auteur. Indiquez votre instrument principal.'),
(5, 'Photographes en Herbe', 'Photographie', 'Espace pour les passionnés de photographie. Partagez vos plus beaux clichés, recevez des conseils, participez à nos défis photo mensuels et échangez techniques.', 1, 9, '2025-11-19 17:04:25', 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=300&h=300&fit=crop', 'publique', 'Photos originales uniquement. Credit des modèles si nécessaire. Critique constructive. Partagez vos paramètres et équipements.'),
(8, 'Dupon', 'Art', 'ihihrtirvkjb', 1, 9, '2025-11-20 09:05:25', 'https://images.unsplash.com/photo-1587174486073-ae5e5cff23aa?w=300&amp;h=300&amp;fit=crop', 'privee', 'jnn&#039;jjjnxdol&amp;'),
(9, 'Développeurs Web France', 'Technologie', 'Communauté des développeurs web en France. Partagez vos projets, posez vos questions et collaborez avec d\'autres passionnés du développement web.', 1, 9, '2025-12-06 16:24:35', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=150', 'publique', 'Respectez les autres membres. Pas de spam. Partagez du contenu pertinent au développement web.'),
(12, 'Landolsi', 'Sport', 'dfghjikokjhgfdxwswdxfcghjklkjhgfdsdfjgukilomp', 3, NULL, '2025-12-12 23:06:11', '', 'publique', ''),
(13, 'gaming', 'Jeux', 'xfcgvhbjnkljhgvfcdxswxfghkjlkm', 3, NULL, '2025-12-13 00:20:44', '', 'publique', '');

-- --------------------------------------------------------

--
-- Structure de la table `communaute_membres`
--

CREATE TABLE `communaute_membres` (
  `communaute_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE `evenement` (
  `id_evenement` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `jeu` varchar(255) NOT NULL,
  `places_max` int(11) NOT NULL,
  `prix` decimal(10,2) DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `Organisateur_ID` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evenement`
--

INSERT INTO `evenement` (`id_evenement`, `nom`, `description`, `date_debut`, `date_fin`, `lieu`, `jeu`, `places_max`, `prix`, `image`, `Organisateur_ID`, `created_at`, `updated_at`) VALUES
(3, 'Championnat Valorant', 'Description de l evenement : Valorant Event\r\n\r\nLe Valorant Event est une experience competitive intense concue pour tous les agents en quete de precision, de strategie et d adrenaline. Inspire de l univers tactique de Valorant, cet evenement rassemble des joueurs prets a s affronter dans des duels rapides, des missions d equipe et des scenarios sous haute tension.\r\n\r\nAu programme :\r\n- Matchs 5v5 dans des maps emblematiques\r\n- Defis de precision sur cibles et reflexes\r\n- Strats et rotations a elaborer en temps reel\r\n- Modes speciaux, comme Spike Rush ou Deathmatch\r\n- Leaderboard pour determiner l agent ultime', '2025-12-19 16:00:00', '2025-12-23 20:00:00', 'Stadium E-sport', 'Valorant et autres jeux', 40, 25.00, NULL, 3, '2025-11-16 12:04:33', '2025-12-03 18:13:17'),
(6, 'one person event', 'Le One Person Event est un challenge gaming totalement inedit ou un seul joueur devient le heros principal. Dans cet evenement solo ultra-immersif, chaque mission, chaque objectif et chaque interaction est concue pour mettre le joueur au centre de l action.\r\n\r\nQue ce soit pour affronter des vagues d ennemis, explorer un univers personnalise, accomplir des quetes chronometrees ou relever des defis e-sport en mode solo, le One Person Event promet une experience intense et taillee sur mesure.\r\n\r\nCet evenement offre un gameplay unique, une montee d adrenaline garantie et une aventure pensee pour tester les reflexes, la strategie et la creativite d un seul gamer.', '2025-12-24 16:52:00', '2025-12-28 16:52:00', 'marsa', 'fifa 2024', 1, 555.00, NULL, 3, '2025-11-17 15:52:19', '2025-12-03 18:11:03'),
(9, 'super hero', 'Description de l evenement : Super Hero Event\r\n\r\nLe Super Hero Event plonge les participants dans un univers epique ou chacun incarne un heros dote de pouvoirs extraordinaires. Cet evenement spectaculaire propose une serie de missions, de defis et de combats mettant a l epreuve la force, l agilite, le courage et l ingeniosite des joueurs.\r\n\r\nAu programme :\r\n- Missions heroiques pour sauver la ville\r\n- Defis de puissance et d agilite inspires des meilleurs super-heros\r\n- Scenarios immersifs : invasion, catastrophe, boss final\r\n- Competitions entre heros pour determiner le champion du jour\r\n- Epreuves en equipe pour unir les forces et vaincre les plus grandes menaces', '2025-12-05 09:51:00', '2025-12-07 09:51:00', 'marsa', 'spiderman ps5', 8, 60.00, NULL, 3, '2025-11-20 08:51:35', '2025-11-27 07:58:51'),
(10, 'Mario', 'Description de l evenement : Mario Event\r\n\r\nLe Mario Event plonge les joueurs directement dans l univers colore et plein d action du Royaume Champignon ! Inspire des celebres aventures de Mario, cet evenement propose une serie de defis fun et dynamiques ou les participants devront courir, esquiver, sauter et collecter un maximum d objets, comme de vrais heros Nintendo.\r\n\r\nAu programme :\r\n- Courses inspirees de Mario Kart\r\n- Defis de plateformes facon Super Mario Bros\r\n- Chasses aux pieces, aux etoiles et aux power-ups\r\n- Mini-jeux competitifs inspires de Mario Party\r\n- Un classement pour couronner le meilleur joueur du Royaume Champignon\r\n\r\nAmbiance fun, defis rapides et atmosphere 100% Mario : un evenement parfait pour les fans, les gamers et tous ceux qui veulent vivre une aventure pleine d energie et de nostalgie.', '2025-12-09 10:45:00', '2025-12-12 10:45:00', 'allemagne', 'Mario', 50, 99.00, NULL, 3, '2025-11-20 09:46:10', '2025-12-03 18:11:27'),
(11, 'sims', 'Description d un evenement Gaming - The Sims\r\n\r\nPlongez dans l univers creatif et delirant de The Sims lors d un evenement gaming unique !\r\nQue vous soyez constructeur passionne, styliste virtuel, scenariste de chaos ou simple fan du jeu, cet evenement est fait pour vous.\r\n\r\nAu programme :\r\n- Competitions de construction : creez la plus belle maison, le personnage le plus original ou le scenario le plus fou.\r\n- Defis en temps reel : challenges creatifs, speed-building, creation de familles improbables.\r\n- Concours et recompenses : cadeaux, points, trophees virtuels et classement des createurs.\r\n- Espace screenshots et storytelling : partagez vos meilleurs cliches et histoires sims.\r\n- Rencontres et communaute : echanges entre joueurs, astuces, mods, extensions, nouveautes.', '2025-12-07 23:11:00', '2025-12-11 23:11:00', 'marsa', 'the sims', 50, 0.00, NULL, 3, '2025-11-24 22:12:14', '2025-12-03 22:12:54'),
(12, 'fortnite', 'Tournois Battle Royale : solo, duo ou squad - survivez jusqu au top 1 !\r\nCompetitions creatives : maps personnalisees, parcours d entrainement, box fight et zone wars.\r\nDefis speciaux : no-build challenge, armes imposees, rotations rapides.\r\nRecompenses et cadeaux : skins, V-bucks, goodies et trophees pour les meilleurs joueurs.\r\nDiffusion en direct : ecrans geants, commentaires, ambiance e-sport.\r\nCommunaute et rencontres : decouvrez d autres joueurs, partagez vos techniques, strategies et astuces.\r\n\r\nDans une atmosphere competitive mais conviviale, cet evenement vous plonge au coeur de l univers Fortnite avec du rythme, du suspense et surtout beaucoup de plaisir.\r\nPreparez vos constructions, affutez votre visee et rejoignez-nous pour un evenement Fortnite explosif !', '2025-12-25 16:39:00', '2025-12-31 16:39:00', 'Stadium E-sport', 'forntite', 50, 99.00, NULL, 3, '2025-11-25 15:40:08', '2025-12-03 18:12:05');

-- --------------------------------------------------------

--
-- Structure de la table `interactions`
--

CREATE TABLE `interactions` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(11) NOT NULL,
  `interaction_type` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `interactions`
--

INSERT INTO `interactions` (`id`, `user_id`, `item_id`, `interaction_type`, `created_at`) VALUES
(16, 5, 1, 'like', '2025-12-09 00:20:51'),
(17, 5, 2, 'comment', '2025-12-08 23:20:51'),
(18, 5, 1, 'share', '2025-12-08 22:20:51'),
(19, 6, 3, 'like', '2025-12-08 21:20:51'),
(20, 5, 4, 'view', '2025-12-08 20:20:51'),
(21, 9, 14, 'comment', '2025-12-09 00:20:51'),
(22, 3, 25, 'like', '2025-12-09 00:20:51'),
(23, 8, 56, 'comment', '2025-12-09 00:20:51'),
(24, 7, 86, 'save', '2025-12-09 00:20:51'),
(25, 5, 27, 'save', '2025-12-09 00:20:51'),
(26, 10, 91, 'view', '2025-12-09 00:20:51'),
(27, 11, 1, 'like', '2025-12-09 00:20:51'),
(28, 6, 18, 'like', '2025-12-09 00:20:51'),
(36, 12, 1, 'like', '2025-12-09 00:20:51'),
(37, 12, 2, 'comment', '2025-12-08 23:20:51'),
(38, 13, 1, 'share', '2025-12-08 22:20:51'),
(39, 14, 3, 'like', '2025-12-08 21:20:51'),
(40, 12, 4, 'view', '2025-12-08 20:20:51'),
(41, 3, 1, 'like', '2025-12-09 00:20:52'),
(42, 3, 2, 'comment', '2025-12-08 23:20:52'),
(43, 5, 1, 'share', '2025-12-08 22:20:52'),
(44, 7, 3, 'like', '2025-12-08 21:20:52'),
(45, 3, 4, 'view', '2025-12-08 20:20:52'),
(46, 3, 1, 'like', '2025-12-09 00:20:52'),
(47, 3, 2, 'comment', '2025-12-08 23:20:52'),
(48, 5, 1, 'share', '2025-12-08 22:20:52'),
(49, 6, 3, 'like', '2025-12-08 21:20:52'),
(50, 3, 4, 'view', '2025-12-08 20:20:52'),
(51, 3, 1, 'like', '2025-12-09 00:22:02'),
(52, 3, 2, 'comment', '2025-12-08 23:22:02'),
(53, 5, 1, 'share', '2025-12-08 22:22:02'),
(54, 7, 3, 'like', '2025-12-08 21:22:02');

-- --------------------------------------------------------

--
-- Structure de la table `participation`
--

CREATE TABLE `participation` (
  `id_participation` int(11) NOT NULL,
  `id_evenement` int(11) NOT NULL,
  `User_ID` int(10) UNSIGNED NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut` enum('confirmed','cancelled','pending') DEFAULT 'confirmed',
  `nom_participant` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `telephone` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `participation`
--

INSERT INTO `participation` (`id_participation`, `id_evenement`, `User_ID`, `date_inscription`, `statut`, `nom_participant`, `email`, `telephone`) VALUES
(20, 10, 6, '2025-12-03 18:15:00', 'confirmed', '', '', ''),
(21, 6, 6, '2025-12-03 18:16:03', 'confirmed', '', '', ''),
(23, 11, 6, '2025-12-03 22:03:48', 'confirmed', '', '', ''),
(27, 9, 5, '2025-12-04 10:18:50', 'confirmed', '', '', ''),
(28, 9, 3, '2025-12-16 21:26:54', 'confirmed', 'Imprina', 'aminelandolsi5000@gmail.com', '21654545753');

-- --------------------------------------------------------

--
-- Structure de la table `publication`
--

CREATE TABLE `publication` (
  `id` int(11) NOT NULL,
  `communaute_id` int(11) DEFAULT NULL,
  `auteur_id` int(11) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `contenu` text NOT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `likes` int(11) DEFAULT 0,
  `commentaires` int(11) DEFAULT 0,
  `date_publication` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `publication`
--

INSERT INTO `publication` (`id`, `communaute_id`, `auteur_id`, `user_id`, `contenu`, `images`, `likes`, `commentaires`, `date_publication`, `date_modification`) VALUES
(1, 1, 1, 9, 'Salut à tous ! Je débute en React et je cherche des ressources pour apprendre. Avez-vous des tutoriels ou cours à recommander ? J&#039;aimerais particulièrement me concentrer sur les hooks et le state management. Merci d&#039;avance ! ?', NULL, 12, 8, '2025-11-19 17:04:25', '2025-12-08 01:14:12'),
(2, 1, 2, 10, 'Je viens de terminer mon premier projet full-stack avec Node.js, Express et React. C\'était un vrai challenge mais très satisfaisant ! Le projet est un gestionnaire de tâches avec authentification. Qui d\'autre utilise cette stack ? Des conseils pour l\'optimisation ?', NULL, 24, 15, '2025-11-19 17:04:25', '2025-12-08 01:14:12'),
(3, 2, 2, 10, 'Nouvelle illustration numérique terminée ! Inspirée par l\'art cyberpunk et les néons de Tokyo. J\'ai utilisé Procreate et Photoshop. J\'ai passé environ 15 heures sur ce projet. Qu\'en pensez-vous ? Des retours sur les couleurs ? ✨', '[\"https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=500\", \"https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=500\"]', 45, 23, '2025-11-19 17:04:25', '2025-12-08 01:14:12'),
(5, 4, 4, 8, 'Je viens de composer ma première chanson originale au piano après 2 ans d\'apprentissage ! C\'est une ballade mélancolique en do mineur. J\'hésite à ajouter des paroles. Des volontaires pour collaborer ? ??', NULL, 34, 18, '2025-11-19 17:04:25', '2025-12-08 01:14:12'),
(6, 5, 1, 9, 'Photo prise ce matin au lever du soleil dans les Alpes. La lumière était parfaite ! J\'ai utilisé un Sony A7III avec objectif 24-70mm, f/8, 1/125s, ISO 100. Le froid était intense mais le résultat en valait la peine ! ?❄️', '[\"https://images.unsplash.com/photo-1501854140801-50d01698950b?w=500\"]', 89, 31, '2025-11-19 17:04:25', '2025-12-08 01:14:12'),
(7, 2, 1, 9, 'vj bhklonkjo', NULL, 0, 0, '2025-11-19 22:26:02', '2025-12-08 01:14:12'),
(8, 1, 1, 9, 'Cette publication a des images de test !', '[\"https://picsum.photos/300/300?random=4\", \"https://picsum.photos/300/300?random=5\"]', 5, 2, '2025-11-19 22:42:54', '2025-12-08 01:14:12'),
(10, 8, 1, 9, 'gvk:bk,kbvhcfwfsw', NULL, 0, 0, '2025-11-20 12:23:39', '2025-12-08 01:14:12'),
(12, 8, 1, 9, 'gcjjjjjjjxdfgfhgjhkjlh;cx', NULL, 0, 0, '2025-11-20 15:05:30', '2025-12-08 01:14:12'),
(14, 4, 1, 9, 'j bkl:njbvgcfvjkhjl', NULL, 0, 0, '2025-11-23 21:54:01', '2025-12-08 01:14:12'),
(15, 8, 1, 9, 'hbkjhenz;lm,kedhbr', NULL, 0, 0, '2025-11-23 21:58:34', '2025-12-08 01:14:12'),
(20, 12, 3, 3, 'Salut à tous ! J&#039;aimerais partager avec vous mes réflexions sur &#039;jeux&#039;. C&#039;est un sujet qui me passionne vraiment et je serais ravi d&#039;échanger avec vous à ce sujet. Qu&#039;en pensez-vous ?', NULL, 0, 0, '2025-12-13 11:23:11', '2025-12-13 11:23:11'),
(21, 13, 3, 3, 'tfyguijouyktreyyrutyuiohyf-dszerytukil', NULL, 0, 0, '2025-12-13 21:23:47', '2025-12-13 21:26:01'),
(22, 2, 3, 3, 'Salut la communauté !\r\n\r\nAujourd&#039;hui, on parle de League of Legends ! Ce MOBA légendaire continue de faire vibrer des millions de joueurs à travers le monde, et ce n&#039;est pas pour rien. Entre les stratégies complexes, les champions emblématiques avec des kits uniques, et l&#039;adrénaline des parties serrées, LoL offre une expérience de jeu sans cesse renouvelée. Que vous soyez un vétéran aguerri qui connaît chaque recoin de la Faille de l&#039;invocateur par cœur, ou un nouveau venu qui découvre encore tous les secrets du jeu, il y a toujours quelque chose à apprendre et à maîtriser.\r\n\r\nQuelle est votre lane préférée et quel champion incarnez-vous le plus souvent pour dominer vos adversaires ?', NULL, 0, 0, '2025-12-16 21:27:53', '2025-12-16 21:27:53');

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
--

CREATE TABLE `reclamation` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `nomClient` varchar(255) NOT NULL,
  `emailClient` varchar(255) NOT NULL,
  `typeReclamation` varchar(100) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `statut` varchar(50) DEFAULT 'en_attente',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reclamation`
--

INSERT INTO `reclamation` (`id`, `user_id`, `nomClient`, `emailClient`, `typeReclamation`, `titre`, `description`, `statut`, `date_creation`) VALUES
(2, NULL, 'Aymen', 'aymen3@gmail.com', 'technique', 'retard de livraison', 'mauvaise livraison ', 'repondu', '2025-11-27 20:43:07'),
(3, NULL, 'missaoui ines', 'ines@esprit.tn', 'retard de livraison', 'retard de livraison', 'il y&amp;amp;#039;a un retard de la livraison de ma commande', 'en_attente', '2025-12-01 13:43:34'),
(4, NULL, 'Nedra', 'nedraouhibi3@gmail.com', 'problème de commande', 'retard de livraison', 'livraison mauvaise', 'en_attente', '2025-12-03 18:10:14'),
(5, NULL, 'ilef', 'ilef@gmail.com', 'problème de commande', 'service client', 'client pas interessée', 'en_attente', '2025-12-11 10:16:38');

-- --------------------------------------------------------

--
-- Structure de la table `reponse`
--

CREATE TABLE `reponse` (
  `id` int(11) NOT NULL,
  `reclamationId` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `message` text NOT NULL,
  `adminName` varchar(255) DEFAULT 'Administrateur',
  `date_reponse` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reponse`
--

INSERT INTO `reponse` (`id`, `reclamationId`, `user_id`, `message`, `adminName`, `date_reponse`) VALUES
(1, 2, NULL, 'hbhhjbghbh', 'Administrateur', '2025-12-06 22:10:32');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `profile_picture_url` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone_number`, `date_of_birth`, `profile_picture_url`, `role`, `status`, `reset_token_hash`, `reset_token_expires_at`) VALUES
(3, 'Imprina', 'aminelandolsi5000@gmail.com', '$2y$10$vmNICiXtnjrNd3JpLnMJQeDednR3VfGBNDKAvk6m54skB9cKFJhMi', 'Amine', 'Landolsi', '52935148', '2005-11-08', 'profile_3_1764075179.jpg', 'admin', 'active', 'fcbe8abe37daebd9f8b4952799f5febcdbf5c89731a74587f78285b7d2cd3697', '2025-12-04 10:50:23'),
(5, 'ghada', 'ghada.benkhalifa@esprit.tn', '$2y$10$yI393GSvyWekhNOg5ZGzdeQOv0s5i4XQT9tMecm0wx7sRXwDJ/7s6', '', '', '', '1995-12-05', '', 'user', 'active', NULL, NULL),
(6, 'selim', 'Selim.ASCHI@esprit.tn', '$2y$10$p.q/KZ18k8hqCoz1.ES9CuQpGoRb1SxwwNVtmx/OqN.SHh9PIDqSG', '', '', '', '2005-08-26', '', 'user', 'active', NULL, NULL),
(7, 'nour.touhemi', 'nourtouhemi9@gmail.com', '$2y$10$GgY718lKSuOM3/6qu8PepeCzZAGJ30m/NDQp205mep.REqhx2eaV6', 'nour', 'touhemi', '', '0000-00-00', '', 'user', 'active', NULL, NULL),
(8, 'sophie.petit', 'sophie.petit@email.com', '$2y$10$E8G9s08oCpWnbRv6dQ2owO6T2p8TNJ1MMu3EqX6WM0jOZrpQRMVhG', 'Sophie', 'Petit', '', '0000-00-00', '', 'user', 'active', NULL, NULL),
(9, 'jean.dupont', 'jean.dupont@email.com', '$2y$10$VWrMN0Nx2rLPVbzg.awiveO9l1q.BHGk0yBbINNeqmMolLRXwWOVy', 'Jean', 'Dupont', '', '0000-00-00', '', 'user', 'active', NULL, NULL),
(10, 'marie.martin', 'marie.martin@email.com', '$2y$10$PJshcjezawqLyBRYYYUDeeda6h6rN.4IgJXtzUVIb0L.pUfNzRNsi', 'Marie', 'Martin', '', '0000-00-00', '', 'user', 'active', NULL, NULL),
(11, 'pierre.bernard', 'pierre.bernard@email.com', '$2y$10$UgPQ/yEq9EgZV23KQqRnh.JYgrd/05T91LNmCzEgp.NNTVWNFYKce', 'Pierre', 'Bernard', '', '0000-00-00', '', 'user', 'active', NULL, NULL),
(12, 'testuser1', 'test1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jean', 'Dupont', '', '0000-00-00', '', 'user', 'active', NULL, NULL),
(13, 'testuser2', 'test2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marie', 'Martin', '', '0000-00-00', '', 'user', 'active', NULL, NULL),
(14, 'testuser3', 'test3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pierre', 'Bernard', '', '0000-00-00', '', 'user', 'active', NULL, NULL),
(16, 'prototype_user', 'prototype@example.com', '482c811da5d5b4bc6d497ffa98491e38', '', '', '', '0000-00-00', '', 'user', 'active', NULL, NULL),
(17, 'testuser1765424961', 'test1765424961@example.local', '$2y$10$63UhQdBUYgDnsjCL2EHAPepk8k.fSU9Y.3PuzsOv/ngBse8uZLayG', 'Test', 'User', '', '1990-01-01', '', 'user', 'active', NULL, NULL),
(18, 'testuser1765425015', 'test1765425015@example.local', '$2y$10$H2/nYdh181U5Zf7EYkpSQenZWpsCPWHqhZWC/MjAveuNQxzcYbrvS', 'Test', 'User', '', '1990-01-01', '', 'user', 'active', NULL, NULL),
(19, 'testuser1765429708,4758', 'test1765429708,4758@example.local', '$2y$10$a28wAmQ.4c9xN89iJJ1X.usg38Ffu2.fQ9ViVH35Ole40JWAyh5P6', 'Test', 'User', '', '1990-01-01', '', 'user', 'active', NULL, NULL),
(20, 'testuser1765429720,27741', 'test1765429720,27741@example.local', '$2y$10$cSVbzAC/ubN136l5Fg66g.2QltxRMtPJThO8LEKMzwsREDVA6f5Dq', 'Test', 'User', '', '1990-01-01', '', 'user', 'active', NULL, NULL),
(21, 'testuser1765429793,82733', 'test1765429793,82733@example.local', '$2y$10$C/8R8FNiylzfsbuqWNzzL.ch5Wd359kM3FFGu/Gh.hzlNvdD7/CGC', 'Test', 'User', '', '1990-01-01', '', 'user', 'active', NULL, NULL),
(22, 'testuser1765429804,10996', 'test1765429804,10996@example.local', '$2y$10$VvjyoE7c.I0Kv1CVmRFxZOzft.UvzQR2W5VYMw0XgXQqlLcmArftC', 'Test', 'User', '', '1990-01-01', '', 'user', 'active', NULL, NULL),
(23, 'testuser1765429971,20723', 'test1765429971,20723@example.local', '$2y$10$ta/ufxGkHMYUCtsf1E6UFuPATsttxa43FPKTvgettKDB7CHisHs62', 'Test', 'User', '', '1990-01-01', '', 'user', 'active', NULL, NULL),
(24, 'Nour_touhemi1', 'nour1touhemi@gmail.com', '$2y$10$w1BVfST6pVV9/.mhDIEequ02ELGDIFurbcB0xvzSORW04YbycH2JG', '', '', '', '2005-07-04', '', 'user', 'active', NULL, NULL),
(25, 'nour_touhemi', 'nour15touhemi@gmail.com', '$2y$10$DvztXTjlYVeFtzeEnJgzXuGrogY6rMDu4uVYSNdj06s78PxHIiVPS', '', '', '', '2005-07-04', '', 'user', 'active', NULL, NULL),
(26, 'aminaaa', 'amina.hanina@gmail.com', '$2y$10$l6Ta5/lC7l2IjjeYNm8Udu.Ar9TCbqbF0YQi4bDkzCnJx9K.s3Umq', '', '', '', '2006-12-15', '', 'user', 'active', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`Article_ID`),
  ADD KEY `Auteur_ID` (`Auteur_ID`),
  ADD KEY `idx_statut` (`Statut`),
  ADD KEY `idx_date_publication` (`Date_Publication`);

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Article_ID` (`Article_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Index pour la table `communaute`
--
ALTER TABLE `communaute`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD KEY `createur_id` (`createur_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `communaute_membres`
--
ALTER TABLE `communaute_membres`
  ADD PRIMARY KEY (`communaute_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id_evenement`),
  ADD KEY `Organisateur_ID` (`Organisateur_ID`),
  ADD KEY `idx_date_debut` (`date_debut`),
  ADD KEY `idx_jeu` (`jeu`);

--
-- Index pour la table `interactions`
--
ALTER TABLE `interactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_interactions_user_id` (`user_id`),
  ADD KEY `idx_interactions_item_id` (`item_id`),
  ADD KEY `idx_interactions_created` (`created_at`);

--
-- Index pour la table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id_participation`),
  ADD UNIQUE KEY `unique_participation` (`id_evenement`,`User_ID`),
  ADD KEY `id_evenement` (`id_evenement`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Index pour la table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`id`),
  ADD KEY `communaute_id` (`communaute_id`),
  ADD KEY `auteur_id` (`auteur_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reclamation_user_id` (`user_id`);

--
-- Index pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reclamation` (`reclamationId`),
  ADD KEY `idx_reponse_user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `Article_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `communaute`
--
ALTER TABLE `communaute`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `interactions`
--
ALTER TABLE `interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT pour la table `participation`
--
ALTER TABLE `participation`
  MODIFY `id_participation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `publication`
--
ALTER TABLE `publication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `reponse`
--
ALTER TABLE `reponse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`Auteur_ID`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`Article_ID`) REFERENCES `articles` (`Article_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `commentaires_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `communaute`
--
ALTER TABLE `communaute`
  ADD CONSTRAINT `communaute_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `communaute_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `communaute_membres`
--
ALTER TABLE `communaute_membres`
  ADD CONSTRAINT `fk_communaute_membres_communaute` FOREIGN KEY (`communaute_id`) REFERENCES `communaute` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`Organisateur_ID`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `interactions`
--
ALTER TABLE `interactions`
  ADD CONSTRAINT `fk_interactions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE,
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `publication`
--
ALTER TABLE `publication`
  ADD CONSTRAINT `publication_ibfk_1` FOREIGN KEY (`communaute_id`) REFERENCES `communaute` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `fk_reclamation_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `fk_reponse_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `reponse_ibfk_1` FOREIGN KEY (`reclamationId`) REFERENCES `reclamation` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
