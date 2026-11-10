-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2026 at 10:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `instruire`
--

-- --------------------------------------------------------

--
-- Table structure for table `inscrieri`
--

CREATE TABLE `inscrieri` (
  `id` int(11) NOT NULL,
  `nume` varchar(100) NOT NULL,
  `prenume` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `data_nasterii` date NOT NULL,
  `oras` varchar(100) NOT NULL,
  `adresa` varchar(255) DEFAULT NULL,
  `curs` varchar(100) NOT NULL,
  `nivel` varchar(100) NOT NULL,
  `competente` text NOT NULL,
  `experienta` int(11) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `motivatie` text NOT NULL,
  `observatii` text DEFAULT NULL,
  `acceptarea` tinyint(1) NOT NULL,
  `promotional` varchar(50) DEFAULT NULL,
  `pret_initial` decimal(10,2) NOT NULL,
  `reducere` decimal(10,2) NOT NULL,
  `pret_final` decimal(10,2) NOT NULL,
  `tip_profil` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inscrieri`
--

INSERT INTO `inscrieri` (`id`, `nume`, `prenume`, `email`, `telefon`, `data_nasterii`, `oras`, `adresa`, `curs`, `nivel`, `competente`, `experienta`, `website`, `motivatie`, `observatii`, `acceptarea`, `promotional`, `pret_initial`, `reducere`, `pret_final`, `tip_profil`, `created_at`) VALUES
(1, 'Adina', 'vacarciuc', 'adinavacarciuc@gmail.com', '+37367987687', '2001-01-29', 'Bălți', 'str stefan cel mare', 'Frontend Development', 'Începător', 'PHP, SQL, Git', 1, 'https://ceiti.md/', 'eu am o motivatie in conformitate cu legislatia republicii moldova', 'e foarte cuvinte omul acesta care elebaoreaza probleme interesante', 1, NULL, 2200.00, 220.00, 1980.00, 'Profil în dezvoltare', '2026-10-28 09:55:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inscrieri`
--
ALTER TABLE `inscrieri`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inscrieri`
--
ALTER TABLE `inscrieri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
