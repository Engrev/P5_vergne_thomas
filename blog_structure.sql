-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 29 mai 2020 à 11:14
-- Version du serveur :  5.7.24
-- Version de PHP : 7.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `blog`
--

-- --------------------------------------------------------

--
-- Structure de la table `b_categories`
--

CREATE TABLE `b_categories` (
  `id_category` int(11) NOT NULL,
  `link` text NOT NULL,
  `name` text NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `b_comments`
--

CREATE TABLE `b_comments` (
  `id_comment` int(11) NOT NULL,
  `id_post` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `content` text NOT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `b_files`
--

CREATE TABLE `b_files` (
  `id_file` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `name` text NOT NULL COMMENT 'Nom généré pour le fichier',
  `uploaded_name` text NOT NULL COMMENT 'Nom d''upload du fichier',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `b_groups`
--

CREATE TABLE `b_groups` (
  `id_group` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `b_posts`
--

CREATE TABLE `b_posts` (
  `id_post` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  `link` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `content` longblob NOT NULL,
  `author` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `b_recaptcha_logs`
--

CREATE TABLE `b_recaptcha_logs` (
  `id_log` int(11) NOT NULL,
  `response` varchar(400) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `score` float UNSIGNED DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `challenge` varchar(20) DEFAULT NULL,
  `hostname` varchar(50) DEFAULT NULL,
  `ip_address` varchar(15) NOT NULL,
  `error_codes` json DEFAULT NULL,
  `_post` json NOT NULL,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `b_users`
--

CREATE TABLE `b_users` (
  `id_user` int(11) NOT NULL,
  `id_group` int(10) UNSIGNED NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `avatar` int(10) UNSIGNED DEFAULT NULL,
  `reset_token` varchar(60) DEFAULT NULL,
  `date_reset_token` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `last_connection` datetime DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_upd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `b_users_infos`
--

CREATE TABLE `b_users_infos` (
  `id_info` int(11) NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `website` text,
  `linkedin` text,
  `github` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `b_categories`
--
ALTER TABLE `b_categories`
  ADD PRIMARY KEY (`id_category`);

--
-- Index pour la table `b_comments`
--
ALTER TABLE `b_comments`
  ADD PRIMARY KEY (`id_comment`);

--
-- Index pour la table `b_files`
--
ALTER TABLE `b_files`
  ADD PRIMARY KEY (`id_file`);

--
-- Index pour la table `b_groups`
--
ALTER TABLE `b_groups`
  ADD PRIMARY KEY (`id_group`);

--
-- Index pour la table `b_posts`
--
ALTER TABLE `b_posts`
  ADD PRIMARY KEY (`id_post`);

--
-- Index pour la table `b_recaptcha_logs`
--
ALTER TABLE `b_recaptcha_logs`
  ADD PRIMARY KEY (`id_log`);

--
-- Index pour la table `b_users`
--
ALTER TABLE `b_users`
  ADD PRIMARY KEY (`id_user`);

--
-- Index pour la table `b_users_infos`
--
ALTER TABLE `b_users_infos`
  ADD PRIMARY KEY (`id_info`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `b_categories`
--
ALTER TABLE `b_categories`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `b_comments`
--
ALTER TABLE `b_comments`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `b_files`
--
ALTER TABLE `b_files`
  MODIFY `id_file` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `b_groups`
--
ALTER TABLE `b_groups`
  MODIFY `id_group` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `b_posts`
--
ALTER TABLE `b_posts`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `b_recaptcha_logs`
--
ALTER TABLE `b_recaptcha_logs`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `b_users`
--
ALTER TABLE `b_users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `b_users_infos`
--
ALTER TABLE `b_users_infos`
  MODIFY `id_info` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
