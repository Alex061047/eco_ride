
-- Base de données : eco_ride

CREATE DATABASE IF NOT EXISTS `eco_ride`;
USE `eco_ride`;


-- Table utilisateurs
CREATE TABLE `utilisateurs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pseudo` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `role` ENUM('passager','chauffeur','passager-chauffeur','employe','admin') NOT NULL,
  `credit` INT DEFAULT 20,
  `photo_profil` VARCHAR(255) DEFAULT NULL,
  `note` TINYINT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table vehicules
CREATE TABLE `vehicules` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `utilisateur_id` INT NOT NULL,
  `marque` VARCHAR(50) NOT NULL,
  `modele` VARCHAR(50) NOT NULL,
  `immatriculation` VARCHAR(20) NOT NULL UNIQUE,
  `couleur` VARCHAR(30) DEFAULT NULL,
  `energie` ENUM('essence','diesel','electrique','hybride') NOT NULL,
  `nb_places` INT NOT NULL,
  `date_immatriculation` DATE NOT NULL DEFAULT '2000-01-01',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table covoiturages
CREATE TABLE `covoiturages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `chauffeur_id` INT NOT NULL,
  `vehicule_id` INT NOT NULL,
  `depart` VARCHAR(100) NOT NULL,
  `arrivee` VARCHAR(100) NOT NULL,
  `date_heure_depart` DATETIME NOT NULL,
  `date_heure_arrivee` DATETIME NOT NULL,
  `prix` DECIMAL(6,2) NOT NULL,
  `nb_places_restantes` INT NOT NULL,
  `etat` ENUM('à venir','en cours','terminé','annulé') NOT NULL DEFAULT 'à venir',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`chauffeur_id`) REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table reservations
CREATE TABLE `reservations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `passager_id` INT NOT NULL,
  `covoiturage_id` INT NOT NULL,
  `statut` ENUM('confirmé','annulé','réalisé') DEFAULT 'confirmé',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`passager_id`) REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table preferences
CREATE TABLE `preferences` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `utilisateur_id` INT NOT NULL,
  `fumeur` TINYINT(1) DEFAULT 0,
  `animaux` TINYINT(1) DEFAULT 0,
  `discussions` TINYINT(1) DEFAULT 1,
  `musique` TINYINT(1) DEFAULT 1,
  `autre` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Données d’exemple
INSERT INTO `utilisateurs` (`pseudo`, `email`, `mot_de_passe`, `role`, `credit`)
VALUES
('José chauffeur', 'jose.chauffeur.eco@gmail.com', 'motdepassehashé', 'chauffeur', 93),
('José passager', 'jose.passager.eco@gmail.com', 'motdepassehashé', 'passager', 180),
('José passager chauffeur', 'jose.passager.chauffeur.eco@gmail.com', 'motdepassehashé', 'passager-chauffeur', 100),
('Employé', 'employe@ecoride.fr', 'motdepassehashé', 'employe', 20),
('Admin', 'admin.ecoride@ecoride.fr', 'motdepassehashé', 'admin', 9999);
