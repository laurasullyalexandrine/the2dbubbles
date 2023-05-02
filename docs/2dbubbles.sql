-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 02 mai 2023 à 09:21
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
(1, 2, 56, 'Jouer à l\'apprenti chimiste', 1, '2023-04-30 20:54:50', NULL),
(5, 7, 52, 'Je veux devenir développeur backend quels sont les prérequis ?', 2, '2023-04-30 20:54:55', NULL),
(6, 7, 52, 'Quel est le salaire d\'un développeur backend junior ?', 1, '2023-04-30 20:54:58', NULL),
(7, 7, 52, 'Quelle étude pour devenir développeur Backend ?', 2, '2023-04-30 20:55:03', NULL),
(8, 13, 52, 'Est ce que ce langage est facile à appréhender ?', 1, '2023-04-30 20:55:08', NULL),
(9, 13, 52, 'Est ce que ce langage est facile à appréhender ?', 0, '2023-04-15 09:23:36', NULL),
(10, 13, 52, 'Est ce que ce langage est facile à appréhender ?', 0, '2023-04-15 09:24:08', NULL),
(11, 13, 52, 'Est ce que ce langage est facile à appréhender ?', 0, '2023-04-15 09:24:33', NULL),
(12, 13, 52, 'Est ce que ce langage est facile à appréhender ?', 0, '2023-04-15 09:24:45', NULL),
(13, 13, 52, 'Est ce que ce langage est facile à appréhender ?', 0, '2023-04-15 09:33:22', NULL),
(14, 13, 52, 'test', 0, '2023-04-15 09:33:40', NULL),
(15, 13, 52, 'test', 0, '2023-04-15 09:34:10', NULL),
(16, 13, 52, 'test', 0, '2023-04-15 09:34:54', NULL),
(17, 13, 52, 'test', 0, '2023-04-15 09:35:13', NULL),
(18, 13, 52, 'test 1', 0, '2023-04-15 09:36:07', NULL),
(19, 13, 52, 'test', 1, '2023-04-30 20:54:28', NULL),
(20, 14, 52, 'Quel merveilleuse pâtisserie française !', 1, '2023-04-15 12:42:36', NULL),
(22, 14, 58, 'Un bonheur pour les papilles !', 1, '2023-04-30 20:54:22', NULL),
(25, 15, 58, 'rien de tel pour décompresser !', 2, '2023-04-30 20:54:17', NULL),
(26, 16, 52, 'mon fils en sont fan !', 2, '2023-04-30 20:54:13', NULL),
(27, 16, 57, 'je suis aussi une grande fan, j\'ai déjà une petite collection de toupies! ????', 1, '2023-04-30 22:05:39', '2023-04-30 22:05:39'),
(28, 14, 57, 'Je vais en acheter cet après midi. Je vous dis ça après!', 1, '2023-04-30 22:04:02', '2023-04-30 22:04:02');

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
(15, 52, 'La musique ', 'Composition', 'La musique est un art et une activité culturelle consistant à combiner sons et silences au cours du temps. Les paramètres principaux sont le rythme, la hauteur, les nuances et le timbre. Elle est aujourd\'hui considérée comme une forme de poésie moderne. La musique donne lieu à des créations, des représentations. De plus, elle peut-être pour un grand nombre d\'entre nous, source de guérison.', 'la-musique', '2023-04-24 06:45:44', '2023-04-24 06:45:44'),
(16, 52, 'Beyblade', 'Jeu', 'Traduit de l\'anglais-Beyblade est une ligne de jouets à toupie développée à l\'origine par Takara, sortie pour la première fois au Japon en juillet 1999, avec sa première série. Suite à la fusion de Takara avec Tomy en 2006, les Beyblades sont désormais développées par Takara Tomy.', 'beyblade', '2023-04-30 13:32:21', NULL);

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
(3, 'Administrateur', 'ROLE_ADMINISTRATEUR', '2023-04-06 12:26:26', '2023-04-06 13:03:06'),
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
(56, 2, 'Julie', 'julie', 'julie@gmail.com', '$2y$12$7liUBtbai0QjsrqQ22eScOhIq/i1vBWPaHrmT4BY6Jmy5P1dAwNDS', '2023-04-06 12:52:44', '2023-04-21 09:39:58'),
(57, 4, 'Clémentine', 'clementine', 'clem88@orange.fr', '$2y$12$IWJ3x0BUFgQCx10r/uuKY.Gft2ak71TfQsW7lxG5p.V89vMe80HLO', '2023-04-21 08:28:18', '2023-04-30 13:37:58'),
(58, 4, 'Thomatitos', 'thomatitos', 'thomas@hotmail.fr', '$2y$12$VWZ5ixSsqtOHfThNT4/3kuj5g7v2lAtoOLeDEGqpjeHTQRwgqcFIu', '2023-04-28 07:53:54', NULL),
(59, 4, 'Jojo', 'jojo', 'jocelyne@orange.fr', '$2y$12$7fWj6ifpO6zuaN0mVTiLF.QM86Ho6n6O8kcVQ45PVE3VKX2dKhgpa', '2023-04-30 14:24:59', NULL),
(61, 4, 'Joel', 'joel', 'joel@gmail.com', '$2y$12$8LGkZEkNhD9PVA3oPCwQqOgbekX0w.KqidX2tPjgxD9rq1xTCIET2', '2023-04-30 14:27:26', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

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
