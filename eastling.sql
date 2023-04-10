-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 07 oct. 2021 à 21:17
-- Version du serveur :  5.7.31
-- Version de PHP : 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `eastling`
--

-- --------------------------------------------------------

--
-- Structure de la table `annotations`
--

DROP TABLE IF EXISTS `annotations`;
CREATE TABLE IF NOT EXISTS `annotations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_id` int(10) UNSIGNED NOT NULL,
  `type` enum('T','S','W','M') COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` int(11) NOT NULL,
  `image_id` int(10) UNSIGNED DEFAULT NULL,
  `areaCoords` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audioStart` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audioEnd` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `annotations_document_id_foreign` (`document_id`),
  KEY `annotations_parent_id_foreign` (`parent_id`),
  KEY `annotations_image_id_foreign` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lang` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('TEXT','WORDLIST') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `recording_date` timestamp NULL DEFAULT NULL,
  `recording_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `available_kindOf` varchar(255) DEFAULT NULL,
  `available_lang` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `document_contributors`
--

DROP TABLE IF EXISTS `document_contributors`;
CREATE TABLE IF NOT EXISTS `document_contributors` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('researcher','speaker','annotator') COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_contributors_document_id_foreign` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `document_titles`
--

DROP TABLE IF EXISTS `document_titles`;
CREATE TABLE IF NOT EXISTS `document_titles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lang` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_titles_document_id_foreign` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `forms`
--

DROP TABLE IF EXISTS `forms`;
CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `annotation_id` int(10) UNSIGNED NOT NULL,
  `kindOf` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forms_annotation_id_foreign` (`annotation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8042 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rank` int(11) NOT NULL,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longblob NOT NULL,
  `document_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `images_document_id_foreign` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(51, '2021_05_26_084300_create_users_table', 1),
(52, '2021_05_26_090858_create_documents_table', 1),
(53, '2021_06_01_073713_create_document_titles_table', 1),
(54, '2021_07_12_161247_create_images_table', 1),
(55, '2021_07_16_092356_create_recordings_table', 1),
(56, '2021_07_25_144300_create_annotations_table', 1),
(57, '2021_07_26_083559_create_forms_table', 1),
(58, '2021_07_26_084103_create_translations_table', 1),
(59, '2021_07_26_084412_create_notes_table', 1),
(60, '2021_07_26_084452_create_document_contributors_table', 1);

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `notable_id` int(11) NOT NULL,
  `notable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kindOf` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lang` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recordings`
--

DROP TABLE IF EXISTS `recordings`;
CREATE TABLE IF NOT EXISTS `recordings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('AUDIO','VIDEO') CHARACTER SET utf8 NOT NULL,
  `content` longblob NOT NULL,
  `document_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recordings_document_id_foreign` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `translations`
--

DROP TABLE IF EXISTS `translations`;
CREATE TABLE IF NOT EXISTS `translations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `annotation_id` int(10) UNSIGNED NOT NULL,
  `lang` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `translations_annotation_id_foreign` (`annotation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `organization`, `last_login_date`, `created_at`, `updated_at`) VALUES
(1, 'test', 'test@test.com', '$2y$10$oOAPiWJIG4.cBmPIlt0kEOU/JBusVY3saJgAth2PYlsgr8Af6.mOu', 'test', '2021-10-07 21:16:10', '2021-10-01 12:12:35', '2021-10-07 21:16:10'),
(2, 'csimon', 'camille.simon2@gmail.com', '$2y$10$/UzoPvOPZ3O3lFO6rj6vEuosIP3ouSLh05F0jRC3cSIYItzQU.kZ.', 'LACITO', '2021-10-07 21:14:00', '2021-10-07 21:13:39', '2021-10-07 21:14:00'),
(3, 'amichaud', 'alexis.michaud@cnrs.fr', '$2y$10$laVYe3XOpmJ2e7DrdsGcru7fk6eMy5UsPwIhEa47zPGsN0X2s/QjO', 'LACITO', NULL, '2021-10-07 21:14:57', '2021-10-07 21:14:57'),
(4, 'sguillaume', 'severine.guillaume@cnrs.fr', '$2y$10$VMyXU9wWa9AXmczSxZ7MSOQZanqIKHRw5G5.7VSp9uprgLatl4KtG', 'LACITO', NULL, '2021-10-07 21:16:01', '2021-10-07 21:16:01');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annotations`
--
ALTER TABLE `annotations`
  ADD CONSTRAINT `annotations_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`),
  ADD CONSTRAINT `annotations_image_id_foreign` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`),
  ADD CONSTRAINT `annotations_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `annotations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `document_contributors`
--
ALTER TABLE `document_contributors`
  ADD CONSTRAINT `document_contributors_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`);

--
-- Contraintes pour la table `document_titles`
--
ALTER TABLE `document_titles`
  ADD CONSTRAINT `document_titles_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`);

--
-- Contraintes pour la table `forms`
--
ALTER TABLE `forms`
  ADD CONSTRAINT `forms_annotation_id_foreign` FOREIGN KEY (`annotation_id`) REFERENCES `annotations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`);

--
-- Contraintes pour la table `recordings`
--
ALTER TABLE `recordings`
  ADD CONSTRAINT `recordings_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`);

--
-- Contraintes pour la table `translations`
--
ALTER TABLE `translations`
  ADD CONSTRAINT `translations_annotation_id_foreign` FOREIGN KEY (`annotation_id`) REFERENCES `annotations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
