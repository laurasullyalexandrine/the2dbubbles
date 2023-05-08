-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 08 mai 2023 à 18:58
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `2dbubbles`
--

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `posts` int(11) DEFAULT NULL,
  `users` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `status` int(1) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `posts`, `users`, `content`, `status`, `created_at`, `updated_at`) VALUES
(20, 14, 52, 'Quel merveilleuse pâtisserie française !', 1, '2023-05-05 08:22:22', '2023-05-05 08:22:22'),
(29, 14, 64, 'je crois que je n\'ai jamais mangé de pâtisserie meilleure que celle là ! Vraiment !', 2, '2023-05-05 08:56:33', '2023-05-05 08:56:33'),
(30, 15, 57, 'Une playlist avec mes morceaux favoris du moment. Rien de tel pour passer agréable journée ! ', 1, '2023-05-05 08:02:24', '2023-05-05 08:02:24');

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `users` int(11) DEFAULT NULL,
  `title` varchar(84) NOT NULL,
  `chapo` varchar(180) NOT NULL,
  `content` text NOT NULL,
  `slug` varchar(84) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id`, `users`, `title`, `chapo`, `content`, `slug`, `created_at`, `updated_at`) VALUES
(2, 52, 'L\'alternante', 'OpenclassRoom', 'On sait DEPUIS longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même.', 'L\'alternante', '2023-04-14 11:47:58', '2023-04-09 09:43:59'),
(7, 52, 'Développeur back-end', 'C\'est quoi un le backend ?', 'C\'est quoi un développeur back-end ?\r\nLe développeur back-end s\'occupe du côté technique et fonctionnel d\'un site web. Contrairement au développeur front-end, celui-ci travaille dans l\'ombre et se charge de toute la partie back-office, c\'est-à-dire les éléments indispensables pour le fonctionnement du site, mais qui sont invisibles des internautes.', 'dveloppeur-back-end', '2023-04-08 16:06:09', NULL),
(13, 52, 'PHP', 'Langage de programmation', 'PHP: Hypertext Preprocessor, plus connu sous son sigle PHP, est un langage de programmation libre, principalement utilisé pour produire des pages Web dynamiques via un serveur HTTP, mais pouvant également fonctionner comme n\'importe quel langage interprété de façon locale. PHP est un langage impératif orienté objet.', 'PHP', '2023-04-11 06:56:11', '2023-04-11 06:56:11'),
(14, 52, 'Saint-honoré', 'Le Saint Honoré : une création de chez Chiboust', 'Le saint-honoré est une pâtisserie française, à base de crème Chantilly, de crème chiboust et de petits choux glacés au sucre. L\'histoire du Saint-Honoré démarre vers 1847. C\'est dans la Maison Chiboust (comme la crème éponyme), célèbre pâtisserie de l\'époque, que la recette est inventée. Inspirée à la base d\'un dessert bordelais, Chiboust donne à sa création le nom de Saint Honoré.', 'saint-honor', '2023-04-24 07:39:20', '2023-04-24 07:39:20'),
(15, 52, 'La musique ', 'Composition', 'La musique est un art et une activité culturelle consistant à combiner sons et silences au cours du temps. Les paramètres principaux sont le rythme, la hauteur, les nuances et le timbre. Elle est aujourd\'hui considérée comme une forme de poésie moderne. La musique donne lieu à des créations, des représentations. De plus, elle peut-être pour un grand nombre d\'entre nous, source de guérison.', 'la-musique', '2023-04-24 06:45:44', '2023-04-24 06:45:44');

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `roleString` varchar(24) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id`, `name`, `roleString`, `created_at`, `updated_at`) VALUES
(2, 'Super_admin', 'ROLE_SUPER_ADMIN', '2023-04-06 12:26:26', '2023-04-21 09:06:07'),
(3, 'Admin', 'ROLE_ADMIN', '2023-04-06 12:26:26', '2023-04-06 13:03:06'),
(4, 'Utilisateur', 'ROLE_UTILISATEUR', '2023-04-06 12:26:26', '2023-04-06 12:26:46');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `roles` int(11) DEFAULT NULL,
  `pseudo` varchar(64) DEFAULT NULL,
  `slug` varchar(64) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `roles`, `pseudo`, `slug`, `email`, `password`, `created_at`, `updated_at`) VALUES
(52, 2, 'Laura', 'laura', 'laura@2dbubbles.com', '$2y$12$WWFcfJ2txPN2vJ9UnuFn/OkJqXlQj/a.CQJc9scuH8UQgSaBmi0Le', '2023-04-01 15:55:52', NULL),
(53, 3, 'Paolito', 'paolito', 'paolito@2dbubbles.com', '$2y$12$la3SACzR0yyqnCvh47LUauYm6uViXj1Ddgtw6dikSbrnlrDUuHJsO', '2023-04-03 06:43:45', '2023-04-07 09:15:56'),
(57, 4, 'Clémentine', 'clementine', 'clem88@orange.fr', '$2y$12$IWJ3x0BUFgQCx10r/uuKY.Gft2ak71TfQsW7lxG5p.V89vMe80HLO', '2023-04-21 08:28:18', '2023-04-30 13:37:58'),
(64, 4, 'David', 'david', 'david@hotmail.fr', '$2y$12$Xogn0NDg2wQTYFX.p/HxWuLoHZQm4x0820Ab5Ejtrstk23al6Ux7u', '2023-05-04 17:24:00', NULL),
(65, 4, 'Sophie', 'sophie', 'sophiedelage@sfr.fr', '$2y$12$bGrobrKsGKm5XM09YBiddeR7EfMVYpfNuosidfCRYqSfMKN3kqU52', '2023-05-08 09:34:14', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts` (`posts`),
  ADD KEY `users` (`users`);

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `users` (`users`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_ibfk_3` (`roles`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`posts`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`users`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`users`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_3` FOREIGN KEY (`roles`) REFERENCES `role` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
