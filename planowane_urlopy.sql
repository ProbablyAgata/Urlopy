-- phpMyAdmin SQL Dump
-- Host: 127.0.0.1
-- Wersja serwera: 10.4.32-MariaDB

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `planowane_urlopy`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT UNIQUE,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pracownik','manager') NOT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Struktura tabeli dla tabeli `wnioski_urlopowe`
--

CREATE TABLE `wnioski_urlopowe` (
  `id` int(11) NOT NULL AUTO_INCREMENT UNIQUE,
  `employee_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `poczatek_urlopu` date NOT NULL,
  `koniec_urlopu` date NOT NULL,
  `powod` text NOT NULL,
  `status` enum('oczekujacy','zatwierdzony','odrzucony') DEFAULT 'oczekujacy',
  `komentarz_kadra` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeksy dla tabeli `wnioski_urlopowe`
--
ALTER TABLE `wnioski_urlopowe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wnioski_urlopowe`
--
ALTER TABLE `wnioski_urlopowe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for table `wnioski_urlopowe`
--
ALTER TABLE `wnioski_urlopowe`
  ADD CONSTRAINT `wnioski_urlopowe_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

UPDATE users 
SET password = '$2y$10$YourNewHashHere'
WHERE username = 'Agata_Ochocinska';