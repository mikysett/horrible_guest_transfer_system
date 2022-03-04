-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Ven 15 Juin 2018 à 22:31
-- Version du serveur :  5.7.22-0ubuntu0.16.04.1
-- Version de PHP :  7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `trans`
--

-- --------------------------------------------------------

--
-- Structure de la table `commenti`
--

CREATE TABLE `commenti` (
  `id` bigint(20) NOT NULL,
  `id_transfer` bigint(20) NOT NULL,
  `tutti` tinyint(4) NOT NULL DEFAULT '0',
  `reception` tinyint(4) NOT NULL DEFAULT '0',
  `barca` tinyint(4) NOT NULL DEFAULT '0',
  `facchini` tinyint(4) NOT NULL DEFAULT '0',
  `taxi` tinyint(4) NOT NULL DEFAULT '0',
  `tipo_commento` tinyint(4) NOT NULL,
  `testo` text COLLATE utf8_unicode_ci,
  `operatore` tinytext COLLATE utf8_unicode_ci,
  `data_creazione` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `note_giornaliere`
--

CREATE TABLE `note_giornaliere` (
  `id` bigint(20) NOT NULL,
  `data` bigint(20) NOT NULL,
  `tutti` tinyint(4) NOT NULL DEFAULT '0',
  `reception` tinyint(4) NOT NULL DEFAULT '0',
  `facchini` tinyint(4) NOT NULL DEFAULT '0',
  `barca` tinyint(4) NOT NULL DEFAULT '0',
  `taxi` tinyint(4) NOT NULL DEFAULT '0',
  `testo` text COLLATE utf8_unicode_ci NOT NULL,
  `data_creazione` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transfer`
--

CREATE TABLE `transfer` (
  `id` bigint(20) NOT NULL,
  `tipo_transfer` smallint(6) NOT NULL DEFAULT '0',
  `nome` tinytext COLLATE utf8_unicode_ci,
  `data_arr` bigint(20) DEFAULT NULL,
  `ora_arr` bigint(20) DEFAULT NULL,
  `luogo_arr` tinytext COLLATE utf8_unicode_ci,
  `volo_arr` tinytext COLLATE utf8_unicode_ci,
  `taxi_arr` tinyint(4) NOT NULL DEFAULT '0',
  `ora_taxi_arr` bigint(20) DEFAULT NULL,
  `porto_partenza_arr` tinyint(4) NOT NULL DEFAULT '0',
  `barca_arr` tinyint(4) DEFAULT '0',
  `porto_arrivo_arr` tinyint(4) NOT NULL DEFAULT '0',
  `ora_barca_arr` tinytext COLLATE utf8_unicode_ci,
  `ora_barca_arr_cal` bigint(20) DEFAULT NULL,
  `stato_arr` tinyint(4) NOT NULL DEFAULT '0',
  `operatore_arr` tinytext COLLATE utf8_unicode_ci,
  `ultima_mod_arr` bigint(20) DEFAULT NULL,
  `data_par` bigint(20) DEFAULT NULL,
  `ora_par` bigint(20) DEFAULT NULL,
  `luogo_par` tinytext COLLATE utf8_unicode_ci,
  `volo_par` tinytext COLLATE utf8_unicode_ci,
  `taxi_par` tinyint(4) NOT NULL DEFAULT '0',
  `ora_taxi_par` bigint(20) DEFAULT NULL,
  `porto_partenza_par` tinyint(4) NOT NULL DEFAULT '0',
  `barca_par` tinyint(4) NOT NULL DEFAULT '0',
  `porto_arrivo_par` tinyint(4) NOT NULL DEFAULT '0',
  `ora_barca_par` bigint(20) DEFAULT NULL,
  `ora_barca_par_cal` bigint(20) DEFAULT NULL,
  `stato_par` tinyint(4) NOT NULL DEFAULT '0',
  `operatore_par` tinytext COLLATE utf8_unicode_ci,
  `ultima_mod_par` bigint(20) DEFAULT NULL,
  `pax_ad` tinyint(4) NOT NULL DEFAULT '0',
  `pax_bam` tinyint(4) NOT NULL DEFAULT '0',
  `camera` text COLLATE utf8_unicode_ci,
  `num_tel` tinytext COLLATE utf8_unicode_ci,
  `email` tinytext COLLATE utf8_unicode_ci,
  `data_creazione` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `commenti`
--
ALTER TABLE `commenti`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `note_giornaliere`
--
ALTER TABLE `note_giornaliere`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `transfer`
--
ALTER TABLE `transfer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `commenti`
--
ALTER TABLE `commenti`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
--
-- AUTO_INCREMENT pour la table `note_giornaliere`
--
ALTER TABLE `note_giornaliere`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `transfer`
--
ALTER TABLE `transfer`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
