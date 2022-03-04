-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 28, 2022 at 12:25 PM
-- Server version: 8.0.28-0ubuntu0.20.04.3
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trans`
--

-- --------------------------------------------------------

--
-- Table structure for table `commenti`
--

CREATE TABLE `commenti` (
  `id` bigint NOT NULL,
  `id_transfer` bigint NOT NULL,
  `tutti` tinyint NOT NULL DEFAULT '0',
  `reception` tinyint NOT NULL DEFAULT '0',
  `governante` tinyint NOT NULL DEFAULT '0',
  `ristorante` tinyint NOT NULL DEFAULT '0',
  `barca` tinyint NOT NULL DEFAULT '0',
  `facchini` tinyint NOT NULL DEFAULT '0',
  `taxi` tinyint NOT NULL DEFAULT '0',
  `cliente` tinyint DEFAULT '0',
  `tipo_commento` tinyint NOT NULL,
  `testo` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `operatore` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `data_creazione` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invii_emails`
--

CREATE TABLE `invii_emails` (
  `id` bigint NOT NULL,
  `id_transfer` bigint DEFAULT NULL,
  `tipo` tinyint NOT NULL DEFAULT '0',
  `stato` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note_giornaliere`
--

CREATE TABLE `note_giornaliere` (
  `id` bigint NOT NULL,
  `data` bigint NOT NULL,
  `tutti` tinyint NOT NULL DEFAULT '0',
  `reception` tinyint NOT NULL DEFAULT '0',
  `facchini` tinyint NOT NULL DEFAULT '0',
  `barca` tinyint NOT NULL DEFAULT '0',
  `taxi` tinyint NOT NULL DEFAULT '0',
  `testo` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data_creazione` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stato_mezzi`
--

CREATE TABLE `stato_mezzi` (
  `id` bigint NOT NULL,
  `mezzo` smallint NOT NULL DEFAULT '0',
  `stato` smallint NOT NULL,
  `timestamp_stato` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testi_trans`
--

CREATE TABLE `testi_trans` (
  `id` bigint NOT NULL,
  `a_scadenza` tinyint NOT NULL DEFAULT '0',
  `inizio_validita` bigint DEFAULT NULL,
  `fine_validita` bigint DEFAULT NULL,
  `sezione` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `titolo` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `note_interne` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `fr` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `it` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `en` text CHARACTER SET utf8 COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transfer`
--

CREATE TABLE `transfer` (
  `id` bigint NOT NULL,
  `link` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `tipo_transfer` smallint NOT NULL DEFAULT '0',
  `titolo` tinyint NOT NULL DEFAULT '0',
  `nome` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `lingua` tinyint NOT NULL DEFAULT '0',
  `pagamento` tinyint NOT NULL DEFAULT '0',
  `data_arr` bigint DEFAULT NULL,
  `ora_arr` bigint DEFAULT NULL,
  `luogo_arr` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `volo_arr` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `taxi_arr` tinyint NOT NULL DEFAULT '0',
  `ora_taxi_arr` bigint DEFAULT NULL,
  `porto_partenza_arr` tinyint NOT NULL DEFAULT '0',
  `barca_arr` tinyint NOT NULL DEFAULT '0',
  `porto_arrivo_arr` tinyint NOT NULL DEFAULT '0',
  `ora_barca_arr` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ora_barca_arr_cal` bigint DEFAULT NULL,
  `bagagli_arr` tinyint NOT NULL DEFAULT '0',
  `camera_arr` tinyint NOT NULL DEFAULT '0',
  `time_cam_arr` bigint NOT NULL DEFAULT '0',
  `stato_arr` tinyint NOT NULL DEFAULT '0',
  `operatore_arr` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ultima_mod_arr` bigint DEFAULT NULL,
  `data_par` bigint DEFAULT NULL,
  `ora_par` bigint DEFAULT NULL,
  `luogo_par` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `volo_par` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `taxi_par` tinyint NOT NULL DEFAULT '0',
  `ora_taxi_par` bigint DEFAULT NULL,
  `porto_partenza_par` tinyint NOT NULL DEFAULT '0',
  `barca_par` tinyint NOT NULL DEFAULT '0',
  `porto_arrivo_par` tinyint NOT NULL DEFAULT '0',
  `ora_barca_par` bigint DEFAULT NULL,
  `ora_barca_par_cal` bigint DEFAULT NULL,
  `bagagli_par` tinyint NOT NULL DEFAULT '0',
  `stato_par` tinyint NOT NULL DEFAULT '0',
  `operatore_par` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ultima_mod_par` bigint DEFAULT NULL,
  `pax_ad` tinyint NOT NULL DEFAULT '0',
  `pax_bam` tinyint NOT NULL DEFAULT '0',
  `camera` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `num_tel` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `num_tel_sec` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email_sec` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `auto_remind` tinyint NOT NULL DEFAULT '1',
  `modificabile` tinyint NOT NULL DEFAULT '10',
  `data_creazione` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commenti`
--
ALTER TABLE `commenti`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invii_emails`
--
ALTER TABLE `invii_emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `note_giornaliere`
--
ALTER TABLE `note_giornaliere`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stato_mezzi`
--
ALTER TABLE `stato_mezzi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testi_trans`
--
ALTER TABLE `testi_trans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transfer`
--
ALTER TABLE `transfer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `commenti`
--
ALTER TABLE `commenti`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invii_emails`
--
ALTER TABLE `invii_emails`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `note_giornaliere`
--
ALTER TABLE `note_giornaliere`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stato_mezzi`
--
ALTER TABLE `stato_mezzi`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testi_trans`
--
ALTER TABLE `testi_trans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transfer`
--
ALTER TABLE `transfer`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
