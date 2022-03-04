-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Sam 22 Septembre 2018 à 18:25
-- Version du serveur :  5.7.23-0ubuntu0.16.04.1
-- Version de PHP :  7.0.32-0ubuntu0.16.04.1

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
  `governante` tinyint(4) NOT NULL DEFAULT '0',
  `ristorante` tinyint(4) NOT NULL DEFAULT '0',
  `barca` tinyint(4) NOT NULL DEFAULT '0',
  `facchini` tinyint(4) NOT NULL DEFAULT '0',
  `taxi` tinyint(4) NOT NULL DEFAULT '0',
  `cliente` tinyint(4) DEFAULT '0',
  `tipo_commento` tinyint(4) NOT NULL,
  `testo` text COLLATE utf8_unicode_ci,
  `operatore` tinytext COLLATE utf8_unicode_ci,
  `data_creazione` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `invii_emails`
--

CREATE TABLE `invii_emails` (
  `id` bigint(20) NOT NULL,
  `id_transfer` bigint(20) DEFAULT NULL,
  `tipo` tinyint(4) NOT NULL DEFAULT '0',
  `stato` tinyint(4) NOT NULL DEFAULT '0'
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
-- Structure de la table `stato_mezzi`
--

CREATE TABLE `stato_mezzi` (
  `id` bigint(20) NOT NULL,
  `mezzo` smallint(6) NOT NULL DEFAULT '0',
  `stato` smallint(6) NOT NULL,
  `timestamp_stato` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `testi_trans`
--

CREATE TABLE `testi_trans` (
  `id` bigint(20) NOT NULL,
  `a_scadenza` tinyint(4) NOT NULL DEFAULT '0',
  `inizio_validita` bigint(20) DEFAULT NULL,
  `fine_validita` bigint(20) DEFAULT NULL,
  `sezione` tinytext COLLATE utf8_unicode_ci,
  `titolo` tinytext COLLATE utf8_unicode_ci,
  `note_interne` text COLLATE utf8_unicode_ci,
  `fr` text COLLATE utf8_unicode_ci,
  `it` text COLLATE utf8_unicode_ci,
  `en` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transfer`
--

CREATE TABLE `transfer` (
  `id` bigint(20) NOT NULL,
  `link` tinytext COLLATE utf8_unicode_ci,
  `tipo_transfer` smallint(6) NOT NULL DEFAULT '0',
  `titolo` tinyint(4) NOT NULL DEFAULT '0',
  `nome` tinytext COLLATE utf8_unicode_ci,
  `lingua` tinyint(4) NOT NULL DEFAULT '0',
  `pagamento` tinyint(4) NOT NULL DEFAULT '0',
  `data_arr` bigint(20) DEFAULT NULL,
  `ora_arr` bigint(20) DEFAULT NULL,
  `luogo_arr` tinytext COLLATE utf8_unicode_ci,
  `volo_arr` tinytext COLLATE utf8_unicode_ci,
  `taxi_arr` tinyint(4) NOT NULL DEFAULT '0',
  `ora_taxi_arr` bigint(20) DEFAULT NULL,
  `porto_partenza_arr` tinyint(4) NOT NULL DEFAULT '0',
  `barca_arr` tinyint(4) NOT NULL DEFAULT '0',
  `porto_arrivo_arr` tinyint(4) NOT NULL DEFAULT '0',
  `ora_barca_arr` tinytext COLLATE utf8_unicode_ci,
  `ora_barca_arr_cal` bigint(20) DEFAULT NULL,
  `bagagli_arr` tinyint(4) NOT NULL DEFAULT '0',
  `camera_arr` tinyint(4) NOT NULL DEFAULT '0',
  `time_cam_arr` bigint(20) NOT NULL DEFAULT '0',
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
  `bagagli_par` tinyint(4) NOT NULL DEFAULT '0',
  `stato_par` tinyint(4) NOT NULL DEFAULT '0',
  `operatore_par` tinytext COLLATE utf8_unicode_ci,
  `ultima_mod_par` bigint(20) DEFAULT NULL,
  `pax_ad` tinyint(4) NOT NULL DEFAULT '0',
  `pax_bam` tinyint(4) NOT NULL DEFAULT '0',
  `camera` text COLLATE utf8_unicode_ci,
  `num_tel` tinytext COLLATE utf8_unicode_ci,
  `num_tel_sec` tinytext COLLATE utf8_unicode_ci,
  `email` tinytext COLLATE utf8_unicode_ci,
  `email_sec` tinytext COLLATE utf8_unicode_ci,
  `auto_remind` tinyint(4) NOT NULL DEFAULT '1',
  `modificabile` tinyint(4) NOT NULL DEFAULT '10',
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
-- Index pour la table `invii_emails`
--
ALTER TABLE `invii_emails`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `note_giornaliere`
--
ALTER TABLE `note_giornaliere`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `stato_mezzi`
--
ALTER TABLE `stato_mezzi`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `testi_trans`
--
ALTER TABLE `testi_trans`
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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1614;
--
-- AUTO_INCREMENT pour la table `invii_emails`
--
ALTER TABLE `invii_emails`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `note_giornaliere`
--
ALTER TABLE `note_giornaliere`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;
--
-- AUTO_INCREMENT pour la table `stato_mezzi`
--
ALTER TABLE `stato_mezzi`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `testi_trans`
--
ALTER TABLE `testi_trans`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `transfer`
--
ALTER TABLE `transfer`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2367;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
