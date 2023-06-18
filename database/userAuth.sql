-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2023 at 05:18 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `userauth`
--

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_attempts`
--

CREATE TABLE `password_reset_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_attempts`
--

INSERT INTO `password_reset_attempts` (`id`, `email`, `timestamp`) VALUES
(1, 'elvocool47@gmail.com', 1687101441);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `last_failed_login` int(11) DEFAULT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `reset_code` varchar(32) DEFAULT NULL,
  `reset_code_expiration` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `login_attempts`, `last_failed_login`, `locked`, `reset_code`, `reset_code_expiration`, `created_at`, `updated_at`) VALUES
(1, 'Elvis', 'elvocool47@gmail.com', '$2y$10$2ygmRWp.uArEmu.aYLr0f.PdsILGRCLXpQYhdNJ8eS76knaux.aVS', 0, NULL, 0, '204805', 1687105042, '2023-06-18 15:17:08', '2023-06-18 15:17:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `password_reset_attempts`
--
ALTER TABLE `password_reset_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `password_reset_attempts`
--
ALTER TABLE `password_reset_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
