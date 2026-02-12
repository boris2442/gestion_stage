-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 12, 2026 at 04:46 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_stagiaires_resotel`
--

-- --------------------------------------------------------

--
-- Table structure for table `demandes`
--

CREATE TABLE `demandes` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau_etude` enum('CEP ou équivalent','BEPC ou équivalent','Probatoire ou équivalent','BAC ou équivalent','BTS ou équivalent','Licence ou équivalent','Master ou équivalent','Doctorat ou équivalent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BAC ou équivalent',
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cni` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_session` int DEFAULT NULL,
  `type_stage` enum('academique','professionnel') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cv_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lettre_motivation_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('en_attente','valide','rejete') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `date_demande` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `demandes`
--

INSERT INTO `demandes` (`id`, `nom`, `prenom`, `email`, `niveau_etude`, `telephone`, `cni`, `id_session`, `type_stage`, `cv_path`, `lettre_motivation_path`, `status`, `date_demande`) VALUES
(1, 'Fuga Voluptatum rep', 'Exercitation sapient', 'puqucojuw@mailinator.com', 'BAC ou équivalent', '+1 (132) 438-8427', 'Id vel nesciunt cup', NULL, 'academique', 'cv_fuga voluptatum rep_1770726493.pdf', 'lettre_fuga voluptatum rep_1770726493.pdf', 'valide', '2026-02-10 12:28:13'),
(2, 'Ex eos quia placeat', 'Eligendi eveniet do', 'nilyhim@mailinator.com', 'BAC ou équivalent', '+1 (256) 601-2789', '2345efgdr5852555', NULL, 'professionnel', 'cv_ex eos quia placeat_1770780092.pdf', 'lettre_ex eos quia placeat_1770780092.pdf', 'valide', '2026-02-11 03:21:32'),
(3, 'Aut tenetur sint dis', 'Nisi dolor repellend', 'covo@mailinator.com', 'BAC ou équivalent', '+1 (853) 895-3328', 'Amet optio molesti', NULL, 'professionnel', 'cv_aut tenetur sint dis_1770780123.pdf', 'lettre_aut tenetur sint dis_1770780123.pdf', 'rejete', '2026-02-11 03:22:03'),
(4, 'Blanditiis placeat ', 'Minima in earum quis', 'volyxujuqa@mailinator.com', 'BAC ou équivalent', '+1 (793) 318-1379', 'Quia ut non nesciunt', NULL, 'professionnel', 'cv_blanditiis placeat _1770780179.pdf', 'lettre_blanditiis placeat _1770780179.pdf', 'valide', '2026-02-11 03:22:59'),
(5, 'Reprehenderit aut re', 'Temporibus quia repr', 'deqanije@mailinator.com', 'CEP ou équivalent', '+1 (171) 435-2081', 'Consequatur quaerat', NULL, 'professionnel', 'cv_reprehenderit aut re_1770781402.pdf', 'lettre_reprehenderit aut re_1770781402.pdf', 'rejete', '2026-02-11 03:43:22'),
(6, 'Sunt repudiandae es', 'Et non recusandae A', 'conepefu@mailinator.com', 'BEPC ou équivalent', '+1 (289) 922-3979', 'Officia est illum ', NULL, 'academique', 'cv_sunt repudiandae es_1770784300.pdf', 'lettre_sunt repudiandae es_1770784300.pdf', 'valide', '2026-02-11 04:31:40'),
(7, 'HELLE', 'DANNISS', 'raqudun@mailinator.com', 'BAC ou équivalent', '+1 (182) 526-2665', 'Commodo quam qui lab', 1, 'professionnel', 'cv_20ef7171_1770825309.pdf', 'lettre_20ef7171_1770825309.pdf', 'valide', '2026-02-11 15:55:09'),
(8, 'Dolor ut reiciendis', 'Blanditiis animi la', 'veracib@mailinator.com', 'Probatoire ou équivalent', '+1 (464) 298-1889', 'Rerum fugit nihil q', 2, 'academique', 'cv_07b3c75b_1770901147.pdf', 'lettre_07b3c75b_1770901147.pdf', 'en_attente', '2026-02-12 12:59:07'),
(9, 'Magni velit magna e', 'Laudantium nihil ea', 'nunyl@mailinator.com', 'CEP ou équivalent', '+1 (443) 299-3859', 'Ea perferendis labor', 2, 'academique', 'cv_d95dc2f8_1770901167.pdf', 'lettre_d95dc2f8_1770901167.pdf', 'en_attente', '2026-02-12 12:59:27');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `id` int NOT NULL,
  `id_stagiaire` int NOT NULL,
  `id_session` int DEFAULT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ouvert','en_cours','resolu') COLLATE utf8mb4_unicode_ci DEFAULT 'ouvert',
  `date_signalement` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `id_stagiaire`, `id_session`, `sujet`, `message`, `status`, `date_signalement`) VALUES
(1, 14, 2, '', '', 'resolu', '2026-02-12 13:14:29'),
(2, 14, 2, 'Iure iure lorem quis', 'Officiis eum aute ar', 'resolu', '2026-02-12 13:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `rapports`
--

CREATE TABLE `rapports` (
  `id` int NOT NULL,
  `id_stagiaire` int NOT NULL,
  `id_session` int NOT NULL,
  `titre_rapport` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fichier_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_depot` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('en_attente','valide','a_corriger') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `commentaire_encadreur` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rapports`
--

INSERT INTO `rapports` (`id`, `id_stagiaire`, `id_session`, `titre_rapport`, `fichier_path`, `date_depot`, `status`, `commentaire_encadreur`) VALUES
(1, 14, 2, 'Rapport ', 'uploads/rapports/rapport_14_1770829641.pdf', '2026-02-11 17:07:21', 'valide', ''),
(2, 14, 2, 'Ex corrupti commodo', 'uploads/rapports/rapport_14_1770829659.pdf', '2026-02-11 17:07:39', 'valide', ''),
(3, 14, 2, 'Aut in ea eum exerci', 'uploads/rapports/rapport_14_1770829938.pdf', '2026-02-11 17:12:18', 'valide', 'Magna natus fugiat '),
(4, 14, 2, 'Dolor quas nostrud i', 'uploads/rapports/rapport_14_1770830842.pdf', '2026-02-11 17:27:22', 'valide', ''),
(5, 14, 2, 'Ipsum in omnis aut ', 'uploads/rapports/rapport_14_1770830953.pdf', '2026-02-11 17:29:13', 'valide', '');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `id_stagiaire` int DEFAULT NULL,
  `id_encadreur` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `titre`, `date_debut`, `date_fin`, `id_stagiaire`, `id_encadreur`, `is_active`) VALUES
(1, 'Promo sept 2025', '2026-02-20', '2026-05-25', NULL, NULL, 0),
(2, 'Promo', '2020-10-17', '2012-11-03', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `taches`
--

CREATE TABLE `taches` (
  `id` int NOT NULL,
  `id_stagiaire` int NOT NULL,
  `id_encadreur` int DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `status` enum('a_faire','en_cours','termine') COLLATE utf8mb4_unicode_ci DEFAULT 'a_faire',
  `note` int DEFAULT NULL,
  `commentaire_encadreur` text COLLATE utf8mb4_unicode_ci,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_session` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taches`
--

INSERT INTO `taches` (`id`, `id_stagiaire`, `id_encadreur`, `titre`, `description`, `date_debut`, `date_fin`, `status`, `note`, `commentaire_encadreur`, `date_creation`, `id_session`) VALUES
(1, 14, 5, 'Voluptatem quas dig', 'Delectus alias assu', NULL, '2026-04-04', 'termine', 12, NULL, '2026-02-11 06:13:51', NULL),
(2, 14, 5, 'Rem iste in consequa', 'Ratione aperiam obca', NULL, '2025-05-03', 'a_faire', 14, NULL, '2026-02-11 06:15:14', NULL),
(3, 14, 5, 'Rem iste in consequa', 'Ratione aperiam obca', NULL, '2025-05-03', 'termine', 12, NULL, '2026-02-11 07:42:20', NULL),
(4, 11, 5, 'Quia mollit voluptat', 'Suscipit sed quaerat', NULL, '2003-06-29', 'a_faire', NULL, NULL, '2026-02-11 07:42:28', NULL),
(5, 11, 5, 'Lorem impedit ut vo', 'Est minima modi sint', NULL, '2001-06-02', 'a_faire', NULL, NULL, '2026-02-11 07:42:38', NULL),
(6, 11, 5, 'Quia quia quis qui a', 'Dolore alias assumen', NULL, '2011-11-13', 'a_faire', 14, NULL, '2026-02-11 07:42:50', NULL),
(7, 14, 5, 'Delectus reprehende', 'Consequatur Quo non', NULL, '1973-09-30', 'termine', 14, NULL, '2026-02-11 08:25:08', NULL),
(8, 11, 5, 'Provident quia aut ', 'Voluptatum delectus', NULL, '1973-06-29', 'a_faire', 12, NULL, '2026-02-11 08:25:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('administrateur','encadreur','stagiaire') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stagiaire',
  `encadreur_id` int DEFAULT NULL,
  `niveau_etude` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_stage` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_session_actuelle` int DEFAULT NULL,
  `note_final` decimal(4,2) DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `password`, `telephone`, `role`, `encadreur_id`, `niveau_etude`, `type_stage`, `date_inscription`, `id_session_actuelle`, `note_final`, `observations`) VALUES
(5, 'Simo', 'Aubin', 'aubinborissimotsebo@gmail.com', '$2y$10$L2IxNjgxNWjeBpcuofmaA.SYW5OJsUzeUyOlUk.osvJnCOZBKzJsa', NULL, 'administrateur', NULL, NULL, NULL, '2026-02-10 14:44:05', NULL, NULL, NULL),
(6, 'Nisi commodi aliqua', 'Delectus nesciunt ', 'hujytin@mailinator.com', '$2y$10$ViXtfjM5G623IcGT739oYO6MeGsJCia5yjZza.ly.llI57pJLFCTu', NULL, 'stagiaire', 13, NULL, NULL, '2026-02-10 16:49:32', 2, NULL, NULL),
(7, 'Laboris proident fu', 'Fugiat illo quia qu', 'navut@mailinator.com', '$2y$10$mngN7l01PfEL9gRIm7nzoOLm1NxRpobs6Sf.jHeXS4YFsuHzEZklq', NULL, 'stagiaire', 13, NULL, NULL, '2026-02-10 16:50:39', 2, NULL, NULL),
(8, 'Et et eum corrupti ', 'Quia sint voluptate ', 'xima@mailinator.com', '$2y$10$SszZnPhhfmIbAuppcOOgu.PXJQ6OTrgTB50IWGZ22CI9Xd3bJnP8e', NULL, 'administrateur', NULL, NULL, NULL, '2026-02-10 16:50:50', NULL, NULL, NULL),
(11, 'Blanditiis placeat ', 'Minima in earum quis', 'volyxujuqa@mailinator.com', '$2y$10$XfdSm8YOM8SG5zIrzG9q3.WMRmpqQ3GuTjagzVQ/ewzLfDcldmlQq', '+1 (793) 318-1379', 'stagiaire', 13, NULL, NULL, '2026-02-11 04:02:29', 2, NULL, NULL),
(12, 'Fuga Voluptatum rep', 'Exercitation sapient', 'puqucojuw@mailinator.com', '$2y$10$icBxrS3H9bVdINhTA44VPeGwhqPVw7jLUrf8toyFc1ctIOuUDrZWC', '+1 (132) 438-8427', 'stagiaire', 13, NULL, NULL, '2026-02-11 04:03:07', 2, NULL, NULL),
(13, 'Ex eos quia placeat', 'Eligendi eveniet do', 'nilyhim@mailinator.com', '12345678', '+1 (256) 601-2789', 'encadreur', 10, 'BAC ou équivalent', 'professionnel', '2026-02-11 04:33:32', NULL, NULL, NULL),
(14, 'Bro', 'Bro', 'hey@gmail.com', '$2y$10$JtKkVf63Yj.YfnL5.qyCUeBeQ7wiiMGIo4hCXtKGFKC1hZUumzTWC', NULL, 'stagiaire', 13, NULL, NULL, '2026-02-11 08:17:44', 2, 14.00, NULL),
(15, 'Quia laboriosam ill', 'Voluptatem odio eum ', 'gisado@mailinator.com', '$2y$10$xeX3eBL3vi9cuKQNjSEO4uCWpvcmoowRUeVSSVuNnSz66JaEuP.qC', NULL, 'stagiaire', 13, NULL, NULL, '2026-02-11 08:21:28', 2, NULL, NULL),
(16, 'Aute explicabo Duis', 'Reprehenderit modi l', 'culefex@mailinator.com', '$2y$10$drKy8LmPH/l5e0KOdQzC3uKDe0PtqSQXZ5HN35bOP.TjdxdKjhHDW', NULL, 'stagiaire', 13, NULL, NULL, '2026-02-11 08:21:36', 2, NULL, NULL),
(17, 'HELLE', 'DANNISS', 'raqudun@mailinator.com', '$2y$10$s51vKrch1vrgPppiO0gHaehFSJ76F6tNGOufSM2XXrr18MkvoPA4W', '+1 (182) 526-2665', 'stagiaire', 13, 'BAC ou équivalent', 'professionnel', '2026-02-11 19:38:55', 2, NULL, NULL),
(18, 'Sunt repudiandae es', 'Et non recusandae A', 'conepefu@mailinator.com', '$2y$10$bWatN5GjcSaTFUSvb0gNruB8uQpri70A.qMNIpcLFyAPzSSBA4bDC', '+1 (289) 922-3979', 'stagiaire', 13, 'BEPC ou équivalent', 'academique', '2026-02-11 19:38:58', 2, NULL, NULL),
(19, 'Aubin', 'SImo', 'simoaubinboris@gmail.com', '$2y$10$XcmKnHGKcZgNMf5dUjSH0OjeM7tVUHuMOwTAdBR7bqkrFUANkzr9K', NULL, 'encadreur', 13, NULL, NULL, '2026-02-12 13:01:42', 2, NULL, NULL),
(20, 'Voluptatem aut disti', 'Voluptas consectetur', 'teva@mailinator.com', '$2y$10$E0gRgekoYFwT9yk7rwjY8.FGR7ehxMXBCelIrtRKL9QXQm1fu1ywK', NULL, 'stagiaire', 19, NULL, NULL, '2026-02-12 13:45:50', 2, 14.00, 'Pas mal de ton cote'),
(21, 'Doloribus ut officia', 'Voluptatibus esse mi', 'zitowyn@mailinator.com', '$2y$10$8DG/m7EEuxVX/CSToajUGeqqVNlxZXlOPhfOFHvOqfMgsHcFvB7b6', NULL, 'stagiaire', 19, NULL, NULL, '2026-02-12 13:45:54', 2, 10.00, 'PAs mal'),
(22, 'Dolore earum ipsam e', 'Deserunt esse assume', 'gegaga@mailinator.com', '$2y$10$VB2k0C4tEKrqJqOviQ8cxuykTtuXlOsMl36aQULhVHBS.q0ek0Bna', NULL, 'stagiaire', 19, NULL, NULL, '2026-02-12 13:46:00', 2, 15.00, 'COngrats'),
(23, 'Explicabo Quam ex i', 'Incidunt consequat', 'vysavu@mailinator.com', '$2y$10$IDejuSej6sSm1n/MYZdWZOcu.lp1DQu1POlrL0sKQ0LZWt3uLGcem', NULL, 'stagiaire', 19, NULL, NULL, '2026-02-12 13:46:06', 2, 14.00, 'COngrats');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `demandes`
--
ALTER TABLE `demandes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_stagiaire` (`id_stagiaire`),
  ADD KEY `fk_incident_session` (`id_session`);

--
-- Indexes for table `rapports`
--
ALTER TABLE `rapports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_stagiaire` (`id_stagiaire`),
  ADD KEY `id_session` (`id_session`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_stagiaire` (`id_stagiaire`),
  ADD KEY `id_encadreur` (`id_encadreur`);

--
-- Indexes for table `taches`
--
ALTER TABLE `taches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tache_stagiaire` (`id_stagiaire`),
  ADD KEY `fk_tache_encadreur` (`id_encadreur`),
  ADD KEY `fk_tache_session` (`id_session`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `demandes`
--
ALTER TABLE `demandes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rapports`
--
ALTER TABLE `rapports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `taches`
--
ALTER TABLE `taches`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `fk_incident_session` FOREIGN KEY (`id_session`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`id_stagiaire`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rapports`
--
ALTER TABLE `rapports`
  ADD CONSTRAINT `rapports_ibfk_1` FOREIGN KEY (`id_stagiaire`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rapports_ibfk_2` FOREIGN KEY (`id_session`) REFERENCES `sessions` (`id`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`id_stagiaire`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`id_encadreur`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `taches`
--
ALTER TABLE `taches`
  ADD CONSTRAINT `fk_tache_encadreur` FOREIGN KEY (`id_encadreur`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tache_session` FOREIGN KEY (`id_session`) REFERENCES `sessions` (`id`),
  ADD CONSTRAINT `fk_tache_stagiaire` FOREIGN KEY (`id_stagiaire`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `taches_ibfk_1` FOREIGN KEY (`id_stagiaire`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
