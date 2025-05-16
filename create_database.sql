-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 16 mai 2025 à 22:47
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
-- Base de données : `eco_ride`
--

-- --------------------------------------------------------

--
-- Structure de la table `covoiturages`
--

CREATE TABLE `covoiturages` (
  `id` int(11) NOT NULL,
  `chauffeur_id` int(11) NOT NULL,
  `vehicule_id` int(11) NOT NULL,
  `depart` varchar(100) NOT NULL,
  `arrivee` varchar(100) NOT NULL,
  `date_heure_depart` datetime NOT NULL,
  `date_heure_arrivee` datetime NOT NULL,
  `prix` decimal(6,2) NOT NULL,
  `nb_places_restantes` int(11) NOT NULL,
  `etat` enum('à venir','en cours','terminé','annulé') NOT NULL DEFAULT 'à venir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `covoiturages`
--

INSERT INTO `covoiturages` (`id`, `chauffeur_id`, `vehicule_id`, `depart`, `arrivee`, `date_heure_depart`, `date_heure_arrivee`, `prix`, `nb_places_restantes`, `etat`) VALUES
(6, 28, 18, 'Cahors', 'Thézac', '2025-05-16 20:11:00', '2025-05-28 21:11:00', 25.00, 19, 'terminé'),
(7, 28, 20, 'Paris', 'Marseille', '2025-05-16 11:03:00', '2025-05-31 23:16:00', 50.00, 15, 'terminé'),
(13, 28, 18, 'Lyon', 'Nice', '2025-05-30 16:48:00', '2025-05-29 18:38:00', 25.00, 4, 'terminé'),
(14, 28, 20, 'Villeneuve-sur-Lot', 'Bordeaux', '2025-06-12 09:30:00', '2025-06-12 11:10:00', 10.00, 2, 'terminé'),
(16, 28, 18, 'Marseille', 'Nice', '2025-12-15 10:20:00', '2025-12-15 12:33:00', 15.00, 3, 'à venir'),
(17, 28, 20, 'Toulouse', 'Bordeaux', '2025-11-06 14:30:00', '2025-11-06 16:56:00', 20.00, 1, 'terminé'),
(18, 28, 20, 'Bergerac', 'Tarbes', '2025-11-06 09:40:00', '2025-11-06 12:55:00', 25.00, 4, 'à venir'),
(19, 34, 22, 'Paris', 'Cenac', '2025-12-24 10:00:00', '2025-12-24 17:18:00', 30.00, 3, 'à venir');

-- --------------------------------------------------------

--
-- Structure de la table `preferences`
--

CREATE TABLE `preferences` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `fumeur` tinyint(1) DEFAULT 0,
  `animaux` tinyint(1) DEFAULT 0,
  `discussions` tinyint(1) DEFAULT 1,
  `musique` tinyint(1) DEFAULT 1,
  `autre` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `preferences`
--

INSERT INTO `preferences` (`id`, `utilisateur_id`, `fumeur`, `animaux`, `discussions`, `musique`, `autre`) VALUES
(2, 28, 0, 1, 1, 1, 'Pas trop bavard'),
(3, 34, 0, 1, 1, 1, 'Je ne discute que de voiture');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `passager_id` int(11) NOT NULL,
  `covoiturage_id` int(11) NOT NULL,
  `statut` enum('confirmé','annulé','réalisé') DEFAULT 'confirmé'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `passager_id`, `covoiturage_id`, `statut`) VALUES
(28, 33, 17, 'réalisé'),
(29, 34, 17, 'réalisé');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('passager','chauffeur','passager-chauffeur','employe','admin') NOT NULL,
  `credit` int(11) DEFAULT 20,
  `photo_profil` varchar(255) DEFAULT NULL,
  `note` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `pseudo`, `email`, `mot_de_passe`, `role`, `credit`, `photo_profil`, `note`) VALUES
(28, 'José chauffeur', 'jose.chauffeur.eco@gmail.com', '$2y$10$IYLk4SjDQo6HnksLZHadxeIStFvGUjnYwC49jurhKxrqTPf05Kz/K', 'chauffeur', 93, '28.jpg', 4),
(29, 'Employe', 'employe@ecoride.fr', '$2y$10$bryOPqm9j.gwQBliLioUeeYlRldW9A/XQQMrkjQX0Z9uUsKgVaz7q', 'employe', 20, 'default.jpg', NULL),
(32, 'Admin', 'admin.ecoride@ecoride.fr', '$2y$10$abjVZvfmDjRf8PX3LBi.7e2P577YJ1DVoV3K6MJSbe1nvW9ziKJmu', 'admin', 20, 'default.jpg', NULL),
(33, 'José passager', 'jose.passager.eco@gmail.com', '$2y$10$midegyxaVn18x1QMGxB55e2LvwaYyTv9NZMVWWin0PyPDiHs7fOH.', 'passager', 180, NULL, NULL),
(34, 'José passager chauffeur', 'jose.passager.chauffeur.eco@gmail.com', '$2y$10$p1v5NphNR56mMkzpuiI3sevKJFIjE/HmjCutUean5SnxVNfFBsUZe', 'passager-chauffeur', 100, NULL, 3);

-- --------------------------------------------------------

--
-- Structure de la table `vehicules`
--

CREATE TABLE `vehicules` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `marque` varchar(50) NOT NULL,
  `modele` varchar(50) NOT NULL,
  `immatriculation` varchar(20) NOT NULL,
  `couleur` varchar(30) DEFAULT NULL,
  `energie` enum('essence','diesel','electrique','hybride') NOT NULL,
  `nb_places` int(11) NOT NULL,
  `date_immatriculation` date NOT NULL DEFAULT '2000-01-01'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vehicules`
--

INSERT INTO `vehicules` (`id`, `utilisateur_id`, `marque`, `modele`, `immatriculation`, `couleur`, `energie`, `nb_places`, `date_immatriculation`) VALUES
(18, 28, 'Toyota', 'Yaris', 'DT-789-YT', 'Noir', 'electrique', 3, '2025-04-03'),
(20, 28, 'Volvo', 'V40', 'AB-652-FQ', 'noir', 'essence', 3, '2024-11-06'),
(22, 34, 'Renault', '4L', 'JO-123-SE', 'Orange', 'essence', 4, '1961-12-25');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `covoiturages`
--
ALTER TABLE `covoiturages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chauffeur_id` (`chauffeur_id`),
  ADD KEY `vehicule_id` (`vehicule_id`);

--
-- Index pour la table `preferences`
--
ALTER TABLE `preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `passager_id` (`passager_id`),
  ADD KEY `covoiturage_id` (`covoiturage_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `immatriculation` (`immatriculation`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `covoiturages`
--
ALTER TABLE `covoiturages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `preferences`
--
ALTER TABLE `preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `vehicules`
--
ALTER TABLE `vehicules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `covoiturages`
--
ALTER TABLE `covoiturages`
  ADD CONSTRAINT `covoiturages_ibfk_1` FOREIGN KEY (`chauffeur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `covoiturages_ibfk_2` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `preferences`
--
ALTER TABLE `preferences`
  ADD CONSTRAINT `preferences_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`passager_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturages` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD CONSTRAINT `vehicules_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
