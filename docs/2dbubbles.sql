-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 24 juin 2023 à 09:51
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.1.17

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
  `postId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `content` text NOT NULL,
  `status` int(1) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `postId`, `userId`, `content`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'C\'est un bon moyen pour moi de décompresser.', 1, '2023-06-02 12:48:18', '2023-06-02 12:48:18'),
(2, 1, 4, 'Une bonne musique sur le chemin du retour, après une dure journée ! ça c\'est le top!', 1, '2023-06-02 12:48:30', '2023-06-02 12:48:30'),
(3, 3, 3, 'Je ne sais pas, cela peut-être utile sûrement mais delà à remplacer le travail d\'un être humain. Je pense qu\'il faut prendre conscience des limites et à avoir le courage de les appliquer!', 1, '2023-06-02 12:49:01', '2023-06-02 12:49:01'),
(4, 2, 3, 'Je trouve que ce langage est très complet et fait des miracles. Je ne comprend pas pourquoi il a été décrié!', 1, '2023-06-02 12:48:57', '2023-06-02 12:48:57');

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `title` varchar(84) NOT NULL,
  `slug` varchar(84) NOT NULL,
  `chapo` varchar(180) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id`, `userId`, `title`, `slug`, `chapo`, `content`, `created_at`, `updated_at`) VALUES
(1, 1, 'La musique', 'la-musique', 'Composition-inspiration', 'La musique est un art et une activité culturelle consistant à combiner sons et silences au cours du temps. Les paramètres principaux sont le rythme, la hauteur, les nuances et le timbre. Elle est aujourd\'hui considérée comme une forme de poésie moderne. La musique donne lieu à des créations, des représentations. ', '2023-05-24 14:27:01', NULL),
(2, 1, 'PHP', 'php', 'Langage de programmation serveur', 'PHP: Hypertext Preprocessor, plus connu sous son sigle PHP, est un langage de programmation libre, principalement utilisé pour produire des pages Web dynamiques via un SERVEUR web, mais pouvant également fonctionner comme n\'importe quel langage interprété de façon locale. PHP est un langage impératif orienté objet.', '2023-05-14 15:18:00', '2023-05-14 15:18:00'),
(3, 1, 'ChatGPT', 'chatgpt', 'Application', 'Une intelligence artificielle au cœur des polémiques, qui déchaine les passions. Alors ?\r\nPOUR ou CONTRE ... NEUTRE peut-être ?', '2023-06-01 21:21:02', '2023-06-01 21:21:02'),
(4, 1, 'Symfony', 'symfony', 'Framework', 'Symfony est un ensemble de composants PHP ainsi qu\'un framework MVC libre écrit en PHP. Il fournit des fonctionnalités modulables et adaptables qui permettent de faciliter et d’accélérer le développement d\'un site web.', '2023-06-01 21:22:38', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(24) NOT NULL,
  `rolestring` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id`, `name`, `rolestring`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'ROLE_SUPER_ADMINE', '2023-06-02 10:16:48', '2023-06-02 10:13:03'),
(2, 'admin', '[\"ROLE_ADMIN\"]', '2023-05-14 10:33:15', NULL),
(3, 'utilisateur', '[\"ROLE_UTILISATEUR\"]', '2023-05-14 10:33:15', NULL),
(6, 'moderateur', 'ROLE_MODERATEUR', '2023-06-24 07:43:50', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `roleId` int(11) NOT NULL,
  `pseudo` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `roleId`, `pseudo`, `slug`, `email`, `password`, `token`, `created_at`, `updated_at`) VALUES
(1, 1, 'Laura', 'laura', 'laura@2dbubbles.com', '$2y$12$OOe9CT00VCtU9PL0aIDVTeHpovZCF1ue8LQGq2.GexgOnUlkflH4i', '', '2023-06-19 11:35:48', '2023-06-19 11:35:48'),
(2, 2, 'Paolito', 'paolito', 'paolito@2dbubbles.com', '$2y$12$9JoAVjz0jle9I86aNNfzIOZZ7qO.mCtTZ0u1Bym2PU/x88g5uxlyy', NULL, '2023-05-14 14:06:05', NULL),
(3, 3, 'Sophie', 'sophie', 'sophiedelage@sfr.com', '$2y$12$o21BtcJOsCLiN6eMJez2Mu8fzO8HoAXyF9FVsdduisB.1aWgxRNcm', 'e2734252d927b0dcbcf9e742edd0a9dcc1b4f0174d1f6b31d2ad9eb9b8bc91a7', '2023-05-26 12:31:59', '2023-05-26 12:31:59'),
(4, 3, 'Julie', 'julie', 'julielescault@gmail.com', '$2y$12$EWcgYQtnqYcIBm1Z7y/6NuuItdrNVxfVUAspXLrfA/ExGoq5XSCnW', '', '2023-05-30 18:43:08', '2023-05-30 18:43:08'),
(5, 3, 'Joël', 'jol', 'joelbarbe@orange.fr', '$2y$12$/vnunjfhAcb/NefmUanLeOtXoWhdu0E8Ca358vy.5E0YVxU1Og1Fu', NULL, '2023-06-02 11:01:32', NULL),
(6, 3, 'David', 'david', 'david@hotmail.fr', '$2y$12$l/s/pyy551xkcwuE6op6J.rKVUHAMDPHEghcg/NMxxfcr8QVj7K9a', NULL, '2023-06-02 13:45:06', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts` (`postId`),
  ADD KEY `users` (`userId`);

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `users` (`userId`);

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
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `roles` (`roleId`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`postId`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`roleId`) REFERENCES `role` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
